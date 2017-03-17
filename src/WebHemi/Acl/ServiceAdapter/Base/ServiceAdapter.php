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

namespace WebHemi\Acl\ServiceAdapter\Base;

use WebHemi\Acl\ServiceAdapter\AbstractServiceAdapter;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserGroupEntity;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractServiceAdapter
{
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
        ? ResourceEntity $resourceEntity = null,
        ? ApplicationEntity $applicationEntity = null
    ) : bool {
        // We assume the best case: the user has access
        $allowed = false;

        /** @var PolicyEntity[] $policies */
        $policies = array_merge($this->getUserPolicies($userEntity), $this->getUserGroupPolicies($userEntity));

        foreach ($policies as $policyEntity) {
            if ($this->isPolicyAllowed($policyEntity, $resourceEntity, $applicationEntity)) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }

    /**
     * Gets the policies assigned to the user.
     *
     * @param UserEntity $userEntity
     * @return PolicyEntity[]
     */
    private function getUserPolicies(UserEntity $userEntity) : array
    {
        /** @var PolicyEntity[] $userPolicies */
        return $this->userToPolicyCoupler->getEntityDependencies($userEntity);
    }

    /**
     * Gets the policies assigned to the group in which the user is.
     *
     * @param UserEntity $userEntity
     * @return PolicyEntity[]
     */
    private function getUserGroupPolicies(UserEntity $userEntity) : array
    {
        /** @var PolicyEntity[] $userGroupPolicies */
        $userGroupPolicies = [];
        /** @var UserGroupEntity[] $userGroups */
        $userGroups = $this->userToGroupCoupler->getEntityDependencies($userEntity);

        foreach ($userGroups as $userGroupEntity) {
            /** @var PolicyEntity[] $groupPolicies */
            $groupPolicies = $this->userGroupToPolicyCoupler->getEntityDependencies($userGroupEntity);
            $userGroupPolicies = array_merge($userGroupPolicies, $groupPolicies);
        }

        return $userGroupPolicies;
    }
}
