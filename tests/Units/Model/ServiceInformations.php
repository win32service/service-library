<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Tests\Units\Model;

use atoum;
use Win32Service\Model\ServiceIdentifier;

class ServiceInformations extends atoum
{
    public function testInfos() {
        $this->if($this->newTestedInstance(ServiceIdentifier::identify('serviceId'), 'Service name', 'My greet service wrote in PHP', 'myService.php', 'run'))
            ->then
            ->assert('init')
                ->object($this->testedInstance)->isInstanceOf('Win32Service\Model\ServiceInformations')
            ->string($this->testedInstance['service'])->isEqualTo('serviceId')
            ;
    }
}
