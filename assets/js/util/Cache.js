
App.service('Cache', ['CacheFactory', '$interval', '$rootScope', function(CacheFactory, $interval, $rootScope) {
    var cache = CacheFactory('default', {
        maxAge: 3600 * 1000, // 60 minutes
        deleteOnExpire: 'aggressive',
        storageMode:    'localStorage',
        storagePrefix:  ''
    });

    cache.clear = function(pattern) {
        var keys = cache.keys(), key, idx;

        for (idx in keys) {
            key = keys[idx];
            if (key.match(pattern)) {
                cache.remove(key);
            }
        }
    };

    // do only store plain response in cache when successful
    var oldPut = cache.put.bind(cache);
    cache.put = function put(key, value, options) {
        if ( value.length == 4 && value[0] == 200) {
            value = value[1];
        }
        oldPut(key, value, options);
    };

    cache.intervalClear = function(pattern, seconds) {
        $interval(function() {
            cache.clear(pattern);
        }, seconds * 1000);
    };

    $rootScope.$on('cache.invalidate', function(event, pattern) {
        cache.clear(pattern);
    });

    return cache;
}]);
