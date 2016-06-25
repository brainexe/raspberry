
App.service('Widget.sensor_input', ['Sensor', function(Sensor) {
    return {
        template: '/templates/widgets/sensor_input.html',
        render: function ($scope, widget) {
            $scope.value = '';

            Sensor.getSensorData(widget.sensor_id, true).success(function(sensor) {
                $scope.sensor = sensor;
            });

            $scope.submit = function(value) {
                $scope.value = '';
                var sensorId = widget.sensor_id;
                Sensor.addValue(sensorId, value);
            }
        }
    };
}]);
