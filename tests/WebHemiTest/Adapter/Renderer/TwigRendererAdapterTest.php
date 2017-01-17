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
     * Test renderer for website.
     */
    public function testRendererForWebsite()
    {
        // Website pages are supported by the test_theme
        $this->environmentManager->setSelectedApplication('website')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Website')
            ->setSelectedTheme('test_theme')
            ->setRequestUri('/some/page');

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

        // Website pages are supported by the test_theme_no_website
        $this->environmentManager->setSelectedApplication('website')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Website')
            ->setSelectedTheme('test_theme_no_website')
            ->setRequestUri('/some/page');

        $adapterObj = new TwigRendererAdapter($this->config, $this->environmentManager);

        $result = $adapterObj->render('test-page');
        $resultData = json_decode($result, true);

        $this->assertInternalType('array', $resultData);
        $this->assertTrue(isset($resultData['template_resource_path']));
        $this->assertEquals(
            '/resources/default_theme/static',
            $resultData['template_resource_path']
        );


        $this->expectException(InvalidArgumentException::class);
        $adapterObj->render('some_non_existing_theme_map_file');
    }

    /**
     * Test renderer for admin login.
     */
    public function testRendererForAdminLogin()
    {
        // Admin login is supported by the test_theme
        $this->environmentManager->setSelectedApplication('admin')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Admin')
            ->setSelectedTheme('test_theme')
            ->setRequestUri('/auth/login');

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

        // Admin login is NOT supported by the test_theme_no_admin
        $this->environmentManager->setSelectedApplication('admin')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Admin')
            ->setSelectedTheme('test_theme_no_admin')
            ->setRequestUri('/auth/login');

        $adapterObj = new TwigRendererAdapter($this->config, $this->environmentManager);

        $result = $adapterObj->render('test-page');
        $resultData = json_decode($result, true);

        $this->assertInternalType('array', $resultData);
        $this->assertTrue(isset($resultData['template_resource_path']));
        $this->assertEquals(
            '/resources/default_theme/static',
            $resultData['template_resource_path']
        );

        $this->expectException(InvalidArgumentException::class);
        $adapterObj->render('some_non_existing_theme_map_file');
    }

    /**
     * Test renderer for admin pages.
     */
    public function testRendererForAdminPage()
    {
        // Admin pages are supported by the test_theme
        $this->environmentManager->setSelectedApplication('admin')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Admin')
            ->setSelectedTheme('test_theme')
            ->setRequestUri('/some/page');

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

        // Admin pages are NOT supported by the test_theme_no_admin
        $this->environmentManager->setSelectedApplication('admin')
            ->setSelectedApplicationUri('/')
            ->setSelectedModule('Admin')
            ->setSelectedTheme('test_theme_no_admin')
            ->setRequestUri('/some/page');

        $adapterObj = new TwigRendererAdapter($this->config, $this->environmentManager);

        $result = $adapterObj->render('test-page');
        $resultData = json_decode($result, true);

        $this->assertInternalType('array', $resultData);
        $this->assertTrue(isset($resultData['template_resource_path']));
        $this->assertEquals(
            '/resources/default_theme/static',
            $resultData['template_resource_path']
        );

        $this->expectException(InvalidArgumentException::class);
        $adapterObj->render('some_non_existing_theme_map_file');
    }
}
