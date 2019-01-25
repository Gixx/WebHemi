<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Acl\ServiceAdapter\Base;

use WebHemi\Acl\ServiceAdapter\AbstractServiceAdapter;
use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\PolicyEntity;
use WebHemi\Data\Entity\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Data\Entity\UserGroupEntity;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends AbstractServiceAdapter
{
    /**
     * Checks if a User can access to a Resource in an Application
     *
     * @param  UserEntity             $userEntity
     * @param  ResourceEntity|null    $resourceEntity
     * @param  ApplicationEntity|null $applicationEntity
     * @return bool
     */
    public function isAllowed(
        UserEntity $userEntity,
        ? ResourceEntity $resourceEntity = null,
        ? ApplicationEntity $applicationEntity = null
    ) : bool {
        // By default we block everything.
        $allowed = false;

        $policies = $this->getUserPolicies($userEntity);
        $policies->merge($this->getUserGroupPolicies($userEntity));

        /** @var PolicyEntity $policyEntity */
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
     * @param  UserEntity $userEntity
     * @return EntitySet
     */
    private function getUserPolicies(UserEntity $userEntity) : EntitySet
    {
        return $this->policyStorage->getPolicyListByUser((int) $userEntity->getUserId());
    }

    /**
     * Gets the policies assigned to the group in which the user is.
     *
     * @param  UserEntity $userEntity
     * @return EntitySet
     */
    private function getUserGroupPolicies(UserEntity $userEntity) : EntitySet
    {
        $userGroups = $this->userStorage->getUserGroupListByUser((int) $userEntity->getUserId());
        $groupPolicies = $this->userStorage->createEntitySet();

        /** @var UserGroupEntity $userGroupEntity */
        foreach ($userGroups as $userGroupEntity) {
            $policyList = $this->policyStorage->getPolicyListByUserGroup((int) $userGroupEntity->getUserGroupId());

            if (!empty($policyList)) {
                $groupPolicies->merge($policyList);
            }
        }
        return $groupPolicies;
    }
}
