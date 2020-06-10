<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

/**
 * There constants are added in v0.4.0 of extension
 */
if (!defined("WIN32_INFO_SERVICE")) {
    /* Win32 Recovery Constants */
    define("WIN32_SC_ACTION_NONE",          0x00000000, true);	/* 0x00000000 No Action */
    define("WIN32_SC_ACTION_REBOOT",        0x00000001, true);  /* 0x00000001 Reboot the computer */
    define("WIN32_SC_ACTION_RESTART",       0x00000002, true);	/* 0x00000002 Restart the service */
    define("WIN32_SC_ACTION_RUN_COMMAND",   0x00000003, true);	/* 0x00000003 Run the command */

    /* Win32 Service informations */
    define("WIN32_INFO_SERVICE", "service");
    define("WIN32_INFO_DISPLAY", "display");
    define("WIN32_INFO_USER", "user");
    define("WIN32_INFO_PASSWORD", "password");
    define("WIN32_INFO_PATH", "path");
    define("WIN32_INFO_PARAMS", "params");
    define("WIN32_INFO_DESCRIPTION", "description");
    define("WIN32_INFO_START_TYPE", "start_type");
    define("WIN32_INFO_LOAD_ORDER", "load_order");
    define("WIN32_INFO_SVC_TYPE", "svc_type");
    define("WIN32_INFO_ERROR_CONTROL", "error_control");
    define("WIN32_INFO_DELAYED_START", "delayed_start");
    define("WIN32_INFO_BASE_PRIORITY", "base_priority");
    define("WIN32_INFO_RECOVERY_DELAY", "recovery_delay");
    define("WIN32_INFO_RECOVERY_ACTION_1", "recovery_action_1");
    define("WIN32_INFO_RECOVERY_ACTION_2", "recovery_action_2");
    define("WIN32_INFO_RECOVERY_ACTION_3", "recovery_action_3");
    define("WIN32_INFO_RECOVERY_RESET_PERIOD", "recovery_reset_period");
    define("WIN32_INFO_RECOVERY_ENABLED", "recovery_enabled");
    define("WIN32_INFO_RECOVERY_REBOOT_MSG", "recovery_reboot_msg");
    define("WIN32_INFO_RECOVERY_COMMAND", "recovery_command");
}

if (!defined('WIN32_SERVICE_CONTROL_INTERROGATE')) {
    define("WIN32_SERVICE_CONTROL_CONTINUE",                0x00000003, true);
    define("WIN32_SERVICE_CONTROL_INTERROGATE",             0x00000004, true);
    define("WIN32_SERVICE_CONTROL_PAUSE",                   0x00000002, true);
    define("WIN32_SERVICE_CONTROL_STOP",                    0x00000001, true);
    define("WIN32_SERVICE_STOPPED",                         0x0000001, true);
    define("WIN32_SERVICE_START_PENDING",                   0x0000002, true);
    define("WIN32_SERVICE_STOP_PENDING",                    0x0000003, true);
    define("WIN32_SERVICE_RUNNING",                         0x0000004, true);
    define("WIN32_SERVICE_CONTINUE_PENDING",                0x0000005, true);
    define("WIN32_SERVICE_PAUSE_PENDING",                   0x0000006, true);
    define("WIN32_SERVICE_PAUSED",                          0x0000007, true);
    define("WIN32_ERROR_ACCESS_DENIED",                     0x00000005, true);
    define("WIN32_ERROR_SERVICE_DOES_NOT_EXIST",            0x00000424, true);
    define("WIN32_NO_ERROR",                                0x00000000, true);
}

if (!function_exists('win32_start_service_ctrl_dispatcher')) {
    function win32_start_service_ctrl_dispatcher($serviceName) {
        return true;
    }
}

if (!function_exists('win32_get_last_control_message')) {
    function win32_get_last_control_message() {
        return WIN32_SERVICE_CONTROL_INTERROGATE;
    }
}

if (!function_exists('win32_set_service_status')) {
    $GLOBALS['__serviceStatus'] = WIN32_SERVICE_START_PENDING;
    function win32_set_service_status($status) {
        $GLOBALS['__serviceStatus'] = $status;
    }
    function win32_query_service_status($ServiceName, $Machine) {
        return ['CurrentState'=>$GLOBALS['__serviceStatus']];
    }
}
if (!function_exists('win32_set_service_exit_mode')) {
    function win32_set_service_exit_mode($GracefulExit) {

    }
}
if (!function_exists('win32_set_service_exit_code')) {
    function win32_set_service_exit_code($ExitCode) {

    }
}
if (!function_exists('win32_send_custom_control')) {
    function win32_send_custom_control($ServiceName, $Control, $Machine) {

    }
}

