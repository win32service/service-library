<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Service;

use Win32Service\Exception\ServiceAccessDeniedException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentificator;
use Win32Service\Model\ServiceStatus;

trait ServiceInformationsTrait
{
    /**
     * Check if the requested service exists.
     *
     * @throws ServiceAccessDeniedException
     * @throws ServiceStatusException
     */
    protected function serviceExists(ServiceIdentificator $serviceId): bool
    {
        $exists = true;
        try {
            $this->getServiceInformations($serviceId);
        } catch (ServiceNotFoundException) {
            //Ok, the script can register the service
            $exists = false;
        }

        return $exists;
    }

    /**
     * @throws ServiceStatusException       an error occurs when read the service information
     * @throws ServiceNotFoundException     the requested service does not exists
     * @throws ServiceAccessDeniedException the user used for run the script does not have the requested right for this action
     */
    protected function getServiceInformations(ServiceIdentificator $service): ServiceStatus
    {
        try {
            $infos = win32_query_service_status($service->serviceId(), $service->machine());
        } catch (\Win32ServiceException $e) {
            $infos = $e->getCode();
        }
        $this->checkResponseAndConvertInExceptionIfNeed($infos, $service);

        if (!\is_array($infos)) {
            throw new ServiceStatusException('Error on read service status', $infos);
        }

        return new ServiceStatus($infos);
    }

    /**
     *
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     */
    protected function checkResponseAndConvertInExceptionIfNeed(mixed $value, ServiceIdentificator $service): void
    {
        if ($value === WIN32_ERROR_SERVICE_DOES_NOT_EXIST) {
            throw new ServiceNotFoundException('Service '.$service->serviceId().' is not found');
        }
        if ($value === WIN32_ERROR_ACCESS_DENIED) {
            throw new ServiceAccessDeniedException('Access to service '.$service->serviceId().' is denied');
        }
    }

    /**
     * @throws Win32ServiceException
     */
    protected function throwExceptionIfError(mixed $value, string $exceptionClass, string $message): void
    {
        if (class_exists($exceptionClass) === false || is_a(
            $exceptionClass,
            Win32ServiceException::class,
            true
        ) === false) {
            throw new Win32ServiceException(sprintf('Cannot throw object as it does not extend Exception or implement Throwable. Class provided "%s"', $exceptionClass));
        }

        if ($value !== WIN32_NO_ERROR) {
            throw new $exceptionClass($message, $value);
        }
    }
}
