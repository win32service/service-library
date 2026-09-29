# win32service library

Library to ease the use of the [Win32Service PHP extension](https://packagist.org/packages/win32service/win32service).
It lets you write a Windows service in PHP, register it in the Windows Service Manager, and start, stop, pause or
unregister it with an object-oriented API.

## Requirements

- Windows
- PHP 8.1 or later
- The `win32service` PHP extension

## Installation

### 1. Install the PHP extension

The extension is distributed through Packagist and is installed with [PIE](https://github.com/php/pie) (the PHP
Installer for Extensions):

```
pie install win32service/win32service
```

- PIE: <https://github.com/php/pie>
- Extension on Packagist: <https://packagist.org/packages/win32service/win32service>

Then check that the extension is loaded:

```
php --ri win32service
```

### 2. Install the library

```
composer require win32service/service-library
```

## Overview

| Class | Purpose |
|-------|---------|
| `Win32Service\Model\ServiceIdentifier` | Identifies a service by its name (and optionally a machine name). |
| `Win32Service\Model\ServiceInformations` | Describes a service to register (display name, script to run, user, dependencies, recovery settings...). |
| `Win32Service\Service\ServiceAdminManager` | Registers and unregisters a service. Needs Administrator privileges. |
| `Win32Service\Service\ServiceStateManager` | Starts, stops, pauses and continues a service. |
| `Win32Service\Model\AbstractServiceRunner` | Base class of the code that runs inside the service. |

## Identify a service

```php
use Win32Service\Model\ServiceIdentifier;

$id = ServiceIdentifier::identify('my_service');

// On a remote machine
$remoteId = ServiceIdentifier::identify('my_service', 'SERVER01');
```

Every manager method accepts any `ServiceIdentificator` (both `ServiceIdentifier` and `ServiceInformations` are one).

## Write the service

Create a class that extends `AbstractServiceRunner` and implement its hooks:

```php
<?php

use Win32Service\Model\AbstractServiceRunner;

final class MyService extends AbstractServiceRunner
{
    protected function setup(): void
    {
        // Called once, while the service is in the "start pending" state.
    }

    protected function run(int $control): void
    {
        // Called in a loop while the service is running (and not paused).
        // $control is the last control message received from the Service Manager.
        // A call should last less than 30 seconds.
        sleep(1);
    }

    protected function beforePause(): void
    {
        // Called before the service enters the "paused" state.
    }

    protected function beforeContinue(): void
    {
        // Called before the service leaves the "paused" state.
    }

    protected function lastRunIsTooSlow(float $duration): void
    {
        // Called when a call to run() lasted more than 30 seconds.
    }

    protected function beforeStop(): void
    {
        // Called before the service enters the "stopped" state. Release your resources here.
    }
}
```

Then create the script that the Service Manager will launch (for example `service.php`):

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Win32Service\Model\ServiceIdentifier;

$service = new MyService();
$service->setServiceId(ServiceIdentifier::identify('my_service'));
$service->doRun();
```

Useful methods of the runner:

- `requestStop()`: stops the service loop without using the Service Manager. Inside a long `run()`, check
  `$this->stopRequested()` and leave the loop when it returns `true`.
- `throw new StopLoopException()` from `run()` stops the service gracefully.
- `setCanPaused(bool $canPaused)`: defines whether the service accepts pause/continue. Call it before `doRun()`.
- `defineExitModeAndCode(bool $exitGraceful, int $exitCode = 1)`: if `$exitGraceful` is `false` and `$exitCode > 0`,
  the Windows recovery settings of the service are triggered when the script exits.
- `lastRunDuration()` and `slowRunDuration()`: duration of the last and of the slowest `run()` call.
- `doRun(int $maxRun = -1, int $threadNumber = -1)`: `$maxRun` limits the number of loops (it is required outside
  Windows, e.g. in tests). When `$threadNumber` is 0 or more, the service name is formatted with `sprintf`, which
  lets you run several instances such as `my_service_%d`.

The runner also reacts to the `PRESHUTDOWN` control message by stopping the service cleanly.

## Register a service

Registering a service needs Administrator privileges.

```php
use Win32Service\Model\ServiceIdentifier;
use Win32Service\Model\ServiceInformations;
use Win32Service\Service\ServiceAdminManager;

$infos = new ServiceInformations(
    ServiceIdentifier::identify('my_service'),
    'My service',                    // displayed name
    'Does something useful in PHP',  // description
    __DIR__.'\\service.php',         // script to run
    '--env=prod'                     // optional script arguments
);

$manager = new ServiceAdminManager();
$manager->registerService($infos);
```

The service runs `php-win.exe` from the directory of the current PHP binary.

Optional settings:

```php
// Run the service under a specific account
$infos->defineUserService('DOMAIN\\user', 'password');

// Start after these services
$infos->defineDependencies(['Tcpip', 'Dnscache']);

// Start automatically, but delayed
$infos->defineIfStartIsDelayed(true);

// Recovery settings: restart after 1 minute on the first two failures, do nothing afterwards
$infos->defineRecoverySettings(
    delay: 60000,          // milliseconds
    enabled: true,
    action_1: WIN32_SC_ACTION_RESTART,
    action_2: WIN32_SC_ACTION_RESTART,
    action_3: WIN32_SC_ACTION_NONE,
    reboot_msg: '',
    command: '',
    reset_period: 1440     // minutes before the failure counter is reset
);
```

Available recovery actions: `WIN32_SC_ACTION_NONE`, `WIN32_SC_ACTION_RESTART`, `WIN32_SC_ACTION_REBOOT` and
`WIN32_SC_ACTION_RUN_COMMAND` (the `command` argument is then required).

## Unregister a service

The service must be stopped first.

```php
$manager->unregisterService(ServiceIdentifier::identify('my_service'));
```

## Control a service

```php
use Win32Service\Service\ServiceStateManager;

$states = new ServiceStateManager();
$id = ServiceIdentifier::identify('my_service');

$states->startService($id);
$states->pauseService($id);
$states->continueService($id);
$states->stopService($id);

// Send a custom control code (128 to 255)
$states->sendCustomControl($id, 130);
```

Each action checks the current state of the service and throws `InvalidServiceStatusException` when the action is
not possible (for example stopping a service that is not running).

## Error handling

All exceptions extend `Win32Service\Exception\Win32ServiceException`.

| Exception | Meaning |
|-----------|---------|
| `ServiceNotFoundException` | The service does not exist. |
| `ServiceAccessDeniedException` | Administrator privileges are required. |
| `ServiceAlreadyRegistredException` | The service is already registered. |
| `ServiceRegistrationException` / `ServiceUnregistrationException` | The registration or unregistration failed. |
| `ServiceMarkedForDeleteException` | The service is marked for deletion; reboot the computer. |
| `InvalidServiceStatusException` | The requested action is not valid in the current state. |
| `ServiceStateActionException` | The Service Manager failed to apply the action. |
| `ServiceStatusException` | The service status could not be read or is unexpected. |
| `RecoveryActionException` | Throw it from the runner to exit without reporting the stop, so the Windows recovery action is triggered. |
| `StopLoopException` | Throw it from `run()` to stop the service loop gracefully. |

```php
use Win32Service\Exception\ServiceNotFoundException;
use Win32Service\Exception\Win32ServiceException;

try {
    $states->startService($id);
} catch (ServiceNotFoundException) {
    echo "The service is not registered.\n";
} catch (Win32ServiceException $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
```

## License

MIT
