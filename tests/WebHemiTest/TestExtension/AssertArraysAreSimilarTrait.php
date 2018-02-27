<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestExtension;

/**
 * Trait AssertArraysAreSimilarTrait.
 */
trait AssertArraysAreSimilarTrait
{
    /**
     * {@inheritDoc}
     *
     * @param $expected
     * @param $actual
     * @param string $message
     * @return mixed
     */
    abstract public static function assertSame($expected, $actual, $message = '');

    /**
     * Compares two arrays.
     *
     * @param array $arrayOne
     * @param array $arrayTwo
     * @param string $message
     *
     * @return bool
     */
    protected function assertArraysAreSimilar(array $arrayOne, array $arrayTwo, string $message = '')
    {
        $result = strcmp(serialize($arrayOne), serialize($arrayTwo));
        $this->assertSame($result, 0, $message);
    }
}
