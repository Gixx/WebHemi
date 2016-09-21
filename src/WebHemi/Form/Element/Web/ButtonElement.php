<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element\Web;

/**
 * Class ButtonElement.
 */
class ButtonElement extends InputElement
{
    /**
     * Returns the element type.
     *
     * @return string
     */
    public function getType()
    {
        return 'button';
    }

    /**
     * Skip original behaviour: button element does not have value.
     *
     * @param mixed $value
     * @return AbstractElement
     */
    public function setValue($value)
    {
        unset($value);
        return $this;
    }

    /**
     * Skip original behaviour: button element does not have value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return '';
    }
}
