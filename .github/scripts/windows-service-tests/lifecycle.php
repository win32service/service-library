<?php

declare(strict_types=1);

/*
 * Shared helpers for the functional tests exercising the real win32service
 * extension: register a service, start it, stop it, then delete it, failing
 * loudly (and trying to clean up) if any step does not behave as expected.
 */

require __DIR__.'/../../../vendor/autoload.php';

use Win32Service\Model\ServiceIdentifier;
use Win32Service\Model\ServiceInformations;
use Win32Service\Service\ServiceAdminManager;
use Win32Service\Service\ServiceStateManager;

function out(string $message): void
{
    fwrite(STDOUT, $message.PHP_EOL);
}

function waitForState(ServiceIdentifier $serviceId, int $expectedState, string $label, int $timeoutSeconds): void
{
    $deadline = microtime(true) + $timeoutSeconds;
    do {
        $status = win32_query_service_status($serviceId->serviceId(), $serviceId->machine());
        if (is_array($status) && ($status['CurrentState'] ?? null) === $expectedState) {
            return;
        }
        usleep(250000);
    } while (microtime(true) < $deadline);

    throw new RuntimeException(sprintf('Timed out waiting for the service to reach state "%s"', $label));
}

function runServiceLifecycleTest(string $serviceName, string $workerScript, string $displayName, int $startTimeoutSeconds): void
{
    $serviceId = ServiceIdentifier::identify($serviceName);

    $infos = new ServiceInformations(
        $serviceId,
        $displayName,
        'Service created by the CI functional test suite',
        $workerScript,
        $serviceName
    );

    $admin = new ServiceAdminManager();
    $state = new ServiceStateManager();
    $registered = false;

    try {
        out(sprintf('Registering service "%s"...', $serviceName));
        $admin->registerService($infos);
        $registered = true;
        out('Service registered.');

        out('Starting service...');
        $state->startService($serviceId);
        waitForState($serviceId, WIN32_SERVICE_RUNNING, 'running', $startTimeoutSeconds);
        out('Service is running.');

        out('Stopping service...');
        $state->stopService($serviceId);
        waitForState($serviceId, WIN32_SERVICE_STOPPED, 'stopped', 30);
        out('Service is stopped.');

        out('Unregistering (deleting) service...');
        $admin->unregisterService($serviceId);
        $registered = false;
        out('Service unregistered.');

        out('Functional test succeeded: register, start, stop and delete all worked.');
    } catch (Throwable $e) {
        fwrite(STDERR, sprintf('Functional test failed: %s%s', $e->getMessage(), PHP_EOL));

        if ($registered) {
            try {
                waitForState($serviceId, WIN32_SERVICE_STOPPED, 'stopped (cleanup)', 10);
            } catch (Throwable) {
                // Best effort cleanup, ignore and try to delete anyway.
            }
            try {
                $admin->unregisterService($serviceId);
            } catch (Throwable $cleanupError) {
                fwrite(STDERR, 'Cleanup (unregister) failed: '.$cleanupError->getMessage().PHP_EOL);
            }
        }

        exit(1);
    }
}
