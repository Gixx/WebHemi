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

namespace WebHemi\Middleware\Action\Auth;

use Exception;
use WebHemi\Adapter\Auth\AuthAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Auth\Result;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserGroupStorage;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\DateTime;
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
    public function getTemplateName() : string
    {
        return 'admin-login';
    }

    /**
     * Gets template data.
     *
     * @throws Exception
     * @return array
     */
    public function getTemplateData() : array
    {
        if ($this->request->getMethod() == 'POST') {
            /** @var Result $result */
            $result = $this->authAdapter->authenticate();

            if (!$result->isValid()) {

            }

            /** @var UserEntity|null $userEntity */
            $userEntity = $this->authAdapter->getIdentity();

            // save new user if we have the username credentials and add him/her to the Guest group
            if (!$userEntity instanceof UserEntity && !empty($userEntity)) {
                $userEntity = $this->registerGuestUser($userEntity);
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
        }

        return [];
    }

    /**
     * Registers a new user as guest.
     *
     * @param string $identity
     * @return UserEntity
     */
    private function registerGuestUser($identity)
    {
        /** @var string $userName */
        $userName = (string) $identity;
        /** @var UserEntity $userEntity */
        $userEntity = $this->userStorage->createEntity();
        $userEntity->setUserName($userName)
            ->setPassword('guest-user')
            ->setActive(true)
            ->setEnabled(true)
            ->setDateCreated(new DateTime('now'));

        $userId = $this->userStorage->saveEntity($userEntity);
        $userGroupEntity = $this->userGroupStorage->getUserGroupByName('guest');
        // add user to the Guests group.
        if ($userId && $userGroupEntity) {
            $this->userToGroupCoupler->setDependency($userEntity, $userGroupEntity);
        }

        return $userEntity;
    }
}
