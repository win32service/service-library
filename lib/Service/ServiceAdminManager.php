<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Service;

use Win32Service\Exception\ServiceAccessDeniedException;
use Win32Service\Exception\InvalidServiceStatusException;
use Win32Service\Exception\ServiceAlreadyRegistredException;
use Win32Service\Exception\ServiceMarkedForDeleteException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceRegistrationException;
use Win32Service\Exception\ServiceUnregistrationException;
use Win32Service\Model\ServiceIdentificator;
use Win32Service\Model\ServiceInformations;

/**
 * Class ServiceAdminManager
 * This service allow you to registrer and unregister the service from ServiceInformations
 * This action need Administrator priveleges
 */
class ServiceAdminManager
{
    use ServiceInformationsTrait;

    /**
     * Register a new service
     * @param ServiceInformations $infos
     * @throws ServiceAccessDeniedException
     * @throws ServiceRegistrationException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws ServiceNotFoundException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function registerService(ServiceInformations $infos)
    {
        if ($this->serviceExists($infos)) {
            throw new ServiceAlreadyRegistredException('Unable to register an existant service', 400);
        }

        $result = win32_create_service($infos->toArray(), $infos->machine());

        $this->checkResponseAndConvertInExceptionIfNeed($result, $infos);
        $this->throwExceptionIfError($result, ServiceRegistrationException::class, 'Error occured during registration service');

    }

    /**
     * Remove the service from the service manager. The service do it stopped before unregistration.
     * @param ServiceIdentificator $infos
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws \Win32Service\Exception\ServiceStatusException
     * @throws \Win32Service\Exception\Win32ServiceException
     */
    public function unregisterService(ServiceIdentificator $infos) {

        $status = $this->getServiceInformations($infos);

        if (!$status->isStopped()) {
            throw new InvalidServiceStatusException('The service can be stoppedd before unregistration');
        }

        $result = win32_delete_service($infos->serviceId(), $infos->machine());

        $this->checkResponseAndConvertInExceptionIfNeed($result, $infos);
        if ($result === WIN32_ERROR_SERVICE_MARKED_FOR_DELETE) {
            throw new ServiceMarkedForDeleteException('The service is marked for delete. Please reboot the computer.', $result);
        }
        $this->throwExceptionIfError($result, ServiceUnregistrationException::class, 'Error occured during unregistration service');
    }
}
