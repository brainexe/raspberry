<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\MemoryUsed;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\System\MemoryUsed
 */
class MemoryUsedTest extends TestCase
{

    /**
     * @var MemoryUsed
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new MemoryUsed();
    }

    public function testIsSupported()
    {
        $parameter = '12';
        $output = new DummyOutput();

        $actual = $this->subject->isSupported($parameter, $output);
        $this->assertInternalType('bool', $actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
