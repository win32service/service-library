<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */

namespace Win32Service\Model;


class ServiceIdentifier implements ServiceIdentificator
{
    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $machine;

    /**
     * ServiceIdentifier constructor.
     * @param string $serviceId
     * @param string $machine
     */
    public function __construct(string $serviceId, string $machine = "")
    {
        $this->serviceId = $serviceId;
        $this->machine = $machine;
    }

    /**
     * @param string $serviceId
     * @param string $machine
     * @return ServiceIdentifier
     */
    public static function identify(string $serviceId, string $machine = ""): self
    {
        return new self($serviceId, $machine);
    }

    /**
     * @return string
     */
    public function machine(): string
    {
        return $this->machine;
    }

    /**
     * @return string
     */
    public function serviceId(): string
    {
        return $this->serviceId;
    }
}
