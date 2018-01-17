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
         * @var \WebHemi\Data\Coupler\UserToGroupCoupler $userToGroupCoupler
         */
        $userToGroupCoupler = $dependencyInjection->get(\WebHemi\Data\Coupler\UserToGroupCoupler::class);
        /**
         * @var \WebHemi\Data\Coupler\UserToPolicyCoupler $userToPolicyCoupler
         */
        $userToPolicyCoupler = $dependencyInjection->get(\WebHemi\Data\Coupler\UserToPolicyCoupler::class);
        /**
         * @var \WebHemi\Data\Coupler\UserGroupToPolicyCoupler $userToGroupToPolicyCoupler
         */
        $userToGroupToPolicyCoupler = $dependencyInjection->get(\WebHemi\Data\Coupler\UserGroupToPolicyCoupler::class);

        $userGroups = $userToGroupCoupler->getEntityDependencies($userEntity);
        $userPolicies = $userToPolicyCoupler->getEntityDependencies($userEntity);

        $userGroupPolicies = [];

        foreach ($userGroups as $userGroupEntity) {
            $userGroupPolicies = array_merge(
                $userGroupPolicies,
                $userToGroupToPolicyCoupler->getEntityDependencies($userGroupEntity)
            );
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
