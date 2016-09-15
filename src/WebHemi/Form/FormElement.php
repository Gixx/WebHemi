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
namespace WebHemi\Form;

use Iterator;
use InvalidArgumentException;
use RuntimeException;
use WebHemi\Form\Validator\FormValidatorInterface;

/**
 * Class FormElement
 */
final class FormElement implements Iterator
{
    /** HTML5 form elements */
    const TAG_FORM = 'form';
    const TAG_INPUT_CHECKBOX = 'checkbox';
    const TAG_INPUT_COLOR = 'color';
    const TAG_INPUT_DATA = 'date';
    const TAG_INPUT_DATETIME = 'datetime';
    const TAG_INPUT_DATETIME_LOCAL = 'datetime-local';
    const TAG_INPUT_EMAIL = 'email';
    const TAG_INPUT_FILE = 'file';
    const TAG_INPUT_HIDDEN = 'hidden';
    const TAG_INPUT_IMAGE = 'image';
    const TAG_INPUT_MONTH = 'month';
    const TAG_INPUT_NUMBER = 'number';
    const TAG_INPUT_PASSWORD = 'password';
    const TAG_INPUT_RADIO = 'radio';
    const TAG_INPUT_RANGE = 'range';
    const TAG_INPUT_SEARCH = 'search';
    const TAG_INPUT_TEL = 'tel';
    const TAG_INPUT_TEXT = 'text';
    const TAG_INPUT_TIME = 'time';
    const TAG_INPUT_URL = 'url';
    const TAG_INPUT_WEEK = 'week';
    const TAG_TEXTAREA = 'textarea';
    const TAG_FIELDSET = 'fieldset';
    const TAG_LEGEND = 'legend';
    const TAG_LABEL = 'label';
    const TAG_BUTTON_SUBMIT = 'submit';
    const TAG_BUTTON_RESET = 'reset';
    const TAG_BUTTON = 'button';
    const TAG_DATALIST = 'datalist';
    const TAG_SELECT = 'select';
    const TAG_OPTION_GROUP = 'optgroup';
    const TAG_OPTION = 'option';
    const TAG_KEYGEN = 'keygen';
    const TAG_OUTPUT = 'output';

    /** @var int */
    protected static $tabIndex = 1;
    /** @var string */
    private $tagName;
    /** @var string */
    private $name;
    /** @var string */
    private $uniqueFormNamePostfix = '';
    /** @var string */
    private $label;
    /** @var mixed */
    private $value;
    /** @var array */
    private $options = [];
    /** @var array */
    private $optionGroups = [];
    /** @var array */
    private $attributes;
    /** @var FormElement */
    private $parentNode;
    /** @var array<FormElement> */
    private $childNodes;
    /** @var array<FormValidatorInterface> */
    private $validators;
    /** @var array */
    private $mandatoryTagParents = [
        self::TAG_FORM => [],
        self::TAG_LEGEND => [
            self::TAG_FIELDSET
        ],
        self::TAG_OPTION => [
            self::TAG_DATALIST,
            self::TAG_OPTION_GROUP,
            self::TAG_SELECT
        ],
        self::TAG_OPTION_GROUP => [
            self::TAG_SELECT
        ],
    ];
    /** @var array */
    private $multiOptionTags = [
        self::TAG_SELECT,
        self::TAG_INPUT_RADIO,
        self::TAG_INPUT_CHECKBOX,
        self::TAG_DATALIST
    ];
    /** @var array */
    private $tabIndexableTags = [
        self::TAG_INPUT_CHECKBOX,
        self::TAG_INPUT_COLOR,
        self::TAG_INPUT_DATA,
        self::TAG_INPUT_DATETIME,
        self::TAG_INPUT_DATETIME_LOCAL,
        self::TAG_INPUT_EMAIL,
        self::TAG_INPUT_FILE,
        self::TAG_INPUT_IMAGE,
        self::TAG_INPUT_MONTH,
        self::TAG_INPUT_NUMBER,
        self::TAG_INPUT_PASSWORD,
        self::TAG_INPUT_RADIO,
        self::TAG_INPUT_RANGE,
        self::TAG_INPUT_SEARCH,
        self::TAG_INPUT_TEL,
        self::TAG_INPUT_TEXT,
        self::TAG_INPUT_TIME,
        self::TAG_INPUT_URL,
        self::TAG_INPUT_WEEK,
        self::TAG_TEXTAREA,
        self::TAG_BUTTON_SUBMIT,
        self::TAG_BUTTON_RESET,
        self::TAG_BUTTON,
        self::TAG_DATALIST,
        self::TAG_SELECT,
        self::TAG_KEYGEN,
    ];

    /**
     * FormElement constructor.
     *
     * @param string $tagName
     * @param string $name
     * @param string $label
     */
    public function __construct($tagName, $name, $label = '')
    {
        $this->tagName = $tagName;
        $this->name = preg_replace('/[^a-z0-9]/', '_', strtolower($name));
        $this->label = $label;

        if (in_array($tagName, $this->tabIndexableTags)) {
            $this->attributes['tabindex'] = self::$tabIndex++;
        }
    }

