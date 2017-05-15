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
declare(strict_types = 1);

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Uri;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Http\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var EnvironmentInterface */
    private $environmentManager;
    /** @var ServerRequest */
    private $request;
    /** @var Response */
    private $response;
    /** @var array */
    private $get;
    /** @var array */
    private $post;
    /** @var array */
    private $server;
    /** @var array */
    private $cookie;
    /** @var array */
    private $files;

    /**
     * ServiceAdapter constructor.
     *
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(EnvironmentInterface $environmentManager)
    {
        $this->environmentManager = $environmentManager;
        $this->get = $environmentManager->getEnvironmentData('GET');
        $this->post = $environmentManager->getEnvironmentData('POST');
        $this->server = $environmentManager->getEnvironmentData('SERVER');
        $this->cookie = $environmentManager->getEnvironmentData('COOKIE');
        $this->files = $environmentManager->getEnvironmentData('FILES');

        $this->initialize();
    }

    /**
     * Initialize adapter: create the ServerRequest and Response instances.
     *
     * @return void
     */
    private function initialize() : void
    {
        $uri = new Uri('');
        $uri = $uri->withScheme($this->getScheme())
            ->withHost($this->getHost())
            ->withPort($this->getServerData('SERVER_PORT', '80'))
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
        $this->response = new Response(Response::STATUS_PROCESSING);
    }

    /**
     * Gets the specific server data, or a default value if not present.
     *
     * @param string $keyName
     * @param string $defaultValue
     * @return string
     */
    private function getServerData(string $keyName, string $defaultValue = '') : string
    {
        if (isset($this->server[$keyName])) {
            $defaultValue = $this->server[$keyName];
        }

        return (string) $defaultValue;
    }

    /**
     * Gets server scheme.
     *
     * @return string
     */
    private function getScheme() : string
    {
        return $this->environmentManager->isSecuredApplication() ? 'https' : 'http';
    }

    /**
     * Gets the server host name.
     *
     * @return string
     */
    private function getHost() : string
    {
        $host = $this->getServerData('HTTP_HOST');
        $name = $this->getServerData('SERVER_NAME');

        if (empty($host) && !empty($name)) {
            $host = $name;
        }

        return (string) preg_replace('/:[0-9]+$/', '', $host);
    }

    /**
     * Gets the server request uri.
     *
     * @return string
     */
    private function getRequestUri() : string
    {
        $requestUri = $this->environmentManager->getRequestUri();

        return (string) current(explode('?', $requestUri));
    }

    /**
     * Gets the server protocol.
     *
     * @return string
     */
    private function getProtocol() : string
    {
        $protocol = '1.1';
        $serverProtocol = $this->getServerData('SERVER_PROTOCOL');

        if (!empty($serverProtocol)) {
            $protocol = str_replace('HTTP/', '', $serverProtocol);
        }

        return (string) $protocol;
    }

    /**
     * Returns the HTTP request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest() : ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Returns the response being sent.
     *
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }
}
