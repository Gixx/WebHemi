/**
 * WebHemi Components: Form
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

if (typeof formOptions === 'undefined') {
    let formOptions = {
        verbose: WebHemiOptions.verbose
    };
}

/**
 * Form Component.
 *
 * @type {{init}}
 */
WebHemi.components.Form = function ()
{
    "use strict";

    /** @type {boolean} */
    let initialized = false;
    /** @type {NodeList} */
    let formElements = [];
    /** @type {number} */
    let idCounter = 1;
    /** @type {string} */
    let formIdPrefix = 'webhemi-form-';
    /** @type {Object} */
    let options = WebHemi.getOptions();
    /** @type {Object} */
    let formDefaultOptions = {
        ajaxFlagAttribute: 'ajax',
        asynchFlagAttribute: 'asynch'
    };

    if (typeof WebHemi.components.Util === 'undefined') {
        throw new ReferenceError('The Util component is required to use this component.');
    }

    /** @type Util */
    let Util = WebHemi.components.Util;

    // Complete the missing option elements.
    for (let i in formDefaultOptions) {
        if (formDefaultOptions.hasOwnProperty(i) && typeof options.availableComponents.Form[i] === 'undefined') {
            options.availableComponents.Form[i] = formDefaultOptions[i];
        }
    }

    let formOptions = options.availableComponents.Form;


    /**
     * Adds special functionality to
     * @param HTMLElement
     * @return {{constructor: FormElement, onBeforeSubmit: onBeforeSubmit, onSuccess: onSuccess, onFailure: onFailure}}
     * @constructor
     */
    let FormElement = function(HTMLElement)
    {
        let isAjax = typeof HTMLElement.dataset[formOptions.ajaxFlagAttribute] !== 'undefined'
            ? Util.inArray(HTMLElement.dataset[formOptions.ajaxFlagAttribute], [1, true, '1', 'true'])
            : false;
        let isAsync = typeof HTMLElement.dataset[formOptions.asynchFlagAttribute] !== 'undefined'
            ? Util.inArray(HTMLElement.dataset[formOptions.asynchFlagAttribute], [1, true, '1', 'true'])
            : true;
        let targetUrl = HTMLElement.getAttribute('action');
        let method = HTMLElement.hasAttribute('action') ? HTMLElement.getAttribute('method') : 'POST';
        let enctype = HTMLElement.hasAttribute('enctype') ? HTMLElement.getAttribute('enctype') : 'application/json';

        if (enctype === 'text/plain') {
            enctype = 'application/json';
        }

        let onBeforeSubmitCallback;
        let onSuccessCallback;
        let onFailureCallback;

        /**
         * Handles the form submit event.
         *
         * @param {Event} event
         * @return {boolean}
         */
        let submitForm = function(event)
        {
            if (typeof onBeforeSubmitCallback !== 'undefined') {
                let returnValue = onBeforeSubmitCallback(event);

                if (returnValue === false) {
                    return false;
                }
            }

            if (isAjax) {
                event.preventDefault();

                let formData = new FormData(HTMLElement);

                Util.ajax({
                    url: targetUrl,
                    data: formData,
                    enctype: enctype,
                    method: method,
                    async: isAsync,
                    success: onSuccessCallback,
                    failure: onFailureCallback
                });
            }

            options.verbose && console.info(
                '%c⚡%c an Form element was submitted. %o',
                'color:orange;font-weight:bold',
                'color:#599bd6',
                '#'+HTMLElement.getAttribute('id')
            );
        };

        // Fix
        if (targetUrl === '') {
            targetUrl = location.href;
        }

        if (targetUrl.slice(-1) !== '/') {
            targetUrl = targetUrl + '/';
        }

        HTMLElement.setAttribute('action', targetUrl);

        Util.addEventListeners([HTMLElement], 'submit', submitForm, this);

        options.verbose && console.info(
            '%c✚%c a'+(isAjax ? 'n Ajax' : '')+' Form element is initialized %o',
            'color:green; font-weight:bold;',
            'color:black;',
            '#'+HTMLElement.getAttribute('id')
        );

        return {
            constructor : FormElement,

            /**
             * Waiting for a callback function. Fires right before the submit.
             *
             * @param {function} callback
             */
            onBeforeSubmit : function(callback)
            {
                if (typeof callback === 'function') {
                    onBeforeSubmitCallback = callback;
                }
            },

            /**
             * Waiting for a callback function. Fires only for AjaxForms when the reponse is a success.
             *
             * @param {function} callback
             */
            onSuccess: function(callback)
            {
                if (typeof callback === 'function') {
                    onSuccessCallback = callback;
                }
            },

            /**
             * Waiting for a callback function. Fires only for AjaxForms when the reponse is a failure.
             *
             * @param {function} callback
             */
            onFailure: function(callback)
            {
                if (typeof callback === 'function') {
                    onFailureCallback = callback;
                }
            }
        }
    };

    if (typeof Util === 'undefined') {
        throw new ReferenceError('The Util component is required to use this component.');
    }

    options.verbose && console.info(
        '%c✔%c the Form component is loaded.',
        'color:green; font-weight:bold;',
        'color:black; font-weight:bold;'
    );

    return {
        /**
         * Initializes the loader and collects the elements.
         */
        init : function()
        {
            if (initialized) {
                return;
            }

            options.verbose && console.group(
                '%c  Looking for Form Elements...',
                'color:#cecece'
            );

            formElements = document.querySelectorAll('form');

            if (formElements.length > 0) {
                for (let i = 0, len = formElements.length; i < len; i++) {
                    if (!formElements[i].hasAttribute('id')) {
                        formElements[i].setAttribute('id', formIdPrefix + (idCounter++));
                    }

                    formElements[i].component = new FormElement(formElements[i]);
                }
            } else {
                options.verbose && console.info(
                    '%c  No Form Elements found.',
                    'color:#cecece; font-weight:bold;'
                );
            }


            options.verbose && console.groupEnd();

            Util.triggerEvent(document, 'WebHemi.Component.Form.Ready');
            initialized = true;
        }
    };
}();
