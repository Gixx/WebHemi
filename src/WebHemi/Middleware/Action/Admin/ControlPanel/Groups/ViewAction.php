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
use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class ViewAction
 */
class ViewAction extends AbstractMiddlewareAction
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
        return 'admin-control-panel-groups-view';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $data = null;

        $params = $this->getRoutingParameters();

        if (isset($params['userGroupId'])) {
            $data = $this->getUserGroupDetails((int) $params['userGroupId']);
        }

        return [
            'data' => $data
        ];
    }

    /**
     * Gets user group details.
     *
     * @param int $userGroupId
     * @return array
     * @throws RuntimeException
     */
    protected function getUserGroupDetails(int $userGroupId) : array
    {
        $userGroupEntity = $this->userGroupStorage->getUserGroupById($userGroupId);

        if (!$userGroupEntity instanceof UserGroupEntity) {
            throw new RuntimeException(
                sprintf(
                    'The requested user group entity with the given ID not found: %s',
                    (string) $userGroupId
                ),
                404
            );
        }

        $data = [
            'readonly' => $userGroupEntity->getReadOnly(),
            'group' => [
                'Id' => $userGroupEntity->getUserGroupId(),
                'Name' => $userGroupEntity->getName(),
                'Title' => $userGroupEntity->getTitle(),
                'Description' => $userGroupEntity->getDescription(),
                'Is read-only?' => $userGroupEntity->getReadOnly() ? 'Yes' : 'No',
                'Date created' => $userGroupEntity->getDateCreated()->format('Y-m-d H:i:s'),
            ],
        ];

        $dateModified = $userGroupEntity->getDateModified();

        if (!$userGroupEntity->getReadOnly() && $dateModified instanceof DateTime) {
            $data['group']['Date modified'] = $dateModified->format('Y-m-d H:i:s');
        }

        return $data;
    }
}
