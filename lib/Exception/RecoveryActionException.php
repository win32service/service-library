<?php

/**
 * @copyright Macintoshplus (c) 2024
 * Added by : Macintoshplus at 29/11/2024 17:51
 */

declare(strict_types=1);

namespace Win32Service\Exception;

/*
 * This exception can be thrown when you need to stop the service
 * to launch the recovery action configured on the current service
 */
class RecoveryActionException extends Win32ServiceException
{
}
