<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Tests\Units\Service;

use atoum;
use mock;

/**
 * Class ServiceStateManager
 * @package Win32Service\Tests\Units\Service
 * @extensions win32service
 */
class ServiceStateManager extends atoum
{
    public function testChangeState()
    {
        $this->assert('start')
            ->given($this->newTestedInstance())
            ->if($this->function->win32_query_service_status = ['CurrentState'=>WIN32_SERVICE_STOPPED])
            ->and($this->function->win32_start_service = WIN32_NO_ERROR)
            ->then
            ->variable($this->testedInstance->startService(\Win32Service\Model\ServiceIdentifier::identify('servideId')))->isNull
        ;
    }
}
