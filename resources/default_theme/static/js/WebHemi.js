/**
 * WebHemi Components
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * Webhemi Component Framework.
 *
 * @param options
 * @return {{constructor: (function(*=)), components: {}, init: init, loadError: loadError}}
 * @constructor
 */
var WebHemi = function(options)
{
    "use strict";

    /** @type {Object} */
    let defaultOptions = {
        path: 'components/',
        components: [
            'Util'
            ,'Registry'
            // ,'Dialog'
            // ,'ProgressDialog'
            // ,'LazyLoadImage'
            // ,'LazyLoadBackgroundImage'
            // ,'Form'
            // ,'BackgroundImageRotator'
            // ,'Folding'
            // ,'SwipePager'
        ],
        verbose: true,
        event: new Event('WebHemi.loaded')
    };

    if (typeof options === 'undefined') {
        options = {};
    }

    // Complete the missing option elements.
    for (let i in defaultOptions) {
        if (defaultOptions.hasOwnProperty(i) && typeof options[i] === 'undefined') {
            options[i] = defaultOptions[i];
        }
    }

    // Correct component path.
    options.path = document.querySelector('body > script[src*="WebHemi.js"]')
        .getAttribute('src').replace(/WebHemi\.js/, '') + options.path;

    /**
     * Loads the compoments one after another.
     *
     * @param {number} index
     */
    let loadComponents = function(index)
    {
        if (typeof index === 'undefined') {
            index = 0;
        }

        if (typeof options.components[index] !== 'undefined') {
            let componentName = options.components[index];

            // if the component is not loaded
            if (typeof window.WebHemi.components[componentName] === 'undefined') {
                let newScriptTag = document.createElement('script');
                newScriptTag.setAttribute('type', 'text/javascript');
                newScriptTag.setAttribute('src', options.path + componentName + '.js');
                newScriptTag.onerror = function(error)
                {
                    let errorMsg = error.toString().split("\n")[0];
                    options.verbose && console.info(
                        '%c✖%c the '+componentName+' component cannot be loaded: %c'+ errorMsg,
                        'color:red',
                        'color:black',
                        'text-decoration:underline;font-style:italic'
                    );
                    window.WebHemi.loadComponent(index + 1);
                };
                newScriptTag.onload = function()
                {
                    window.WebHemi.components[componentName].init();
                    window.WebHemi.loadComponent(index + 1);
                };

                document.body.appendChild(newScriptTag);
            }
        } else {
            setTimeout(function(){
                options.verbose && console.groupEnd();
                options.verbose && console.info(
                    '%c✔%c WebHemi is ready.',
                    'color:green; font-weight:bold;',
                    'color:black; font-weight:bold;'
                );
                document.dispatchEvent(options.event);
            }, 500);
        }
    };

    options.verbose && console.clear();
    options.verbose && console.info(
        '%cWelcome to the WebHemi Component Framework!',
        'color:black; font-weight:bold;font-size:20px'
    );
    options.verbose && console.group(
        '%c  looking for components...',
        'color:#cecece'
    );

    return {
        constructor: WebHemi,
        components: {},

        /**
         * Initializes the component.
         */
        init: function () {
            loadComponents();
        },

        /**
         * Loads a specific component.
         *
         * @param index
         */
        loadComponent: function(index) {
            loadComponents(index);
        },

        /**
         * Return the configuration.
         *
         * @return {*}
         */
        getOptions: function () {
            return options;
        }
    };
}();
window.WebHemi = WebHemi;
window.WebHemi.init();

