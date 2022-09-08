<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Model;

use Stringable;
class ServiceIdentifier implements ServiceIdentificator, Stringable
{
    /**
     * ServiceIdentifier constructor.
     */
    public function __construct(private string $serviceId, private string $machine = '')
    {
    }
    public function __toString(): string
    {
        return sprintf('%s%s', empty($this->machine) ? '' : $this->machine.'\\', $this->serviceId);
    }
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
