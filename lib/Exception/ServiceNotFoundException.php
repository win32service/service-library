<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Exception;

/**
 * Class ServiceNotFoundException the requested service does not exists.
 */
class ServiceNotFoundException extends Win32ServiceException
{
    public function __construct(string $message = 'The service is not found', int $code = WIN32_ERROR_SERVICE_DOES_NOT_EXIST)
    {
        parent::__construct($message, $code);
    }
}
