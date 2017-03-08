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

namespace WebHemi\Middleware\Security;

use WebHemi\Auth\ServiceInterface as AuthServiceInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Logger\ServiceInterface as LoggerInterface;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class AccessLogMiddleware.
 */
class AccessLogMiddleware implements MiddlewareInterface
{
    /** @var LoggerInterface */
    private $logger;
    /** @var AuthServiceInterface */
    private $authAdapter;
    /** @var EnvironmentInterface */
    private $environmentManager;

    /**
     * AccessLogMiddleware constructor.
     *
     * @param LoggerInterface      $logger
     * @param AuthServiceInterface $authAdapter
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(
        LoggerInterface $logger,
        AuthServiceInterface $authAdapter,
        EnvironmentInterface $environmentManager
    ) {
        $this->logger = $logger;
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
    }

    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     * The only hard requirement is that a middleware MUST return an instance of \Psr\Http\Message\ResponseInterface.
     * Each middleware SHOULD invoke the next middleware and pass it Request and Response objects as arguments.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return void
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface&$response) : void
    {
        $identity = 'Unauthenticated user';
        $requestAttributes = $request->getAttributes();
        $actionMiddleware = isset($requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS])
            ? $requestAttributes[ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS]
            : 'N/A';

        if ($this->authAdapter->hasIdentity()) {
            /** @var UserEntity $userEntity */
            $userEntity = $this->authAdapter->getIdentity();
            $identity = $userEntity->getEmail();
        }

        $data = [
            'User' => $identity,
            'IP' => $this->environmentManager->getClientIp(),
            'RequestUri' => $request->getUri()->getPath().'?'.$request->getUri()->getQuery(),
            'RequestMethod' => $request->getMethod(),
            'Action' => $actionMiddleware,
            'Parameters' => $request->getParsedBody()
        ];

        $response = $response->withStatus(ResponseInterface::STATUS_PROCESSING);

        $this->logger->log('info', json_encode($data));
    }
}
