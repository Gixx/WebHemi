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
namespace WebHemiTest\Routing;

use InvalidArgumentException;
use WebHemi\Routing\Result;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ResultTest.
 */
class ResultTest extends TestCase
{
    /**
     * Test general usage.
     */
    public function testGeneralResult()
    {
        $result = new Result();
        $this->assertInstanceOf(Result::class, $result);

        $result->setMatchedMiddleware('Some data');
        $this->assertEquals($result->getMatchedMiddleware(), 'Some data');

        $result->setStatus(200);
        $this->assertEquals($result->getStatus(), Result::CODE_FOUND);
        $this->assertEquals($result->getStatusReason(), 'Resource found.');

        $result->setStatus(404);
        $this->assertEquals($result->getStatus(), Result::CODE_NOT_FOUND);
        $this->assertEquals($result->getStatusReason(), 'The requested resource cannot be found.');

        $result->setStatus(405);
        $this->assertEquals($result->getStatus(), Result::CODE_BAD_METHOD);
        $this->assertEquals($result->getStatusReason(), 'Bad request method was used by the client.');
    }

    /**
     * Test whether the instance throws exception for invalid status code.
     *
     * @throws InvalidArgumentException
     */
    public function testException()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $result = new Result();
        $result->setStatus(102);
    }
}
