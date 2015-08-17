<?php

namespace Tests\Homie\Display;

use BrainExe\Expression\Language;
use Homie\Display\Renderer;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class RendererTest extends TestCase
{

    /**
     * @var Renderer
     */
    private $subject;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setUp()
    {
        $this->language = $this->getMock(Language::class, [], [], '', false);
        $this->subject  = new Renderer($this->language);
    }

    public function testRender()
    {
        $settings = new Settings();
        $settings->content = [
            'line 1',
            'line 2',
        ];

        $this->language
            ->expects($this->exactly(2))
            ->method('evaluate')
            ->willReturnMap([
                ['line 1', [], 'result 1'],
                ['line 2', [], 'result 2']
            ]);

        $actual = $this->subject->render($settings);

        $this->assertEquals(['result 1', 'result 2'], $actual);
    }
}
