<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Service;


use Win32Service\Exception\InvalidServiceStatusException;
use Win32Service\Exception\ServiceStateActionException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentificator;

class ServiceStateManager
{
    use ServiceInformationsTrait;

    /**
     * @param ServiceIdentificator $serviceId
     * @throws InvalidServiceStatusException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function startService(ServiceIdentificator $serviceId)
    {
        $this->actionForService($serviceId, 'start', 'Stopped');
    }

    /**
     * @param ServiceIdentificator $serviceId
     * @throws InvalidServiceStatusException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function stopService(ServiceIdentificator $serviceId)
    {
        $this->actionForService($serviceId, 'stop', 'Running');
    }

    /**
     * @param ServiceIdentificator $serviceId
     * @throws InvalidServiceStatusException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function pauseService(ServiceIdentificator $serviceId)
    {
        $this->actionForService($serviceId, 'pause', 'Running');
    }

    /**
     * @param ServiceIdentificator $serviceId
     * @throws InvalidServiceStatusException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function continueService(ServiceIdentificator $serviceId)
    {
        $this->actionForService($serviceId, 'continue', 'Paused');
    }

    /**
     * @param ServiceIdentificator $serviceId
     * @param int $control
     * @throws Win32ServiceException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     */
    public function sendCustomControl(ServiceIdentificator $serviceId, int $control)
    {
        $result = win32_send_custom_control($serviceId->serviceId(), $serviceId->machine(), $control);
        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId);
        $this->throwExceptionIfError(
            $result,
            Win32ServiceException::class,
            sprintf('Unable to custom control %d', $control)
        );
    }

    /**
     * @param ServiceIdentificator $serviceId
     * @param string $action
     * @param string $check
     * @throws InvalidServiceStatusException
     * @throws \Win32Service\Exception\ServiceAccessDeniedException
     * @throws \Win32Service\Exception\ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    private function actionForService(ServiceIdentificator $serviceId, string $action, string $check)
    {
        $status = $this->getServiceInformations($serviceId);

        $function = sprintf('is%s', $check);

        if (!method_exists($status, $function)) {
            throw new \LogicException(sprintf('The class %s does not implements %s', get_class($status), $function));
        }

        if(!$status->{$function}()) {
            throw new InvalidServiceStatusException(sprintf('The service is not %s',$check));
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
                # code...
                break;
        }

        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId);
        $this->throwExceptionIfError(
            $result,
            ServiceStateActionException::class,
            sprintf('Unable to %s service', $action)
        );

    }
}
