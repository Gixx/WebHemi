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

namespace WebHemi\Form\ServiceAdapter\Base;

use JsonSerializable;
use RuntimeException;
use WebHemi\Form\ElementInterface;
use WebHemi\Form\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface, JsonSerializable
{
    /** @var string */
    private $name;
    /** @var string */
    private $action;
    /** @var string */
    private $method;
    /** @var array */
    private $formElements = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param string|null $name
     * @param string|null $action
     * @param string      $method
     */
    public function __construct(string $name = null, string $action = null, string $method = 'POST')
    {
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;
    }

    /**
     * Initializes the form if it didn't happen in the constructor. (Used mostly in presets).
     *
     * @param string $name
     * @param string $action
     * @param string $method
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function initialize(string $name, string $action, string $method = 'POST') : ServiceInterface
    {
        if (isset($this->name) || isset($this->action)) {
            throw new RuntimeException('The form had been already initialized!', 1000);
        }

        $this->name = $name;
        $this->action = $action;
        $this->method = $method;

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
     * Adds an element to the form.
     *
     * @param ElementInterface $formElement
     * @return ServiceInterface
     */
    public function addElement(ElementInterface $formElement) : ServiceInterface
    {
        $elementName = $formElement->getName();

        if (!isset($this->formElements[$elementName])) {
            $elementName = $this->name.'['.$elementName.']';
            $formElement->setName($elementName);
        }

        $this->formElements[$elementName] = $formElement;

        return $this;
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
     * @param array $data
     * @return ServiceInterface
     */
    public function loadData(array $data) : ServiceInterface
    {
        $formData = $data[$this->name] ?? [];

        foreach ($formData as $elementName => $elementValue) {
            $fullName = $this->name.'['.$elementName.']';
            /** @var ElementInterface $formElement */
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
