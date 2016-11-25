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
namespace WebHemi\Middleware;

use RuntimeException;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;

/**
 * Class FinalMiddleware.
 */
class FinalMiddleware implements MiddlewareInterface
{
    /** @var RendererAdapterInterface */
    private $templateRenderer;

    /**
     * FinalMiddleware constructor.
     *
     * @param RendererAdapterInterface $templateRenderer
     */
    public function __construct(RendererAdapterInterface $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    /**
     * Sends out the headers and prints the response body to the output.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface $response)
    {
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE') && headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent');
        }
        // @codeCoverageIgnoreEnd

        // Handle errors here.
        if (!in_array($response->getStatusCode(), [ResponseInterface::STATUS_OK, ResponseInterface::STATUS_REDIRECT])) {
            $errorTemplate = 'error-'.$response->getStatusCode();
            $exception = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION);
            $body = $this->templateRenderer->render($errorTemplate, ['exception' => $exception]);
            $response = $response->withBody($body);
        }

        $response = $this->injectContentLength($response);

        // Skip sending output when PHP Unit is running.
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            $this->sendHttpHeader($response);
            $this->sendOutputHeaders($response->getHeaders());

            echo $response->getBody();
        }
        // @codeCoverageIgnoreEnd

        return $response;
    }

    /**
     * Inject the Content-Length header if is not already present.
     *
     * NOTE: if there will be chunk content displayed, check if the response getSize counts the real size correctly
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function injectContentLength(ResponseInterface $response)
    {
        if (!$response->hasHeader('Content-Length') && !is_null($response->getBody()->getSize())) {
            $response = $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
        }

        return $response;
    }

    /**
     * Filter a header name to word case.
     *
     * @param string $headerName
     *
     * @return string
     */
    private function filterHeaderName($headerName)
    {
        $filtered = str_replace('-', ' ', $headerName);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    /**
     * Sends the HTTP header.
     *
     * @param ResponseInterface $response
     *
     * @codeCoverageIgnore - vendor and core function calls
     */
    private function sendHttpHeader(ResponseInterface $response)
    {
        $reasonPhrase = $response->getReasonPhrase();
        header(sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reasonPhrase ? ' '.$reasonPhrase : '')
        ));
    }

    /**
     * Sends out output headers.
     *
     * @param array $headers
     *
     * @codeCoverageIgnore - vendor and core function calls in loop
     */
    private function sendOutputHeaders(array $headers)
    {
        foreach ($headers as $headerName => $values) {
            $name  = $this->filterHeaderName($headerName);
            $first = true;
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $first);
                $first = false;
            }
        }
    }
}
