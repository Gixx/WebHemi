<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\TestService;

use WebHemi\Auth\ServiceAdapter\AbstractServiceAdapter;
use WebHemi\Auth\CredentialInterface as AuthCredentialInterface;
use WebHemi\Auth\ResultInterface as AuthResultInterface;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class EmptyAuthAdapter
 */
class EmptyAuthAdapter extends AbstractServiceAdapter
{
    /**
     * Authenticates the user.
     *
     * @param AuthCredentialInterface $credential
     * @return AuthResultInterface
     */
    public function authenticate(AuthCredentialInterface $credential) : AuthResultInterface
    {
        $crentialData = $credential->getCredentials();
        $authResultShouldBe = $crentialData['authResultShouldBe'];

        if ($authResultShouldBe < 1) {
            return $this->getNewAuthResultInstance()
                ->setCode($authResultShouldBe);
        }

        /** @var UserEntity $userEntity */
        $userEntity = $this->getDataStorage()->createEntity();
        $userEntity->setKeyData(123)
            ->setUserName('test');

        $this->setIdentity($userEntity);

        return $this->getNewAuthResultInstance()
            ->setCode(AuthResultInterface::SUCCESS);
    }
}
