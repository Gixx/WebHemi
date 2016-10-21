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

use WebHemi\Form\Element\MultiOptionElementInterface;

/**
 * Class SelectElement.
 */
class SelectElement extends RadioElement
{
    /** @var string */
    protected $type = 'select';

    /**
     * Returns the element name. If parameter is TRUE, then the method should include all the parents' names as well.
     *
     * @param boolean $getFulNodeName
     * @return string
     */
    public function getName($getFulNodeName = true)
    {
        $name = parent::getName($getFulNodeName);

        if ($getFulNodeName
            && count($this->options) > 1
            && !empty($this->attributes['multiple'])
        ) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return SelectElement
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $valuesToSelect = $this->getValuesToSelect($value);

        foreach ($this->options as $group => $options) {
            foreach ($options as $index => $option) {
                $this->options[$group][$index]['checked'] = in_array($option['value'], $valuesToSelect);
            }
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
        $selectedValues = [];

        foreach ($this->options as $options) {
            foreach ($options as $option) {
                if ($option['checked']) {
                    $selectedValues[] = $option['value'];
                }
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

        foreach ($options as $option) {
            $checked = !empty($option['checked']);
            $group = !empty($option['group']) ? $option['group'] : 'Default';
            $attributes = isset($option['attributes']) ? $option['attributes'] : [];
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
     * @return SelectElement
     */
    protected function setOption($label, $value, $checked, $group, array $attributes = [])
    {
        // For <select> tag, the option grouping is allowed.
        if (!isset($this->options[$group])) {
            $this->options[$group] = [];
        }

        $this->optionGroups[$group] = $group;

        $this->options[$group][$label] = [
            'label' => $label,
            'value' => $value,
            'checked' => $checked,
            'attributes' => $attributes
        ];

        return $this;
    }

    /**
     * Checks if the Select box has groupped options.
     *
     * @return bool
     */
    public function isGroupedSelect()
    {
        return count($this->optionGroups) > 1;
    }
}
