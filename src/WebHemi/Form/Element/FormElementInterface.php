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

    /**
     * AbstractFormElement constructor.
     *
     * @param string $tagName
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($tagName, $name = '', $label = '', $value = null);

    /**
     * Returns the element tag name.
     *
     * @return string
     */
    public function getTagName();

    /**
     * Sets element name.
     *
     * @param string $name
     * @return FormElementInterface
     */
    public function setName($name);

    /**
     * Returns the element name.
     *
     * @return string
     */
    public function getName();

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
     * Sets multiple attributes.
     *
     * @param array $attributes
     * @return FormElementInterface
     */
    public function setAttributes(array $attributes);

    /**
     * Gets all the attributes.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Sets the element errors. Usually the validator should set it, but it is allowed to set from outside too.
     *
     * @param array $errors
     * @return FormElementInterface
     */
    public function setErrors(array $errors);

    /**
     * Gets validation errors.
     *
     * @return array
     */
    public function getErrors();

    /**
     * Sets the element validators.
     *
     * @param array<FormValidatorInterface> $validators
     * @return FormElementInterface
     */
    public function setValidators(array $validators);

    /**
     * Gets the element validators.
     *
     * @return array<FormValidatorInterface>
     */
    public function getValidators();

    /**
     * Validates element value.
     *
     * @param bool $reValidate
     * @return bool
     */
    public function isValid($reValidate = false);

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

    /**
     * Set the child nodes for the element.
     *
     * @param array<FormElementInterface> $childNodes
     * @return FormElementInterface
     */
    public function setChildNodes(array $childNodes);

    /**
     * Checks if there are child elements.
     *
     * @return boolean
     */
    public function hasChildNodes();

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElementInterface>
     */
    public function getChildNodes();
}
