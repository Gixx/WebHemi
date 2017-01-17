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

namespace WebHemi\Form\Html;

use InvalidArgumentException;
use WebHemi\Form\FormElementInterface;
use WebHemi\Traits\CamelCaseToUnderScoreTrait;
use WebHemi\Validator\ValidatorInterface;

class HtmlFormElement implements FormElementInterface
{
    public const HTML_ELEMENT_BUTTON = 'button';
    public const HTML_ELEMENT_FORM = 'form';
    public const HTML_ELEMENT_INPUT_CHECKBOX = 'checkbox';
    public const HTML_ELEMENT_INPUT_FILE = 'file';
    public const HTML_ELEMENT_INPUT_HIDDEN = 'hidden';
    public const HTML_ELEMENT_INPUT_IMAGE = 'IMAGE';
    public const HTML_ELEMENT_INPUT_PASSWORD = 'password';
    public const HTML_ELEMENT_INPUT_RADIO = 'radio';
    public const HTML_ELEMENT_INPUT_RESET = 'reset';
    public const HTML_ELEMENT_INPUT_SUBMIT = 'submit';
    public const HTML_ELEMENT_INPUT_TEXT = 'text';
    public const HTML_ELEMENT_SELECT = 'select';
    public const HTML_ELEMENT_TEXTAREA = 'textarea';

    /** @var string */
    private $name;
    /** @var string */
    private $id;
    /** @var string */
    private $label;
    /** @var string */
    private $type;
    /** @var array */
    private $values = [];
    /** @var array */
    private $valueRange = [];
    /** @var array<ValidatorInterface> */
    private $validators = [];
    /** @var array */
    private $errors = [];
    /** @var array */
    protected $validTypes = [
        self::HTML_ELEMENT_BUTTON,
        self::HTML_ELEMENT_FORM,
        self::HTML_ELEMENT_INPUT_CHECKBOX,
        self::HTML_ELEMENT_INPUT_FILE,
        self::HTML_ELEMENT_INPUT_HIDDEN,
        self::HTML_ELEMENT_INPUT_IMAGE,
        self::HTML_ELEMENT_INPUT_PASSWORD,
        self::HTML_ELEMENT_INPUT_RADIO,
        self::HTML_ELEMENT_INPUT_RESET,
        self::HTML_ELEMENT_INPUT_SUBMIT,
        self::HTML_ELEMENT_INPUT_TEXT,
        self::HTML_ELEMENT_SELECT,
        self::HTML_ELEMENT_TEXTAREA,
    ];

    /**
     * Converts CamelCase text to under_score equivalent.
     * Converts any text to Identifier-like string
     */
    use CamelCaseToUnderScoreTrait;

    /**
     * HtmlFormElement constructor.
     *
     * @param string $type       The type of the form element.
     * @param string $name       For HTML forms it is the name attribute.
     * @param string $label      Optional. The label of the element. If not set the $name should be used.
     * @param array  $values     Optional. The values of the element.
     * @param array  $valueRange Optional. The range of interpretation.
     */
    public function __construct(
        string $type,
        string $name,
        string $label = null,
        array $values = [],
        array $valueRange = []
    ) {
        if (!in_array($type, $this->validTypes)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The given type "%s" is not a valid %s type.',
                    $type,
                    __CLASS__
                )
            );
        }

        $this->setName($name);

        $this->type = $type;
        $this->label = $label;
        $this->values = $values;
        $this->valueRange = $valueRange;
    }

    /**
     * Sets element's name.
     *
     * @param string $name
     * @throws InvalidArgumentException
     * @return FormElementInterface
     */
    public function setName(string $name) : FormElementInterface
    {
        if (empty($name)) {
            throw new InvalidArgumentException('During conversion the argument value become an empty string!');
        }

        $this->name = $name;

        $name = $this->camelCaseToUnderscore($name);
        $name = preg_replace('/[^a-zA-Z0-9]/', '_', $name);
        $this->id = trim(str_replace('__', '_', $name), '_');

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
        return $this->id;
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
     * @return FormElementInterface
     */
    public function setLabel(string $label) : FormElementInterface
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
     * @return FormElementInterface
     */
    public function setValues(array $values) : FormElementInterface
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
     * @return FormElementInterface
     */
    public function setValueRange(array $valueRange) : FormElementInterface
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
     * @return FormElementInterface
     */
    public function addValidator(ValidatorInterface $validator) : FormElementInterface
    {
        $this->validators[] = $validator;
    }

    /**
     * Validates the element.
     *
     * @return FormElementInterface
     */
    public function validate() : FormElementInterface
    {
        $result = [];

        /** @var ValidatorInterface $validator */
        foreach ($this->validators as $validator) {
            if (!$validator->validate($this->values)) {
                $result[get_class($validator)] = $validator->getErrors();
            }
        }

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
