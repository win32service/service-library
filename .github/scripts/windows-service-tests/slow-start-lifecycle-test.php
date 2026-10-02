<?php

declare(strict_types=1);

/*
 * Slow-start case: the service needs more than 30 seconds before reporting
 * it is running (e.g. a big Symfony project warming up its cache in
 * setup()). The service must still be registerable, startable, stoppable
 * and deletable, it just stays in START_PENDING for longer.
 */

require __DIR__.'/lifecycle.php';

runServiceLifecycleTest(
    'win32service_lib_slow_start_test',
    __DIR__.'\\slow-start-service-worker.php',
    'Win32Service Library slow-start functional test',
    90
);
