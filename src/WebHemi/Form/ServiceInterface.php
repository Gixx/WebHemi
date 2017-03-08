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
     */
    public function __construct(string $name, string $action, string $method = 'POST');

    /**
     * Gets form name.
     *
     * @return string
     */
    public function getName() : string;

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
     * Adds an element to the form.
     *
     * @param ElementInterface $formElement
     * @return ServiceInterface
     */
    public function addElement(ElementInterface $formElement) : ServiceInterface;

    /**
     * Returns all the elements assigned.
     *
     * @return array<ElementInterface>
     */
    public function getElements() : array;

    /**
     * Loads data into the form.
     *
     * @param array $data
     * @return ServiceInterface
     */
    public function loadData(array $data) : ServiceInterface;
}
