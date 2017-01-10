<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element;

/**
 * Interface MultiOptionElementInterface
 */
interface MultiOptionElementInterface extends FormElementInterface
{
    /**
     * Set label-value options for the element.
     *
     * @param array $options
     * @return MultiOptionElementInterface
     */
    public function setOptions(array $options);

    /**
     * Checks if the element has value options.
     *
     * @return bool
     */
    public function hasOptions();

    /**
     * Gets element value options.
     *
     * @return array
     */
    public function getOptions();
}
