<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Fixtures;

use WebHemi\Validator\ValidatorInterface;

/**
 * Class TestTrueValidator.
 */
class TestFalseValidator implements ValidatorInterface
{
    /**
     * Validates data.
     *
     * @param mixed $data
     * @return boolean
     */
    public function validate($data)
    {
        unset($data);
        return false;
    }

    /**
     * Gets error from validation.
     *
     * @return mixed
     */
    public function getError()
    {
        return 'The data is not valid';
    }
}
