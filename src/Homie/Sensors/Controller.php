<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Guest;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\Settings\Settings;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Homie\Sensors\GetValue\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo split into second controller for sensor values
 * @ControllerAnnotation("SensorsController")
 */
class Controller
{

    use EventDispatcherTrait;

    const SETTINGS_ACTIVE_SENSORS = 'sensors:active_sensors';
    const SETTINGS_TIMESPAN       = 'sensors:timespan';

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var SensorValuesGateway
     */
    private $valuesGateway;

    /**
     * @var Builder
     */
    private $voBuilder;

    /**
     * @var SensorBuilder;
     */
    private $builder;

    /**
     * @var Chart
     */
    private $chart;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @Inject({
     *  "@SensorGateway",
     *  "@SensorValuesGateway",
     *  "@Chart", "@SensorBuilder",
     *  "@Sensor.VOBuilder",
     *  "@User.Settings"
     * })
     * @param SensorGateway $gateway
     * @param SensorValuesGateway $valuesGateway
     * @param Chart $chart
     * @param SensorBuilder $builder
     * @param Builder $voBuilder
     * @param Settings $settings
     */
    public function __construct(
        SensorGateway $gateway,
        SensorValuesGateway $valuesGateway,
        Chart $chart,
        SensorBuilder $builder,
        Builder $voBuilder,
        Settings $settings
    ) {
        $this->gateway        = $gateway;
        $this->valuesGateway  = $valuesGateway;
        $this->chart          = $chart;
        $this->builder        = $builder;
        $this->voBuilder      = $voBuilder;
        $this->settings       = $settings;
    }

    /**
     * @return array
     * @Route("/sensors/", name="sensor.index")
     */
    public function sensors()
    {
        return [
            'types'   => $this->builder->getSensors(),
            'fromIntervals' => Chart::getTimeSpans(),
            'sensors' => array_map([$this->voBuilder, 'buildFromArray'], $this->gateway->getSensors())
        ];
    }

    /**
     * @param Request $request
     * @param string $activeSensorIds
     * @return string
     * @Route("/sensors/load/{activeSensorIds}/", name="sensor.loadall")
     */
    public function indexSensor(Request $request, $activeSensorIds)
    {
        $userId = $request->attributes->get('userId');
        $from   = (int)$request->query->get('from');
        if (!$from) {
            $from = (int)$this->settings->get($userId, self::SETTINGS_TIMESPAN) ?: Chart::DEFAULT_TIME;
        }

        $activeSensorIds = $this->getActiveSensorIds($activeSensorIds, $userId);
        if ($request->query->get('save')) {
            $this->settings->set($userId, self::SETTINGS_ACTIVE_SENSORS, $activeSensorIds);
            $this->settings->set($userId, self::SETTINGS_TIMESPAN, $from);
        }

        $activeSensorIds = array_unique(array_map('intval', explode(':', $activeSensorIds)));
        $sensorsRaw      = $this->gateway->getSensors($activeSensorIds);
        $sensorValues    = $this->addValues($activeSensorIds, $sensorsRaw, $from);

        $json = $this->chart->formatJsonData($sensorsRaw, $sensorValues);

        return [
            'sensors'       => $sensorsRaw,
            'activeSensorIds' => array_values($activeSensorIds),
            'json'          => $json,
            'currentFrom'   => $from
        ];
    }

    /**
     * @todo frontend missing
     * @param Request $request
     * @return SensorVO
     * @Route("/sensors/", name="sensors.add", methods="POST")
     */
    public function addSensor(Request $request)
    {
        $sensorType  = $request->request->get('type');
        $name        = $request->request->get('name');
        $description = $request->request->get('description');
        $pin         = $request->request->get('pin');
        $interval    = $request->request->getInt('interval');
        $node        = $request->request->getInt('node');
        $color       = $request->request->get('color');

        $sensorVo = $this->voBuilder->build(
            null,
            $name,
            $description,
            $interval,
            $node,
            $pin,
            $sensorType,
            $color
        );

        $this->gateway->addSensor($sensorVo);

        return $sensorVo;
    }

    /**
     * @todo frontend missing -> remove?
     * @param Request $request
     * @param integer $sensorId
     * @return boolean
     * @Route("/sensors/{sensor_id}/espeak/", name="sensor.espeak", methods="POST")
     */
    public function espeak(Request $request, $sensorId)
    {
        unset($request);
        $sensor    = $this->gateway->getSensor($sensorId);
        $formatter = $this->builder->getFormatter($sensor['type']);
        $text      = $formatter->getEspeakText($sensor['lastValue']);

        $espeakVo = new EspeakVO($text);
        $event    = new EspeakEvent($espeakVo);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @param Request $request
     * @param integer $sensorId
     * @return boolean
     * @Route("/sensors/{sensor_id}/value/", name="sensor.submitValue", methods="POST")
     */
    public function addValue(Request $request, $sensorId)
    {
        $value    = $request->request->get('value');
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);

        $this->valuesGateway->addValue($sensorVo, $value);

        return true;
    }

