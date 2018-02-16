<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Middleware\Common;

use RuntimeException;
use Throwable;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Logger\ServiceInterface as LoggerInterface;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class FinalMiddleware.
 */
class FinalMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthInterface
     */
    private $authAdapter;
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;
    /**
     * @var LoggerInterface
     */
    private $logAdapter;

    /**
     * FinalMiddleware constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param EnvironmentInterface $environmentManager
     * @param LoggerInterface      $logAdapter
     */
    public function __construct(
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager,
        LoggerInterface $logAdapter
    ) {
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
        $this->logAdapter = $logAdapter;
    }

    /**
     * Sends out the headers and prints the response body to the output.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        // Handle errors here.
        if (!in_array($response->getStatusCode(), [ResponseInterface::STATUS_OK, ResponseInterface::STATUS_REDIRECT])) {
            $exception = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION)
                ?? new RuntimeException($response->getReasonPhrase(), $response->getStatusCode());

            // Make sure that the Request has the exception.
            $request = $request->withAttribute(ServerRequestInterface::REQUEST_ATTR_MIDDLEWARE_EXCEPTION, $exception);

            $this->logErrorResponse($exception, $request, $response);
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
            /**
             * @var UserEntity $userEntity
             */
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
     * Convert the exception into plain text instead of the fancy HTML output of the xdebug...
     *
     * @param  Throwable $exception
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
