<?php

declare(strict_types=1);

/*
 * Simulates a service whose setup() takes longer than 30 seconds, such as a
 * large Symfony application warming up its cache before it can serve
 * requests. The service stays in the START_PENDING state for the whole
 * duration of setup().
 */

require __DIR__.'/../../../vendor/autoload.php';

use Win32Service\Model\AbstractServiceRunner;
use Win32Service\Model\ServiceIdentifier;

final class SlowStartFunctionalTestServiceRunner extends AbstractServiceRunner
{
    protected function setup(): void
    {
        sleep(35);
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

$runner = new SlowStartFunctionalTestServiceRunner();
$runner->setServiceId(ServiceIdentifier::identify($serviceName));
$runner->doRun();
