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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
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
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        if (headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent');
        }

        $content = $response->getBody();

        // Handle errors here.
        if ($response->getStatusCode() !== 200) {
            $errorTemplate = 'error-'.$response->getStatusCode();
            $error = $request->getAttribute('exception');
            $content = $this->templateRenderer->render($errorTemplate, ['exception' => $error]);
        }

        $response = $this->injectContentLength($response);

        $reasonPhrase = $response->getReasonPhrase();
        header(sprintf(
            'HTTP/%s %d%s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reasonPhrase ? ' '.$reasonPhrase : '')
        ));

        foreach ($response->getHeaders() as $headerName => $values) {
            $name  = $this->filterHeaderName($headerName);
            $first = true;
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $first);
                $first = false;
            }
        }

        echo $content;
        return $response;
    }

    /**
     * Inject the Content-Length header if is not already present.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function injectContentLength(ResponseInterface $response)
    {
        if (!$response->hasHeader('Content-Length')&& !is_null($response->getBody()->getSize())) {
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
}
