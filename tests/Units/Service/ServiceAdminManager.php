<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Tests\Units\Service;

use atoum;
use mock;

/**
 * Class ServiceStateManager
 * @package Win32Service\Tests\Units\Service
 * @extensions win32service
 */
class ServiceAdminManager extends atoum
{
    public function testRegistration()
    {
        $this->assert('start')
            ->given($this->newTestedInstance())
            ->if($this->function->win32_query_service_status = ['CurrentState'=>WIN32_ERROR_SERVICE_DOES_NOT_EXIST])
            ->and($this->function->win32_create_service = WIN32_NO_ERROR)
            ->then
            ->variable($this->testedInstance->registerService(
                new \Win32Service\Model\ServiceInformations(
                    \Win32Service\Model\ServiceIdentifier::identify('servideId'),
                    'Test Service Add',
                    'My description',
                    'me.php',
                    'run'
                )
            ))->isNull
        ;
    }
    public function testRegistrationTwice()
    {
        $this->assert('start')
            ->given($this->newTestedInstance())
            ->if($this->function->win32_query_service_status = ['CurrentState'=>WIN32_SERVICE_STOPPED])
            ->and($this->function->win32_create_service = WIN32_NO_ERROR)
            ->then
            ->exception(function () {
                $this->testedInstance->registerService(
                    new \Win32Service\Model\ServiceInformations(
                        \Win32Service\Model\ServiceIdentifier::identify('servideId'),
                        'Test Service Add',
                        'My description',
                        'me.php',
                        'run'
                    )
                );
            })->isInstanceOf('Win32Service\Exception\ServiceRegistrationException')->hasMessage('Unable to register an existant service')
        ;
    }
}
