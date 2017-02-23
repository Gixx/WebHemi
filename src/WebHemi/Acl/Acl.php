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

namespace WebHemi\Acl;

use WebHemi\Adapter\Acl\AclAdapterInterface;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class Acl
 */
class Acl implements AclAdapterInterface
{
    /** @var UserToPolicyCoupler */
    private $userToPolicyCoupler;
    /** @var UserToGroupCoupler */
    private $userToGroupCoupler;
    /** @var UserGroupToPolicyCoupler */
    private $userGroupToPolicyCoupler;

    /**
     * Acl constructor.
     *
     * @param UserToPolicyCoupler $userToPolicyCoupler
     * @param UserToGroupCoupler $userToGroupCoupler
     * @param UserGroupToPolicyCoupler $userGroupToPolicyCoupler
     */
    public function __construct(
        UserToPolicyCoupler $userToPolicyCoupler,
        UserToGroupCoupler $userToGroupCoupler,
        UserGroupToPolicyCoupler $userGroupToPolicyCoupler
    ) {
        $this->userToPolicyCoupler = $userToPolicyCoupler;
        $this->userToGroupCoupler = $userToGroupCoupler;
        $this->userGroupToPolicyCoupler = $userGroupToPolicyCoupler;
    }

    /**
     * Checks if a User can access to a Resource in an Application
     *
     * @param UserEntity             $userEntity
     * @param ResourceEntity|null    $resourceEntity
     * @param ApplicationEntity|null $applicationEntity
     * @return bool
     */
    public function isAllowed(
        UserEntity $userEntity,
        ?ResourceEntity $resourceEntity = null,
        ?ApplicationEntity $applicationEntity = null
    ) : bool {
        // We assume the best case: the user has access
        $allowed = false;

        /** @var array<PolicyEntity> $policies */
        $policies = array_merge($this->getUserPolicies($userEntity), $this->getUserGroupPolicies($userEntity));
        foreach ($policies as $policyEntity) {
            $allowed = $allowed || $this->checkPolicy($policyEntity, $applicationEntity, $resourceEntity);
        }

        return $allowed;
    }

    /**
     * Gets the policies assigned to the user.
     *
     * @param UserEntity $userEntity
     * @return array<PolicyEntity>
     */
    private function getUserPolicies(UserEntity $userEntity) : array
    {
        /** @var array<PolicyEntity> $userPolicies */
        return $this->userToPolicyCoupler->getEntityDependencies($userEntity);
    }

    /**
     * Gets the policies assigned to the group in which the user is.
     *
     * @param UserEntity $userEntity
     * @return array<PolicyEntity>
     */
    private function getUserGroupPolicies(UserEntity $userEntity) : array
    {
        /** @var array<PolicyEntity> $userGroupPolicies */
        $userGroupPolicies = [];
        /** @var array<UserGroupEntity> $userGroups */
        $userGroups = $this->userToGroupCoupler->getEntityDependencies($userEntity);

        foreach ($userGroups as $userGroupEntity) {
            /** @var array<PolicyEntity> $groupPolicies */
            $groupPolicies = $this->userGroupToPolicyCoupler->getEntityDependencies($userGroupEntity);
            $userGroupPolicies = array_merge($userGroupPolicies, $groupPolicies);
        }

        return $userGroupPolicies;
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
        $policyApplicationId = $policyEntity->getApplicationId();
        $policyResourceId = $policyEntity->getResourceId();
        $applicationId = $applicationEntity ? $applicationEntity->getApplicationId() : null;
        $resourceId = $resourceEntity ? $resourceEntity->getResourceId() : null;

        // The user has access when:
        // - user/user's group has a policy that connected to the current application OR any application AND
        // - user/user's group has a policy that connected to the current resource OR any resource
        if ((is_null($policyApplicationId) || $policyApplicationId === $applicationId)
            && (is_null($policyResourceId) || $policyResourceId === $resourceId)
        ) {
            return $policyEntity->getAllowed();
        }

        return false;
    }
}
