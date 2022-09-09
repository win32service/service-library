<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Service;

use LogicException;
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
            $errorMessage = $e->getMessage();
        }
        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId, $errorMessage ?? '');
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
            throw new LogicException(sprintf('The class %s does not implements %s', $status::class, $function));
        }

        if ($status->{$function}() === false) {
            throw new InvalidServiceStatusException(sprintf('The service is not %s', $check));
        }

        try {
            $result = match ($action) {
                'start' => win32_start_service($serviceId->serviceId(), $serviceId->machine()),
                'stop' => win32_stop_service($serviceId->serviceId(), $serviceId->machine()),
                'pause' => win32_pause_service($serviceId->serviceId(), $serviceId->machine()),
                'continue' => win32_continue_service($serviceId->serviceId(), $serviceId->machine()),
                default => throw new ServiceStateActionException(sprintf('Action "%s" for service is unknown', $action)),
            };
        } catch (\Win32ServiceException $e) {
            $result = $e->getCode();
            $errorMessage = $e->getMessage();
        }

        $this->checkResponseAndConvertInExceptionIfNeed($result, $serviceId, $errorMessage ?? null);
        $this->throwExceptionIfError(
            $result,
            ServiceStateActionException::class,
            sprintf('Unable to %s service. %s', $action, $errorMessage ?? '')
        );
    }
}
