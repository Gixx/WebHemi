<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemiTest\Fixtures;

use WebHemi\Adapter\Auth\AbstractAuthAdapter;
use WebHemi\Auth\Result;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Class EmptyAuthAdapter
 */
class EmptyAuthAdapter extends AbstractAuthAdapter
{
    public $authResultShouldBe = 1;

    /**
     * Authenticates the user.
     *
     * @return Result
     */
    public function authenticate()
    {
        if ($this->authResultShouldBe < 1) {
            return $this->getAuthResult()
                ->setCode($this->authResultShouldBe);
        }

        /** @var UserEntity $userEntity */
        $userEntity = $this->getDataStorage()->createEntity();
        $userEntity->setKeyData(123)
            ->setUserName('test');

        return $this->getAuthResult()
            ->setCode(Result::SUCCESS)
            ->setIdentity($userEntity);
    }
}
