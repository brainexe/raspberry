<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SwitchWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Radio\Radios;
use Homie\Radio\VO\RadioVO;

class SwitchWidgetTest extends TestCase
{

    /**
     * @var SwitchWidget
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $radios;

    public function setUp()
    {
        $this->radios  = $this->getMock(Radios::class, [], [], '', false);
        $this->subject = new SwitchWidget($this->radios);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SwitchWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $radio = new RadioVO();
        $radio->radioId = $radioId = 122;
        $radio->name    = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn([
                 $radioId => $radio
             ]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $radio = new RadioVO();
        $radio->radioId = $radioId = 122;
        $radio->name    = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn([
                $radioId => $radio
            ]);

        $actualResult = json_encode($this->subject);
        $expected = '{"name":"Switch","description":"Control your switches.","parameters":{"title":{"name":"Title","type":"text","default":"Switch"},"switchIds":{"name":"Switch","values":{"122":"radio"},"type":"multi_select"},"width":{"name":"Width","type":"number","min":1,"max":12,"default":4},"height":{"name":"Height","type":"number","min":1,"max":12,"default":3}},"widgetId":"switch","width":4,"height":3}';
        $this->assertEquals($expected, $actualResult);
    }
}