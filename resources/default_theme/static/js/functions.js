/**
 * WebHemi
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * Returns the event element path.
 *
 * @param {Event} event
 * @return {Array}
 */
function getEventPath(event) {
    var path = (event.composedPath && event.composedPath()) || event.path,
        target = event.target;

    if (typeof path !== 'undefined') {
        // Safari doesn't include Window, and it should.
        path = (path.indexOf(window) < 0) ? path.concat([window]) : path;
        return path;
    }

    if (target === window) {
        return [window];
    }

    function getParents(node, memo) {
        memo = memo || [];
        var parentNode = node.parentNode;

        if (!parentNode) {
            return memo;
        }
        else {
            return getParents(parentNode, memo.concat([parentNode]));
        }
    }

    return [target]
        .concat(getParents(target))
        .concat([window]);
}
