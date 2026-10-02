<?php

declare(strict_types=1);

/*
 * Simulates a service whose process takes longer than 30 seconds to do its
 * heavy bootstrapping, such as a large Symfony application warming up its
 * cache, before it even registers with the Service Control Manager. The
 * delay happens before doRun() (and therefore before
 * win32_start_service_ctrl_dispatcher() is called), so the SCM is left
 * waiting for the service to connect at all, which is what actually triggers
 * its own "did not respond in a timely fashion" timeout (event 7009) -
 * sleeping inside setup() only affects the START_PENDING checkpoint, which
 * is too late to reproduce that behaviour.
 */

require __DIR__.'/../../../vendor/autoload.php';

use Win32Service\Model\AbstractServiceRunner;
use Win32Service\Model\ServiceIdentifier;

final class SlowStartFunctionalTestServiceRunner extends AbstractServiceRunner
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

// Simulate the slow bootstrap (e.g. Symfony cache warmup) before the
// process even connects to the Service Control Manager.
sleep(35);

$runner = new SlowStartFunctionalTestServiceRunner();
$runner->setServiceId(ServiceIdentifier::identify($serviceName));
$runner->doRun();
