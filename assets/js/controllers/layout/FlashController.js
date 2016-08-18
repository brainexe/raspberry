
App.controller('FlashController', /*@ngInject*/ function ($scope, $timeout, lodash) {
    const TIMEOUT = 5000;

    $scope.flashBag = [];
    var timeouts = [];

    /**
     * @param {String} message
     * @param {String} type (success, warning, info, danger)
     */
    function addFlash(message, type = 'success') {
        var item = {type, message};

        $scope.flashBag.push(item);

        timeouts.push($timeout(function () {
            lodash.pull($scope.flashBag, item);
        }, TIMEOUT));
    }

    $scope.$on('flash', (type, args) => addFlash(args[0], args[1]));

    $scope.removeFlash = function(index) {
        $timeout.cancel(timeouts[index]);
        $scope.flashBag.splice(index, 1);
        timeouts.splice(index, 1);
    };
});
