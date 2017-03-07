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

namespace WebHemi\Renderer\Helper;

use WebHemi\Acl\ServiceInterface as AclInterface;
use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;

/**
 * Class IsAllowedHelper
 */
class IsAllowedHelper implements HelperInterface
{
    /** @var ConfigurationInterface */
    private $configuration;
    /** @var EnvironmentInterface */
    private $environmentManager;
    /** @var AclInterface */
    private $aclAdapter;
    /** @var AuthInterface */
    private $authAdapter;
    /** @var ResourceStorage */
    private $resourceStorage;
    /** @var ApplicationStorage */
    private $applicationStorage;

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'isAllowed';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return 'isAllowed(string resourceName = null, string applicationName = null) : bool';
    }

    /**
     * Should return a description text.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Checks if the given user has access to the given resource in the given application.';
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
        /** @var UserEntity $userEntity */
        $userEntity = $this->authAdapter->getIdentity();
        // Without user, access should be denied.
        if (!$userEntity) {
            return false;
        }

        $arguments = func_get_args();

        $applicationName = $arguments[1] ?? $this->environmentManager->getSelectedApplication();
        /** @var null|ApplicationEntity $applicationEntity */
        $applicationEntity = $this->applicationStorage->getApplicationByName($applicationName);

        // For invalid applications the path will be checked against the current (valid) application
        if (!$applicationEntity instanceof ApplicationEntity) {
            $applicationName = $this->environmentManager->getSelectedApplication();
        }

        $resourceName = $arguments[0] ?? '';
        $this->checkResourceNameAgainstRouting($resourceName, $applicationName);
        /** @var null|ResourceEntity $resourceEntity */
        $resourceEntity = $this->resourceStorage->getResourceByName($resourceName);

        return $this->aclAdapter->isAllowed($userEntity, $resourceEntity, $applicationEntity);
    }

    /**
     * Matches the given resource name against router URLs and if found, changes it to the assigned middleware name.
     * @TODO: make sure it is NOT possible to give a custom resource with a name that can match against a router path.
     *
     * @param string $resourceName
     * @param string $applicationName
     */
    private function checkResourceNameAgainstRouting(string &$resourceName, string $applicationName) : void
    {
        $applicationConfig = $this->configuration
            ->getData('applications/'.$applicationName);

        $applicationRouteConfig = $this->configuration
            ->getData('router/'.$applicationConfig['module']);

        $tempName = $resourceName;
        $tempName = '/'.trim($tempName, '/');

        foreach ($applicationRouteConfig as $route) {
            if ($route['path'] == $tempName) {
                $resourceName = $route['middleware'];
                break;
            }
        }
    }
}
