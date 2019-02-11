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

if (!function_exists('win32_start_service_ctrl_dispatcher')) {
    function win32_start_service_ctrl_dispatcher($serviceName) {
        return true;
    }
}

if (!function_exists('win32_get_last_control_message')) {
    function win32_get_last_control_message($serviceName) {
        return WIN32_SERVICE_CONTROL_INTERROGATE;
    }
}

if (!function_exists('win32_set_service_status')) {
    function win32_set_service_status($status) {

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