    /**
     * Set unique identifier for the form.
     *
     * @param string $uniqueFormNamePostfix
     * @return FormElement
     */
    public function setUniqueFormNamePostfix($uniqueFormNamePostfix)
    {
        if ($this->tagName != self::TAG_FORM) {
            throw new RuntimeException('This method can be applied only fot the <form> element.');
        }

        $this->uniqueFormNamePostfix = $uniqueFormNamePostfix;

        return $this;
    }

    /**
     * Returns the element tag name.
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * Sets parent element name
     *
     * @param FormElement $formElement
     * @throws RuntimeException
     * @return FormElement
     */
    public function setParentNode(FormElement $formElement)
    {
        $parentTagName = $formElement->getTagName();

        if (isset($this->mandatoryTagParents[$this->tagName])
            && !in_array($parentTagName, $this->mandatoryTagParents[$this->tagName])
        ) {
            throw new RuntimeException(
                sprintf(
                    'Cannot set `%s` as child element of `%s`.',
                    $this->tagName,
                    $parentTagName
                )
            );
        }

        $this->parentNode = $formElement;

        return $this;
    }

    /**
     * Returns the element name.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->name;

        if (isset($this->parentNode)) {
            $name = $this->parentNode->getName() . '[' . $this->name . ']';
        } elseif (!empty($this->uniqueFormNamePostfix)) {
            $name .= '_' . $this->uniqueFormNamePostfix;
        }

        if (count($this->options) > 1
            && $this->tagName  == self::TAG_SELECT
            && !empty($this->attributes['multiple'])
        ) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Gets element Id.
     *
     * @return string
     */
    public function getId()
    {
        return 'id_' . trim(preg_replace('/[^a-z0-9]/', '_', $this->getName()), '_');
    }

    /**
     * Returns the element label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return FormElement
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set label-value options for the element.
     *
     * @param array $options
     * @throws RuntimeException
     * @return FormElement
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option) {
            $checked = !empty($option['checked']);
            $group = !empty($option['group']) ? $option['group'] : 'Default';
            $this->setOption($option['label'], $option['value'], $checked, $group);
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
     * @return FormElement
     */
    public function setOption($label, $value, $checked = false, $group = 'Default')
    {
        if (!in_array($this->tagName, $this->multiOptionTags)) {
            throw new RuntimeException(sprintf('Cannot set value options for `%s` element.', $this->tagName));
        }

        $option = &$this->options;

        // For <select> tag, the option groupping is allowed.
        if ($this->tagName == self::TAG_SELECT) {
            if (!isset($this->options[$group])) {
                $this->options[$group] = [];
            }

            $option = &$this->options[$group];

            $this->optionGroups[$group] = $group;
        }

        $option[$label] = [
            'label' => $label,
            'value' => $value,
            'checked' => $checked
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
     * Checks if the Select box has groupped options.
     *
     * @return bool
     */
    public function isGrouppedSelect()
    {
        return count($this->optionGroups) > 1;
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

    /**
     * Set child node for the element.
     *
     * @param FormElement $childNode
     * @return FormElement
     */
    public function addChildNode(FormElement $childNode)
    {
        $childNode->setParentNode($this);

        $this->childNodes[] = $childNode;

        return $this;
    }

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElement>
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Sets element attribute.
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     * @return FormElement
     */
    public function setAttribute($key, $value)
    {
        if ($key == 'name') {
            throw new InvalidArgumentException('Cannot change element name after it has been initialized.');
        }

        if ($key == 'id') {
            throw new InvalidArgumentException('Element ID is generated from name. Call $element->getId();');
        }

        if (!is_scalar($value)) {
            throw new InvalidArgumentException('Element attribute can hold scalar data only.');
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Sets multiple attributes.
     *
     * @param array $attributes
     * @return FormElement
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Gets element attribute.
     *
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            throw new RuntimeException(sprintf('Invalid attribute: `%s`', $name));
        }

        return $this->attributes[$name];
    }

    /**
     * Gets all the attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Adds validator to the form.
     *
     * @param FormValidatorInterface $validator
     * @return FormElement
     */
    public function addValidator(FormValidatorInterface $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Validates element value.
     *
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->validators as $validator) {
            if (!$validator->validate($this->value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the current element.
     *
     * @return FormElement
     */
    final public function current()
    {
        return current($this->childNodes);
    }

    /**
     * Moves the pointer forward to next element.
     *
     * @return void
     */
    final public function next()
    {
        next($this->childNodes);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed
     */
    final public function key()
    {
        return key($this->childNodes);
    }

    /**
     * Checks if current position is valid.
     *
     * @return boolean
     */
    final public function valid()
    {
        $key = key($this->childNodes);

        return ($key !== null && $key !== false);
    }

    /**
     * Rewinds the Iterator to the first element.
     *
     * @return void
     */
    final public function rewind()
    {
        reset($this->childNodes);
    }
}
