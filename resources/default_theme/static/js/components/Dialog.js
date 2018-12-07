/**
 * WebHemi Components: Dialog
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * Dialog Component.
 *
 * @type {{init}}
 */
WebHemi.components.Dialog = function()
{
    "use strict";

    /** @type {boolean} */
    let initialized = false;
    /** @type {boolean} */
    let nativeSupport = true;
    /** @type {NodeList} */
    let dialogElements = [];
    /** @type {number} */
    let idCounter = 1;
    /** @type {string} */
    let dialogIdPrefix = 'webhemi-dialog-';
    /** @type {string} */
    let dialogShieldId = 'webhemi-dialog-shield';
    /** @type {Object} */
    let options = WebHemi.getOptions();

    if (typeof WebHemi.components.Util === 'undefined') {
        throw new ReferenceError('The Util component is required to use this component.');
    }

    /** @type Util */
    let Util = WebHemi.components.Util;

    /**
     * Dialog element handler.
     *
     * @param HTMLElement
     * @return {{constructor: DialogElement, open: open, close: close}}
     * @constructor
     */
    let DialogElement = function(HTMLElement)
    {
        if (!nativeSupport) {
            dialogPolyfill.registerDialog(HTMLElement);
        }

        options.verbose && console.info(
            '%c✚%c A Dialog Element is initialized %o',
            'color:green; font-weight:bold;',
            'color:black;',
            '#'+HTMLElement.getAttribute('id')
        );

        return {
            constructor: DialogElement,

            /**
             * Opens the dialog window.
             *
             * @return {DialogElement}
             */
            open : function()
            {
                if (!HTMLElement.hasAttribute('open')) {
                    HTMLElement.showModal();
                    if (!nativeSupport) {
                        document.getElementById(dialogShieldId).style.display = 'block';
                    }
                }

                return this;
            },

            /**
             * Closes the dialog window.
             *
             * @return {DialogElement}
             */
            close : function()
            {
                if (HTMLElement.hasAttribute('open')) {
                    HTMLElement.close();
                    if (!nativeSupport) {
                        document.getElementById(dialogShieldId).style.display = 'none';
                    }
                }

                return this;
            }
        }
    };

    options.verbose && console.info(
        '%c✔%c The Dialog Component is loaded.',
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
                '%c  Looking for Dialog Elements...',
                'color:#cecece'
            );
            dialogElements = document.querySelectorAll('dialog');

            if (dialogElements.length > 0) {
                if (!dialogElements[0].showModal) {
                    nativeSupport = false;
                }

                if (!nativeSupport && typeof dialogPolyfill === 'undefined') {
                    throw new ReferenceError('Dialog polyfill is not supported. Please include the \'dialog-polyfill.js\' first.');
                }

                // If there's no native browser support and there's no full-size dialog shield defined, we create one.
                if (!nativeSupport) {
                    let dialogShield = document.getElementById(dialogShieldId);

                    if (!dialogShield) {
                        dialogShield = document.createElement('DIV');
                        dialogShield.setAttribute('id', dialogShieldId);
                        dialogShield.setAttribute(
                            'style',
                            'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.1);z-index:1000;display:none;');
                        document.body.appendChild(dialogShield);
                    }
                }

                for (let i = 0, len = dialogElements.length; i < len; i++) {
                    if (!dialogElements[i].hasAttribute('id')) {
                        dialogElements[i].setAttribute('id', dialogIdPrefix + (idCounter++));
                    }

                    dialogElements[i].component = new DialogElement(dialogElements[i]);
                }
            } else {
                options.verbose && console.info(
                    '%c  No Dialog Elements found.',
                    'color:#cecece; font-weight:bold;'
                );
            }

            options.verbose && console.groupEnd();

            Util.triggerEvent(document, 'WebHemi.Component.Dialog.Ready');
            initialized = true;
        }
    };
}();
