
App.service('Expression', ['$http', 'Cache', function($http, Cache) {
    Cache.intervalClear('^/expression/', 60);

    return {
        getData: function() {
            return $http.get('/expressions/');
        },

        evaluate: function(expression, cached) {
            return $http.get('/expressions/evaluate/', {
                params: {expression: expression},
                cache: cached ? Cache : false
            });
        },

        save: function(expression) {
            return $http.put('/expressions/', expression);
        },

        deleteExpression: function(expressionId) {
            return  $http.delete('/expressions/{0}/'.format(expressionId));
        },

        deleteEvent: function(eventId) {
            return $http.delete('/stats/event/?job_id={0}'.format(eventId));
        },

        addCron: function(cron) {
            return $http.post('/expressions/cron/', cron);
        },

        invalidate: function() {
            return Cache.clear('/expression/');
        }
    }
}]);
