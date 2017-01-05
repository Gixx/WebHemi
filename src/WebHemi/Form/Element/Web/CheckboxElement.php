<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element\Web;

/**
 * Class RadioElement.
 */
class CheckboxElement extends RadioElement
{
    /** @var string */
    protected $type = 'checkbox';

    /**
     * Sets element value.
     *
     * @param string $value
     * @return CheckboxElement
     */
    public function setValue($value)
    {
        if (empty($this->options)) {
            $this->value = boolval($value) ? 1 : 0;
        } else {
            parent::setValue($value);
        }

        return $this;
    }

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (empty($this->options)) {
            return $this->value;
        }

        return parent::getValue();
    }
}
