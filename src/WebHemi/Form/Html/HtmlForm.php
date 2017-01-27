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

use JsonSerializable;
use WebHemi\Form\FormElementInterface;
use WebHemi\Form\FormInterface;

/**
 * Class HtmlForm
 */
class HtmlForm implements FormInterface, JsonSerializable
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
     * FormInterface constructor.
     *
     * @param string $name
     * @param string $action
     * @param string $method
     */
    public function __construct(string $name, string $action, string $method = 'POST')
    {
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;
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
     * @param FormElementInterface $formElement
     * @return FormInterface
     */
    public function addElement(FormElementInterface $formElement) : FormInterface
    {
        $elementName = $formElement->getName();
        $elementName = $this->name.'['.$elementName.']';
        $formElement->setName($elementName);

        $this->formElements[$elementName] = $formElement;

        return $this;
    }

    /**
     * Returns all the elements assigned.
     *
     * @return array<FormElementInterface>
     */
    public function getElements() : array
    {
        return $this->formElements;
    }

    /**
     * Loads data into the form.
     *
     * @param array $data
     * @return FormInterface
     */
    public function loadData(array $data) : FormInterface
    {
        $formData = $data[$this->name] ?? [];

        foreach ($formData as $elementName => $elementValue) {
            $fullName = $this->name.'['.$elementName.']';
            /** @var FormElementInterface $formElement */
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
         * @var FormElementInterface $formElement
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
