<?php

namespace Tests\Homie\Index;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Index\Controller;

/**
 * @covers Homie\Index\Controller
 */
class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Controller(true, null, ['en', 'de']);
    }

    public function testIndex()
    {
        if (!is_dir(ROOT . 'web')) {
            mkdir(ROOT . 'web');
        }
        if (!is_file(ROOT . 'web/index.html')) {
            file_put_contents(ROOT . 'web/index.html', '');
        }

        $actualResult = $this->subject->index();

        $this->assertEquals(200, $actualResult->getStatusCode());
    }

    public function testConfig()
    {
        $actual   = $this->subject->config();
        $expected = [
            'debug'     => true,
            'socketUrl' => null,
            'locales'   => ['en', 'de']
        ];

        $this->assertEquals($expected, $actual);
    }
}
