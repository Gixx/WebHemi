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
namespace WebHemi\Validator;

/**
 * Interface ValidatorInterface
 */
interface ValidatorInterface
{
    /**
     * Validates data.
     *
     * @param mixed $data
     * @return boolean
     */
    public function validate($data);

    /**
     * Gets error from validation.
     *
     * @return mixed
     */
    public function getError();
}
