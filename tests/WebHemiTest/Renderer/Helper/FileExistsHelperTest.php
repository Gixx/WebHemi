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
namespace WebHemiTest\Renderer\Helper;

use PHPUnit\Framework\TestCase;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemi\Renderer\Helper\FileExistsHelper;
use WebHemiTest\TestService\EmptyEnvironmentManager;

/**
 * Class FileExistsHelperTest.
 */
class FileExistsHelperTest extends TestCase
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
     * Tests the helper
     */
    public function testHelper()
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

        $environmentManager->setDocumentRoot($this->documentRoot);

        $helper = new FileExistsHelper($environmentManager);

        $this->assertTrue($helper('/testfile.txt'));
        $this->assertFalse($helper('some/non/existing/file.txt'));
    }
}
