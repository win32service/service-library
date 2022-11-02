<?php
/**
 * This file is part of Win32Service Library package.
 *
 * @copy Win32Service (c) 2018-2019
 *
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

namespace Win32Service\Model;

use ArrayAccess;
use Stringable;
use InvalidArgumentException;
/**
 * Class ServiceInformations.
 *
 * This class contains the configuration information for create en new service.
 */
class ServiceInformations implements ArrayAccess, ServiceIdentificator, Stringable
{
    /**
     * @var array
     */
    protected static $reservedKeys = [
        WIN32_INFO_SERVICE,
        WIN32_INFO_DISPLAY,
        WIN32_INFO_DESCRIPTION,
        WIN32_INFO_PATH,
        WIN32_INFO_PARAMS,
        WIN32_INFO_USER,
        WIN32_INFO_PASSWORD,
    ];
    /**
     * @var array<string, int|string> Information of service
     */
    private array $datas = [];

    public function __construct(
        private ServiceIdentifier $serviceId,
        string $serviceDiplayedName = '',
        string $serviceDescripton = '',
        string $scriptToRun = '',
        string $scriptParams = ''
    ) {
        $this->datas[WIN32_INFO_DISPLAY] = $serviceDiplayedName;
        $this->datas[WIN32_INFO_DESCRIPTION] = $serviceDescripton;
        $this->datas[WIN32_INFO_PATH] = \dirname(PHP_BINARY).'\\php-win.exe';
        $this->datas[WIN32_INFO_PARAMS] = sprintf('"%s" %s', $scriptToRun, $scriptParams);
    }
    /**
     * Display Name or Id.
     */
    public function __toString(): string
    {
        return (string) isset($this->datas[WIN32_INFO_DISPLAY]) ?
            $this->datas[WIN32_INFO_DISPLAY] : $this->serviceId();
    }

    public function defineUserService(string $userName, string $password): self
    {
        $this->datas[WIN32_INFO_USER] = $userName;
        $this->datas[WIN32_INFO_PASSWORD] = $password;

        return $this;
    }
    public function defineDependencies(array $dependencies): self
    {
        foreach ($dependencies as $key => $service) {
            if (!\is_string($service)) {
                throw new InvalidArgumentException(sprintf('The service name at the key \'%s\' is invalid', $key));
            }
        }
        $this->datas[WIN32_INFO_DEPENDENCIES] = $dependencies;

        return $this;
    }
    public function defineIfStartIsDelayed(bool $startingDelayed): void
    {
        $this->datas[WIN32_INFO_DELAYED_START] = $startingDelayed;
    }
    /**
     * @param int    $delay        Delay before trigger the action. In milliseconds (1000 = 1s)
     * @param bool   $enabled      Enable the recovery setting
     * @param int    $action_1     the action does be triggered on the first fail
     * @param int    $action_2     the action does be triggered on the second fail
     * @param int    $action_3     the action does be triggered on the next fail
     * @param string $reboot_msg   message sends to the Windows Log if the action is REBOOT
     * @param string $command      command does be run if the action is 'run command'
     * @param int    $reset_period The period before reset the fail counter. In minute.
     */
    public function defineRecoverySettings(int $delay, bool $enabled, int $action_1, int $action_2, int $action_3, string $reboot_msg, string $command, int $reset_period): void
    {
        if ($delay < 1) {
            throw new InvalidArgumentException('The delay must be greater than 0');
        }
        if ($reset_period < 0) {
            throw new InvalidArgumentException('The delay must be equal or greater than 0');
        }
        if (empty($command) && ($action_1 === WIN32_SC_ACTION_RUN_COMMAND || $action_2 === WIN32_SC_ACTION_RUN_COMMAND || $action_3 === WIN32_SC_ACTION_RUN_COMMAND)) {
            throw new InvalidArgumentException('The command argument must be defined if one action is "run command"');
        }
        $validAction = [
            WIN32_SC_ACTION_NONE,
            WIN32_SC_ACTION_REBOOT,
            WIN32_SC_ACTION_RESTART,
            WIN32_SC_ACTION_RUN_COMMAND,
        ];
        if (!\in_array($action_1, $validAction)) {
            throw new InvalidArgumentException('The argument action_1 must be equals one constant: WIN32_SC_ACTION_NONE, WIN32_SC_ACTION_REBOOT, WIN32_SC_ACTION_RESTART, WIN32_SC_ACTION_RUN_COMMAND');
        }
        if (!\in_array($action_2, $validAction)) {
            throw new InvalidArgumentException('The argument action_2 must be equals one constant: WIN32_SC_ACTION_NONE, WIN32_SC_ACTION_REBOOT, WIN32_SC_ACTION_RESTART, WIN32_SC_ACTION_RUN_COMMAND');
        }
        if (!\in_array($action_3, $validAction)) {
            throw new InvalidArgumentException('The argument action_3 must be equals one constant: WIN32_SC_ACTION_NONE, WIN32_SC_ACTION_REBOOT, WIN32_SC_ACTION_RESTART, WIN32_SC_ACTION_RUN_COMMAND');
        }

        $this->datas[WIN32_INFO_RECOVERY_DELAY] = $delay;
        $this->datas[WIN32_INFO_RECOVERY_ACTION_1] = $action_1;
        $this->datas[WIN32_INFO_RECOVERY_ACTION_2] = $action_2;
        $this->datas[WIN32_INFO_RECOVERY_ACTION_3] = $action_3;
        $this->datas[WIN32_INFO_RECOVERY_RESET_PERIOD] = $reset_period;
        $this->datas[WIN32_INFO_RECOVERY_ENABLED] = $enabled;
        $this->datas[WIN32_INFO_RECOVERY_REBOOT_MSG] = $reboot_msg;
        $this->datas[WIN32_INFO_RECOVERY_COMMAND] = $command;
    }
    public function serviceId(): string
    {
        return $this->serviceId->serviceId();
    }
    public function machine(): string
    {
        return $this->serviceId->machine();
    }
    public function offsetExists(mixed $offset): bool
    {
        if ($offset === WIN32_INFO_SERVICE) {
            return true;
        }

        return \array_key_exists($offset, $this->datas);
    }
    /**
     * @return mixed|string
     */
    public function offsetGet(mixed $offset): mixed
    {
        if ($offset === WIN32_INFO_SERVICE) {
            return $this->serviceId();
        }

        return $this->datas[$offset];
    }
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!\in_array($offset, self::$reservedKeys)) {
            $this->datas[$offset] = $value;
        }
    }
    public function offsetUnset(mixed $offset): void
    {
        if (!\in_array($offset, self::$reservedKeys)) {
            unset($this->datas[$offset]);
        }
    }
    public function toArray(): array
    {
        $dataArray = $this->datas;
        $dataArray[WIN32_INFO_SERVICE] = $this->serviceId();

        return $dataArray;
    }
}
