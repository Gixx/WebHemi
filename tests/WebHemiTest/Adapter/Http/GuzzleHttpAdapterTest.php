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
namespace WebHemiTest\Adapter\Http;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use WebHemi\Adapter\Http\HttpAdapterInterface;
use WebHemi\Adapter\Http\GuzzleHttp\GuzzleHttpAdapter;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GuzzleHttpAdapterTest.
 */
class GuzzleHttpAdapterTest extends TestCase
{
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

    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->server = [
            'HTTP_HOST'    => 'unittest.dev',
            'SERVER_NAME'  => 'unittest.dev',
            'REQUEST_URI'  => '/',
            'QUERY_STRING' => '',
        ];
    }

    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $this->assertInstanceOf(HttpAdapterInterface::class, $testObj);
        $this->assertInstanceOf(ServerRequest::class, $testObj->getRequest());
        $this->assertInstanceOf(Response::class, $testObj->getResponse());
    }

    /**
     * Tests if can determine the right scheme.
     */
    public function testGetScheme()
    {
        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $result = $this->invokePrivateMethod($testObj, 'getScheme');
        $this->assertEquals('http', $result);

        $server = $this->server;
        $server['HTTPS'] = 'on';

        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $server,
            $this->cookie,
            $this->files
        );

        $result = $this->invokePrivateMethod($testObj, 'getScheme');
        $this->assertEquals('https', $result);
    }

    /**
     * Tests if can determine the right host.
     */
    public function testGetHost()
    {
        $server = $this->server;
        $server['HTTP_HOST'] = '';
        $server['SERVER_NAME']  = 'unittest.dev:8080';

        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $server,
            $this->cookie,
            $this->files
        );

        $result = $this->invokePrivateMethod($testObj, 'getHost');
        $this->assertEquals('unittest.dev', $result);
    }

    /**
     * Tests if can determine the right protocol.
     */
    public function testGetProtocol()
    {
        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $this->server,
            $this->cookie,
            $this->files
        );

        $result = $this->invokePrivateMethod($testObj, 'getProtocol');
        $this->assertEquals('1.1', $result);

        $server = $this->server;
        $server['SERVER_PROTOCOL'] = 'HTTP/1.0';

        $testObj = new GuzzleHttpAdapter(
            $this->get,
            $this->post,
            $server,
            $this->cookie,
            $this->files
        );

        $result = $this->invokePrivateMethod($testObj, 'getProtocol');
        $this->assertEquals('1.0', $result);
    }
}
