<?php

namespace Tests\Homie\Gpio;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\GpioManager;
use Homie\Gpio\Pin;
use Homie\Gpio\PinGateway;
use Homie\Client\LocalClient;
use Homie\Gpio\PinLoader;
use Homie\Gpio\PinsCollection;

class GpioManagerTest extends TestCase
{

    /**
     * @var GpioManager
     */
    private $subject;

    /**
     * @var PinGateway|MockObject
     */
    private $pinGateway;

    /**
     * @var LocalClient|MockObject
     */
    private $client;

    /**
     * @var PinLoader|MockObject
     */
    private $pinLoader;

    public function setUp()
    {
        $this->pinGateway = $this->getMock(PinGateway::class, [], [], '', false);
        $this->client = $this->getMock(LocalClient::class, [], [], '', false);
        $this->pinLoader = $this->getMock(PinLoader::class, [], [], '', false);

        $this->subject = new GpioManager($this->pinGateway, $this->client, $this->pinLoader, './gpio');
    }

    public function testGetPins()
    {
        $pinId = 12;

        $descriptions = [
            $pinId => $description = 'description'
        ];

        $pin = new Pin();
        $pin->setWiringId($pinId);

        $collection = new PinsCollection();
        $collection->add($pin);

        $this->pinLoader
            ->expects($this->once())
            ->method('loadPins')
            ->willReturn($collection);

        $this->pinGateway
            ->expects($this->once())
            ->method('getPinDescriptions')
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPins();

        $this->assertEquals($collection, $actualResult);
        $this->assertEquals($description, $pin->getDescription());
    }

    public function testSetPin()
    {
        $gpioId = 10;
        $status = true;
        $value  = true;

        $pin = new Pin();
        $pin->setWiringId($gpioId);

        $this->pinLoader
            ->expects($this->once())
            ->method('loadPin')
            ->with($gpioId)
            ->willReturn($pin);

        $this->client
            ->expects($this->at(0))
            ->method('execute')
            ->with("./gpio mode 10 'OUT'");

        $this->client
            ->expects($this->at(1))
            ->method('execute')
            ->with("./gpio write 10 1");

        $actualResult = $this->subject->setPin($gpioId, $status, $value);

        $this->assertEquals($pin, $actualResult);
    }

    public function testSetDescription()
    {
        $pinId       = 100;
        $description = 'test';

        $this->pinGateway
            ->expects($this->once())
            ->method('setDescription')
            ->with($pinId, $description);

        $this->subject->setDescription($pinId, $description);
    }
}
