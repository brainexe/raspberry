<?php

namespace Tests\Homie\Sensors\Sensors\Aggregate;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use Homie\Sensors\Sensors\Aggregate\Aggregated;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class AggregatedTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Aggregated
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis   = $this->getRedisMock();
        $this->subject = new Aggregated();
        $this->subject->setRedis($this->redis);
    }

    public function testAddValue()
    {
        $identifier = 'foo';
        $value = 100;

        $this->redis
            ->expects($this->once())
            ->method('hincrbyfloat')
            ->with(Aggregated::REDIS_KEY, $identifier, $value);

        $this->subject->addValue($identifier, $value);
    }

    public function testGetCurrent()
    {
        $identifier = 'foo';

        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Aggregated::REDIS_KEY, $identifier)
            ->willReturn(42);

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(Aggregated::REDIS_KEY, [$identifier]);

        $actual = $this->subject->getCurrent($identifier);

        $this->assertEquals(42, $actual);
    }
}
