<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Homie\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class GpioManager
{

    const GPIO_COMMAND_READALL   = '%s readall';
    const GPIO_COMMAND_DIRECTION = '%s mode %d %s';
    const GPIO_COMMAND_VALUE     = '%s write %d %d';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinGateway
     */
    private $gateway;

    /**
     * @var PinLoader
     */
    private $loader;

    /**
     * @var string
     */
    private $gpioExecutable;

    /**
     * @Inject({"@PinGateway", "@HomieClient", "@PinLoader", "%gpio.executable%"})
     * @param PinGateway $gateway
     * @param ClientInterface $client
     * @param PinLoader $loader
     * @param $gpioExecutable
     */
    public function __construct(
        PinGateway $gateway,
        ClientInterface $client,
        PinLoader $loader,
        $gpioExecutable
    ) {
        $this->gateway        = $gateway;
        $this->client         = $client;
        $this->loader         = $loader;
        $this->gpioExecutable = $gpioExecutable;
    }

    /**
     * @return PinsCollection
     */
    public function getPins()
    {
        $descriptions = $this->gateway->getPinDescriptions();

        $pins = $this->loader->loadPins();

        foreach ($pins->getAll() as $pin) {
            /** @var Pin $pin */
            if (!empty($descriptions[$pin->getWiringId()])) {
                $pin->setDescription($descriptions[$pin->getWiringId()]);
            }
        }

        return $pins;
    }

    /**
     * @param integer $pinId
     * @param string $status
     * @param boolean $value
     * @return Pin
     */
    public function setPin($pinId, $status, $value)
    {
        $pin = $this->loader->loadPin($pinId);

        $pin->setMode($status ? Pin::DIRECTION_OUT : Pin::DIRECTION_IN);
        $pin->setValue($value ? 1 : 0);

        $this->updatePin($pin);

        return $pin;
    }

    /**
     * @param int $pinId
     * @param string $description
     */
    public function setDescription($pinId, $description)
    {
        $this->gateway->setDescription($pinId, $description);
    }

    /**
     * @param Pin $pin Pin
     */
    private function updatePin(Pin $pin)
    {
        $pinValue = (bool)$pin->getValue();

        $command = sprintf(
            self::GPIO_COMMAND_DIRECTION,
            $this->gpioExecutable,
            $pin->getWiringId(),
            escapeshellarg($pin->getMode())
        );
        $this->client->execute($command);

        $command = sprintf(
            self::GPIO_COMMAND_VALUE,
            $this->gpioExecutable,
            $pin->getWiringId(),
            $pinValue
        );
        $this->client->execute($command);
    }
}
