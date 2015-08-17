<?php

namespace Tests\Homie\Display;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use Homie\Display\Gateway;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Display\Gateway
 */
class GatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Gateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis        = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class);
        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testAddDisplay()
    {
        $setting = new Settings();

        $generatedId = 11222;

        $expected = new Settings();
        $expected->displayId = $generatedId;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($generatedId);

        $this->redis
            ->expects($this->once())
            ->method('hSet')
            ->with(Gateway::KEY, $generatedId, serialize($expected));

        $this->subject->addDisplay($setting);
    }

    public function testGetAll()
    {
        $settings = new Settings();
        $settings->content = '1212';

        $list = [
            serialize($settings)
        ];
        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::KEY)
            ->willReturn($list);

        $actual = $this->subject->getall();

        $this->assertEquals([$settings], iterator_to_array($actual));
    }

    public function testDelete()
    {
        $displayId = 42;

        $this->redis
            ->expects($this->once())
            ->method('hDel')
            ->with(Gateway::KEY, [$displayId]);

        $this->subject->delete($displayId);
    }
}
