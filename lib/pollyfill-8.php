<?php

declare(strict_types=1);
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */
if (\PHP_VERSION_ID < 80000) {
    return;
}
/*
 * There constants are added in v0.4.0 of extension.
 */
if (!\defined('WIN32_INFO_SERVICE')) {
    /* Win32 Recovery Constants */
    \define('WIN32_SC_ACTION_NONE', 0x00000000);    /* 0x00000000 No Action */
    \define('WIN32_SC_ACTION_RESTART', 0x00000001);    /* 0x00000001 Restart the service */
    \define('WIN32_SC_ACTION_REBOOT', 0x00000002);  /* 0x00000002 Reboot the computer */
    \define('WIN32_SC_ACTION_RUN_COMMAND', 0x00000003);    /* 0x00000003 Run the command */

    /* Win32 Service information */
    \define('WIN32_INFO_SERVICE', 'service');
    \define('WIN32_INFO_DISPLAY', 'display');
    \define('WIN32_INFO_USER', 'user');
    \define('WIN32_INFO_PASSWORD', 'password');
    \define('WIN32_INFO_PATH', 'path');
    \define('WIN32_INFO_PARAMS', 'params');
    \define('WIN32_INFO_DESCRIPTION', 'description');
    \define('WIN32_INFO_START_TYPE', 'start_type');
    \define('WIN32_INFO_LOAD_ORDER', 'load_order');
    \define('WIN32_INFO_SVC_TYPE', 'svc_type');
    \define('WIN32_INFO_ERROR_CONTROL', 'error_control');
    \define('WIN32_INFO_DELAYED_START', 'delayed_start');
    \define('WIN32_INFO_BASE_PRIORITY', 'base_priority');
    \define('WIN32_INFO_DEPENDENCIES', 'dependencies');
    \define('WIN32_INFO_RECOVERY_DELAY', 'recovery_delay');
    \define('WIN32_INFO_RECOVERY_ACTION_1', 'recovery_action_1');
    \define('WIN32_INFO_RECOVERY_ACTION_2', 'recovery_action_2');
    \define('WIN32_INFO_RECOVERY_ACTION_3', 'recovery_action_3');
    \define('WIN32_INFO_RECOVERY_RESET_PERIOD', 'recovery_reset_period');
    \define('WIN32_INFO_RECOVERY_ENABLED', 'recovery_enabled');
    \define('WIN32_INFO_RECOVERY_REBOOT_MSG', 'recovery_reboot_msg');
    \define('WIN32_INFO_RECOVERY_COMMAND', 'recovery_command');
}

if (!\defined('WIN32_SERVICE_CONTROL_INTERROGATE')) {
    \define('WIN32_SERVICE_CONTROL_CONTINUE', 0x00000003);
    \define('WIN32_SERVICE_CONTROL_INTERROGATE', 0x00000004);
    \define('WIN32_SERVICE_CONTROL_PAUSE', 0x00000002);
    \define('WIN32_SERVICE_CONTROL_STOP', 0x00000001);
    \define('WIN32_SERVICE_STOPPED', 0x0000001);
    \define('WIN32_SERVICE_START_PENDING', 0x0000002);
    \define('WIN32_SERVICE_STOP_PENDING', 0x0000003);
    \define('WIN32_SERVICE_RUNNING', 0x0000004);
    \define('WIN32_SERVICE_CONTINUE_PENDING', 0x0000005);
    \define('WIN32_SERVICE_PAUSE_PENDING', 0x0000006);
    \define('WIN32_SERVICE_PAUSED', 0x0000007);

    \define('WIN32_ERROR_ACCESS_DENIED', 0x00000005);
    \define('WIN32_ERROR_CIRCULAR_DEPENDENCY', 0x00000423);
    \define('WIN32_ERROR_DATABASE_DOES_NOT_EXIST', 0x00000429);
    \define('WIN32_ERROR_DEPENDENT_SERVICES_RUNNING', 0x0000041B);
    \define('WIN32_ERROR_DUPLICATE_SERVICE_NAME', 0x00000436);
    \define('WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT', 0x00000427);
    \define('WIN32_ERROR_INSUFFICIENT_BUFFER', 0x0000007A);
    \define('WIN32_ERROR_INVALID_DATA', 0x0000000D);
    \define('WIN32_ERROR_INVALID_HANDLE', 0x00000006);
    \define('WIN32_ERROR_INVALID_LEVEL', 0x0000007C);
    \define('WIN32_ERROR_INVALID_NAME', 0x0000007B);
    \define('WIN32_ERROR_INVALID_PARAMETER', 0x00000057);
    \define('WIN32_ERROR_INVALID_SERVICE_ACCOUNT', 0x00000421);
    \define('WIN32_ERROR_INVALID_SERVICE_CONTROL', 0x0000041C);
    \define('WIN32_ERROR_PATH_NOT_FOUND', 0x00000003);
    \define('WIN32_ERROR_SERVICE_ALREADY_RUNNING', 0x00000420);
    \define('WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL', 0x00000425);
    \define('WIN32_ERROR_SERVICE_DATABASE_LOCKED', 0x0000041F);
    \define('WIN32_ERROR_SERVICE_DEPENDENCY_DELETED', 0x00000433);
    \define('WIN32_ERROR_SERVICE_DEPENDENCY_FAIL', 0x0000042C);
    \define('WIN32_ERROR_SERVICE_DISABLED', 0x00000422);
    \define('WIN32_ERROR_SERVICE_DOES_NOT_EXIST', 0x00000424);
    \define('WIN32_ERROR_SERVICE_EXISTS', 0x00000431);
    \define('WIN32_ERROR_SERVICE_LOGON_FAILED', 0x0000042D);
    \define('WIN32_ERROR_SERVICE_MARKED_FOR_DELETE', 0x00000430);
    \define('WIN32_ERROR_SERVICE_NO_THREAD', 0x0000041E);
    \define('WIN32_ERROR_SERVICE_NOT_ACTIVE', 0x00000426);
    \define('WIN32_ERROR_SERVICE_REQUEST_TIMEOUT', 0x0000041D);
    \define('WIN32_ERROR_SHUTDOWN_IN_PROGRESS', 0x0000045B);
    \define('WIN32_ERROR_SERVICE_SPECIFIC_ERROR', 0x0000042A);

    \define('WIN32_NO_ERROR', 0x00000000);
}

