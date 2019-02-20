<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Model;


interface RunnerServiceInterface
{
    public function setServiceId(ServiceIdentificator $serviceId);

    public function defineExitModeAndCode(bool $exitGraceful, int $exitCode = 1): void;

    public function doRun(int $maxRun = -1, int $threadNumber = -1);
}