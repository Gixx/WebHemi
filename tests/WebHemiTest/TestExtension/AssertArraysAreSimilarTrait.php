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
namespace WebHemiTest\TestExtension;

/**
 * Trait AssertArraysAreSimilarTrait.
 */
trait AssertArraysAreSimilarTrait
{
    /**
     * @inheritDoc
     */
//    abstract public static function assertSame($expected, $actual, string $message = '') : void;

    /**
     * Compares two arrays.
     *
     * @param array $arrayOne
     * @param array $arrayTwo
     * @param string $message
     */
    protected function assertArraysAreSimilar(array $arrayOne, array $arrayTwo, string $message = '') : void
    {
        $result = strcmp(serialize($arrayOne), serialize($arrayTwo));
        $this->assertSame($result, 0, $message);
    }
}
