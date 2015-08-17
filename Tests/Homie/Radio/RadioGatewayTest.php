<?php

namespace Tests\Homie\Radio;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\RadioGateway;
use Homie\Radio\VO\RadioVO;
use BrainExe\Core\Util\IdGenerator;

/**
 * @covers Homie\Radio\RadioGateway
 */
class RadioGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var RadioGateway
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
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new RadioGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetRadios()
    {
        $radioIds = [
            $radioId = 1
        ];

        $result = ['result'];

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(RadioGateway::REDIS_RADIO_IDS)
            ->willReturn($radioIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("radios:$radioId");

        $this->redis
            ->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $actualResult = $this->subject->getRadios();

        $this->assertEquals($result, $actualResult);
    }

    public function testGetRadio()
    {
        $radioId = 10;

        $radio = ['radio'];

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("radios:$radioId")
            ->willReturn($radio);

        $actualResult = $this->subject->getRadio($radioId);

        $this->assertEquals($radio, $actualResult);
    }

    public function testGetRadioIds()
    {
        $radioIds = [
            1
        ];

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(RadioGateway::REDIS_RADIO_IDS)
            ->willReturn($radioIds);

        $actualResult = $this->subject->getRadioIds();

        $this->assertEquals($radioIds, $actualResult);
    }

    public function testAddRadio()
    {
        $radioId = "11880";

        $radioVo = new RadioVO();
        $radioVo->radioId     = $radioId;
        $radioVo->name        = $name = 'name';
        $radioVo->description = $description = 'description';
        $radioVo->pin         = $pin = 'pin';
        $radioVo->code        = $code = 'code';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($radioId);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $key = "radios:$radioId";

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with($key, [
                'radioId' => $radioId,
                'name' => $name,
                'description' => $description,
                'pin' => $pin,
                'code' => $code,
                'status' => $radioVo->status,
            ]);

        $this->redis
            ->expects($this->once())
            ->method('SADD')
            ->with(RadioGateway::REDIS_RADIO_IDS, [$radioId]);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->addRadio($radioVo);

        $this->assertEquals($radioId, $actualResult);
    }

    public function testEditRadio()
    {
        $radioVo = new RadioVO();
        $radioVo->radioId = $radioId = 10;
        $radioVo->status  = 1;

        $this->redis
            ->expects($this->once())
            ->method('hMset')
            ->with("radios:$radioId", [
                'radioId' => $radioId,
                'code' => null,
                'pin' => null,
                'name' => null,
                'description' => null,
                'status' => 1,
                'switchId' => null
            ]);

        $this->subject->editRadio($radioVo);
    }

    public function testDeleteRadio()
    {
        $radioId = 10;

        $this->redis
            ->expects($this->once())
            ->method('SREM')
            ->with(RadioGateway::REDIS_RADIO_IDS, $radioId);

        $this->redis
            ->expects($this->once())
            ->method('DEL')
            ->with("radios:$radioId");

        $this->subject->deleteRadio($radioId);
    }
}
