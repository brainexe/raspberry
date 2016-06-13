
App.service('Widget.command', ['Command', function(Command) {
    return {
        render: function ($scope, widget) {
            $scope.value = '';

            $scope.execute = function(value) {
                Command.execute(value).success(function(output) {
                    $scope.output = output;
                });

                $scope.value = '';
            }
        }
    };
}]);

