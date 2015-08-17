<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Application\UserException;
use Exception;
use Homie\Client\ClientInterface;

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
     * @var string
     */
    private $gpioExecutable;

    /**
     * @Inject({"@HomieClient", "%gpio.executable%"})
     * @param ClientInterface $client
     * @param string $gpioExecutable
     */
    public function __construct(ClientInterface $client, $gpioExecutable)
    {
        $this->client = $client;
        $this->gpioExecutable = $gpioExecutable;
    }

    /**
     * @param string $pin
     * @return Pin
     */
    public function loadPin($pin)
    {
        $pins = $this->loadPins();

        return $pins->getByWiringId($pin);
    }

    /**
     * @return PinsCollection
     * @throws UserException
     */
    public function loadPins()
    {
        if (null !== $this->pins) {
            return $this->pins;
        }

        try {
            $command = sprintf(GpioManager::GPIO_COMMAND_READALL, $this->gpioExecutable);
            $file = $this->client->executeWithReturn($command);
        } catch (Exception $e) {
            $file = file_get_contents(__DIR__ . '/gpio.txt');
        }

        $lines = explode("\n", $file);
        $type  = trim($lines[0], ' +-');
        $this->pins = new PinsCollection($type);

        $lines = array_slice($lines, 3, -4);

        $pins = [];
        foreach ($lines as $line) {
            $line = substr($line, 2, -1);

            list($part1, $part2) = explode('||', $line);
            $part1 = explode('|', $part1);
            $part2 = array_reverse(explode('|', $part2));

            $pins[] = $part1;
            $pins[] = $part2;
        }

        foreach ($pins as $r) {
            $matches = array_map('trim', $r);

            $pin = new Pin();
            $pin->setWiringId((int)$matches[1]);
            $pin->setName($matches[2]);
            $pin->setMode($matches[3]);
            $pin->setValue((bool)$matches[4]);
            $pin->setPhysicalId((int)$matches[5]);

            $this->pins->add($pin);
        }

        return $this->pins;
    }
}
