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
namespace WebHemiTest\Renderer\Filter;

use PHPUnit\Framework\TestCase;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\DependencyInjection\ServiceAdapter\Symfony\ServiceAdapter as DependencyInjectionAdapter;
use WebHemi\Environment;
use WebHemi\Renderer;
use WebHemi\Renderer\Filter\TagParserFilter;
use WebHemi\Renderer\Filter\Tags\Url;
use WebHemiTest\TestService\EmptyEnvironmentManager;

/**
 * Class TagparserFilterTest.
 */
class TagparserFilterTest extends TestCase
{
    /** @var EmptyEnvironmentManager */
    protected $environmentManager;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $configData = [
            'applications' => [],
            'dependencies' => [
                'Global' => [
                    Environment\ServiceInterface::class => [
                        'class' => EmptyEnvironmentManager::class,
                    ],
                    Renderer\Filter\Tags\Url::class => [
                        'arguments' => [
                            Environment\ServiceInterface::class
                        ],
                        'shared'    => true,
                    ],
                ]
            ]
        ];

        $config = new Config($configData);
        $server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $this->environmentManager = new EmptyEnvironmentManager($config, [], [], $server, [], []);
    }

    /**
     * Test the filter class.
     */
    public function testFilter()
    {
        $this->environmentManager->setRequestUri('/some/folders/for/test.html');
        $tagFilter = new Url($this->environmentManager);
        $filter = new TagparserFilter($tagFilter);

        $url = '/some/folders/for/test.html';
        $input = 'Lorem ipsum dolor #sit# amet consectetur adipiscing #Url#. #Sed# ut auctor mauris.';

        $expectedResult = 'Lorem ipsum dolor #sit# amet consectetur adipiscing '.$url.'. #Sed# ut auctor mauris.';
        $actualResult = $filter($input);

        $this->assertSame($expectedResult, $actualResult);
    }
}
