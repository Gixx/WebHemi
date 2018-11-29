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
namespace WebHemiTest;

use Throwable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebHemi\GeneralLib;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;

/**
 * Class StringLibTest
 */
class GeneralLibTest extends TestCase
{
    use AssertTrait;

    /**
     * Tests the mergeArrayOverwrite method.
     */
    public function testMerge()
    {
        try {
            $paramA = [];
            GeneralLib::mergeArrayOverwrite($paramA);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1000, $exception->getCode());
        }

        try {
            $paramA = [];
            $paramB = 1;
            GeneralLib::mergeArrayOverwrite($paramA, $paramB);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1001, $exception->getCode());
        }

        $paramA = [
            'level1A' => 'value1',
            'level1B' => [
                'old' => 'not new',
                'test' => 'value'
            ]
        ];
        $paramB = [
            'level1B' => [
                'test' => false,
                'new' => 'not old'
            ]
        ];
        $expectedResult = [
            'level1A' => 'value1',
            'level1B' => [
                'old' => 'not new',
                'test' => false,
                'new' => 'not old'
            ]
        ];

        $actualResult = GeneralLib::mergeArrayOverwrite($paramA, $paramB);

        $this->assertArraysAreSimilar($expectedResult, $actualResult);
    }
}
