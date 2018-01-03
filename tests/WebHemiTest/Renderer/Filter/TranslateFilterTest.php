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
namespace WebHemiTest\Renderer\Filter;

use PHPUnit\Framework\TestCase;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Renderer\Filter\TranslateFilter;
use WebHemiTest\TestService\EmptyEnvironmentManager;
use WebHemiTest\TestService\EmptyI18nDriver;
use WebHemiTest\TestService\EmptyI18nService;

/**
 * Class TranslateFilterTest.
 */
class TranslateFilterTest extends TestCase
{
    /** @var EmptyI18nDriver */
    protected $i18nDriver;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $configFile = require __DIR__ . '/../../test_config.php';
        $config = new Config($configFile);
        $server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $environmentManager = new EmptyEnvironmentManager($config, [], [], $server, [], []);

        $i18nService = new EmptyI18nService($config, $environmentManager);
        $this->i18nDriver = new EmptyI18nDriver($i18nService);

        $this->i18nDriver->dictionary = [
            'nothing' => 'something',
            'something' => 'anything',
            '%d + %d = %d' => '%d plus %d is %d',
            'Freedom is %s' => 'Word is %s'
        ];
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['nothing', 'something', null, null, null],
            ['something', 'anything', null, null, null],
            ['anything', 'anything', null, null, null],
            ['%d + %d = %d', '%d plus %d is %d', 1, 2, 3],
            ['Freedom is %s', 'Word is %s', 'speech', null, null]
        ];
    }

    /**
     * Test the filter class.
     *
     * @dataProvider dataProvider
     */
    public function testFilter($input, $output, $param1, $param2, $param3)
    {
        $filter = new TranslateFilter($this->i18nDriver);

        if (!empty($param3)) {
            $expectedResult = sprintf($output, $param1, $param2, $param3);
            $actualResult = $filter($input, $param1, $param2, $param3);
        } elseif (!empty($param2)) {
            $expectedResult = sprintf($output, $param1, $param2);
            $actualResult = $filter($input, $param1, $param2);
        } elseif (!empty($param1)) {
            $expectedResult = sprintf($output, $param1);
            $actualResult = $filter($input, $param1);
        } else {
            $expectedResult = sprintf($output);
            $actualResult = $filter($input);
        }

        $this->assertSame($expectedResult, $actualResult);
    }
}
