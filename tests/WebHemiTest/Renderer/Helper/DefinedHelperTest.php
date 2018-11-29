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
namespace WebHemiTest\Renderer\Helper;

use PHPUnit\Framework\TestCase;
use WebHemi\Renderer\HelperInterface as RendererHelperInterface;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemi\Renderer\Helper\DefinedHelper;
use WebHemiTest\TestService\EmptyEnvironmentManager;

/**
 * Class DefinedHelperTest.
 */
class DefinedHelperTest extends TestCase
{
    /** @var ConfigInterface */
    private $config;
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
    /** @var string */
    protected $documentRoot;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../test_config.php';
        $this->config = new Config($config);
        $this->documentRoot = realpath(__DIR__ . '/../../TestDocumentRoot/');
    }

    /**
     * Creates a prepared Environment manager for the tests.
     *
     * @param string $feature website|admin|admin_login
     * @param string $theme   default|test_theme|test_theme_no_admin|test_theme_no_admin_login|test_theme_no_website
     * @return EmptyEnvironmentManager
     */
    private function getEnvironment($feature, $theme)
    {
        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];

        $environmentManager = new EmptyEnvironmentManager(
            $this->config,
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $requestUri = $feature == 'admin_login' ? '/auth/login' : '/';

        $environmentManager->setDocumentRoot($this->documentRoot);
        $environmentManager->setRequestUri($requestUri);
        $environmentManager->setSelectedTheme($theme);

        if ($feature == 'website') {
            $environmentManager->setSelectedApplication('website');
            $environmentManager->setSelectedModule('Website');
        } else {
            $environmentManager->setSelectedApplication('admin');
            $environmentManager->setSelectedModule('Admin');
        }

        return $environmentManager;
    }

    /**
     * Tests constructor to check theme detection.
     */
    public function testConstructorThemePaths()
    {
        $docroot = $this->documentRoot;

        $environmentManager = $this->getEnvironment('website', 'default');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertInstanceOf(RendererHelperInterface::class, $helper);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'defaultTemplateViewPath', $helper);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);

        $environmentManager = $this->getEnvironment('website', 'test_theme');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/vendor_themes/test_theme/view', 'templateViewPath', $helper);

        $environmentManager = $this->getEnvironment('website', 'fake_theme');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);
    }

    /**
     * Tests constructor to check feature detection.
     */
    public function testConstructorThemeFeatures()
    {
        $docroot = $this->documentRoot;

        // Website
        $environmentManager = $this->getEnvironment('website', 'test_theme');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/vendor_themes/test_theme/view', 'templateViewPath', $helper);

        $environmentManager = $this->getEnvironment('website', 'test_theme_no_website');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);

        // Admin
        $environmentManager = $this->getEnvironment('admin', 'test_theme');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/vendor_themes/test_theme/view', 'templateViewPath', $helper);

        $environmentManager = $this->getEnvironment('admin', 'test_theme_no_admin_login');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals(
            $docroot.'/resources/vendor_themes/test_theme_no_admin_login/view',
            'templateViewPath',
            $helper
        );

        $environmentManager = $this->getEnvironment('admin', 'test_theme_no_website');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals(
            $docroot.'/resources/vendor_themes/test_theme_no_website/view',
            'templateViewPath',
            $helper
        );

        $environmentManager = $this->getEnvironment('admin', 'test_theme_no_admin');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);

        // Admin login
        $environmentManager = $this->getEnvironment('admin_login', 'test_theme_no_admin');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals(
            $docroot.'/resources/vendor_themes/test_theme_no_admin/view',
            'templateViewPath',
            $helper
        );

        $environmentManager = $this->getEnvironment('admin_login', 'test_theme_no_admin_login');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);
    }

    /**
     * Tests the helper invoke
     */
    public function testHelperForThemeWithMissingFile()
    {
        $docroot = $this->documentRoot;

        $environmentManager = $this->getEnvironment('website', 'default');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'defaultTemplateViewPath', $helper);
        $this->assertAttributeEquals($docroot.'/resources/default_theme/view', 'templateViewPath', $helper);

        $this->assertTrue($helper('@WebHemi/unit/test.twig'));
        $this->assertTrue($helper('@WebHemi/unit/default_only.twig'));

        // In this case the @Theme should be identical to @WebHemi
        $this->assertTrue($helper('@Theme/unit/test.twig'));
        $this->assertTrue($helper('@Theme/unit/default_only.twig'));

        $environmentManager = $this->getEnvironment('website', 'test_theme');
        $helper = new DefinedHelper($this->config, $environmentManager);
        $this->assertAttributeEquals($docroot.'/resources/vendor_themes/test_theme/view', 'templateViewPath', $helper);

        $this->assertTrue($helper('@WebHemi/unit/test.twig'));
        $this->assertTrue($helper('@WebHemi/unit/default_only.twig'));

        $this->assertTrue($helper('@Theme/unit/test.twig'));
        $this->assertFalse($helper('@Theme/unit/default_only.twig'));
    }
}
