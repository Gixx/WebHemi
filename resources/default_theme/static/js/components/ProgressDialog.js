/**
 * WebHemi Components: Progress Dialog
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * Progress Dialog Component.
 *
 * @example Requires a <dialog> element with the following structure:
 *
 * <dialog id="{WebHemiOptions.availableComponents.ProgressDialog.dialogId}" data-progress="%progressId%">
 *   <p class="{WebHemiOptions.availableComponents.ProgressDialog.dialogInfo.titleElementClass}">%title%</p>
 *   <progress value="0" max="100"></progress>
 *   <p class="{WebHemiOptions.availableComponents.ProgressDialog.dialogInfo.counterElementClass}"></p>
 * </dialog>
 *
 * Custom {WebHemiOptions} should be provided in the represented structure BEFORE the WebHemi.init();
 *
 * Data coming from the backend should contain:
 * - %progressId% - an identifier for the current progress (e.g.: sessionId)
 * - %title% - the title of the dialog window
 *
 * @type {{init}}
 */
WebHemi.components.ProgressDialog = function()
{
    "use strict";

    /** @type {boolean} */
    let initialized = false;
    /** @type {HTMLElement} */
    let progressDialogElement;
    /** @type {number} */
    let progressIntervalTime = 500;
    /** @type {number} */
    let progressIntervalId = null;
    /** @type {Object} */
    let options = WebHemi.getOptions();
    /** @type {Object} */
    let progressDialogDefaultOptions = {
        targetUrl: '/progress.php?id=%progressId%',
        dialogId: 'webhemi-dialog-progress',
        counterType: 'percent',
        dialogInfo : {
            progressIdDataAttribute : 'progress',
            titleElementClass : 'progress-title',
            counterElementClass : 'progress-counter'
        },
    };

    if (typeof WebHemi.components.Util === 'undefined') {
        throw new ReferenceError('The Util Component is required to use this component.');
    }

    if (typeof WebHemi.components.Dialog === 'undefined') {
        throw new ReferenceError('The Dialog Component is required to use this component.');
    }

    /** @type Util */
    let Util = WebHemi.components.Util;
    /** @type Dialog */
    let Dialog = WebHemi.components.Dialog;

    // Complete the missing option elements.
    for (let i in progressDialogDefaultOptions) {
        if (progressDialogDefaultOptions.hasOwnProperty(i) && typeof options.availableComponents.ProgressDialog[i] === 'undefined') {
            options.availableComponents.ProgressDialog[i] = progressDialogDefaultOptions[i];
        }
    }

    let dialogOptions = options.availableComponents.ProgressDialog;

    if (dialogOptions.counterType !== 'counter' || dialogOptions.counterType !== 'percent') {
        dialogOptions.counterType = 'percent';
    }

    /**
     * Progress Dialog element handler.
     *
     * @param HTMLElement
     * @return {{constructor: ProgressDialogElement, open: open, close: close, start: start, stop: stop, setTitle: setTitle, reset: reset}}
     * @constructor
     */
    let ProgressDialogElement = function(HTMLElement)
    {
        let DialogReference = HTMLElement.component;

        let progressId = typeof HTMLElement.dataset[dialogOptions.dialogInfo.sessionIdDataAttribute] !== 'undefined'
            ? HTMLElement.dataset[dialogOptions.dialogInfo.progressIdDataAttribute]
            : '';

        let progressBarElement = HTMLElement.querySelector('#'+dialogOptions.dialogId+' > progress');
        let counterElement = HTMLElement.querySelector('#'+dialogOptions.dialogId+' > .'+dialogOptions.dialogInfo.counterElementClass);
        let titleElement = HTMLElement.querySelector('#'+dialogOptions.dialogId+' > .'+dialogOptions.dialogInfo.titleElementClass);

        function loadProgressData()
        {
            let jsonUrl = dialogOptions.targetUrl.replace(/%progressId%/, progressId);

            options.verbose && console.info('      -> HTTP GET: '+jsonUrl);

            Util.ajax({
                url: jsonUrl,
                method: 'GET',
                success: function(data) {
                    options.verbose && console.info('      <- Receive data...');

                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    let counter = parseInt(Math.ceil(data.current / data.total * 100));

                    if (dialogOptions.counterType === 'counter') {
                        counterElement.innerHTML = data.current + ' / ' + data.total;
                    } else {
                        counterElement.innerHTML = counter + '%';
                    }

                    progressBarElement.value = counter;

                    if (counter === 100 && progressIntervalId) {
                        clearInterval(progressIntervalId);
                        progressIntervalId = null;
                        Util.triggerEvent(document, 'WebHemi.Component.ProgressDialog.Progress.End');
                    }
                }
            });
        }

        console.info(
            '%c✚%c the Progress Dialog component is attached to a Dialog element %o',
            'color:green; font-weight:bold;',
            'color:black;',
            '#'+HTMLElement.getAttribute('id')
        );

        return {
            constructor: ProgressDialogElement,

            /**
             * Opens the progress dialog window.
             *
             * @return {ProgressDialogElement}
             */
            open : function()
            {
                this.reset();
                DialogReference.open();

                return this;
            },

            /**
             * Closes the progress dialog window.
             *
             * @return {ProgressDialogElement}
             */
            close : function()
            {
                DialogReference.close();

                return this;
            },

            /**
             * Opens the progress dialog window and updates the counter.
             *
             * @return {ProgressDialogElement}
             */
            start : function()
            {
                this.open();
                progressIntervalId = setInterval((function() { loadProgressData(); }).bind(this), progressIntervalTime);

                return this;
            },

            /**
             * Stops the counter progress and closes the progress dialog window.
             *
             * @return {ProgressDialogElement}
             */
            stop : function()
            {
                this.close();

                if (progressIntervalId) {
                    clearInterval(progressIntervalId);
                    progressIntervalId = null;
                }

                return this;
            },

            /**
             * Sets title for the progress dialog window.
             *
             * @param {string} title
             * @return {ProgressDialogElement}
             */
            setTitle : function(title)
            {
                titleElement.innerHTML = title;

                return this;
            },

            /**
             * Reset the progressbar and the counter.
             *
             * @return {ProgressDialogElement}
             */
            reset : function()
            {
                counterElement.innerHTML = '';

                return this;
            }
        };
    };

    options.verbose && console.info(
        '%c✔%c The Progress Dialog Component is loaded.',
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

            options.verbose && console.group('%c  Looking for Progress Dialog Element...', 'color:#cecece');

            progressDialogElement = document.querySelector('#'+dialogOptions.dialogId);

            if (progressDialogElement) {
                if (typeof progressDialogElement.component === 'undefined'
                    || progressDialogElement.component.constructor.name !== 'DialogElement'
                ) {
                    throw new ReferenceError('The dialog element #'+dialogOptions.dialogId+' is not registered as DialogElement.');
                }

                // Overwrite the element's component with the extended component.
                progressDialogElement.component = new ProgressDialogElement(progressDialogElement);
            } else {
                options.verbose && console.info(
                    '%c  No Progress Dialog Element found.',
                    'color:#cecece; font-weight:bold;'
                );
            }

            options.verbose && console.groupEnd();

            Util.triggerEvent(document, 'WebHemi.Component.ProgressDialog.Ready');
            initialized = true;
        }
    };
}();
