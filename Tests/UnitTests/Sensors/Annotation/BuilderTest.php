<?php

namespace Tests\Homie\Sensors\Annotation;

use Doctrine\Common\Annotations\Reader;
use Homie\Sensors\Annotation\SensorBuilder;
use Homie\Sensors\Annotation\Sensor;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;

class BuilderTest extends TestCase
{

    public function testBuild()
    {
        /** @var Reader $reader */
        $reader = $this->getMock(Reader::class);

        $subject = new SensorBuilder($reader);

        $data = [];
        $annotation = new Sensor($data);
        $annotation->name = 'name';

        /** @var ReflectionClass|MockObject $class */
        $class = $this->getMock(ReflectionClass::class, [], [], '', false);
        $class->expects($this->once())
              ->method('getMethods')
              ->willReturn([]);

        $data = $subject->build($class, $annotation);

        $definition = new Definition();
        $definition->setPublic(false);
        $definition->addTag(CompilerPass::TAG);
        $expected = [
            'name',
            $definition
        ];

        $this->assertEquals($expected, $data);
    }
}