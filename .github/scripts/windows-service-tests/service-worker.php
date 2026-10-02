<?php

declare(strict_types=1);

/*
 * Minimal service runner used by lifecycle-test.php to exercise a real
 * registration/start/stop/delete cycle against the win32service extension.
 * It is launched by the Windows Service Control Manager, not by Composer
 * autoloading, so it requires the library's autoloader itself.
 */

require __DIR__.'/../../../vendor/autoload.php';

use Win32Service\Model\AbstractServiceRunner;
use Win32Service\Model\ServiceIdentifier;

final class FunctionalTestServiceRunner extends AbstractServiceRunner
{
    protected function setup(): void
    {
    }

    protected function run(int $control): void
    {
        usleep(200000);
    }

    protected function beforePause(): void
    {
    }

    protected function beforeContinue(): void
    {
    }

    protected function lastRunIsTooSlow(float $duration): void
    {
    }

    protected function beforeStop(): void
    {
    }
}

$serviceName = $argv[1] ?? null;
if ($serviceName === null) {
    fwrite(STDERR, 'Missing service name argument.'.PHP_EOL);
    exit(1);
}

$runner = new FunctionalTestServiceRunner();
$runner->setServiceId(ServiceIdentifier::identify($serviceName));
$runner->doRun();
