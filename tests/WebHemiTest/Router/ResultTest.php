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
namespace WebHemiTest\Router;

use InvalidArgumentException;
use WebHemi\Router\Result\Result;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class ResultTest.
 */
class ResultTest extends TestCase
{
    use AssertTrait;

    /**
     * Test general usage.
     */
    public function testGeneralResult()
    {
        $result = new Result();
        $this->assertInstanceOf(Result::class, $result);

        $result->setMatchedMiddleware('Some data');
        $this->assertEquals($result->getMatchedMiddleware(), 'Some data');

        $testParam = [
            'param_1' => 'data 1',
            'param_2' => 'data 2'
        ];

        $result->setParameters($testParam);
        $this->assertArraysAreSimilar($testParam, $result->getParameters());

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
        $this->expectException(InvalidArgumentException::class);

        $result = new Result();
        $result->setStatus(102);
    }
}
