/**
 * WebHemi Component Framework
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

if (typeof window.WebHemiOptions === 'undefined') {
    window.WebHemiOptions = {};
}

/**
 * Webhemi Component Framework.
 *
 * @type {{constructor, components, init, getOptions}}
 */
var WebHemi = function(options)
{
    "use strict";

    /** @type {Object} */
    let defaultOptions = {
        path: 'components/',
        loadComponents: [
            'Util',
            'Registry',
            'Dialog',
            'ProgressDialog'
        ],
        availableComponents: {
            Util: {},
            Registry: {},
            Dialog: {},
            ProgressDialog: {},
            Form: {},
            LazyLoadImage: {},
            LazyLoadBackgroundImage: {},
            BackgroundImageRotator: {},
            Folding: {},
        },
        verbose: true
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
     * @return {Promise}
     */
    let loadComponents = function(index)
    {
        if (typeof index === 'undefined') {
            index = 0;
        }

        if (typeof options.loadComponents[index] === 'undefined') {
            return Promise.resolve();
        }

        let componentName = options.loadComponents[index];

        return loadComponent(componentName).then(function () {
            return loadComponents(index + 1);
        }).catch(function(error) {
            options.verbose && console.info(
                '%c✖%c the ' + error.component + ' component cannot be loaded: %c' + error.message,
                'color:red',
                'color:black',
                'text-decoration:underline;font-style:italic'
            );
            return loadComponents(index + 1);
        });
    };

    /**
     * Loads a specific compoment.
     *
     * @param {string} componentName
     * @return {Promise}
     */
    let loadComponent = function(componentName)
    {
        return new Promise(function(resolve, reject) {
            if (typeof options.availableComponents[componentName] !== 'undefined') {
                // if the component is not loaded
                if (typeof window.WebHemi.components[componentName] === 'undefined') {
                    let newScriptTag = document.createElement('script');
                    newScriptTag.setAttribute('type', 'text/javascript');
                    newScriptTag.setAttribute('src', options.path + componentName + '.js');
                    newScriptTag.onerror = function(error)
                    {
                        let errorMsg = error.toString().split("\n")[0];
                        reject({component: componentName, message: errorMsg});
                    };
                    newScriptTag.onload = function()
                    {
                        window.WebHemi.components[componentName].init();
                        resolve();
                    };

                    document.body.appendChild(newScriptTag);
                }
            } else {
                reject({component: componentName, message: 'not a valid component'});
            }
        });
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

        /**
         * Component container.
         */
        components: {},

        /**
         * Initializes the component.
         */
        init: function ()
        {
            loadComponents().finally(function() {
                options.verbose && console.groupEnd();
                options.verbose && console.info(
                    '%c✔%c WebHemi is ready.',
                    'color:green; font-weight:bold;',
                    'color:black; font-weight:bold;'
                );
                let readyEvent = new Event('WebHemi.Ready');
                document.dispatchEvent(readyEvent);
            });
        },

        /**
         * Return the configuration.
         *
         * @return {*}
         */
        getOptions: function ()
        {
            return options;
        }
    };
}(window.WebHemiOptions);
window.WebHemi = WebHemi;
window.WebHemi.init();

