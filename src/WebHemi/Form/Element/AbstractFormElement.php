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
namespace WebHemi\Form\Element;

use Iterator;

/**
 * Class AbstractFormElement
 */
abstract class AbstractFormElement implements FormElementInterface, Iterator
{
    /** @var int */
    protected static $tabIndex = 1;

    /** @var string */
    protected $tagName;
    /** @var string */
    protected $name;
    /** @var string */
    protected $label;
    /** @var mixed */
    protected $value;
    /** @var array */
    protected $attributes = [];
    /** @var array<FormValidatorInterface> */
    protected $validators = [];
    /** @var array */
    protected $errors = [];
    /** @var FormElementInterface */
    protected $parentNode = null;
    /** @var array<FormElementInterface> */
    protected $childNodes = [];

    /** @var array */
    protected $mandatoryTagParents = [
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
    protected $multiOptionTags = [
        self::TAG_SELECT,
        self::TAG_INPUT_RADIO,
        self::TAG_INPUT_CHECKBOX,
        self::TAG_DATALIST
    ];
    /** @var array */
    protected $tabIndexableTags = [
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
     * AbstractFormElement constructor.
     *
     * @param string $tagName
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    abstract public function __construct($tagName, $name = '', $label = '', $value = null);

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
     * Sets element name. The implementation should decide if it is allowed after init.
     *
     * @param string $name
     * @return FormElementInterface
     */
    abstract public function setName($name);

    /**
     * Returns the element name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets element label.
     *
     * @param string $label
     * @return FormElementInterface
     */
    abstract public function setLabel($label);

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
     * @return FormElementInterface
     */
    abstract public function setValue($value);

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
     * Sets multiple attributes.
     *
     * @param array $attributes
     * @return FormElementInterface
     */
    abstract public function setAttributes(array $attributes);

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
     * Sets the element errors. Usually the validator should set it, but it is allowed to set from outside too.
     *
     * @param array $errors
     * @return FormElementInterface
     */
    abstract public function setErrors(array $errors);

    /**
     * Gets validation errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets the element validators.
     *
     * @param array<FormValidatorInterface> $validators
     * @return FormElementInterface
     */
    abstract public function setValidators(array $validators);

    /**
     * Gets the element validators.
     *
     * @return array<FormValidatorInterface>
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Validates element value.
     *
     * @param bool $reValidate
     * @return bool
     */
    abstract public function isValid($reValidate = false);

    /**
     * Sets the parent element.
     *
     * @param FormElementInterface $formElement
     * @return FormElementInterface
     */
    abstract public function setParentNode(FormElementInterface $formElement);

    /**
     * Gets the parent element.
     *
     * @return FormElementInterface
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * Set the child nodes for the element.
     *
     * @param array<FormElementInterface> $childNodes
     * @return FormElementInterface
     */
    abstract public function setChildNodes(array $childNodes);

    /**
     * Checks if there are child elements.
     *
     * @return boolean
     */
    public function hasChildNodes()
    {
        return !empty($this->childNodes);
    }

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElementInterface>
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Return the current element.
     *
     * @return FormElementInterface
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
