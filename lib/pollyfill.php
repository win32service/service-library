<?php
/**
 * @copy In Extenso (c) 2018
 * Added by : cameleon at 10/12/18 11:05
 */

/**
 * There constants are added in v0.4.0 of extension
 */
if (!defined("WIN32_INFO_SERVICE")) {
    define("WIN32_INFO_SERVICE", "service");
    define("WIN32_INFO_DISPLAY", "display");
    define("WIN32_INFO_USER", "user");
    define("WIN32_INFO_PASSWORD", "password");
    define("WIN32_INFO_PATH", "path");
    define("WIN32_INFO_PARAMS", "params");
    define("WIN32_INFO_DESCRIPTION", "description");
    define("WIN32_INFO_START_TYPE", "start_type");
}

if (!defined('WIN32_SERVICE_CONTROL_INTERROGATE')) {
    define("WIN32_SERVICE_CONTROL_CONTINUE",                0x00000003, true);
    define("WIN32_SERVICE_CONTROL_INTERROGATE",             0x00000004, true);
    define("WIN32_SERVICE_CONTROL_PAUSE",                   0x00000002, true);
    define("WIN32_SERVICE_CONTROL_STOP",                    0x00000001, true);
    define("WIN32_SERVICE_STOPPED",             0x0000001, true);
    define("WIN32_SERVICE_START_PENDING",       0x0000002, true);
    define("WIN32_SERVICE_STOP_PENDING",        0x0000003, true);
    define("WIN32_SERVICE_RUNNING",             0x0000004, true);
    define("WIN32_SERVICE_CONTINUE_PENDING",    0x0000005, true);
    define("WIN32_SERVICE_PAUSE_PENDING",       0x0000006, true);
    define("WIN32_SERVICE_PAUSED",              0x0000007, true);
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
    function win32_send_custom_control($ServiceName, $Machine) {

    }
}

