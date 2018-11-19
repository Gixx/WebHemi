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

namespace WebHemi\Form;

/**
 * Interface ServiceInterface
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
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
    );

    /**
     * Initializes the form if it didn't happen in the constructor. (Used mostly in presets).
     *
     * @param  string $name
     * @param  string $action
     * @param  string $method
     * @param  string $enctype
     * @return ServiceInterface
     */
    public function initialize(
        string $name = '',
        string $action = '',
        string $method = 'POST',
        string $enctype = 'application/x-www-form-urlencoded'
    ) : ServiceInterface;

    /**
     * Gets form name.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Gets form ID.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Gets form action.
     *
     * @return string
     */
    public function getAction() : string;

    /**
     * Gets form method.
     *
     * @return string
     */
    public function getMethod() : string;

    /**
     * Gets form enctype.
     *
     * @return string
     */
    public function getEnctype() : string;

    /**
     * Adds an element to the form.
     *
     * @param  ElementInterface $formElement
     * @return ServiceInterface
     */
    public function addElement(ElementInterface $formElement) : ServiceInterface;

    /**
     * Returns an element
     *
     * @param  string $elementName
     * @return ElementInterface
     */
    public function getElement(string $elementName) : ElementInterface;

    /**
     * Returns all the elements assigned.
     *
     * @return ElementInterface[]
     */
    public function getElements() : array;

    /**
     * Loads data into the form.
     *
     * @param  array $data
     * @return ServiceInterface
     */
    public function loadData(array $data) : ServiceInterface;

    /**
     * Validates the form.
     *
     * @return bool
     */
    public function validate() : bool;
}
