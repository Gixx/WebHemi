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
class FormElement implements Iterator
{
    const TAG_INPUT_TEXT = 'text';
    const TAG_INPUT_PASSWORD = 'password';
    const TAG_FIELDSET = 'fieldset';
    const TAG_BUTTON_SUBMIT = 'submit';
    const TAG_BUTTON_RESET = 'reset';
    const TAG_BUTTON = 'button';

    /** @var int */
    protected static $tabIndex = 1;
    /** @var string */
    private $tagName;
    /** @var string */
    private $name;
    /** @var string */
    private $label;
    /** @var mixed */
    private $value;
    /** @var array */
    private $attributes;
    /** @var FormElement */
    private $parentNode;
    /** @var FormElement[] */
    private $childNodes;
    /** @var FormValidatorInterface[] */
    private $validators;

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
        $this->name = $name;
        $this->label = $label;
        $this->attributes['tabindex'] = self::$tabIndex++;
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
     * @return FormElement
     */
    public function setParentNode(FormElement $formElement)
    {
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
        if (isset($this->parentNode)) {
            return $this->parentNode->getName() . '[' . $this->name . ']';
        }

        return $this->name;
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
     * @return FormElement[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Sets element attribute.
     *
     * @param string $key
     * @param string $value
     * @throws InvalidArgumentException
     * @return FormElement
     */
    public function setAttribute($key, $value)
    {
        if ($key == 'name') {
            throw new InvalidArgumentException('Cannot change element name after it has been initialized.');
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
            throw new RuntimeException('Invalid attribute: "' . $name . '"');
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
