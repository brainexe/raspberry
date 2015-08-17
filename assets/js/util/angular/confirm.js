/**
 * @source https://github.com/venil7/Angular-ui-confirm
 */
App.directive('confirm', ['$modal', function($modal) {
    return {
        restrict: 'A',
        scope: {
            confirm: '&confirm',
            confirmHeader: '@',
            confirmText:   '@'
        },
        link: function(scope, elem) {
            elem.on('click', function() {
                var modalInstance = $modal.open({
                    templateUrl: asset('/templates/modal/confirm.html'),
                    controller: ["$scope", "$modalInstance", "text", "header", '_', function($scope, $modalInstance, text, header, _) {
                        $scope.message = text   || _('Confirm');
                        $scope.header  = header || _('Confirm');
                        $scope.ok = function () {
                            $modalInstance.close(true);
                        };
                        $scope.cancel = function () {
                            $modalInstance.dismiss('cancel');
                        };
                    }],
                    resolve: {
                        text: function() {
                            return scope.confirmText;
                        },
                        header: function() {
                            return scope.confirmHeader;
                        }
                    }
                });
                modalInstance.result.then(function () {
                    scope.confirm();
                });
            });
        }
    };
}]);
