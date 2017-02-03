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

use Exception;
use WebHemi\Adapter\Acl\AclAdapterInterface;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Middleware\Action;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class AclMiddleware.
 */
class AclMiddleware implements MiddlewareInterface
{
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var AclAdapterInterface */
    private $aclAdapter;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var ApplicationStorage */
    private $applicationStorage;
    /** @var ResourceStorage */
    private $resourceStorage;
    /** @var UserMetaStorage */
    private $userMetaStorage;
    /** @var array */
    private $middlewareWhiteList = [
        Action\Auth\LoginAction::class,
        Action\Auth\LogoutAction::class,
    ];

    /**
     * AclMiddleware constructor.
     *
     * @param AuthAdapterInterface     $authAdapter
     * @param AclAdapterInterface      $aclAdapter
     * @param EnvironmentManager       $environmentManager
     * @param ApplicationStorage       $applicationStorage
     * @param ResourceStorage          $resourceStorage
     * @param UserMetaStorage          $userMetaStorage
     */
    public function __construct(
        AuthAdapterInterface $authAdapter,
        AclAdapterInterface $aclAdapter,
        EnvironmentManager $environmentManager,
        ApplicationStorage $applicationStorage,
        ResourceStorage $resourceStorage,
        UserMetaStorage $userMetaStorage
    ) {
        $this->authAdapter = $authAdapter;
        $this->aclAdapter = $aclAdapter;
        $this->environmentManager = $environmentManager;
        $this->applicationStorage = $applicationStorage;
        $this->resourceStorage = $resourceStorage;
        $this->userMetaStorage = $userMetaStorage;
    }

    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     * The only hard requirement is that a middleware MUST return an instance of \Psr\Http\Message\ResponseInterface.
     * Each middleware SHOULD invoke the next middleware and pass it Request and Response objects as arguments.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @throws Exception
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        $actionMiddleware = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS);
        $identity = false;

        if (in_array($actionMiddleware, $this->middlewareWhiteList)) {
            return;
        }

        if ($this->authAdapter->hasIdentity()) {
            $identity = $this->authAdapter->getIdentity();
        }

        if ($identity instanceof UserEntity) {
            $selectedApplication = $this->environmentManager->getSelectedApplication();
            /** @var ApplicationEntity $applicationEntity */
            $applicationEntity = $this->applicationStorage->getApplicationByName($selectedApplication);
            /** @var ResourceEntity $resourceEntity */
            $resourceEntity = $this->resourceStorage->getResourceByName($actionMiddleware);

            // Check the user against the application and resource
            $hasAccess = $this->aclAdapter->isAllowed($identity, $resourceEntity, $applicationEntity);

            $request = $this->setIdentityForTemplate($request, $identity);

            if (!$hasAccess) {
                throw new Exception('Forbidden', 403);
            }
        } else {
            $appUri = rtrim($this->environmentManager->getSelectedApplicationUri(), '/');
            $response = $response->withStatus(ResponseInterface::STATUS_REDIRECT, 'Found')
                ->withHeader('Location', $appUri.'/auth/login');
        }
    }

    /**
     * Set identified user data for the templates
     *
     * @param ServerRequestInterface $request
     * @param UserEntity             $identity
     * @return ServerRequestInterface
     */
    private function setIdentityForTemplate(
        ServerRequestInterface $request,
        UserEntity $identity
    ) : ServerRequestInterface {
        // Set authenticated user for the templates
        $templateData = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, []);
        $templateData['authenticated_user'] = $identity;
        $templateData['authenticated_user_meta'] = [];
        $userMeta = $this->userMetaStorage->getUserMetaForUserId($identity->getUserId());
        /** @var UserMetaEntity $metaEntity */
        foreach ($userMeta as $metaEntity) {
            $templateData['authenticated_user_meta'][$metaEntity->getMetaKey()] = $metaEntity->getMetaData();
        }

        return $request->withAttribute(ServerRequestInterface::REQUEST_ATTR_DISPATCH_DATA, $templateData);
    }
}
