<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\Middleware\Action\Auth;

use Exception;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class LoginAction
 */
class LoginAction extends AbstractMiddlewareAction
{
    /** @var AuthAdapterInterface */
    private $authAdapter;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var UserStorage */
    private $userStorage;
    /** @var UserGroupStorage */
    private $userGroupStorage;
    /** @var UserToGroupCoupler */
    private $userToGroupCoupler;

    /**
     * MetaDataAction constructor.
     *
     * @param AuthAdapterInterface $authAdapter
     * @param EnvironmentManager   $environmentManager
     * @param UserStorage          $userStorage
     * @param UserGroupStorage     $userGroupStorage
     * @param UserToGroupCoupler   $userToGroupCoupler
     */
    public function __construct(
        AuthAdapterInterface $authAdapter,
        EnvironmentManager $environmentManager,
        UserStorage $userStorage,
        UserGroupStorage $userGroupStorage,
        UserToGroupCoupler $userToGroupCoupler
    ) {
        $this->authAdapter = $authAdapter;
        $this->environmentManager = $environmentManager;
        $this->userStorage = $userStorage;
        $this->userGroupStorage = $userGroupStorage;
        $this->userToGroupCoupler = $userToGroupCoupler;
    }

    /**
     * Gets template map name or template file path.
     * This middleware does not have any output.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return '';
    }

    /**
     * Gets template data.
     *
     * @throws Exception
     * @return array
     */
    public function getTemplateData()
    {
        /** @var null|string|UserEntity $userEntity */
        $userEntity = $this->authAdapter->getIdentity();

        // save new user if we have the username credentials and add him/her to the Guest group
        if (!$userEntity instanceof UserEntity && !empty($userEntity)) {
            /** @var string $userName */
            $userName = $userEntity;
            /** @var UserEntity $userEntity */
            $userEntity = $this->userStorage->createEntity();
            $userEntity->setUserName($userName)
                ->setPassword('SSO-user')
                ->setActive(true)
                ->setEnabled(true)
                ->setDateCreated(new DateTime('now'));

            $userId = $this->userStorage->saveEntity($userEntity);
            // add user to the Guests group.
            if ($userId) {
                $userGroupEntity = $this->userGroupStorage->getUserGroupByName('guest');
                $this->userToGroupCoupler->setDependency($userEntity, $userGroupEntity);
            }
        }

        if ($userEntity instanceof UserEntity) {
            $this->authAdapter->setIdentity($userEntity);
        }

        $url = 'http'.($this->environmentManager->isSecuredApplication() ? 's' : '').'://'.
            $this->environmentManager->getApplicationDomain().
            $this->environmentManager->getSelectedApplicationUri();

        $this->response = $this->response
            ->withStatus(302, 'Found')
            ->withHeader('Location', $url);

        return [];
    }
}
