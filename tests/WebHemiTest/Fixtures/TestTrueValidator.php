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
namespace WebHemiTest\Fixtures;

use WebHemi\Validator\ValidatorInterface;

/**
 * Class TestTrueValidator.
 */
class TestTrueValidator implements ValidatorInterface
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
        return true;
    }

    /**
     * Gets error from validation.
     *
     * @return mixed
     */
    public function getError()
    {
        return null;
    }
}
