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
use Win32Service\Model\ServiceIdentifier;
use Win32Service\Service\ServiceStateManager;
use Win32Service\Tests\Stub\Win32ServiceFunctions;

class ServiceStateManagerTest extends TestCase
{
    protected function setUp(): void
    {
        Win32ServiceFunctions::reset();
    }

    public function testChangeState(): void
    {
        Win32ServiceFunctions::willReturn('win32_query_service_status', ['CurrentState' => WIN32_SERVICE_STOPPED]);
        Win32ServiceFunctions::willReturn('win32_start_service', WIN32_NO_ERROR);

        (new ServiceStateManager())->startService(ServiceIdentifier::identify('servideId'));

        $this->assertCount(1, Win32ServiceFunctions::calls('win32_start_service'));
    }
}
