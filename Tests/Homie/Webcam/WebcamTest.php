<?php

namespace Tests\Homie\Webcam\Webcam;

use League\Flysystem\Filesystem;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Webcam\Webcam;
use Homie\Webcam\WebcamVO;
use BrainExe\Core\EventDispatcher\EventDispatcher;

class WebcamTest extends TestCase
{

    /**
     * @var Webcam
     */
    private $subject;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->filesystem = $this->getMock(Filesystem::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Webcam($this->filesystem);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetPhotos()
    {
        $filePath     = '/www/something/relative.ext';
        $fileBaseName = 'relative.ext';
        $fileCTime    = 10;

        $files = [
            [
                'path'      => $filePath,
                'basename'  => $fileBaseName,
                'timestamp' => $fileCTime,
                'extension' => 'ext'
            ]
        ];

        $this->filesystem
            ->expects($this->once())
            ->method('listContents')
            ->with(Webcam::ROOT)
            ->willReturn($files);

        $expectedWebcam            = new WebcamVO();
        $expectedWebcam->filePath  = $filePath;
        $expectedWebcam->webcamId  = $fileBaseName;
        $expectedWebcam->name      = $fileBaseName;
        $expectedWebcam->webPath   = 'Webcam/relative.ext';
        $expectedWebcam->timestamp = $fileCTime;
        $expectedWebcam->extension = 'ext';

        $actualResult = $this->subject->getFiles();

        $this->assertEquals([$expectedWebcam], $actualResult);
        $this->assertEquals($fileBaseName, $expectedWebcam->getWebcamId());
    }

    public function testRecent()
    {
        $filePath     = '/www/something/relative.ext';
        $fileBaseName = 'relative.ext';
        $fileCTime    = 10;

        $files = [
            [
                'path'      => $filePath,
                'basename'  => $fileBaseName,
                'timestamp' => $fileCTime,
                'extension' => 'ext'
            ]
        ];

        $this->filesystem
            ->expects($this->once())
            ->method('listContents')
            ->with(Webcam::ROOT)
            ->willReturn($files);

        $expected            = new WebcamVO();
        $expected->filePath  = $filePath;
        $expected->webcamId  = $fileBaseName;
        $expected->name      = $fileBaseName;
        $expected->webPath   = 'Webcam/relative.ext';
        $expected->timestamp = $fileCTime;
        $expected->extension = 'ext';

        $actual = $this->subject->getRecentImage();

        $this->assertEquals($expected, $actual);
    }

    public function testRecentEmpty()
    {
        $files = [];

        $this->filesystem
            ->expects($this->once())
            ->method('listContents')
            ->with(Webcam::ROOT)
            ->willReturn($files);

        $actual = $this->subject->getRecentImage();
        $this->assertEquals([], $actual);
    }

    public function testDelete()
    {
        $filename = 'id';

        $this->filesystem
            ->expects($this->once())
            ->method('delete')
            ->with($filename);

        $this->subject->delete($filename);
    }
}
