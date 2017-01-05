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

use WebHemi\Form\Element\MultiOptionElementInterface;

/**
 * Class RadioElement.
 */
class RadioElement extends AbstractTabindexElement implements MultiOptionElementInterface
{
    /** @var string */
    protected $type = 'radio';
    /** @var array */
    protected $options = [];
    /** @var array */
    protected $optionGroups = [];

    /**
     * Resets the object when cloning.
     */
    public function __clone()
    {
        parent::__clone();

        $this->options = [];
        $this->optionGroups = [];
    }

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return RadioElement
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $valuesToSelect = $this->getValuesToSelect($value);

        // Go through the options and change the defaults.
        foreach ($this->options as &$option) {
            $option['checked'] = in_array($option['value'], $valuesToSelect);
        }

        return $this;
    }

    /**
     * Collects the selected values for multi option element.
     *
     * @param $value
     * @return array
     */
    protected function getValuesToSelect($value)
    {
        $isAssociativeArray = array_keys($value) !== range(0, count($value) - 1);
        $valuesToSelect = [];

        // Go through the given data and collect the selected ones.
        foreach ($value as $key => $data) {
            if ($isAssociativeArray && $data == 1) {
                $valuesToSelect[] = $key;
            } elseif (!$isAssociativeArray) {
                $valuesToSelect[] = $data;
            }
        }

        return $valuesToSelect;
    }

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        $selectedValues = [];

        foreach ($this->options as $option) {
            if ($option['checked']) {
                $selectedValues[] = $option['value'];
            }
        }

        return $selectedValues;
    }

    /**
     * Set label-value options for the element.
     *
     * @param array $options
     * @return RadioElement
     */
    public function setOptions(array $options)
    {
        /** @var MultiOptionElementInterface $this */
        $this->options = [];
        $this->optionGroups = [];

        // The tabulator index is an automatically set attribute for all elements. Since this element group is generated
        // from the options, the element should manipulate the global tabulator index counter
        self::$tabIndex--;

        foreach ($options as $option) {
            $checked = !empty($option['checked']);
            $group = !empty($option['group']) ? $option['group'] : 'Default';
            $attributes = isset($option['attributes']) ? $option['attributes'] : [];
            $attributes['tabindex'] = self::$tabIndex++;
            $this->setOption($option['label'], $option['value'], $checked, $group, $attributes);
        }

        return $this;
    }

    /**
     * Sets label-value option for the element.
     *
     * @param string  $label
     * @param string  $value
     * @param boolean $checked
     * @param string  $group
     * @param array   $attributes
     * @return RadioElement
     */
    protected function setOption($label, $value, $checked, $group, array $attributes = [])
    {
        $this->options[$label] = [
            'label' => $label,
            'value' => $value,
            'checked' => $checked,
            'group' => $group,
            'attributes' => $attributes
        ];

        return $this;
    }

    /**
     * Checks if the element has value options.
     *
     * @return bool
     */
    public function hasOptions()
    {
        return !empty($this->options);
    }

    /**
     * Gets element value options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