    /**
     * @param Request $request
     * @param integer $sensorId
     * @return boolean
     * @Route("/sensors/{sensor_id}/force/", name="sensor.forceGetValue", methods="POST")
     */
    public function forceGetValue(Request $request, $sensorId)
    {
        unset($request);
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);

        $event = new Event($sensorVo);
        $this->dispatchInBackground($event);

        return true;
    }

    /**
     * @todo frontend missing -> remove?
     * @Route("/sensors/{sensor_id}/slim/", name="sensor.slim", methods="GET")
     * @param Request $request
     * @param integer $sensorId
     * @return array
     */
    public function slim(Request $request, $sensorId)
    {
        unset($request);
        $sensor         = $this->gateway->getSensor($sensorId);
        $formatter      = $this->builder->getFormatter($sensor['type']);
        $formattedValue = $formatter->getEspeakText($sensor['lastValue']);

        return [
            'sensor'                 => $sensor,
            'sensor_value_formatted' => $formattedValue
        ];
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.delete", methods="DELETE")
     * @param int $sensorId
     * @param Request $request
     * @return bool
     */
    public function delete(Request $request, $sensorId)
    {
        unset($request);

        $this->gateway->deleteSensor($sensorId);

        return true;
    }

    /**
     * @Route("/sensors/{sensorId}/", name="sensor.edit", methods="PUT")
     * @param int $sensorId
     * @param Request $request
     * @return SensorVO
     */
    public function edit(Request $request, $sensorId)
    {
        $sensor   = $this->gateway->getSensor($sensorId);
        $sensorVo = $this->voBuilder->buildFromArray($sensor);
        $sensorVo->type        = $request->request->get('type');
        $sensorVo->name        = $request->request->get('name');
        $sensorVo->description = $request->request->get('description');
        $sensorVo->pin         = $request->request->get('pin');
        $sensorVo->interval    = $request->request->getInt('interval');
        $sensorVo->color       = $request->request->get('color');

        $this->gateway->save($sensorVo);

        return $sensorVo;
    }

    /**
     * @Route("/sensors/api/", name="sensor.api")
     * @Guest
     * @return array
     */
    public function api()
    {
        $sensors = $this->gateway->getSensors();

        $values = [];

        foreach ($sensors as $sensor) {
            $key = sprintf('%s_%s', $sensor['type'], $sensor['sensorId']);
            $values[$key] = $sensor['lastValue'];
        }

        return $values;
    }

    /**
     * @todo frontend missing -> remove?
     * @param Request $request
     * @param int $sensorId
     * @Route("/sensors/{sensorId}/value/", name="sensor.value", methods="GET")
     * @return array
     */
    public function getValue(Request $request, $sensorId)
    {
        unset($request);

        $sensor         = $this->gateway->getSensor($sensorId);
        $sensorObj      = $this->builder->build($sensor['type']);
        $formatter      = $this->builder->getFormatter($sensor['type']);
        $formattedValue = $formatter->getEspeakText($sensor['lastValue']);

        return [
            'sensor'               => $sensor,
            'sensorValueFormatted' => $formattedValue,
            'sensorObj'            => $sensorObj,
        ];
    }

    /**
     * @param int[] $activeSensorIds
     * @param array $sensorsRaw
     * @param int $from
     * @return array
     */
    private function addValues(array $activeSensorIds, array &$sensorsRaw, $from)
    {
        $sensorValues = [];
        foreach ($sensorsRaw as &$sensor) {
            $sensorId = $sensor['sensorId'];

            if (!empty($sensor['lastValue'])) {
                $formatter           = $this->builder->getFormatter($sensor['type']);
                $sensor['lastValue'] = $formatter->formatValue($sensor['lastValue']);
            }

            if ($activeSensorIds && !in_array($sensorId, $activeSensorIds)) {
                continue;
            }
            $sensorValues[$sensorId] = $this->valuesGateway->getSensorValues($sensorId, $from);
        }

        return $sensorValues;
    }

    /**
     * @param string $activeSensorIds
     * @param int $userId
     * @return string
     */
    protected function getActiveSensorIds($activeSensorIds, $userId)
    {
        if (empty($activeSensorIds)) {
            $activeSensorIds = $this->settings->get($userId, self::SETTINGS_ACTIVE_SENSORS) ?: '0';
        }

        if (empty($activeSensorIds)) {
            $availableSensorIds = $this->gateway->getSensorIds();
            $activeSensorIds    = implode(':', $availableSensorIds);
        }

        return $activeSensorIds;
    }
}
