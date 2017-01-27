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

namespace WebHemi\Form;

use WebHemi\Validator\ValidatorInterface;

/**
 * Interface FormElementInterface
 */
interface FormElementInterface
{
    /**
     * FormElementInterface constructor.
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
    );

    /**
     * Sets element's name.
     *
     * @param string $name
     * @return FormElementInterface
     */
    public function setName(string $name) : FormElementInterface;

    /**
     * Gets the element's name.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Gets the element's unique id generated from the name.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Gets the element's type.
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Sets the element's label.
     *
     * @param string $label
     * @return FormElementInterface
     */
    public function setLabel(string $label) : FormElementInterface;

    /**
     * Gets the element's label.
     *
     * @return string
     */
    public function getLabel() : string;

    /**
     * Sets the range of interpretation. Depends on the element type how it is used: exact element list or a min/max.
     *
     * @param array $valueRange
     * @return FormElementInterface
     */
    public function setValueRange(array $valueRange) : FormElementInterface;

    /**
     * Get the range of interpretation.
     *
     * @return array
     */
    public function getValueRange() : array;

    /**
     * Sets the values.
     *
     * @param array $values
     * @return FormElementInterface
     */
    public function setValues(array $values) : FormElementInterface;

    /**
     * Gets the values.
     *
     * @return array
     */
    public function getValues() : array;

    /**
     * Adds a validator to the element.
     *
     * @param ValidatorInterface $validator
     * @return FormElementInterface
     */
    public function addValidator(ValidatorInterface $validator) : FormElementInterface;

    /**
     * Validates the element.
     *
     * @return FormElementInterface
     */
    public function validate() : FormElementInterface;

    /**
     * Set custom error.
     *
     * @param string $validator
     * @param string $error
     * @return FormElementInterface
     */
    public function setError(string $validator, string $error) : FormElementInterface;

    /**
     * Returns the errors collected during the validation.
     *
     * @return array
     */
    public function getErrors() : array;
}
