<?php

declare(strict_types=1);
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Win32Service\Exception\ServiceRegistrationException;
use Win32Service\Exception\Win32ServiceException;
use Win32Service\Model\ServiceIdentifier;
use Win32Service\Model\ServiceInformations;
use Win32Service\Service\ServiceAdminManager;
use Win32Service\Tests\Stub\Win32ServiceFunctions;

class ServiceAdminManagerTest extends TestCase
{
    protected function setUp(): void
    {
        Win32ServiceFunctions::reset();
    }

    public function testRegistration(): void
    {
        Win32ServiceFunctions::willReturn('win32_query_service_status', WIN32_ERROR_SERVICE_DOES_NOT_EXIST);
        Win32ServiceFunctions::willReturn('win32_create_service', WIN32_NO_ERROR);

        (new ServiceAdminManager())->registerService($this->infos());

        $this->assertCount(1, Win32ServiceFunctions::calls('win32_create_service'));
    }

    public function testRegistrationError(): void
    {
        Win32ServiceFunctions::willReturn('win32_query_service_status', WIN32_ERROR_SERVICE_DOES_NOT_EXIST);
        Win32ServiceFunctions::willReturn('win32_create_service', new \Win32ServiceException('error', WIN32_ERROR_DUPLICATE_SERVICE_NAME));

        $this->expectException(Win32ServiceException::class);

        (new ServiceAdminManager())->registerService($this->infos());
    }

    public function testRegistrationTwice(): void
    {
        Win32ServiceFunctions::willReturn('win32_query_service_status', ['CurrentState' => WIN32_SERVICE_STOPPED]);
        Win32ServiceFunctions::willReturn('win32_create_service', WIN32_NO_ERROR);

        $this->expectException(ServiceRegistrationException::class);
        $this->expectExceptionMessage('Unable to register an existant service');

        (new ServiceAdminManager())->registerService($this->infos());
    }

    private function infos(): ServiceInformations
    {
        return new ServiceInformations(
            ServiceIdentifier::identify('servideId'),
            'Test Service Add',
            'My description',
            'me.php',
            'run'
        );
    }
}
