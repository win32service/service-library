<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Model;

class ServiceStatus
{
    const CURRENT_STATE = 'CurrentState';
    private $datas;

    public function __construct(array $datas)
    {
        $this->datas = $datas;
    }

    public function isStopped(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_STOPPED;
    }

    public function isRunning(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_RUNNING;
    }

    public function isPaused(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_PAUSED;
    }

    public function starting(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_START_PENDING;
    }
}
