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
namespace WebHemiTest;

use PHPUnit\Framework\TestCase;
use WebHemi\StringLib;

/**
 * Class StringLibTest
 */
class StringLibTest extends TestCase
{
    /**
     * Data provider for the CamelCase test.
     *
     * @return array
     */
    public function camelCaseDataProvider()
    {
        return [
            ['CamelCase', 'camel_case'],
            ['Camel_Case', 'camel__case'],
            ['__CamelCase__', '___camel_case__'],
            ['CAMELCASE', 'c_a_m_e_l_c_a_s_e'],
        ];
    }

    /**
     * Tests the convertCamelCaseToUnderscore method.
     *
     * @param string $input
     * @param string $expectedOutput
     *
     * @dataProvider camelCaseDataProvider
     */
    public function testCamelCaseToUnderscore($input, $expectedOutput)
    {
        $actualOutput = StringLib::convertCamelCaseToUnderscore($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    /**
     * Data provider for the under_score test.
     *
     * @return array
     */
    public function underscoreDataProvider()
    {
        return [
            ['camel_case', 'CamelCase'],
            ['camel__case', 'Camel_Case'],
            ['___camel_case__', '__CamelCase__'],
            ['c_a_M_e_l_C_a_s_e','CAMELCASE'],
        ];
    }

    /**
     * Tests the convertUnderscoreToCamelCase method.
     *
     * @param string $input
     * @param string $expectedOutput
     *
     * @dataProvider underscoreDataProvider
     */
    public function testUnderscoreToCamelCase($input, $expectedOutput)
    {
        $actualOutput = StringLib::convertUnderscoreToCamelCase($input);

        $this->assertSame($expectedOutput, $actualOutput);
    }

    /**
     * Data provider for the under_score test.
     *
     * @return array
     */
    public function nonAlphanumeric()
    {
        return [
            ['under_score', '', 'under_score'],
            ['CamelCase', '', 'CamelCase'],
            ['array[representation]', '', 'array_representation'],
            ['array[representation]', '[]', 'array[representation]'],
            ['array[representation]', ']', 'array_representation]'],
            ['[({*&%$})]','',''],
        ];
    }

    /**
     * Tests the convertNonAlphanumericToUnderscore method.
     *
     * @param string $input
     * @param string $extraCharacters
     * @param string $expectedOutput
     *
     * @dataProvider nonAlphanumeric
     */
    public function testNonAlphanumeric($input, $extraCharacters, $expectedOutput)
    {
        $actualOutput = StringLib::convertNonAlphanumericToUnderscore($input, $extraCharacters);

        $this->assertSame($expectedOutput, $actualOutput);
    }
}
