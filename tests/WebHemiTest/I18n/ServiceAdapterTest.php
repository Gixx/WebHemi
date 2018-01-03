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
namespace WebHemiTest\I18n;

use PHPUnit\Framework\TestCase;
use WebHemi\I18n\ServiceAdapter\Base\ServiceAdapter as I18nAdapter;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemiTest\TestService\EmptyEnvironmentManager;

/**
 * Class ServiceAdapterTest.
 */
class ServiceAdapterTest extends TestCase
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
    /** @var string */
    protected $defaultTimezone;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->defaultTimezone = date_default_timezone_get();

        $config = require __DIR__ . '/../test_config.php';
        $this->config = new Config($config);
        $this->documentRoot = realpath(__DIR__.'/../TestDocumentRoot/');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        date_default_timezone_set($this->defaultTimezone);
    }

    /**
     * Creates a prepared Environment manager for the tests.
     *
     * @param string $feature website|admin|admin_login
     * @return EmptyEnvironmentManager
     */
    private function getEnvironment($feature)
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
        $environmentManager->setSelectedTheme('default');

        if ($feature == 'website') {
            $environmentManager->setSelectedApplication('website');
            $environmentManager->setSelectedModule('Website');
        } elseif ($feature == 'admin') {
            $environmentManager->setSelectedApplication('admin');
            $environmentManager->setSelectedModule('Admin');
        } else {
            $environmentManager->setSelectedApplication('some_app');
            $environmentManager->setSelectedModule('Website');
        }

        return $environmentManager;
    }

    /**
     * Data provider for testLocale()
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['website', 'en_GB.utf8', 'GB', 'UTF-8', 'Europe/London'],
            ['admin', 'en_US.utf8', 'US', 'UTF-8', 'America/Detroit'],
            ['some_app', 'pt_BR.utf8', 'BR', 'UTF-8', 'America/Sao_Paulo'],
        ];
    }

    /**
     * Tests getters.
     *
     * @param $application
     * @param $expectedLocale
     * @param $expectedTerritory
     * @param $expectedCodeSet
     * @param $expectedTimeZone
     *
     * @dataProvider dataProvider
     */
    public function testLocale($application, $expectedLocale, $expectedTerritory, $expectedCodeSet, $expectedTimeZone)
    {
        $configuration = $this->config;
        $environmentManager = $this->getEnvironment($application);

        $adapter = new I18nAdapter($configuration, $environmentManager);

        $this->assertSame($expectedLocale, $adapter->getLocale());
        $this->assertSame($expectedTerritory, $adapter->getTerritory());
        $this->assertSame($expectedCodeSet, $adapter->getCodeSet());
        $this->assertSame($expectedTimeZone, $adapter->getTimeZone());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1000);

        $adapter->setLocale('not_a_locale.UTF-8');
    }
}
