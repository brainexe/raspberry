<?php

namespace Raspberry\Gpio;

use BrainExe\Core\Application\UserException;
use Exception;
use Raspberry\Client\ClientInterface;

/**
 * @Service(public=false)
 */
class PinLoader
{

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var PinsCollection
     */
    private $pins = null;

    /**
     * @Inject("@RaspberryClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $pin
     * @return Pin
     */
    public function loadPin($pin)
    {
        $pins = $this->loadPins();

        return $pins->get($pin);
    }

    /**
     * @return PinsCollection
     */
    public function loadPins()
    {
        if (null !== $this->pins) {
            return $this->pins;
        }

        $this->pins = new PinsCollection();

        try {
            $results = $this->client->executeWithReturn(GpioManager::GPIO_COMMAND_READALL);
        } catch (Exception $e) {
            throw new UserException('No GPIO pins found. You have to install "readall".');
        }

        $results = explode("\n", $results);
        $results = array_slice($results, 3, -2);

        foreach ($results as $r) {
            $matches = explode('|', $r);
            $matches = array_map('trim', $matches);

            $pin = new Pin();
            $pin->setID((int)$matches[1]);
            $pin->setName($matches[4]);
            $pin->setDirection($matches[5]);
            $pin->setValue((int)('High' === $matches[6]));

            $this->pins->add($pin);
        }

        return $this->pins;
    }
}
