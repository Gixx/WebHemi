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
namespace WebHemiTest;

/**
 * Class AssertTrait.
 */
trait AssertTrait
{
    /**
     * Compares two arrays.
     *
     * @param array $arrayOne
     * @param array $arrayTwo
     *
     * @return bool
     */
    protected function assertArraysAreSimilar(array $arrayOne, array $arrayTwo)
    {
        $result = strcmp(serialize($arrayOne), serialize($arrayTwo));
        $this->assertSame($result, 0);
    }
}
