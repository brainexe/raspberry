<?php

namespace Tests\Homie\Dashboard;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\WidgetFactory;
use Homie\Dashboard\WidgetInterface;
use Homie\Dashboard\Widgets\TimeWidget;

/**
 * @covers Homie\Dashboard\WidgetFactory
 */
class WidgetFactoryTest extends TestCase
{

    /**
     * @var WidgetFactory
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new WidgetFactory();
        $this->subject->addWidget(new TimeWidget());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid widget: invalid
     */
    public function testGetInvalidWidget()
    {
        $this->subject->getWidget('invalid');
    }

    public function testGetValidWidget()
    {
        $actualResult = $this->subject->getWidget(TimeWidget::TYPE);

        $this->assertTrue($actualResult instanceof WidgetInterface);
    }

    public function testGetWidgetTypes()
    {
        $actualResult = $this->subject->getAvailableWidgets();

        $this->assertEquals([new TimeWidget()], $actualResult);
    }
}
