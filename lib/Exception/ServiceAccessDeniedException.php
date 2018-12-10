<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Exception;

use Throwable;

/**
 * Class ServiceAccessDeniedException the user used for run the script does not have the requested right for this action
 * @package Win32Service\Exception
 */
class ServiceAccessDeniedException extends Win32ServiceException
{
    public function __construct(string $message = "Access to service is denied", int $code = WIN32_ERROR_ACCESS_DENIED)
    {
        parent::__construct($message, $code);
    }
}
