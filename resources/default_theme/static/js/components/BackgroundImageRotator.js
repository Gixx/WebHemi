/**
 * WebHemi Components: Background Image Rotator
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

/**
 * BackgroundImageRotator component.
 *
 * @example A full setup for the component
 *
 * <HTMLElement id="optional-element-id"
 *              data-webhemi-component="BackgroundImageRotator"
 *              data-background-set-resource="absolute/path/to/images"
 *              data-background-set="01.jpg,02.jpg,http://foo.org/image.png,03.jpg"
 *              data-background-set-speed="{#speed}"
 * >...</HTMLElement>
 *
 * @legend
 *  HTMLElement - any block-level element. If the element has a background image by default, it will be included in the
 *                background image set.
 *  id="optional-element-id" -  giving an ID for the element is optional. If not set, the component will generate one.
 *  data-webhemi-component="BackgroundImageRotator" - MANDATORY to activate the component
 *  data-background-set-resource - a website-local folder where the images are present
 *  data-background-set - a comma separated list of images. Any absolute link will load as is. The rest will be loaded
 *                from the folder defined in the `data-background-set-resource` attribute
 *  data-background-set-speed - the speed of switching the images in millisecond. Minimum is 400, maximum is 18000.
 *
 * @type {{init}}
 */
WebHemi.components.BackgroundImageRotator = function()
{
    "use strict";

    /** @type {boolean} */
    let initialized = false;
    /** @type {NodeList} */
    let rotatableBackgroundElements = [];
    /** @type {number} */
    let idCounter = 1;
    /** @type {Object} */
    let options = WebHemi.getOptions();
    /** @type {Object} */
    let componentDefaultOptions = {
        componentIdPrefix: 'webhemi-rbe-',
        containerClass: 'background-set',
        setResourceAttribute: 'backgroundSetResource',
        setSpeedAttribute: 'backgroundSetSpeed',
        imageSetAttribute: 'backgroundSet',
        autoStart: true,
        rotateInterval: 5000
    };

    if (typeof WebHemi.components.Util === 'undefined') {
        throw new ReferenceError('The Util component is required to use this component.');
    }

    /** @type Util */
    let Util = WebHemi.components.Util;

    // Complete the missing option elements.
    for (let i in componentDefaultOptions) {
        if (componentDefaultOptions.hasOwnProperty(i) && typeof options.availableComponents.BackgroundImageRotator[i] === 'undefined') {
            options.availableComponents.BackgroundImageRotator[i] = componentDefaultOptions[i];
        }
    }

    let componentOptions = options.availableComponents.BackgroundImageRotator;

    /**
     * Adds changeable background functionality to an element.
     *
     * @param HTMLElement
     * @return {{constructor: BackgroundImageRotatorElement, start: start, stop: stop}}
     * @constructor
     */
    let BackgroundImageRotatorElement = function(HTMLElement)
    {
        /** @type {number} */
        const MIN_DURATION = 400;
        /** @type {number} */
        const MAX_DURATION = 18000;

        /** @type {Array} */
        let imageCache = [];
        /** @type {number} */
        let rotateInterval;
        /** @type {string} */
        let resourcePath;
        /** @type {number} */
        let rotateIntervalTimer = null;
        /** @type {number} */
        let transitionDuration;
        /** @type {number} */
        let min;
        /** @type {number} */
        let max;
        /** @type {number} */
        let loadedIndex;

        options.verbose && console.info(
            '%c➤%c a Rotatable Background Image Element found... %o',
            'color:blue; font-weight:bold;',
            'color:black;',
            '#'+HTMLElement.getAttribute('id')
        );

        /**
         * Preload Images
         *
         * @param images
         * @return {Promise<void>}
         */
        let preloadImages = async function(images)
        {
            options.verbose && console.group(
                '%c  preloading images...',
                'color:#cecece'
            );

            let failed = 0;
            const promises = images.map(function(imageUrl) {
                if (imageUrl.indexOf('/') === -1) {
                    imageUrl = resourcePath + imageUrl
                }

                return new Promise(function(resolve, reject){
                    let image = new Image();
                    image.addEventListener('load', function(){
                        imageCache.push(image);
                        options.verbose && console.info(
                            '%c⚡%c a background image is cached %c '+image.src,
                            'color:orange; font-weight:bold;',
                            'color:black;',
                            'font-style:italic'
                        );
                        resolve();
                    });
                    image.addEventListener('error', function () {
                        options.verbose && console.info(
                            '%c✖%c a background image is not found %c'+image.src,
                            'color:red',
                            'color:black',
                            'font-style:italic'
                        );
                        reject();
                    });

                    image.src = imageUrl;
                });
            });

            // Wait for all image load
            await Promise.all(promises)
                .catch(function() {
                    failed++;
                });

            let extraInfo = '';

            if (failed > 0) {
                extraInfo = (images.length - failed) + ' out of ';
            }

            options.verbose && console.info(extraInfo + (images.length) + ' images are cached.');
            options.verbose && console.groupEnd();

            return Promise.resolve();
        };

        /**
         * Set the transition styles
         *
         * @param imageIndex
         * @return {Promise<void>}
         */
        let setTransitionStyles = function(imageIndex)
        {
            HTMLElement.setAttribute('style', getBackgroundCssByIndex(imageIndex));

            if (HTMLElement.classList.contains('fade')) {
                HTMLElement.classList.remove('fade');
            }

            let styleContainer = document.querySelector('style#' + HTMLElement.getAttribute('id')+'-style');

            if (!styleContainer) {
                styleContainer = document.createElement('style');
                styleContainer.setAttribute('id', HTMLElement.getAttribute('id')+'-style');
                document.querySelector('html > head').append(styleContainer);
            }

            styleContainer.innerText = '#' + HTMLElement.getAttribute('id') + '::before { ' +
                    'content: \'\'; ' +
                    'display: block; ' +
                    'position: absolute; ' +
                    'top: 0; ' +
                    'left: 0; ' +
                    'width: 100%; ' +
                    'height: 100%; ' +
                    'opacity:0; ' +
                    'transition: opacity 0s; ' +
                    getBackgroundCssByIndex(imageIndex + 1) +
                '} ' +
                '#' + HTMLElement.getAttribute('id')+'.fade::before { opacity:1; transition: opacity ' + (transitionDuration / 1000) + 's ease-in-out; }';

            return Promise.resolve();
        };

        /**
         * Gets the background CSS definitions for image indentified by cache index
         *
         * @param imageIndex
         * @return {string}
         */
        let getBackgroundCssByIndex = function(imageIndex) {
            let imageUrl = getImageUrlByIndex(imageIndex);

            return getBackgroundCssByUrl(imageUrl);
        };

        /**
         * Gets the background CSS definitions for image indentified by url.
         *
         * @param imageUrl
         * @return {string}
         */
        let getBackgroundCssByUrl = function(imageUrl) {
            let backgroundGradient = 'linear-gradient(to bottom, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0) 30%, rgba(0,0,0,0) 70%, rgba(0,0,0,0.5) 100%)';
            let backgroundPosition = '0px 0px';
            let backgroundSize = 'cover';
            let backgroundRepeat = 'no-repeat';

            return 'background-image: ' + backgroundGradient + ', url(\'' + imageUrl + '\'); ' +
                'background-position:' + backgroundPosition + '; ' +
                'background-size:' + backgroundSize + '; ' +
                'background-repeat:' + backgroundRepeat + '; ';
        };

        /**
         * Gets an image Url from the cached images
         *
         * @param {number} index
         * @return {string}
         */
        let getImageUrlByIndex = function(imageIndex)
        {
            if (typeof imageCache[imageIndex] === 'undefined') {
                imageIndex = 0;
            }

            return imageCache[imageIndex].getAttribute('src');
        };

        /**
         * Sets the duration time.
         *
         * @param {number} transitionSpeed in ms.
         */
        let setTransitionSpeed = function(transitionSpeed)
        {
            transitionSpeed = Math.min(MAX_DURATION, Math.max(MIN_DURATION, transitionSpeed));
            rotateInterval = transitionSpeed;
            transitionDuration = Math.round(rotateInterval / 4);

            let extraInfo = '';

            if (transitionSpeed === MIN_DURATION) {
                extraInfo = ' (min)';
            } else if (transitionSpeed === MAX_DURATION) {
                extraInfo = ' (max)';
            }

            options.verbose && console.info(
                '%c⚡%c the background transition speed is set to '+rotateInterval+'ms.'+extraInfo,
                'color:orange; font-weight:bold;',
                'color:black;'
            );

            return Promise.resolve();
        };

        loadedIndex = 0;
        resourcePath = typeof HTMLElement.dataset[componentOptions.setResourceAttribute] !== 'undefined'
            ? HTMLElement.dataset[componentOptions.setResourceAttribute]
            : null;

        // Correct url ending
        if (resourcePath.length && resourcePath.slice(-1) !== '/') {
            resourcePath = resourcePath + '/';
        }

        rotateInterval = typeof HTMLElement.dataset[componentOptions.setSpeedAttribute] !== 'undefined'
            ? parseInt(HTMLElement.dataset[componentOptions.setSpeedAttribute])
            : componentOptions.rotateInterval;

        let images = HTMLElement.dataset[componentOptions.imageSetAttribute].split(',');

        // Check if there were images defined
        if (images.length === 1 && images[0] === '') {
            throw new ReferenceError('No background images are defined.');
        }

        // Check if there is any background image added to element from CSS
        let defaultBackgroundImage = window.getComputedStyle(HTMLElement).backgroundImage;

        if (defaultBackgroundImage !== 'none') {
            let pattern = /^.*url\((?:\'|\"|)(?<url>[^\'\"\)]+)(?:\'|\"|)\).*$/g;
            let match = pattern.exec(defaultBackgroundImage);

            if (match && typeof match.groups.url !== 'undefined') {
                images.unshift(match.groups.url);
            }
        }

        preloadImages(images).then(function() {
            options.verbose && console.group(
                '%c  set transition speed...',
                'color:#cecece'
            );
            let result = setTransitionSpeed(rotateInterval);
            options.verbose && console.groupEnd();
            return result;
        }).then(function() {
            options.verbose && console.group(
                '%c  preloading styles...',
                'color:#cecece'
            );
            HTMLElement.classList.add('fade');
            let result = setTransitionStyles(loadedIndex);
            options.verbose && console.groupEnd();
            return result;
        }).then(function() {
            options.verbose && console.info(
                '%c✚%c a Rotatable Background Image element is initialized %o',
                'color:green; font-weight:bold;',
                'color:black;',
                '#'+HTMLElement.getAttribute('id')
            );
        });

        return {
            constructor: BackgroundImageRotatorElement,

            /**
             * @type {boolean}
             */
            status : true,

            /**
             * Starts image rotation.
             */
            start : function()
            {
                rotateIntervalTimer = setInterval(function()
                {
                    HTMLElement.classList.add('fade');
                    // wait for the css transition
                    setTimeout(function()
                    {
                        // increment/reset background index
                        if(++loadedIndex >= imageCache.length) {
                            loadedIndex = 0;
                        }

                        setTransitionStyles(loadedIndex)
                    }, Math.round(1.2 * transitionDuration));
                }, rotateInterval);
            },

            /**
             * Stops image rotation.
             */
            stop : function()
            {
                if (rotateIntervalTimer !== null) {
                    clearInterval(rotateIntervalTimer);
                    rotateIntervalTimer = null;
                }
            },

            /**
             * Change the transition speed im ms.
             *
             * @param {number} transitionSpeed
             */
            setTransitionSpeed : function(transitionSpeed)
            {
                if (!isNaN(transitionSpeed)) {
                    this.stop();
                    setTransitionSpeed(parseInt(transitionSpeed));
                    this.start();
                }
            }
        };
    };

    options.verbose && console.info(
        '%c✔%c the Background Image Rotator component is loaded.',
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
                '%c  Looking for component elements...',
                'color:#cecece'
            );

            rotatableBackgroundElements = document.querySelectorAll('*[data-webhemi-component="BackgroundImageRotator"]');

            if (rotatableBackgroundElements.length > 0) {
                for (let i = 0, len = rotatableBackgroundElements.length; i < len; i++) {
                    // Add component id if not exists
                    if (!rotatableBackgroundElements[i].hasAttribute('id')) {
                        rotatableBackgroundElements[i].setAttribute('id', componentOptions.componentIdPrefix+(idCounter));
                    }

                    // Add component style class if not exists
                    if (!rotatableBackgroundElements[i].classList.contains(componentOptions.containerClass+'-'+(idCounter))) {
                        rotatableBackgroundElements[i].classList.add(componentOptions.containerClass+'-'+(idCounter));
                    }

                    idCounter++;

                    let imageRotateElement = new BackgroundImageRotatorElement(rotatableBackgroundElements[i]);

                    if (imageRotateElement.status === true) {
                        rotatableBackgroundElements[i].component = imageRotateElement;
                        rotatableBackgroundElements[i].component.start();
                    }
                }
            } else {
                options.verbose && console.info(
                    '%c  No component elements found.',
                    'color:#cecece; font-weight:bold;'
                );
            }

            options.verbose && console.groupEnd();

            Util.triggerEvent(document, 'WebHemi.Component.BackgroundImageRotator.Ready');
            initialized = true;
        }
    };
}();
