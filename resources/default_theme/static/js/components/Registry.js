/**
 * WebHemi Components: Registry
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * Registry Component.
 *
 * @type {{init, has, get, set, setConfig, unset}}
 */
WebHemi.components.Registry = function()
{
    "use strict";

    /** @type {boolean} */
    let initialized = false;
    /** @let {Object} container   The associative array for key-value pairs */
    let container = {};
    /** @type {Object} */
    let options = WebHemi.getOptions();

    if (typeof WebHemi.components.Util === 'undefined') {
        throw new ReferenceError('The Util component is required to use this component.');
    }

    /** @type Util */
    let Util = WebHemi.components.Util;

    options.verbose && console.info(
        '%c✔%c The Regsitry Component is loaded.',
        'color:green; font-weight:bold;',
        'color:black; font-weight:bold;'
    );

    return {
        /**
         * Initializes the component.
         */
        init: function ()
        {
            initialized = true;
            Util.triggerEvent(document, 'WebHemi.Component.Registry.Ready');
        },

        /**
         * Checks whether the the key is present in the container
         *
         * @param {string} key   The key to check
         * @return {boolean}     TRUE if exists, FALSE otherwise
         */
        has : function (key)
        {
            return WebHemi.components.Util.isset(container[key]);
        },

        /**
         * Retrieves the value for the given key
         *
         * @param {string} key          The key to look for
         * @param {string} defaultValue A default value to return if no such key.
         * @return {*}   The value if key is found, NULL/default otherwise
         */
        get : function (key, defaultValue)
        {
            return this.has(key) ? container[key] : (typeof defaultValue !== 'undefined' ? defaultValue : null);
        },

        /**
         * Set the value for a key
         *
         * @param {string} key    A unique ID
         * @param {*} value   The value to set
         * @return {Registry}
         */
        set : function (key, value)
        {
            container[key] = value;

            return this;
        },

        /**
         * Set key-value pairs
         *
         * @param {Object} param   Key-value pairs
         * @return {Registry}
         */
        setConfig : function (param)
        {
            if (Util.isObject(param)) {
                for (let key in param) {
                    this.set(key, param[key]);
                }
            }
            return this;
        },

        /**
         * Removes a key from the container
         *
         * @param {string} key   The key to delete
         * @return {Registry}
         */
        unset : function (key)
        {
            if (this.has(key)) {
                container[key] = undefined;
            }
            return this;
        }
    }
}();
