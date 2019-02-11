<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Model;


use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Service\ServiceInformationsTrait;

abstract class AbstractServiceRunner
{
    use ServiceInformationsTrait;
    /**
     * @var ServiceIdentificator
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

    public function __construct(ServiceIdentificator $serviceId)
    {
        $this->serviceId = $serviceId;
        $this->paused = false;
        $this->slowRunduration = 0.0;
        $this->lastRunDuration = 0.0;
    }

    public function lastRunDuration(): float {
        return $this->lastRunDuration;
    }

    public function slowRunDuration(): float {
        return $this->slowRunduration;
    }

    /**
     * This function is runnning in loop. The running duration is limited to 30 seconds
     * @param int $control contains the last control.
     * @return void
     */
    protected abstract function run(int $control): void;

    /**
     * Implement this function setup the service before run loop
     */
    protected abstract function setup(): void;

    /**
     * Implement this function for run short code before service entering in pause status
     */
    protected abstract function beforePause(): void;

    /**
     * Implement this function for run short code before service continue (after pause)
     */
    protected abstract function beforeContinue(): void;

    /**
     * Implement this function for run short code before service entering in stop status
     */
    protected abstract function beforeStop(): void;

    /**
     * Implement this function for tun short code if the main run code is too slow
     */
    protected abstract function lastRunIsTooSlow(float $duration): void;

    /**
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     */
    public function doRun() {
        if (true !== win32_start_service_ctrl_dispatcher($this->serviceId->serviceId())) {
            throw new Win32ServiceException('Error on start service controller');
        }
        $status = $this->getServiceInformations($this->serviceId);
        if (!$status->starting()) {
            throw new ServiceStatusException("Service is not in starting...");
        }
        win32_set_service_status(WIN32_SERVICE_START_PENDING);
        $this->setup();

        while (WIN32_SERVICE_CONTROL_STOP != $ctr_msg = win32_get_last_control_message()) {
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
                // If not paused, run the action loop.
                $startRun = microtime(true);
                $this->run($ctr_msg);
                $this->lastRunDuration= microtime(true) - $startRun;
                if ($this->slowRunduration < $this->lastRunDuration) {
                    $this->slowRunduration = $this->lastRunDuration;
                }
                // if run ins too slow, call the special function on service
                if ($this->lastRunDuration > 30.0) {
                    $this->lastRunIsTooSlow($this->lastRunDuration);
                }
            }

            $status = $this->getServiceInformations($this->serviceId);
        }
        win32_set_service_status(WIN32_SERVICE_STOP_PENDING);
        $this->beforeStop();
        win32_set_service_status(WIN32_SERVICE_STOPPED);

    }

    /**
     * Define how the script do exit. If exit with $exitGraceful = false and $exitCode > 0, the recovery paramaters
     * defined for the service will be executed. Otherwise, the service stop without recovery operation.
     *
     * See this page for exit code value :
     *
     * @param bool $exitGraceful If true, the PHP srcipt exit without error. If false, the exit is alway with error.
     * @param int $exitCode If $exitGraceful is false, this value must be geater than 0.
     */
    protected function defineExitModeAndCode(bool $exitGraceful, int $exitCode = 1): void
    {
        if (!function_exists('win32_set_service_exit_mode')) {
            return;
        }

        win32_set_service_exit_mode($exitGraceful);
        win32_set_service_exit_code($exitCode);
    }
}

