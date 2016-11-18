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

/**
 * Interface FormElementInterface
 */
interface FormElementInterface
{
    /**
     * AbstractFormElement constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($name = '', $label = '', $value = null);

    /**
     * Returns the element type.
     *
     * @return string
     */
    public function getType();

    /**
     * Sets element name.
     *
     * @param string $name
     * @return FormElementInterface
     */
    public function setName($name);

    /**
     * Returns the element name. If parameter is TRUE, then the method should include all the parents' names as well.
     *
     * @param boolean $getFulNodeName
     * @return string
     */
    public function getName($getFulNodeName = true);

    /**
     * Sets element label.
     *
     * @param string $label
     * @return FormElementInterface
     */
    public function setLabel($label);

    /**
     * Returns the element label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return FormElementInterface
     */
    public function setValue($value);

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Sets multiple attributes with deleting the existing ones.
     *
     * @param array $attributes
     * @return FormElementInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Adds multiple attributes to the existing ones.
     *
     * @param array $attributes
     * @return FormElementInterface
     */
    public function addAttributes(array $attributes);

    /**
     * Gets all the attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Resets the tabulator index internal counter.
     *
     * @return FormElementInterface
     */
    public function resetTabIndex();

    /**
     * Sets and increments the tabulator index globally. This method should be used only on visible elements.
     *
     * @return FormElementInterface
     */
    public function setTabIndex();

    /**
     * Sets the element validators.
     *
     * @param array<ValidatorInterface> $validators
     * @return FormElementInterface
     */
    public function setValidators(array $validators);

    /**
     * Gets the element validators.
     *
     * @return array<ValidatorInterface>
     */
    public function getValidators();

    /**
     * Validates the element's value. If parameter is TRUE, the method should ignore any previous validation results.
     *
     * @param bool $reValidate
     * @return bool
     */
    public function isValid($reValidate = false);

    /**
     * Sets the element error messages. Usually the validator should set it, but it is allowed to set from outside too.
     *
     * @param array $errors
     * @return FormElementInterface
     */
    public function setErrors(array $errors);

    /**
     * Checks if there are error messages set.
     *
     * @return boolean
     */
    public function hasErrors();

    /**
     * Gets the element error messages.
     *
     * @return array
     */
    public function getErrors();

    /**
     * Sets the parent element.
     *
     * @param FormElementInterface $formElement
     * @return FormElementInterface
     */
    public function setParentNode(FormElementInterface $formElement);

    /**
     * Gets the parent element.
     *
     * @return FormElementInterface
     */
    public function getParentNode();
}
