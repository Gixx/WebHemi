<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\Element\Html;

use InvalidArgumentException;
use WebHemi\Form\ElementInterface;
use WebHemi\StringLib;
use WebHemi\Validator\ValidatorInterface;

/**
 * Class AbstractElement.
 */
abstract class AbstractElement implements ElementInterface
{
    /** @var string */
    private $name;
    /** @var string */
    private $identifier;
    /** @var string */
    private $label;
    /** @var string */
    private $type;
    /** @var array */
    private $values = [];
    /** @var array */
    private $valueRange = [];
    /** @var ValidatorInterface[] */
    private $validators = [];
    /** @var array */
    private $errors = [];
    /** @var array */
    protected $validTypes = [];

    /**
     * AbstractElement constructor.
     *
     * @param string $type       The type of the form element.
     * @param string $name       For HTML forms it is the name attribute.
     * @param string $label      Optional. The label of the element. If not set the $name should be used.
     * @param array  $values     Optional. The values of the element.
     * @param array  $valueRange Optional. The range of interpretation.
     */
    public function __construct(
        string $type = null,
        string $name = null,
        string $label = null,
        array $values = [],
        array $valueRange = []
    ) {
        if (!empty($type)) {
            $this->setType($type);
        }

        if (!empty($name)) {
            $this->setName($name);
        }

        $this->label = $label;
        $this->values = $values;
        $this->valueRange = $valueRange;
    }

    /**
     * Sets element's name.
     *
     * @param string $name
     * @throws InvalidArgumentException
     * @return ElementInterface
     */
    public function setName(string $name) : ElementInterface
    {
        $name = StringLib::convertCamelCaseToUnderscore($name);
        $name = StringLib::convertNonAlphanumericToUnderscore($name, '[]');

        if (empty($name)) {
            throw new InvalidArgumentException('During conversion the argument value become an empty string!', 1000);
        }

        $this->name = $name;
        $this->identifier = 'id_'.StringLib::convertNonAlphanumericToUnderscore($name);

        return $this;
    }

    /**
     * Gets the element's name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets the element's name.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->identifier;
    }

    /**
     * Sets the element's type.
     *
     * @param string $type
     * @return ElementInterface
     */
    public function setType(string $type) : ElementInterface
    {
        if (!in_array($type, $this->validTypes)) {
            throw new InvalidArgumentException(
                sprintf('%s is not a valid %s type', $type, get_called_class()),
                1001
            );
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Gets the element's type.
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Sets the element's label.
     *
     * @param string $label
     * @return ElementInterface
     */
    public function setLabel(string $label) : ElementInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Gets the element's label.
     *
     * @return string
     */
    public function getLabel() : string
    {
        return $this->label ?? $this->name;
    }

    /**
     * Sets the values.
     *
     * @param array $values
     * @return ElementInterface
     */
    public function setValues(array $values) : ElementInterface
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Gets the values.
     *
     * @return array
     */
    public function getValues() : array
    {
        return $this->values;
    }

    /**
     * Sets the range of interpretation. Depends on the element type how it is used: exact element list or a min/max.
     *
     * @param array $valueRange
     * @return ElementInterface
     */
    public function setValueRange(array $valueRange) : ElementInterface
    {
        $this->valueRange = $valueRange;

        return $this;
    }

    /**
     * Get the range of interpretation.
     *
     * @return array
     */
    public function getValueRange() : array
    {
        return $this->valueRange;
    }

    /**
     * Adds a validator to the element.
     *
     * @param ValidatorInterface $validator
     * @return ElementInterface
     */
    public function addValidator(ValidatorInterface $validator) : ElementInterface
    {
        $validatorClass = get_class($validator);
        $this->validators[$validatorClass] = $validator;

        return $this;
    }

    /**
     * Validates the element.
     *
     * @return ElementInterface
     */
    public function validate() : ElementInterface
    {
        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validatorClass => $validator) {
            $isValid = $validator->validate($this->values);
            if (!$isValid) {
                $this->errors[$validatorClass] = $validator->getErrors();
            } else {
                $this->setValues($validator->getValidData());
            }
        }

        return $this;
    }

    /**
     * Set custom error.
     *
     * @param string $validator
     * @param string $error
     * @return ElementInterface
     */
    public function setError(string $validator, string $error) : ElementInterface
    {
        $this->errors[$validator] = [$error];

        return $this;
    }

    /**
     * Returns the errors collected during the validation.
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
