<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

/**
 * There constants are added in v0.4.0 of extension.
 */
if (!\defined('WIN32_INFO_SERVICE')) {
    /* Win32 Recovery Constants */
    \define('WIN32_SC_ACTION_NONE', 0x00000000, false);    /* 0x00000000 No Action */
    \define('WIN32_SC_ACTION_REBOOT', 0x00000001, false);  /* 0x00000001 Reboot the computer */
    \define('WIN32_SC_ACTION_RESTART', 0x00000002, false);    /* 0x00000002 Restart the service */
    \define('WIN32_SC_ACTION_RUN_COMMAND', 0x00000003, false);    /* 0x00000003 Run the command */

    /* Win32 Service informations */
    \define('WIN32_INFO_SERVICE', 'service', false);
    \define('WIN32_INFO_DISPLAY', 'display', false);
    \define('WIN32_INFO_USER', 'user', false);
    \define('WIN32_INFO_PASSWORD', 'password', false);
    \define('WIN32_INFO_PATH', 'path', false);
    \define('WIN32_INFO_PARAMS', 'params', false);
    \define('WIN32_INFO_DESCRIPTION', 'description', false);
    \define('WIN32_INFO_START_TYPE', 'start_type', false);
    \define('WIN32_INFO_LOAD_ORDER', 'load_order', false);
    \define('WIN32_INFO_SVC_TYPE', 'svc_type', false);
    \define('WIN32_INFO_ERROR_CONTROL', 'error_control', false);
    \define('WIN32_INFO_DELAYED_START', 'delayed_start', false);
    \define('WIN32_INFO_BASE_PRIORITY', 'base_priority', false);
    \define('WIN32_INFO_DEPENDENCIES', 'dependencies', false);
    \define('WIN32_INFO_RECOVERY_DELAY', 'recovery_delay', false);
    \define('WIN32_INFO_RECOVERY_ACTION_1', 'recovery_action_1', false);
    \define('WIN32_INFO_RECOVERY_ACTION_2', 'recovery_action_2', false);
    \define('WIN32_INFO_RECOVERY_ACTION_3', 'recovery_action_3', false);
    \define('WIN32_INFO_RECOVERY_RESET_PERIOD', 'recovery_reset_period', false);
    \define('WIN32_INFO_RECOVERY_ENABLED', 'recovery_enabled', false);
    \define('WIN32_INFO_RECOVERY_REBOOT_MSG', 'recovery_reboot_msg', false);
    \define('WIN32_INFO_RECOVERY_COMMAND', 'recovery_command', false);
}

