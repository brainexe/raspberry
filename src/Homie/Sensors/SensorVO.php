<?php

namespace Homie\Sensors;

class SensorVO
{

    /**
     * @var int
     */
    public $sensorId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $pin;

    /**
     * @var int
     */
    public $interval;

    /**
     * @var integer
     */
    public $node;

    /**
     * @var int - unix timestamp
     */
    public $lastValueTimestamp;

    /**
     * @var float
     */
    public $lastValue;

    /**
     * @var float
     */
    public $color;
}
