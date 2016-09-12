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
namespace WebHemi\Form;

/**
 * Interface FormInterface
 */
interface FormInterface
{
    /**
     * FormInterface constructor. Creates <FORM> element.
     *
     * @param string $name
     * @param string $action
     * @param string $method
     */
    public function __construct($name, $action = '', $method = 'POST');

    /**
     * Gets the form elements.
     *
     * @return FormElement[];
     */
    public function getChildNodes();

    /**
     * Validates the form.
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Sets form data.
     *
     * @param array $data
     * @return FormInterface
     */
    public function setData(array $data);

    /**
     * Returns the form data.
     *
     * @return array
     */
    public function getData();
}
