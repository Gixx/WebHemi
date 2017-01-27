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

namespace WebHemiTest\Fixtures;

use WebHemi\Adapter\Auth\AbstractAuthAdapter;
use WebHemi\Adapter\Auth\AuthCredentialInterface;
use WebHemi\Adapter\Auth\AuthResultInterface;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class EmptyAuthAdapter
 */
class EmptyAuthAdapter extends AbstractAuthAdapter
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
