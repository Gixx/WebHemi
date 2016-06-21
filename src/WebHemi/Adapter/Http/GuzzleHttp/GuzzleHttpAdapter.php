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
namespace WebHemi\Adapter\Http\GuzzleHttp;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use WebHemi\Adapter\Http\HttpAdapterInterface;

/**
 * Class GuzzleHttpAdapter.
 */
class GuzzleHttpAdapter implements HttpAdapterInterface
{
    /** @var ServerRequest */
    private $request;
    /** @var ResponseInterface */
    private $response;

    /** @var array */
    private $server;
    /** @var array */
    private $get;
    /** @var array */
    private $post;
    /** @var array */
    private $cookie;
    /** @var array */
    private $files;

    /**
     * GuzzleHTTPAdapter constructor.
     *
     * @param array $server
     * @param array $get
     * @param array $post
     * @param array $cookie
     * @param array $files
     */
    public function __construct(array $server, array $get, array $post, array $cookie, array $files)
    {
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->cookie = $cookie;
        $this->files = $files;

        $this->initialize();
    }

    /**
     * Initialize adapter: create the ServerRequest and Response instances.
     */
    private function initialize()
    {
        $uri = new Uri('');
        $uri = $uri->withScheme($this->getScheme())
            ->withHost($this->getHost())
            ->withPort($this->getServerData('SERVER_PORT', 80))
            ->withPath($this->getRequestUri())
            ->withQuery($this->getServerData('QUERY_STRING', ''));

        $serverRequest = new ServerRequest(
            $this->getServerData('REQUEST_METHOD', 'GET'),
            $uri,
            [],
            new LazyOpenStream('php://input', 'r+'),
            $this->getProtocol(),
            $this->server
        );
        $this->request = $serverRequest
            ->withCookieParams($this->cookie)
            ->withQueryParams($this->get)
            ->withParsedBody($this->post);
        // Create a Response with HTTP 102 - Processing.
        $this->response = new Response(102);
    }

    /**
     * Gets the specific server data, or a default value if not present.
     *
     * @param string $keyName
     * @param mixed  $defaultValue
     *
     * @return mixed|null
     */
    private function getServerData($keyName, $defaultValue = '')
    {
        if (isset($this->server[$keyName])) {
            $defaultValue = $this->server[$keyName];
        }

        return $defaultValue;
    }

    /**
     * Gets server scheme.
     *
     * @return string
     */
    private function getScheme()
    {
        $scheme = 'http';
        $https = $this->getServerData('HTTPS', 'off');

        if ($https == 'on') {
            $scheme = 'https';
        }

        return $scheme;
    }

    /**
     * Gets the server host name.
     *
     * @return string
     */
    private function getHost()
    {
        $host = $this->getServerData('HTTP_HOST');
        $name = $this->getServerData('SERVER_NAME');

        if (empty($host) && !empty($name)) {
            $host = $name;
        }

        return preg_replace('/:[0-9]+$/', '', $host);
    }

    /**
     * Gets the server request uri.
     *
     * @return string
     */
    private function getRequestUri()
    {
        $requestUri = $this->getServerData('REQUEST_URI', '/');

        return current(explode('?', $requestUri));
    }

    /**
     * Gets the server protocol.
     *
     * @return string
     */
    private function getProtocol()
    {
        $protocol = '1.1';
        $serverProtocol = $this->getServerData('SERVER_PROTOCOL');

        if (!empty($serverProtocol)) {
            $protocol = str_replace('HTTP/', '', $serverProtocol);
        }

        return $protocol;
    }

    /**
     * Returns the HTTP request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the response being sent.
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}
