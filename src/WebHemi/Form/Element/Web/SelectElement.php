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
 * Class SelectElement.
 */
class SelectElement extends RadioElement
{
    /**
     * SelectElement constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($name = '', $label = '', $value = null)
    {
        parent::__construct($name, $label, $value);

        $this->setTabIndex();
    }

    /**
     * Returns the element type.
     *
     * @return string
     */
    public function getType()
    {
        return 'select';
    }

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
     * Sets label-value option for the element.
     *
     * @param string  $label
     * @param string  $value
     * @param boolean $checked
     * @param string  $group
     * @return SelectElement
     */
    protected function setOption($label, $value, $checked, $group)
    {
        // For <select> tag, the option grouping is allowed.
        if (!isset($this->options[$group])) {
            $this->options[$group] = [];
        }

        $this->optionGroups[$group] = $group;

        $this->options[$group][$label] = [
            'label' => $label,
            'value' => $value,
            'checked' => $checked
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
