<?php

namespace Tests\Homie\VoiceControl;

use Homie\VoiceControl\VoiceEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\VoiceControl\VoiceEvent
 */
class VoiceEventTest extends TestCase
{

    public function testConstruct()
    {
        $event = new VoiceEvent('text');

        $this->assertEquals(VoiceEvent::SPEECH, $event->getEventName());
        $this->assertEquals('text', $event->getText());
    }
}
