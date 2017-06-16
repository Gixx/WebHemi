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

namespace WebHemi\Validator;

/**
 * Interface ValidatorInterface
 */
interface ValidatorInterface
{
    /**
     * Validates data.
     *
     * @param array $data
     * @return bool
     */
    public function validate(array $values) : bool;

    /**
     * Retrieve valid data.
     *
     * @return array
     */
    public function getValidData() : array;

    /**
     * Gets errors from validation.
     *
     * @return array
     */
    public function getErrors() : array;
}
