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

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Groups;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class ListAction
 */
class ListAction extends AbstractMiddlewareAction
{
    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var EnvironmentInterface */
    protected $environmentManager;
    /** @var UserGroupStorage */
    protected $userGroupStorage;

    /**
     * GroupManagementAction constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface $environmentManager
     * @param UserGroupStorage $userGroupStorage
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        UserGroupStorage $userGroupStorage
    ) {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;
        $this->userGroupStorage = $userGroupStorage;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-control-panel-groups-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $data = $this->getUserGroupList();

        return [
            'data' => $data
        ];
    }

    /**
     * Gets the whole user group record list.
     *
     * @return array
     */
    protected function getUserGroupList() : array
    {
        $dataList = [];
        $entityList = $this->userGroupStorage->getUserGroups();

        /** @var UserGroupEntity $userGroupEntity */
        foreach ($entityList as $userGroupEntity) {
            $dataList[] = [
                'id' => $userGroupEntity->getUserGroupId(),
                'name' => $userGroupEntity->getName(),
                'title' => $userGroupEntity->getTitle(),
                'description' => $userGroupEntity->getDescription()
            ];
        }

        return $dataList;
    }
}