if (!\defined('WIN32_SERVICE_CONTROL_INTERROGATE')) {
    \define('WIN32_SERVICE_CONTROL_CONTINUE', 0x00000003, false);
    \define('WIN32_SERVICE_CONTROL_INTERROGATE', 0x00000004, false);
    \define('WIN32_SERVICE_CONTROL_PAUSE', 0x00000002, false);
    \define('WIN32_SERVICE_CONTROL_STOP', 0x00000001, false);
    \define('WIN32_SERVICE_STOPPED', 0x0000001, false);
    \define('WIN32_SERVICE_START_PENDING', 0x0000002, false);
    \define('WIN32_SERVICE_STOP_PENDING', 0x0000003, false);
    \define('WIN32_SERVICE_RUNNING', 0x0000004, false);
    \define('WIN32_SERVICE_CONTINUE_PENDING', 0x0000005, false);
    \define('WIN32_SERVICE_PAUSE_PENDING', 0x0000006, false);
    \define('WIN32_SERVICE_PAUSED', 0x0000007, false);

    \define('WIN32_ERROR_ACCESS_DENIED', 0x00000005, false);
    \define('WIN32_ERROR_CIRCULAR_DEPENDENCY', 0x00000423, false);
    \define('WIN32_ERROR_DATABASE_DOES_NOT_EXIST', 0x00000429, false);
    \define('WIN32_ERROR_DEPENDENT_SERVICES_RUNNING', 0x0000041B, false);
    \define('WIN32_ERROR_DUPLICATE_SERVICE_NAME', 0x00000436, false);
    \define('WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT', 0x00000427, false);
    \define('WIN32_ERROR_INSUFFICIENT_BUFFER', 0x0000007A, false);
    \define('WIN32_ERROR_INVALID_DATA', 0x0000000D, false);
    \define('WIN32_ERROR_INVALID_HANDLE', 0x00000006, false);
    \define('WIN32_ERROR_INVALID_LEVEL', 0x0000007C, false);
    \define('WIN32_ERROR_INVALID_NAME', 0x0000007B, false);
    \define('WIN32_ERROR_INVALID_PARAMETER', 0x00000057, false);
    \define('WIN32_ERROR_INVALID_SERVICE_ACCOUNT', 0x00000421, false);
    \define('WIN32_ERROR_INVALID_SERVICE_CONTROL', 0x0000041C, false);
    \define('WIN32_ERROR_PATH_NOT_FOUND', 0x00000003, false);
    \define('WIN32_ERROR_SERVICE_ALREADY_RUNNING', 0x00000420, false);
    \define('WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL', 0x00000425, false);
    \define('WIN32_ERROR_SERVICE_DATABASE_LOCKED', 0x0000041F, false);
    \define('WIN32_ERROR_SERVICE_DEPENDENCY_DELETED', 0x00000433, false);
    \define('WIN32_ERROR_SERVICE_DEPENDENCY_FAIL', 0x0000042C, false);
    \define('WIN32_ERROR_SERVICE_DISABLED', 0x00000422, false);
    \define('WIN32_ERROR_SERVICE_DOES_NOT_EXIST', 0x00000424, false);
    \define('WIN32_ERROR_SERVICE_EXISTS', 0x00000431, false);
    \define('WIN32_ERROR_SERVICE_LOGON_FAILED', 0x0000042D, false);
    \define('WIN32_ERROR_SERVICE_MARKED_FOR_DELETE', 0x00000430, false);
    \define('WIN32_ERROR_SERVICE_NO_THREAD', 0x0000041E, false);
    \define('WIN32_ERROR_SERVICE_NOT_ACTIVE', 0x00000426, false);
    \define('WIN32_ERROR_SERVICE_REQUEST_TIMEOUT', 0x0000041D, false);
    \define('WIN32_ERROR_SHUTDOWN_IN_PROGRESS', 0x0000045B, false);
    \define('WIN32_ERROR_SERVICE_SPECIFIC_ERROR', 0x0000042A, false);

    \define('WIN32_NO_ERROR', 0x00000000, false);
}

if (!\function_exists('win32_start_service_ctrl_dispatcher')) {
    function win32_start_service_ctrl_dispatcher($serviceName)
    {
        return true;
    }
}

if (!\function_exists('win32_get_last_control_message')) {
    function win32_get_last_control_message()
    {
        return WIN32_SERVICE_CONTROL_INTERROGATE;
    }
}

if (!\function_exists('win32_set_service_status')) {
    $GLOBALS['__serviceStatus'] = WIN32_SERVICE_START_PENDING;
    function win32_set_service_status($status)
    {
        $GLOBALS['__serviceStatus'] = $status;
    }

    function win32_query_service_status($ServiceName, $Machine)
    {
        return ['CurrentState' => $GLOBALS['__serviceStatus']];
    }
}
if (!\function_exists('win32_set_service_exit_mode')) {
    function win32_set_service_exit_mode($GracefulExit)
    {
    }
}
if (!\function_exists('win32_set_service_exit_code')) {
    function win32_set_service_exit_code($ExitCode)
    {
    }
}
if (!\function_exists('win32_send_custom_control')) {
    function win32_send_custom_control($ServiceName, $Control, $Machine)
    {
    }
}
