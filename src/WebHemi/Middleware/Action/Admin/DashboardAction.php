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

namespace WebHemi\Middleware\Action\Admin;

use WebHemi\Data\Entity\EntitySet;
use WebHemi\Data\Entity\UserGroupEntity;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class DashboardAction.
 */
class DashboardAction extends AbstractMiddlewareAction
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
     * DashboardAction constructor.
     *
     * @param AuthInterface        $authAdapter
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(
        AuthInterface $authAdapter,
        EnvironmentInterface $environmentManager
    ) {
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-dashboard';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        global $dependencyInjection;

        $userEntity = $this->authAdapter->getIdentity();
        /**
         * @var \WebHemi\Data\Storage\UserStorage $userStorage
         */
        $userStorage = $dependencyInjection->get(\WebHemi\Data\Storage\UserStorage::class);
        /**
         * @var \WebHemi\Data\Storage\PolicyStorage $policyStorage
         */
        $policyStorage = $dependencyInjection->get(\WebHemi\Data\Storage\PolicyStorage::class);

        $userGroups = $userStorage->getUserGroupListByUser($userEntity->getUserId());
        $userPolicies = $policyStorage->getPolicyListByUser($userEntity->getUserId());
        /** @var EntitySet $userGroupPolicies */
        $userGroupPolicies = null;

        /** @var UserGroupEntity $userGroupEntity */
        foreach ($userGroups as $userGroupEntity) {
            $policyList = $policyStorage->getPolicyListByUserGroup($userGroupEntity->getUserGroupId());

            if (empty($userGroupPolicies)) {
                $userGroupPolicies = $policyList;
            } else {
                $userGroupPolicies->merge($policyList);
            }
        }

        // @TODO TBD
        return [
            'user' => $userEntity ?? false,
            'user_groups' => $userGroups ?? false,
            'user_policies' => $userPolicies ?? false,
            'user_group_policies' => $userGroupPolicies ?? false,
        ];
    }
}
