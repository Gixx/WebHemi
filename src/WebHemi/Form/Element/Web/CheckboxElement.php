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
 * Class RadioElement.
 */
class CheckboxElement extends RadioElement
{
    /** @var string */
    protected $type = 'checkbox';

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return RadioElement
     */
    public function setValue($value)
    {
        if (empty($this->options) && is_numeric($value)) {
            $this->value = $value ? 1 : 0;
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
