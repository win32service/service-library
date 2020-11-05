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
use Win32Service\Exception\ServiceAlreadyRegistredException;
use Win32Service\Exception\ServiceMarkedForDeleteException;
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\ServiceRegistrationException;
use Win32Service\Exception\ServiceStatusException;
use Win32Service\Exception\ServiceUnregistrationException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentificator;
use Win32Service\Model\ServiceInformations;

/**
 * Class ServiceAdminManager
 * This service allow you to registrer and unregister the service from ServiceInformations
 * This action need Administrator priveleges.
 */
class ServiceAdminManager
{
    use ServiceInformationsTrait;

    /**
     * Register a new service.
     *
     * @throws ServiceAccessDeniedException
     * @throws ServiceRegistrationException
     * @throws ServiceStatusException
     * @throws ServiceNotFoundException
     * @throws Win32ServiceException
     */
    public function registerService(ServiceInformations $infos): void
    {
        if ($this->serviceExists($infos)) {
            throw new ServiceAlreadyRegistredException('Unable to register an existant service', 400);
        }
        try {
            $result = win32_create_service($infos->toArray(), $infos->machine());
        } catch (\Win32ServiceException $e) {
            $result = $e->getCode();
        }
        $this->checkResponseAndConvertInExceptionIfNeed($result, $infos);
        $this->throwExceptionIfError(
            $result,
            ServiceRegistrationException::class,
            'Error occured during registration service'
        );
    }

    /**
     * Remove the service from the service manager. The service do it stopped before unregistration.
     *
     * @throws InvalidServiceStatusException
     * @throws ServiceAccessDeniedException
     * @throws ServiceNotFoundException
     * @throws ServiceStatusException
     * @throws Win32ServiceException
     */
    public function unregisterService(ServiceIdentificator $infos): void
    {
        $status = $this->getServiceInformations($infos);

        if (!$status->isStopped()) {
            throw new InvalidServiceStatusException('The service can be stoppedd before unregistration');
        }
        try {
            $result = win32_delete_service($infos->serviceId(), $infos->machine());
        } catch (\Win32ServiceException $e) {
            $result = $e->getCode();
        }
        $this->checkResponseAndConvertInExceptionIfNeed($result, $infos);
        if ($result === WIN32_ERROR_SERVICE_MARKED_FOR_DELETE) {
            throw new ServiceMarkedForDeleteException('The service is marked for delete. Please reboot the computer.', $result);
        }
        $this->throwExceptionIfError(
            $result,
            ServiceUnregistrationException::class,
            'Error occured during unregistration service'
        );
    }
}
