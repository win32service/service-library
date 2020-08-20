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
 * Class ServiceAccessDeniedException the user used for run the script does not have the requested right for this action.
 */
class ServiceAccessDeniedException extends Win32ServiceException
{
    public function __construct(string $message = 'Access to service is denied', int $code = WIN32_ERROR_ACCESS_DENIED)
    {
        parent::__construct($message, $code);
    }
}
