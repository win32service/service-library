<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Service;


use Win32Service\Exception\InvalidServiceStatusException;
use Win32Service\Exception\ServiceStateActionException;
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
        $this->actionForService($serviceId, 'win32_start_service', 'isStopped');
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
        $this->actionForService($serviceId, 'win32_stop_service', 'isRunning');
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
        $this->actionForService($serviceId, 'win32_pause_service', 'isRunning');
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
        $this->actionForService($serviceId, 'win32_continue_service', 'isPaused');
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

        if (!$status->{$check}()) {
            throw new InvalidServiceStatusException('The service is not stopped');
        }

        $result = $action($serviceId->serviceId(), $serviceId->machine());

        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId);
        $this->throwExceptionIfError(
            $result,
            ServiceStateActionException::class,
            'Unable to start service'
        );

    }
}
