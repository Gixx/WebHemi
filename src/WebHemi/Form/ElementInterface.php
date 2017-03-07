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

use WebHemi\Validator\ServiceInterface as ValidatorInterface;

/**
 * Interface ElementInterface.
 */
interface ElementInterface
{
    /**
     * ElementInterface constructor.
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
     * @return ElementInterface
     */
    public function setName(string $name) : ElementInterface;

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
     * @return ElementInterface
     */
    public function setLabel(string $label) : ElementInterface;

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
     * @return ElementInterface
     */
    public function setValueRange(array $valueRange) : ElementInterface;

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
     * @return ElementInterface
     */
    public function setValues(array $values) : ElementInterface;

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
     * @return ElementInterface
     */
    public function addValidator(ValidatorInterface $validator) : ElementInterface;

    /**
     * Validates the element.
     *
     * @return ElementInterface
     */
    public function validate() : ElementInterface;

    /**
     * Set custom error.
     *
     * @param string $validator
     * @param string $error
     * @return ElementInterface
     */
    public function setError(string $validator, string $error) : ElementInterface;

    /**
     * Returns the errors collected during the validation.
     *
     * @return array
     */
    public function getErrors() : array;
}
