<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Service;

use Win32Service\Exception\InvalidServiceStatusException;
use Win32Service\Exception\ServiceAccessDeniedException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceStateActionException;
use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentificator;

class ServiceStateManager
{
    use ServiceInformationsTrait;

    /**
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    public function startService(ServiceIdentificator $serviceId): void
    {
        $this->actionForService($serviceId, 'start', 'Stopped');
    }

    /**
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    public function stopService(ServiceIdentificator $serviceId): void
    {
        $this->actionForService($serviceId, 'stop', 'Running');
    }

    /**
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    public function pauseService(ServiceIdentificator $serviceId): void
    {
        $this->actionForService($serviceId, 'pause', 'Running');
    }

    /**
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    public function continueService(ServiceIdentificator $serviceId): void
    {
        $this->actionForService($serviceId, 'continue', 'Paused');
    }

    /**
     * @throws Win32ServiceException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     */
    public function sendCustomControl(ServiceIdentificator $serviceId, int $control): void
    {
        try {
            $result = win32_send_custom_control($serviceId->serviceId(), $serviceId->machine(), $control);
        } catch (\Win32ServiceException $e) {
            $result = $e->getCode();
        }
        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId);
        $this->throwExceptionIfError(
            $result,
            Win32ServiceException::class,
            sprintf('Unable to custom control %d', $control)
        );
    }

    /**
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    private function actionForService(ServiceIdentificator $serviceId, string $action, string $check): void
    {
        $status = $this->getServiceInformations($serviceId);

        $function = sprintf('is%s', $check);

        if (!method_exists($status, $function)) {
            throw new \LogicException(sprintf('The class %s does not implements %s', \get_class($status), $function));
        }

        if ($status->{$function}() === false) {
            throw new InvalidServiceStatusException(sprintf('The service is not %s', $check));
        }

        switch ($action) {
            case 'start':
                $result = win32_start_service($serviceId->serviceId(), $serviceId->machine());
                break;
            case 'stop':
                $result = win32_stop_service($serviceId->serviceId(), $serviceId->machine());
                break;
            case 'pause':
                $result = win32_pause_service($serviceId->serviceId(), $serviceId->machine());
                break;
            case 'continue':
                $result = win32_continue_service($serviceId->serviceId(), $serviceId->machine());
                break;
            default:
                throw new ServiceStateActionException(sprintf('Action "%s" for service is unknown', $action));
        }

        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId);
        $this->throwExceptionIfError(
            $result,
            ServiceStateActionException::class,
            sprintf('Unable to %s service', $action)
        );
    }
}
