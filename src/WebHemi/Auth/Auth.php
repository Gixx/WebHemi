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

namespace WebHemi\Auth;

use WebHemi\Adapter\Auth\AbstractAuthAdapter;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;

/**
 * Class Auth
 *
 * @codeCoverageIgnore - unfinished code
 */
final class Auth extends AbstractAuthAdapter
{
    /**
     * Authenticates the user.
     *
     * @return Result
     */
    public function authenticate() : Result
    {
        /** @var Result $result */
        $result = $this->getNewAuthResultInstance();

        /** @var UserStorage $dataStorage */
        $dataStorage = $this->getDataStorage();
        $user = $dataStorage->getUserById(1);
        if ($user instanceof UserEntity) {
            $this->setIdentity($user);
            $result->setCode(Result::SUCCESS);
        } else {
            $result->setCode(Result::FAILURE_IDENTITY_NOT_FOUND);
        }
        return $result;
    }

    /**
     * Gets the authenticated user's entity.
     *
     * @return DataEntityInterface|null
     */
    public function getIdentity() : ?DataEntityInterface
    {
        $identity = parent::getIdentity();
        // TODO implement

        return $identity;
    }
}
