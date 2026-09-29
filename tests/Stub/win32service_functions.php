<?php

declare(strict_types=1);

namespace Win32Service\Service;

use Win32Service\Tests\Stub\Win32ServiceFunctions;

function win32_query_service_status(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_query_service_status', $args);
}

function win32_create_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_create_service', $args);
}

function win32_delete_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_delete_service', $args);
}

function win32_start_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_start_service', $args);
}

function win32_stop_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_stop_service', $args);
}

function win32_pause_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_pause_service', $args);
}

function win32_continue_service(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_continue_service', $args);
}

function win32_send_custom_control(mixed ...$args): mixed
{
    return Win32ServiceFunctions::call('win32_send_custom_control', $args);
}
