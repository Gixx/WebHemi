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

namespace WebHemi\Renderer\Helper;

use WebHemi\Acl\ServiceInterface as AclInterface;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Data\Entity\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Data\Storage\ResourceStorage;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;

/**
 * Class IsAllowedHelper
 */
class IsAllowedHelper implements HelperInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var EnvironmentInterface
     */
    private $environmentManager;
    /**
     * @var AclInterface
     */
    private $aclAdapter;
    /**
     * @var AuthInterface
     */
    private $authAdapter;
    /**
     * @var ResourceStorage
     */
    private $resourceStorage;
    /**
     * @var ApplicationStorage
     */
    private $applicationStorage;

    /**
     * Should return the name of the helper.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'isAllowed';
    }

    /**
     * Should return the name of the helper.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{% if isAllowed("resource_name", ["POST"[, "application_name"]]) %}';
    }

    /**
     * Should return a description text.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Checks if the given user has access to the given resource.';
    }

    /**
     * Gets helper options for the render.
     *
     * @return             array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * IsAllowedHelper constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     * @param AclInterface           $aclAdapter
     * @param AuthInterface          $authAdapter
     * @param ResourceStorage        $resourceStorage
     * @param ApplicationStorage     $applicationStorage
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        AclInterface $aclAdapter,
        AuthInterface $authAdapter,
        ResourceStorage $resourceStorage,
        ApplicationStorage $applicationStorage
    ) {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;
        $this->aclAdapter = $aclAdapter;
        $this->authAdapter = $authAdapter;
        $this->resourceStorage = $resourceStorage;
        $this->applicationStorage = $applicationStorage;
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return bool
     */
    public function __invoke() : bool
    {
        /**
         * @var UserEntity $userEntity
         */
        $userEntity = $this->authAdapter->getIdentity();
        // Without user, access should be denied.
        if (!$userEntity) {
            return false;
        }

        $arguments = func_get_args();

        $applicationName = $arguments[2] ?? $this->environmentManager->getSelectedApplication();
        /**
         * @var null|ApplicationEntity $applicationEntity
         */
        $applicationEntity = $this->applicationStorage->getApplicationByName($applicationName);

        // For invalid applications the path will be checked against the current (valid) application
        if (!$applicationEntity instanceof ApplicationEntity) {
            $applicationName = $this->environmentManager->getSelectedApplication();
            /**
             * @var null|ApplicationEntity $applicationEntity
             */
            $applicationEntity = $this->applicationStorage->getApplicationByName($applicationName);
        }

        $resourceName = $arguments[0] ?? '';
        /**
         * @var null|ResourceEntity $resourceEntity
         */
        $resourceEntity = $this->resourceStorage->getResourceByName($resourceName);

        return $this->aclAdapter->isAllowed($userEntity, $resourceEntity, $applicationEntity);
    }
}
