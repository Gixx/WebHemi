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

namespace WebHemiTest\Adapter\Exception;

use PHPUnit_Framework_TestCase as TestCase;
use WebHemi\Adapter\Exception\InitException;

/**
 * Class InitExceptionTest.
 */
class InitExceptionTest extends TestCase
{
    /**
     * A fake test just to trigger coveralls.
     */
    public function testException()
    {
        $exception = new InitException();

        $this->assertInstanceOf(InitException::class, $exception);
    }
}
