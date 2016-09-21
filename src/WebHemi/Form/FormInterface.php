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
     * Gets form name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set unique identifier for the form. Activates only when the auto-complete feature is in 'false' state.
     *
     * @param string $salt
     * @return FormInterface
     */
    public function setNameSalt($salt);

    /**
     * Sets form auto-complete feature on/off.
     *
     * @param bool $autoComplete
     * @return FormInterface
     */
    public function setAutoComplete($autoComplete = true);

    /**
     * Gets the form elements.
     *
     * @return array<FormElementInterface>
     */
    public function getNodes();

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
