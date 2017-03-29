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

namespace WebHemi\Middleware\Common;

use RuntimeException;
use Throwable;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Logger\ServiceInterface as LoggerInterface;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Renderer\ServiceInterface as RendererInterface;

/**
 * Class FinalMiddleware.
 */
class FinalMiddleware implements MiddlewareInterface
{
    /** @var RendererInterface */
    private $templateRenderer;
    /** @var AuthInterface */
    private $authAdapter;
    /** @var EnvironmentInterface */
    private $environmentManager;
    /** @var LoggerInterface */
    private $logAdapter;

    /**
     * FinalMiddleware constructor.
     *
     * @param RendererInterface    $templateRenderer
     * @param AuthInterface        $authAdapter
     * @param EnvironmentInterface $environmentManager
     * @param LoggerInterface      $logAdapter
     */
    public function __construct(
        RendererInterface $templateRenderer,
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager,
        LoggerInterface $logAdapter
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
        $this->logAdapter = $logAdapter;
    }

    /**
     * Sends out the headers and prints the response body to the output.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE') && headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent', 1000);
        }
        // @codeCoverageIgnoreEnd

        // Handle errors here.
        if (!in_array($response->getStatusCode(), [ResponseInterface::STATUS_OK, ResponseInterface::STATUS_REDIRECT])) {
            $exception = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION)
                ?? new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());
            $this->prepareErrorResponse($exception, $request, $response);
            $this->logErrorResponse($exception, $request, $response);
        }

        // Skip sending output when PHP Unit is running.
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            $this->sendOutput($request, $response);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Prepares error response: Body and Data
     *
     * @param Throwable              $exception
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     */
    private function prepareErrorResponse(
        Throwable $exception,
        ServerRequestInterface&$request,
        ResponseInterface&$response
    ) : void {
        $errorTemplate = 'error-'.$response->getStatusCode();

        /** @var array $data */
        $templateData = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);
        $templateData['exception'] = $exception;

        if ($request->isXmlHttpRequest()) {
            $request = $request->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, $templateData);
        } else {
            $body = $this->templateRenderer->render($errorTemplate, $templateData);
            $response = $response->withBody($body);
        }
    }

    /**
     * Logs the error.
     *
     * @param Throwable              $exception
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     */
    private function logErrorResponse(
        Throwable $exception,
        ServerRequestInterface&$request,
        ResponseInterface&$response
    ) : void {
        $identity = 'Unauthenticated user';

        if ($this->authAdapter->hasIdentity()) {
            /** @var UserEntity $userEntity */
            $userEntity = $this->authAdapter->getIdentity();
            $identity = $userEntity->getEmail();
        }

        $logData = [
            'User' => $identity,
            'IP' => $this->environmentManager->getClientIp(),
            'RequestUri' => $request->getUri()->getPath().'?'.$request->getUri()->getQuery(),
            'RequestMethod' => $request->getMethod(),
            'Error' => $response->getStatusCode().' '.$response->getReasonPhrase(),
            'Exception' => $this->getExceptionAsString($exception),
            'Parameters' => $request->getParsedBody()
        ];
        $this->logAdapter->log('error', json_encode($logData));
    }

    /**
     * Inject the Content-Length header if is not already present.
     *
     * NOTE: if there will be chunk content displayed, check if the response getSize counts the real size correctly
     *
     * @param ResponseInterface $response
     * @return void
     *
     * @codeCoverageIgnore - no putput for tests.
     */
    private function injectContentLength(ResponseInterface&$response) : void
    {
        if (!$response->hasHeader('Content-Length') && !is_null($response->getBody()->getSize())) {
            $response = $response->withHeader('Content-Length', (string) $response->getBody()->getSize());
        }
    }

    /**
     * Filter a header name to word case.
     *
     * @param string $headerName
     * @return string
     */
    private function filterHeaderName(string $headerName) : string
    {
        $filtered = str_replace('-', ' ', $headerName);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }

    /**
     * Sends the HTTP header.
     *
     * @param ResponseInterface $response
     * @return void
     *
     * @codeCoverageIgnore - vendor and core function calls
     */
    private function sendHttpHeader(ResponseInterface $response) : void
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
     * @return void
     *
     * @codeCoverageIgnore - vendor and core function calls in loop
     */
    private function sendOutputHeaders(array $headers) : void
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

    /**
     * Sends output according to the request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     *
     * @codeCoverageIgnore - no output for tests
     */
    private function sendOutput(ServerRequestInterface $request, ResponseInterface $response) : void
    {
        if ($request->isXmlHttpRequest()) {
            $templateData = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA);

            $output = json_encode($templateData);
        } else {
            $this->injectContentLength($response);
            $output = $response->getBody();
        }

        $this->sendHttpHeader($response);
        $this->sendOutputHeaders($response->getHeaders());

        echo $output;
    }

    /**
     * Convert the exception into plain text instead of the fancy HTML output of the xdebug...
     *
     * @param Throwable $exception
     * @return string
     */
    private function getExceptionAsString(Throwable $exception)
    {
        return 'Exception ('.$exception->getCode().'): "'.$exception->getMessage().'" '
            .'in '.$exception->getFile().' on line '.$exception->getLine().PHP_EOL
            .'Call stack'.PHP_EOL
            .$exception->getTraceAsString();
    }
}
