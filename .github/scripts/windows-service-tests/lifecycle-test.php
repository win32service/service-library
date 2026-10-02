<?php

declare(strict_types=1);

/*
 * Functional test exercising the real win32service extension: registers a
 * service, starts it, stops it, then deletes it, failing loudly (and trying
 * to clean up) if any step does not behave as expected. This only runs on
 * the windows-extension-tests workflow, against a real PHP + extension.
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

function waitForState(ServiceIdentifier $serviceId, int $expectedState, string $label, int $timeoutSeconds = 30): void
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

$serviceName = 'win32service_lib_functional_test_'.getmypid();
$serviceId = ServiceIdentifier::identify($serviceName);
$workerScript = __DIR__.'\\service-worker.php';

$infos = new ServiceInformations(
    $serviceId,
    'Win32Service Library functional test',
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
    waitForState($serviceId, WIN32_SERVICE_RUNNING, 'running');
    out('Service is running.');

    out('Stopping service...');
    $state->stopService($serviceId);
    waitForState($serviceId, WIN32_SERVICE_STOPPED, 'stopped');
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
