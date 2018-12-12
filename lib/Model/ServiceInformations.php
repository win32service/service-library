<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018
 * @author "Jean-Baptiste Nahan" <jean-baptiste@nahan.fr>
 */
namespace Win32Service\Model;

/**
 * Class ServiceInformations
 *
 * This class contains the configuration information for create en new service.
 */
class ServiceInformations implements \ArrayAccess, ServiceIdentificator
{
    static protected $resevedKeys = [
        WIN32_INFO_SERVICE,
        WIN32_INFO_DISPLAY,
        WIN32_INFO_DESCRIPTION,
        WIN32_INFO_PATH,
        WIN32_INFO_PARAMS,
        WIN32_INFO_USER,
        WIN32_INFO_PASSWORD
    ];


    /**
     * @var array Informations of service
     */
    private $datas = [];

    /**
     * @var ServiceIdentifier
     */
    private $serviceId;

    /**
     * ServiceInformations constructor.
     * @param ServiceIdentifier $serviceIdentifier
     * @param string $serviceDiplayedName
     * @param string $serviceDescripton
     * @param string $scriptToRun
     * @param string $scriptParams
     */
    public function __construct(
        ServiceIdentifier $serviceIdentifier,
        $serviceDiplayedName = "",
        $serviceDescripton = "",
        $scriptToRun = "",
        $scriptParams = ""
    )
    {
        $this->datas[WIN32_INFO_DISPLAY] = $serviceDiplayedName;
        $this->datas[WIN32_INFO_DESCRIPTION] = $serviceDescripton;
        $this->datas[WIN32_INFO_PATH] = '"' . dirname(PHP_BINARY) . '\\php-win.exe"';
        $this->datas[WIN32_INFO_PARAMS] = sprintf('"%s" %s', $scriptToRun, $scriptParams);
        $this->serviceId = $serviceIdentifier;
    }

    /**
     * @param string $userName
     * @param string $password
     * @return ServiceInformations
     */
    public function defineUserService($userName, $password) :self {
        $this->datas[WIN32_INFO_USER] = $userName;
        $this->datas[WIN32_INFO_PASSWORD] = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function serviceId(): string
    {
        return $this->serviceId->serviceId();
    }

    /**
     * @return string
     */
    public function machine(): string
    {
        return $this->serviceId->machine();
    }


    public function offsetExists($offset)
    {
        if ($offset === WIN32_INFO_SERVICE) {
            return true;
        }
        return array_key_exists($this->datas, $offset);
    }

    public function offsetGet($offset)
    {
        if ($offset === WIN32_INFO_SERVICE) {
            return $this->serviceId();
        }
        return $this->datas[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!in_array($offset, $this->resevedKeys)) {
            $this->datas[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        if (!in_array($offset, $this->resevedKeys)) {
            unset($this->datas[$offset]);
        }
    }

    /**
     * Display Name or Id
     * @return string
     */
    public function __toString()
    {
        return (string) isset($this->datas[WIN32_INFO_DISPLAY])?
            $this->datas[WIN32_INFO_DISPLAY]:$this->serviceId();
    }

    public function toArray(): array
    {
        $datas = $this->datas;
        $datas[WIN32_INFO_SERVICE]=$this->serviceId();
        return $datas;
    }
}
