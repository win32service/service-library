<?php

declare(strict_types=1);
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Win32Service\Model\ServiceIdentifier;
use Win32Service\Model\ServiceInformations;

class ServiceInformationsTest extends TestCase
{
    public function testInfos(): void
    {
        $infos = new ServiceInformations(ServiceIdentifier::identify('serviceId'), 'Service name', 'My greet service wrote in PHP', 'myService.php', 'run');

        $this->assertSame('serviceId', $infos['service']);
    }
}
