
var App = angular.module('homie', [
        'ang-drag-drop',
        'ngRoute',
        'ngSanitize',
        'angular-cache',
        'ui.bootstrap',
        'ui.select',
        'as.sortable',
        'gettext'
    ]).config(['$routeProvider', '$httpProvider', '$provide', function ($routeProvider, $httpProvider, $provide) {
        // needed for translated routes
        $provide.factory('$routeProvider', function () {
            return $routeProvider;
        });
        $provide.factory('$httpProvider', function () {
            return $httpProvider;
        });

        $httpProvider.interceptors.push(['$templateCache', 'Cache', function($templateCache, Cache) {
            return {
                request: function(request) {
                    var url = request.url;
                    if (url.match(/\.html/)) {
                        var cached;
                        if ((cached = $templateCache.get(url)) && !Cache.get(url)) {
                            Cache.put(url, cached);
                        }
                        request.cacheKey = url;
                        request.cache = Cache;
                    }

                    return request;
                },
                response: function(response) {
                    if (response.config.cacheKey && !Cache.get(response.config.cacheKey)) {
                        Cache.put(response.config.cacheKey, response.data);
                    }
                    return response;
                }
            };
        }]);
    }]).run(['$routeProvider', '$httpProvider', 'controllers', '$rootScope', '$http', 'CacheFactory', function ($routeProvider, $httpProvider, controllers, $rootScope, $http, CacheFactory) {
        // init routing
        controllers = controllers();
        for (var i in controllers) {
            var metadata = controllers[i];
            $routeProvider.when('/' + metadata.url, metadata);
        }
        $routeProvider.otherwise({redirectTo: '/index'});

        // show error messages as flash
        $httpProvider.defaults.transformResponse.push(function(response, headers, code) {
            if (headers('X-Flash-Type')) {
                $rootScope.$broadcast('flash', [headers('X-Flash-Message'), headers('X-Flash-Type')]);
            }
            return response;
        });
    }]
);
