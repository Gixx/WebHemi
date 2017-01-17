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
 * Interface FormInterface
 */
interface FormInterface
{
    /**
     * FormInterface constructor.
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
     * @param FormElementInterface $formElement
     * @return FormInterface
     */
    public function addElement(FormElementInterface $formElement) : FormInterface;

    /**
     * Returns all the elements assigned.
     *
     * @return array<FormElementInterface>
     */
    public function getElements() : array;
}
