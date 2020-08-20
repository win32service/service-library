<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
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
     */
    public function __construct(string $serviceId, string $machine = '')
    {
        $this->serviceId = $serviceId;
        $this->machine = $machine;
    }

    public function __toString(): string
    {
        return sprintf('%s%s', empty($this->machine) ? '' : $this->machine.'\\', $this->serviceId);
    }

    /**
     * @return ServiceIdentifier
     */
    public static function identify(string $serviceId, string $machine = ''): self
    {
        return new self($serviceId, $machine);
    }

    public function machine(): string
    {
        return $this->machine;
    }

    public function serviceId(): string
    {
        return $this->serviceId;
    }
}
