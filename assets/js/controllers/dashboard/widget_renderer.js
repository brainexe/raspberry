
App.directive('contentItem', ['$compile', 'TemplateLoader', 'WidgetFactory', function ($compile, TemplateLoader, WidgetFactory) {
    var linker = function(scope, element) {
        var widget = scope.widget;

        scope.setTitle = function (title) {
            scope.$parent.title = title;
        };

        var renderer = WidgetFactory(widget.type);
        var template = TemplateLoader(renderer.template || '/templates/widgets/' + widget.type + '.html');

        template.success(function(html) {
            element[0].innerHTML = html;
            $compile(element.contents())(scope);

            renderer.render(scope, widget, element);
        });
    };

    return {
        restrict: "E",
        link: linker,
        scope: {
            template: '=',
            widget:   '=',
            type:     '='
        }
    };
}]);

App.controller('WidgetController', ['$scope', 'Dashboard', function ($scope, Dashboard) {
    var widgetPayload  = $scope.$parent.widget;

    Dashboard.getCachedMetadata().success(function(data) {
        var metadata = data.widgets[widgetPayload.type];

        $scope.title  = widgetPayload.title || metadata.name || widgetPayload.name;
        $scope.widget = widgetPayload;
    });
}]);
