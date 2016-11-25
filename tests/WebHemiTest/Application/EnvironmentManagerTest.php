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
namespace WebHemiTest\Application;

use InvalidArgumentException;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\Config;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class EnvironmentManagerTest.
 */
class EnvironmentManagerTest extends TestCase
{
    /** @var array */
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

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->config = [
            'applications' => [
                'website' => [
                    'module' => 'Website',
                    'type'   => 'domain',
                    'path'   => 'www'
                ],
                'admin' => [
                    'module' => 'Admin',
                    'type'   => 'directory',
                    'path'   => 'admin',
                ]
            ],
            'themes' => [
                'default' => [],
                'test_theme' => []
            ]
        ];
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'HTTP_REFERER' => 'http://foo.org?uri='.urlencode('https://www.youtube.com/'),
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];
    }

    /**
     * Returns the config in the correct order.
     *
     * @return array
     */
    private function getOrderedConfig()
    {
        // It is important that the custom application should be checked first, then the 'admin', and the 'website' last
        $this->config['applications'] = array_reverse($this->config['applications']);

        return $this->config;
    }

    /**
     * Tests constructor with basic data.
     */
    public function testConstructor()
    {
        $config = new Config($this->config);

        $testObj = new EnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $this->assertInstanceOf(EnvironmentManager::class, $testObj);
        $this->assertEquals(EnvironmentManager::DEFAULT_APPLICATION, $testObj->getSelectedApplication());
        $this->assertEquals(EnvironmentManager::DEFAULT_APPLICATION_URI, $testObj->getSelectedApplicationUri());
        $this->assertEquals(EnvironmentManager::DEFAULT_MODULE, $testObj->getSelectedModule());
        $this->assertEquals(EnvironmentManager::DEFAULT_THEME, $testObj->getSelectedTheme());
        $this->assertEquals(EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH, $testObj->getResourcePath());
        $this->assertEquals(realpath(__DIR__.'/../../../'), $testObj->getDocumentRoot());

        $actualServerData = $testObj->getEnvironmentData('SERVER');
        $expectedServerData = $this->server;
        $expectedServerData['HTTP_REFERER'] = urldecode($expectedServerData['HTTP_REFERER']);

        $this->assertArraysAreSimilar($actualServerData, $expectedServerData);

        $this->setExpectedException(InvalidArgumentException::class);
        $testObj->getEnvironmentData('WEBSERVER');
    }

    /**
     * Tests directory-based application.
     */
    public function testDirectoryApplicationSettings()
    {
        $this->config['applications']['TestApplication'] = [
            'type' => 'directory',
            'path' => 'test_app',
        ];
        $this->server['REQUEST_URI'] = '/test_app/some_page';

        $config = new Config($this->getOrderedConfig());

        $testObj = new EnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $this->assertInstanceOf(EnvironmentManager::class, $testObj);
        $this->assertEquals('TestApplication', $testObj->getSelectedApplication());
        $this->assertEquals('/test_app', $testObj->getSelectedApplicationUri());
        $this->assertEquals('/test_app/some_page', $testObj->getRequestUri());
    }

    /**
     * Tests domain-based application.
     */
    public function testDomainApplicationSettings()
    {
        $this->config['applications']['TestApplication'] = [
            'type' => 'domain',
            'path' => 'test.app',
        ];
        $this->server['HTTP_HOST'] = 'test.app.unittest.dev';
        $this->server['SERVER_NAME'] = 'test.app.unittest.dev';
        $this->server['REQUEST_URI'] = '/test_app/some_page';

        $config = new Config($this->getOrderedConfig());

        $testObj = new EnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $this->assertInstanceOf(EnvironmentManager::class, $testObj);
        $this->assertEquals('TestApplication', $testObj->getSelectedApplication());
        $this->assertEquals('/', $testObj->getSelectedApplicationUri());
        $this->assertEquals('test.app.unittest.dev', $testObj->getApplicationDomain());
        $this->assertFalse($testObj->isSecuredApplication());
    }

    /**
     * Tests vendor theme resource path.
     */
    public function testThemePathSettings()
    {
        $this->config['applications']['TestApplication'] = [
            'type' => 'domain',
            'path' => 'test.app',
            'theme' => 'test_theme'

        ];
        $this->server['HTTP_HOST'] = 'test.app.unittest.dev';
        $this->server['SERVER_NAME'] = 'test.app.unittest.dev';
        $this->server['REQUEST_URI'] = '/test_app/some_page';

        $config = new Config($this->getOrderedConfig());

        $testObj = new EnvironmentManager(
            $config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $this->assertInstanceOf(EnvironmentManager::class, $testObj);
        $this->assertEquals('TestApplication', $testObj->getSelectedApplication());
        $this->assertEquals('/', $testObj->getSelectedApplicationUri());
        $this->assertEquals('/resources/vendor_themes/test_theme', $testObj->getResourcePath());
    }
}
