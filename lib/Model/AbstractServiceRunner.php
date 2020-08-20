<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Model;

use Win32Service\Exception\ServiceAccessDeniedException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\StopLoopException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Service\ServiceInformationsTrait;

abstract class AbstractServiceRunner implements RunnerServiceInterface
{
    use ServiceInformationsTrait;

    /**
     * @var ServiceIdentificator|null
     */
    private $serviceId;

    /**
     * @var bool
     */
    private $paused;
    /**
     * @var float
     */
    private $slowRunduration;

    /**
     * @var float
     */
    private $lastRunDuration;

    /**
     * @var bool
     */
    private $stopRequested;

    /**
     * @var int
     */
    private $threadNumber;

    public function __construct()
    {
        $this->paused = false;
        $this->serviceId = null;
        $this->threadNumber = -1;
        $this->stopRequested = false;
        $this->slowRunduration = 0.0;
        $this->lastRunDuration = 0.0;
    }

    /**
     * Implement this function setup the service before run loop.
     */
    abstract protected function setup(): void;

    public function setServiceId(ServiceIdentificator $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    public function getThreadNumber(): int
    {
        return $this->threadNumber;
    }

    /**
     * Return the last duration execution of run().
     */
    public function lastRunDuration(): float
    {
        return $this->lastRunDuration;
    }

    /**
     * Return the slowet time of run() execution.
     */
    public function slowRunDuration(): float
    {
        return $this->slowRunduration;
    }

    /**
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     */
    public function doRun(int $maxRun = -1, int $threadNumber = -1): void
    {
        if ($this->serviceId === null) {
            throw new Win32ServiceException(sprintf("Unable to start a service without ServiceIdentificator. Please call method '%s::setServiceId' before call '%s'", static::class, __METHOD__));
        }

        $this->threadNumber = $threadNumber;
        if ($threadNumber > -1) {
            $this->serviceId = new ServiceIdentifier(
                sprintf($this->serviceId->serviceId(), $threadNumber),
                $this->serviceId->machine()
            );
        }
        $this->init($maxRun);

        $loopCount = 0;
        if (win32_start_service_ctrl_dispatcher($this->serviceId->serviceId()) !== true) {
            throw new Win32ServiceException('Error on start service controller');
        }
        $status = $this->getServiceInformations($this->serviceId);
        if (!$status->starting()) {
            throw new ServiceStatusException('Service is not in starting...');
        }
        win32_set_service_status(WIN32_SERVICE_START_PENDING);
        $this->setup();
        win32_set_service_status(WIN32_SERVICE_RUNNING);

        while (($ctr_msg = win32_get_last_control_message()) != WIN32_SERVICE_CONTROL_STOP && !$this->stopRequested) {
            if ($ctr_msg === WIN32_SERVICE_CONTROL_INTERROGATE) {
                win32_set_service_status($this->paused ? WIN32_SERVICE_PAUSED : WIN32_SERVICE_RUNNING);
            } elseif ($ctr_msg === WIN32_SERVICE_CONTROL_CONTINUE && $status->isPaused()) {
                $this->paused = false;
                win32_set_service_status(WIN32_SERVICE_CONTINUE_PENDING);
                $this->beforeContinue();
                win32_set_service_status(WIN32_SERVICE_RUNNING);
            } elseif ($ctr_msg === WIN32_SERVICE_CONTROL_PAUSE && $status->isRunning()) {
                $this->paused = true;
                win32_set_service_status(WIN32_SERVICE_PAUSE_PENDING);
                $this->beforePause();
                win32_set_service_status(WIN32_SERVICE_PAUSED);
            }

            if (!$this->paused) {
                try {
                    ++$loopCount;
                    if ($maxRun > 0 && $loopCount > $maxRun) {
                        throw new StopLoopException('Max loop reached');
                    }
                    // If not paused, run the action loop.
                    $startRun = microtime(true);
                    $this->run($ctr_msg);
                    $this->lastRunDuration = microtime(true) - $startRun;
                    if ($this->slowRunduration < $this->lastRunDuration) {
                        $this->slowRunduration = $this->lastRunDuration;
                    }
                    // if run is too slow, call the special function on service
                    if ($this->lastRunDuration > 30.0) {
                        $this->lastRunIsTooSlow($this->lastRunDuration);
                    }
                } catch (StopLoopException $e) {
                    $this->requestStop();
                }
            }

            $status = $this->getServiceInformations($this->serviceId);
        }
        win32_set_service_status(WIN32_SERVICE_STOP_PENDING);
        $this->beforeStop();
        win32_set_service_status(WIN32_SERVICE_STOPPED);
    }

    /**
     * Request stop the service without use the Service Manager.
     */
    public function requestStop(): void
    {
        $this->stopRequested = true;
    }

    /**
     * Define how the script do exit. If exit with $exitGraceful = false and $exitCode > 0, the recovery paramaters
     * defined for the service will be executed. Otherwise, the service stop without recovery operation.
     *
     * See this page for exit code value :
     *
     * @param bool $exitGraceful If true, the PHP srcipt exit without error. If false, the exit is alway with error.
     * @param int  $exitCode     if $exitGraceful is false, this value must be geater than 0
     */
    public function defineExitModeAndCode(bool $exitGraceful, int $exitCode = 1): void
    {
        if (!\function_exists('win32_set_service_exit_mode')) {
            return;
        }

        win32_set_service_exit_mode($exitGraceful);
        win32_set_service_exit_code($exitCode);
    }

    /**
     * Implement this function for run short code before service continue (after pause).
     */
    abstract protected function beforeContinue(): void;

    /**
     * Implement this function for run short code before service entering in pause status.
     */
    abstract protected function beforePause(): void;

    /**
     * This function is running in loop. The running duration is limited to 30 seconds.
     * If your code work over 30 seconds, please consider using the generator.
     *
     * @See http://php.net/manual/en/language.generators.syntax.php
     * @See http://php.net/manual/en/class.generator.php
     *
     * @param int $control contains the last control
     */
    abstract protected function run(int $control): void;

    /**
     * Implement this function for tun short code if the main run code is too slow.
     */
    abstract protected function lastRunIsTooSlow(float $duration): void;

    /**
     * Implement this function for run short code before service entering in stop status.
     */
    abstract protected function beforeStop(): void;

    /**
     * Return the value if stopping service is requested.
     * If you use a loop in the `run` function, please check if stop is requested for each loop.
     * And break the loop if this function return true.
     */
    protected function stopRequested(): bool
    {
        return $this->stopRequested;
    }

    /**
     * Check and initialize the service.
     *
     * @throws Win32ServiceException
     */
    private function init(int $maxRun): void
    {
        if ($this->serviceId === null) {
            throw new  Win32ServiceException('Please run '.__CLASS__.'::__construct');
        }

        if (strtolower(PHP_OS) !== 'winnt' && $maxRun < 1) {
            throw new  Win32ServiceException('Please define runMax argument greater than 0');
        }
    }
}