if (!\function_exists('win32_start_service_ctrl_dispatcher')) {
    /**
     * @throws ValueError            on invalid parameter
     * @throws Win32ServiceException when current SAPI is not 'cli'
     */
    function win32_start_service_ctrl_dispatcher(string $name, bool $gracefulMode = true): void
    {
    }
}

if (!\function_exists('win32_get_last_control_message')) {
    /**
     * @throws Win32ServiceException when current SAPI is not 'cli'
     */
    function win32_get_last_control_message(): int
    {
        return WIN32_SERVICE_CONTROL_INTERROGATE;
    }
}

if (!\function_exists('win32_set_service_status')) {
    \define('WIN32_FAKE_SERVICE_STATUS', '__serviceStatus');
    $GLOBALS[WIN32_FAKE_SERVICE_STATUS] = WIN32_SERVICE_START_PENDING;
    /**
     * @throws ValueError            on invalid parameter
     * @throws Win32ServiceException when current SAPI is not 'cli'
     */
    function win32_set_service_status(int $status, int $checkpoint = 0): void
    {
        $GLOBALS[WIN32_FAKE_SERVICE_STATUS] = $status;
    }

    /**
     * @throws ValueError            when $serviceName is empty
     * @throws Win32ServiceException on error
     */
    function win32_query_service_status(string $serviceName, ?string $machine = null): array
    {
        return ['CurrentState' => $GLOBALS[WIN32_FAKE_SERVICE_STATUS]];
    }
}
if (!\function_exists('win32_set_service_exit_mode')) {
    /**
     * Empty function beacause, it not work on non WINNT operating System.
     * Declared only for method exists.
     *
     * @throws Win32ServiceException when current SAPI is not 'cli'
     */
    function win32_set_service_exit_mode(bool $gracefulMode = true): bool
    {
        return $gracefulMode;
    }
}
if (!\function_exists('win32_set_service_exit_code')) {
    /**
     * Empty function beacause, it not work on non WINNT operating System.
     * Declared only for method exists.
     *
     * @throws Win32ServiceException when current SAPI is not 'cli'
     */
    function win32_set_service_exit_code(int $exitCode = 1): int
    {
        return $exitCode;
    }
}
if (!\function_exists('win32_send_custom_control')) {
    /**
     * Empty function beacause, it not work on non WINNT operating System.
     * Declared only for method exists.
     *
     * @throws ValueError            when $serviceName is empty string
     * @throws ValueError            when $control is not beetween 128 and 255
     * @throws Win32ServiceException on error
     */
    function win32_send_custom_control(string $serviceName, int $control, ?string $machine = null): void
    {
    }
}

if (!class_exists(\Win32ServiceException::class)) {
    class Win32ServiceException extends \Exception
    {
    }
}
