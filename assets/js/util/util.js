
/**
 * Pseudo function needed to mark asset links. during console assets:dump the links are replaced by hashed ones
 * @param {String} filename
 * @deprecated
 * @returns {String}
 */
function asset(filename) {
    return filename;
}

/**
 * @returns String {string}
 */
String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined'
            ? args[number]
            : match;
    });
};

/**
 * @source http://stackoverflow.com/questions/5767325/remove-a-specific-element-from-an-array-in-javascript
 * @param value
 * @returns {Array}
 */
Array.prototype.removeByValue = function(value) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] === value) {
            this.splice(i, 1);
            i--;
        }
    }
    return this;
};
