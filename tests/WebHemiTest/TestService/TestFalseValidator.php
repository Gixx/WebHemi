<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Validator\ValidatorInterface;

/**
 * Class TestFalseValidator.
 */
class TestFalseValidator implements ValidatorInterface
{
    /**
     * Set validator options.
     *
     * @param array $options
     */
    public function setOptions(array $options) : void
    {
        unset($options);
    }

    /**
     * Validates data.
     *
     * @param array $values
     * @return boolean
     */
    public function validate(array $values) : bool
    {
        unset($values);
        return false;
    }

    /**
     * Retrieve valid data.
     *
     * @return array
     */
    public function getValidData() : array
    {
        return [];
    }

    /**
     * Gets error from validation.
     *
     * @return array
     */
    public function getErrors() : array
    {
        return ['The data is not valid'];
    }
}
