
App.controller('SensorController', ['$scope', '$modal', 'Sensor', function ($scope, $modal, Sensor) {
    $scope.sensors           = {};
    $scope.activeSensorIds   = '';
    $scope.currentFrom       = 0;
    $scope.fromIntervals     = {}; // todo sorting in angular is fuzzy
    $scope.availableSensors  = {};

    $scope.openModal = function () {
        $modal.open({
            templateUrl: asset('/templates/new_sensor.html'),
            controller: 'NewSensorController'
        });
    };

    Sensor.getValues('0').success(function (data) {
        $scope.sensors          = data.sensors;
        $scope.activeSensorIds  = data.activeSensorIds;
        $scope.currentFrom      = data.currentFrom;
        $scope.fromIntervals    = data.fromIntervals;
        $scope.availableSensors = data.availableSensors;

        $scope.graph = new Rickshaw.Graph({
            element : document.getElementById("chart"),
            width   : document.getElementsByClassName('content')[0].offsetWidth - 100,
            interpolation: 'basis',
            height  : 500,
            min     : 'auto',
            renderer: 'line',
            series  : data.json
        });

        new Rickshaw.Graph.Axis.Time({graph: $scope.graph});

        var yAxis = new Rickshaw.Graph.Axis.Y({
            graph      : $scope.graph,
            orientation: 'left',
            tickFormat : Rickshaw.Fixtures.Number.formatKMBT,
            element    : document.getElementById('yAxis')
        });

        $scope.graph.render();

        new Rickshaw.Graph.HoverDetail({
            graph: $scope.graph,
            formatter: function(series, x, y) {
                var date = new Date(x * 1000);
                var dateString = '<span class="date">{0} {1}:{2}</span><br />'.format(date.toDateString(), date.getHours(), parseInt(date.getMinutes()));
                var content = dateString + series.name + ": " + y;
                return content;
            },
            xFormatter: function(x) {
                return new Date(x * 1000).toDateString();
            },
        });

        new Rickshaw.Graph.Legend({
            element: document.querySelector('#legend'),
            graph: $scope.graph
        });
    });

    /**
     * @param {Number} sensorId
     * @returns {boolean}
     */
    $scope.isSensorActive = function (sensorId) {
        return $scope.activeSensorIds && $scope.activeSensorIds.indexOf(~~sensorId) > -1;
    };

    /**
     * @param {Number} sensorId
     * @param {Number} from
     */
    $scope.sensorView = function (sensorId, from) {
        sensorId = ~~sensorId;
        $scope.currentFrom = from = from || $scope.currentFrom;

        if (sensorId) {
            if ($scope.isSensorActive(sensorId)) {
                var index = $scope.activeSensorIds.indexOf(sensorId);
                $scope.activeSensorIds.splice(index, 1);
            } else {
                $scope.activeSensorIds.push(sensorId);
            }
        }

        var activeIds  = $scope.activeSensorIds.join(':') || "0";
        var parameters = '?from={0}'.format($scope.currentFrom);

        Sensor.getValues(activeIds, parameters).success(function (data) {
            updateGraph(data.json);
        });

        return false;
    };

    $scope.editModal = function () {
        $modal.open({
            templateUrl: asset('/templates/admin/sensors.html'),
            controller : 'AdminSensorsController',
            windowClass: 'dialog_800'
        });
    };

    /**
     * @param sensorValues
     */
    function updateGraph(sensorValues) {
        var oldActive = $scope.graph.series.active;
        sensorValues.active = oldActive;
        $scope.graph.series = sensorValues;
        $scope.graph.update();

        var legend = document.querySelector('#legend');
        legend.innerHTML = '';
        new Rickshaw.Graph.Legend({
            element: legend,
            graph: $scope.graph
        });
    }
}]);
