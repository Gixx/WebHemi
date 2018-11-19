<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\ServiceAdapter\Base;

use JsonSerializable;
use RuntimeException;
use InvalidArgumentException;
use WebHemi\Form\ElementInterface;
use WebHemi\Form\ServiceInterface;
use WebHemi\StringLib;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface, JsonSerializable
{
    /**
     * @var string
     */
    private $identifier = '';
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $action;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $enctype;
    /**
     * @var array
     */
    private $formElements = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param string $name
     * @param string $action
     * @param string $method
     * @param string $enctype
     */
    public function __construct(
        string $name = '',
        string $action = '',
        string $method = 'POST',
        string $enctype = 'application/x-www-form-urlencoded'
    ) {
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;
        $this->enctype = $enctype;

        if (!empty($this->name)) {
            $this->name = StringLib::convertCamelCaseToUnderscore($name);
            $this->identifier = StringLib::convertNonAlphanumericToUnderscore($this->name);
        }
    }

    /**
     * Initializes the form if it didn't happen in the constructor. (Used mostly in presets).
     *
     * @param  string $name
     * @param  string $action
     * @param  string $method
     * @param string $enctype
     *
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function initialize(
        string $name = '',
        string $action = '',
        string $method = 'POST',
        string $enctype = 'application/x-www-form-urlencoded'
    ) : ServiceInterface {
        if (!empty($this->name) || !empty($this->action)) {
            throw new RuntimeException('The form had been already initialized!', 1000);
        }

        $this->name = StringLib::convertCamelCaseToUnderscore($name);
        $this->identifier = StringLib::convertNonAlphanumericToUnderscore($this->name);
        $this->action = $action;
        $this->method = $method;
        $this->enctype = $enctype;

        return $this;
    }

    /**
     * Gets form name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Gets form ID.
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->identifier;
    }

    /**
     * Gets form action.
     *
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * Gets form method.
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Gets form enctype.
     *
     * @return string
     */
    public function getEnctype() : string
    {
        return $this->enctype;
    }

    /**
     * Adds an element to the form.
     *
     * @param  ElementInterface $formElement
     * @throws InvalidArgumentException
     * @return ServiceInterface
     */
    public function addElement(ElementInterface $formElement) : ServiceInterface
    {
        $elementName = $formElement->getName();
        $elementName = $this->name.'['.$elementName.']';

        if (isset($this->formElements[$elementName])) {
            throw new InvalidArgumentException(
                sprintf('The element "%s" in field list is ambiguous.', $elementName),
                1001
            );
        }

        $formElement->setName($elementName);
        $this->formElements[$elementName] = $formElement;

        return $this;
    }

    /**
     * Returns an element
     *
     * @param  string $elementName
     * @throws InvalidArgumentException
     * @return ElementInterface
     */
    public function getElement(string $elementName) : ElementInterface
    {
        $elementNames = array_keys($this->formElements);
        $elementName = StringLib::convertNonAlphanumericToUnderscore($elementName);
        $matchingElementNames = preg_grep('/.*\['.$elementName.'\]/', $elementNames);

        if (empty($matchingElementNames)) {
            throw new InvalidArgumentException(
                sprintf('The element "%s" does not exist in this form.', $elementName),
                1002
            );
        }

        return $this->formElements[current($matchingElementNames)];
    }

    /**
     * Returns all the elements assigned.
     *
     * @return ElementInterface[]
     */
    public function getElements() : array
    {
        return $this->formElements;
    }

    /**
     * Loads data into the form.
     *
     * @param  array $data
     * @return ServiceInterface
     */
    public function loadData(array $data) : ServiceInterface
    {
        $formData = $data[$this->name] ?? [];

        foreach ($formData as $elementName => $elementValue) {
            $fullName = $this->name.'['.$elementName.']';
            /**
             * @var ElementInterface $formElement
             */
            $formElement = $this->formElements[$fullName] ?? null;

            if ($formElement) {
                if (!is_array($elementValue)) {
                    $elementValue = [$elementValue];
                }
                $formElement->setValues($elementValue);
            }
        }

        return $this;
    }

    /**
     * Validates the form.
     *
     * @return bool
     */
    public function validate() : bool
    {
        $isValid = true;

        /**
         * @var string $index
         * @var ElementInterface $formElement
         */
        foreach ($this->formElements as $index => $formElement) {
            $this->formElements[$index] = $formElement->validate();

            if (!empty($formElement->getErrors())) {
                $isValid = false;
            }
        }

        return $isValid;
    }

    /**
     * Defines the data which are presented during the json serialization.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $formData = [
            'name' => $this->name,
            'action' => $this->action,
            'method' => $this->method,
            'data' => [],
            'errors' => []
        ];

        /**
         * @var string $elementName
         * @var ElementInterface $formElement
         */
        foreach ($this->formElements as $formElement) {
            $formData['data'][$formElement->getId()] = $formElement->getValues();

            $errors = $formElement->getErrors();

            if (!empty($errors)) {
                $formData['errors'][$formElement->getId()] = $errors;
            }
        }

        return $formData;
    }
}
