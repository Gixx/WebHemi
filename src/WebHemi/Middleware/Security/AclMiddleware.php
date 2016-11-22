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

namespace WebHemi\Middleware\Security;

use Exception;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Auth\Result;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Middleware\MiddlewareInterface;
use WebHemi\Middleware\Action;

/**
 * Class AclMiddleware.
 */
class AclMiddleware implements MiddlewareInterface
{
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var UserToPolicyCoupler */
    private $userToPolicyCoupler;
    /** @var UserToGroupCoupler */
    private $userToGroupCoupler;
    /** @var UserGroupToPolicyCoupler */
    private $userGroupToPolicyCoupler;
    private $applicationStorage;
    private $resourceStorage;
    /** @var array */
    private $middlewareWhiteList = [
        Action\Auth\LoginAction::class,
        Action\Auth\LogoutAction::class,
    ];

    /**
     * AclMiddleware constructor.
     * @param EnvironmentManager       $environmentManager
     * @param AuthAdapterInterface     $authAdapter
     * @param UserToPolicyCoupler      $userToPolicyCoupler
     * @param UserToGroupCoupler       $userToGroupCoupler
     * @param UserGroupToPolicyCoupler $userGroupToPolicyCoupler
     * @param ApplicationStorage       $applicationStorage
     * @param ResourceStorage          $resourceStorage
     */
    public function __construct(
        EnvironmentManager $environmentManager,
        AuthAdapterInterface $authAdapter,
        UserToPolicyCoupler $userToPolicyCoupler,
        UserToGroupCoupler $userToGroupCoupler,
        UserGroupToPolicyCoupler $userGroupToPolicyCoupler,
        ApplicationStorage $applicationStorage,
        ResourceStorage $resourceStorage
    ) {
        $this->environmentManager = $environmentManager;
        $this->authAdapter = $authAdapter;
        $this->userToPolicyCoupler = $userToPolicyCoupler;
        $this->userToGroupCoupler = $userToGroupCoupler;
        $this->userGroupToPolicyCoupler = $userGroupToPolicyCoupler;
        $this->applicationStorage = $applicationStorage;
        $this->resourceStorage = $resourceStorage;
    }

    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     * The only hard requirement is that a middleware MUST return an instance of \Psr\Http\Message\ResponseInterface.
     * Each middleware SHOULD invoke the next middleware and pass it Request and Response objects as arguments.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @throws Exception
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        $actionMiddleware = $request->getAttribute(ServerRequestInterface::REQUEST_ATTR_RESOLVED_ACTION_CLASS);
        $identity = false;

        if (in_array($actionMiddleware, $this->middlewareWhiteList)) {
            return $response;
        }

        if (!$this->authAdapter->hasIdentity()) {
            /** @var Result $result */
            $result = $this->authAdapter->authenticate();

            if ($result->isValid()) {
                $identity = $result->getIdentity();
                $this->authAdapter->setIdentity($identity);
            }
        } else {
            $identity = $this->authAdapter->getIdentity();
        }

        if ($identity instanceof UserEntity) {
            /** @var array<UserGroupEntity> $userGroups */
            $userGroups = $this->userToGroupCoupler->getEntityDependencies($identity);
            /** @var array<PolicyEntity> $policies */
            $policies = $this->userToPolicyCoupler->getEntityDependencies($identity);

            foreach ($userGroups as $userGroupEntity) {
                /** @var array<PolicyEntity> $userGroupPolicies */
                $userGroupPolicies = $this->userGroupToPolicyCoupler->getEntityDependencies($userGroupEntity);
                $policies = array_merge($userGroupPolicies, $policies);
            }

            $selectedApplication = $this->environmentManager->getSelectedApplication();
            /** @var ApplicationEntity $applicationEntity */
            $applicationEntity = $this->applicationStorage->getApplicationByName($selectedApplication);
            /** @var ResourceEntity $resourceEntity */
            $resourceEntity = $this->resourceStorage->getResourceByName($actionMiddleware);

            // We assume the worst case: no access
            $hasAccess = false;

            /** @var PolicyEntity $policyEntity */
            foreach ($policies as $policyEntity) {
                $policyApplication = $policyEntity->getApplicationId();
                $policyResource = $policyEntity->getResourceId();

                // The user has access when:
                // - user has a policy which allows access AND
                // - user has a policy that connected to the current application OR any application AND
                // - user has a policy that connected to the current resource OR any resource
                if ($policyEntity->getAllowed() &&
                    ($policyApplication == null || $policyApplication == $applicationEntity->getApplicationId()) &&
                    ($policyResource == null || $policyResource == $resourceEntity->getResourceId())
                ) {
                    $hasAccess = true;
                    break;
                }
            }

            if (!$hasAccess) {
                $response = $response->withStatus(403, 'Forbidden');
            }
        } else {
            $response = $response->withStatus(401, 'Unauthorized');
        }

        return $response;
    }
}
