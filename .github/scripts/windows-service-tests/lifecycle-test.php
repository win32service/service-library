<?php

declare(strict_types=1);

/*
 * Nominal case: register, start, stop and delete a service whose setup()
 * returns quickly.
 */

require __DIR__.'/lifecycle.php';

runServiceLifecycleTest(
    'win32service_lib_functional_test',
    __DIR__.'\\service-worker.php',
    'Win32Service Library functional test',
    30
);
