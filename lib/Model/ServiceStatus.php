<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
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


    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_STOPPED;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_RUNNING;
    }

    /**
     * @return bool
     */
    public function isPaused(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_PAUSED;
    }

    /**
     * @return bool
     */
    public function starting(): bool
    {
        return isset($this->datas[self::CURRENT_STATE]) &&
            $this->datas[self::CURRENT_STATE] === WIN32_SERVICE_START_PENDING;
    }


}
