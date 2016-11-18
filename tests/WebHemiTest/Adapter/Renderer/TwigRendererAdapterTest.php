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
namespace WebHemiTest\Adapter\Renderer;

use InvalidArgumentException;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\Config;
use WebHemiTest\AssertTrait;
use WebHemiTest\Fixtures\EmptyEnvironmentManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TwigRendererAdapterTest.
 */
class TwigRendererAdapterTest extends TestCase
{
    /** @var Config */
    protected $config;
    /** @var array */
    protected $get = [];
    /** @var array */
    protected $post = [];
    /** @var array */
    protected $server;
    /** @var array */
    protected $cookie = [];
    /** @var array */
    protected $files = [];
    /** @var EmptyEnvironmentManager */
    protected $environmentManager;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../Fixtures/test_config.php';
        $this->config = new Config($config);
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $this->environmentManager = new EmptyEnvironmentManager(
            $this->config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $resultObj = new TwigRendererAdapter($this->config, $this->environmentManager);
        $this->assertInstanceOf(RendererAdapterInterface::class, $resultObj);
        $this->assertAttributeInstanceOf(\Twig_Environment::class, 'adapter', $resultObj);

        // test if the config doesn't has the selected theme
        $this->environmentManager->setSelectedTheme('someNoneExistingTheme');
        $resultObj = new TwigRendererAdapter($this->config, $this->environmentManager);
        $this->assertInstanceOf(RendererAdapterInterface::class, $resultObj);
    }

    /**
     * Test renderer.
     */
    public function testRenderer()
    {
        $adapterObj = new TwigRendererAdapter($this->config, $this->environmentManager);

        $result = $adapterObj->render('test-page');
        $resultData = json_decode($result, true);

        $this->assertInternalType('array', $resultData);
        $this->assertTrue(isset($resultData['template_resource_path']));
        $this->assertEquals(
            $this->environmentManager->getResourcePath().'/static',
            $resultData['template_resource_path']
        );
        $this->assertEquals('Hello World!', $resultData['message']);

        $result = $adapterObj->render('unit/test.twig');
        $resultDataOther = json_decode($result, true);

        $this->assertArraysAreSimilar($resultData, $resultDataOther);


        $this->setExpectedException(InvalidArgumentException::class);
        $adapterObj->render('some_non_existing_theme_map_file');
    }
}
