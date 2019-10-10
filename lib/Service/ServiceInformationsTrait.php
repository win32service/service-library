<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Service;


use Win32Service\Exception\ServiceAccessDeniedException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceRegistrationException;
use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentificator;
use Win32Service\Model\ServiceStatus;

trait ServiceInformationsTrait
{
    /**
     * @param ServiceIdentificator $service
     *
     * @return ServiceStatus
     * @throws ServiceStatusException an error occurs when read the service information
     * @throws ServiceNotFoundException the requested service does not exists
     * @throws ServiceAccessDeniedException the user used for run the script does not have the requested right for this action
     */
    protected function getServiceInformations(ServiceIdentificator $service): ServiceStatus {
        $infos = win32_query_service_status($service->serviceId(), $service->machine());

        $this->checkResponseAndConvertInExceptionIfNeed($infos, $service);

        if (!is_array($infos)) {
            throw new ServiceStatusException('Error on read service status', $infos);
        }

        return new ServiceStatus($infos);
    }

    /**
     * Check if the requested service exists
     * @param ServiceIdentificator $serviceId
     * @return bool
     * @throws ServiceAccessDeniedException
     * @throws ServiceStatusException
     */
    protected function serviceExists(ServiceIdentificator $serviceId) {
        $exists = true;
        try {
            $this->getServiceInformations($serviceId);
        }catch (ServiceNotFoundException $e) {
            //Ok, the script can register the service
            $exists = false;
        }
        return $exists;
    }

    /**
     * @param $value
     * @param ServiceIdentificator $service
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     */
    protected function checkResponseAndConvertInExceptionIfNeed($value, ServiceIdentificator $service) {
        if ($value === WIN32_ERROR_SERVICE_DOES_NOT_EXIST) {
            throw new ServiceNotFoundException('Service '.$service->serviceId().' is not found');
        }
        if ($value === WIN32_ERROR_ACCESS_DENIED) {
            throw new ServiceAccessDeniedException('Access to service '.$service->serviceId().' is denied');
        }
    }

    /**
     * @param $value
     * @param string $exceptionClass
     * @param string $message
     * @throws Win32ServiceException
     */
    protected function throwExceptionIfError($value, $exceptionClass, $message) {
        if ($value !== WIN32_NO_ERROR) {
            throw new $exceptionClass($message, $value);
        }
    }

}
