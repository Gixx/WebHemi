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

use Exception;
use InvalidArgumentException;
use Iterator;
use RuntimeException;
use WebHemi\Form\Element\FormElementInterface;
use WebHemi\Form\Element\NestedElementInterface;
use WebHemi\Form\Traits\CamelCaseToUnderScoreTrait;
use WebHemi\Form\Traits\IteratorTrait;
use WebHemi\Validator\ValidatorInterface;

/**
 * Class AbstractElement
 */
abstract class AbstractElement implements FormElementInterface, Iterator
{
    /** @var string */
    protected $type = '';
    /** @var int */
    protected static $tabIndex = 1;

    /** @var string */
    protected $name;
    /** @var string */
    protected $label;
    /** @var mixed */
    protected $value;
    /** @var array */
    protected $attributes = [];
    /** @var array<ValidatorInterface> */
    protected $validators = [];
    /** @var array */
    protected $errors = [];
    /** @var FormElementInterface */
    protected $parentNode;
    /** @var array */
    protected $mandatoryParentTypes = [];

    // The implementation of the Iterator interface.
    use IteratorTrait;
    // CamelCase to under_score converter
    use CamelCaseToUnderScoreTrait;

    /**
     * AbstractFormElement constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($name = '', $label = '', $value = null)
    {
        $this->name = preg_replace('/[^a-z0-9]/', '_', strtolower($name));
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * Resets the object when cloning.
     */
    public function __clone()
    {
        unset($this->parentNode);
        $this->name = '';
        $this->label = '';
        $this->value = '';
        $this->attributes = [];
        $this->validators = [];
        $this->errors = [];
        $this->mandatoryParentTypes = [];
    }

    /**
     * Returns the element type.
     *
     * @throws Exception
     * @return string
     */
    final public function getType()
    {
        if (empty($this->type)) {
            throw new Exception(
                sprintf(
                    'You must specify the element type in the $type class property. In %s',
                    get_called_class()
                )
            );
        }

        return $this->type;
    }

    /**
     * Sets element name. The implementation should decide if it is allowed after init.
     *
     * @param string $name
     * @return AbstractElement
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the element name. If parameter is TRUE, then the method should include all the parents' names as well.
     *
     * @param boolean $getFulNodeName
     * @return string
     */
    public function getName($getFulNodeName = true)
    {
        $name = $this->name;

        if ($getFulNodeName && isset($this->parentNode)) {
            if ($this->parentNode instanceof FormElementInterface) {
                $name = $this->parentNode->getName().'['.$this->name.']';
            }
        }

        return $name;
    }

    /**
     * Sets element label.
     *
     * @param string $label
     * @return AbstractElement
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
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
     * @return AbstractElement
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
     * Gets element Id.
     *
     * @return string
     */
    public function getId()
    {
        $name = $this->getName();
        $md5Match = [];

        // Rip off the unique form prefix to make possible to work with fixed CSS id selectors.
        if (preg_match('/^.+(?P<md5>\_[a-f0-9]{32})($|\_.*$)/', $name, $md5Match)) {
            $name = str_replace($md5Match['md5'], '', $name);
        }

        $elementId = 'id_'.trim(preg_replace('/[^a-zA-Z0-9]/', '_', $name), '_');
        $elementId = $this->camelCaseToUnderscore($elementId);

        return str_replace('__', '_', $elementId);
    }

    /**
     * Sets multiple attributes.
     *
     * @param array $attributes
     * @return AbstractElement
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = [];

        return $this->addAttributes($attributes);
    }

    /**
     * Adds multiple attributes.
     *
     * @param array $attributes
     * @return AbstractElement
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Sets element attribute.
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     * @return AbstractElement
     */
    protected function setAttribute($key, $value)
    {
        $forbiddenAttributes = ['name', 'id'];

        // Skip forbidden attributes.
        if (in_array($key, $forbiddenAttributes)) {
            return $this;
        }

        if (!is_scalar($value)) {
            throw new InvalidArgumentException('Element attribute can hold scalar data only.');
        }

        $this->attributes[$key] = $value;

        return $this;
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
     * Gets element attribute.
     *
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            throw new InvalidArgumentException(sprintf('Invalid attribute: `%s`', $name));
        }

        return $this->attributes[$name];
    }

    /**
     * Resets the tabulator index internal counter.
     *
     * @return AbstractElement
     */
    public function resetTabIndex()
    {
        self::$tabIndex = 1;

        return $this;
    }

    /**
     * Sets and increments the tabulator index globally. This method should be used only on visible elements.
     *
     * @return AbstractElement
     */
    public function setTabIndex()
    {
        $this->attributes['tabindex'] = self::$tabIndex++;

        return $this;
    }

    /**
     * Sets the element errors. Usually the validator should set it, but it is allowed to set from outside too.
     *
     * @param array $errors
     * @return AbstractElement
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Checks if there are error messages set.
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

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
     * @param array<ValidatorInterface> $validators
     * @return FormElementInterface
     */
    public function setValidators(array $validators)
    {
        $this->validators = [];

        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    /**
     * Adds validator to the form.
     *
     * @param ValidatorInterface $validator
     * @return AbstractElement
     */
    protected function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Gets the element validators.
     *
     * @return array<ValidatorInterface>
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
    public function isValid($reValidate = false)
    {
        if ($reValidate) {
            $this->errors = [];

            /** @var ValidatorInterface $validator */
            foreach ($this->validators as $validator) {
                if (!$validator->validate($this->value)) {
                    $this->errors[] = $validator->getError();
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Sets the parent element.
     *
     * @param FormElementInterface $formElement
     * @throws RuntimeException
     * @return AbstractElement
     */
    public function setParentNode(FormElementInterface $formElement)
    {
        if (!$formElement instanceof NestedElementInterface) {
            throw new RuntimeException(
                sprintf(
                    'Cannot set `%s` as child element of `%s`.',
                    $this->getType(),
                    $formElement->getType()
                )
            );
        }

        $this->parentNode = $formElement;

        return $this;
    }

    /**
     * Gets the parent element.
     *
     * @return FormElementInterface
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }
}
