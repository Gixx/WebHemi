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
declare(strict_types=1);

namespace WebHemi\Middleware\Security;

use Exception;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserMetaEntity;
use WebHemi\Data\Entity\User\UserEntity;
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
     * @param AuthAdapterInterface     $authAdapter
     * @param EnvironmentManager       $environmentManager
     * @param UserToPolicyCoupler      $userToPolicyCoupler
     * @param UserToGroupCoupler       $userToGroupCoupler
     * @param UserGroupToPolicyCoupler $userGroupToPolicyCoupler
     * @param ApplicationStorage       $applicationStorage
     * @param ResourceStorage          $resourceStorage
     * @param UserMetaStorage          $userMetaStorage
     */
    public function __construct(
        AuthAdapterInterface $authAdapter,
        EnvironmentManager $environmentManager,
        UserToPolicyCoupler $userToPolicyCoupler,
        UserToGroupCoupler $userToGroupCoupler,
        UserGroupToPolicyCoupler $userGroupToPolicyCoupler,
        ApplicationStorage $applicationStorage,
        ResourceStorage $resourceStorage,
        UserMetaStorage $userMetaStorage
    ) {
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
        $this->userToPolicyCoupler = $userToPolicyCoupler;
        $this->userToGroupCoupler = $userToGroupCoupler;
        $this->userGroupToPolicyCoupler = $userGroupToPolicyCoupler;
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
            // First we check the group policies
            /** @var array<UserGroupEntity> $userGroups */
            $userGroups = $this->userToGroupCoupler->getEntityDependencies($identity);
            /** @var array<PolicyEntity> $userGroupPolicies */
            $userGroupPolicies = [];
            foreach ($userGroups as $userGroupEntity) {
                /** @var array<PolicyEntity> $groupPolicies */
                $groupPolicies = $this->userGroupToPolicyCoupler->getEntityDependencies($userGroupEntity);
                $userGroupPolicies = array_merge($userGroupPolicies, $groupPolicies);
            }
            $hasAccess = $this->checkPolicies($userGroupPolicies, $applicationEntity, $resourceEntity);

            // Then we check the personal policies
            /** @var array<PolicyEntity> $userPolicies */
            $userPolicies = $this->userToPolicyCoupler->getEntityDependencies($identity);
            $hasAccess = $hasAccess && $this->checkPolicies($userPolicies, $applicationEntity, $resourceEntity);

            $request = $this->setIdentityForTemplate($request, $identity);

            if (!$hasAccess) {
                $response = $response->withStatus(ResponseInterface::STATUS_FORBIDDEN, 'Forbidden');
            }
        } else {
            $appUri = rtrim($this->environmentManager->getSelectedApplicationUri(), '/');
            $response = $response->withStatus(ResponseInterface::STATUS_REDIRECT, 'Found')
                ->withHeader('Location', $appUri.'/auth/login');
        }
    }

    /**
     * Checks policies for application and resource
     *
     * @param array<PolicyEntity>      $policies
     * @param ApplicationEntity|null   $applicationEntity
     * @param ResourceEntity|null      $resourceEntity
     * @return bool
     */
    private function checkPolicies(
        array $policies,
        ?ApplicationEntity $applicationEntity = null,
        ?ResourceEntity $resourceEntity = null
    ) : bool {
        // We assume the best case: the user has access
        $hasAccess = true;

        /** @var PolicyEntity $policyEntity */
        foreach ($policies as $policyEntity) {
            $hasAccess = $hasAccess && $this->checkPolicy($policyEntity, $applicationEntity, $resourceEntity);
        }

        return $hasAccess;
    }

    /**
     * Check a concrete policy.
     *
     * @param PolicyEntity           $policyEntity
     * @param ApplicationEntity|null $applicationEntity
     * @param ResourceEntity|null    $resourceEntity
     * @return bool
     */
    private function checkPolicy(
        PolicyEntity $policyEntity,
        ?ApplicationEntity $applicationEntity = null,
        ?ResourceEntity $resourceEntity = null
    ) : bool {
        $applicationId = $applicationEntity ? $applicationEntity->getApplicationId() : null;
        $resourceId = $resourceEntity ? $resourceEntity->getResourceId() : null;
        $policyApplicationId = $policyEntity->getApplicationId();
        $policyResourceId = $policyEntity->getResourceId();

        // The user has access when:
        // - user has a policy that connected to the current application OR any application AND
        // - user has a policy that connected to the current resource OR any resource
        if (($policyApplicationId == null || $policyApplicationId == $applicationId)
            && ($policyResourceId == null || $policyResourceId == $resourceId)
        ) {
            return $policyEntity->getAllowed();
        }

        // At this point we know that the current policy doesn't belong to this application or resource, so no need
        // to block the user.
        return true;
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
