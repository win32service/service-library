<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Exception;

use Throwable;

/**
 * Class ServiceNotFoundException the requested service does not exists
 * @package Win32Service\Exception
 */
class ServiceNotFoundException extends Win32ServiceException
{
    public function __construct(string $message = "The service is not found", int $code = WIN32_ERROR_SERVICE_DOES_NOT_EXIST)
    {
        parent::__construct($message, $code);
    }
}
