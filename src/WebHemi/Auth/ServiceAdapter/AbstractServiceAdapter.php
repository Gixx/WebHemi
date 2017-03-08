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

namespace WebHemi\Auth\ServiceAdapter;

use WebHemi\Auth;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;

/**
 * Class AbstractServiceAdapter
 */
abstract class AbstractServiceAdapter implements Auth\ServiceInterface
{
    /** @var Auth\ResultInterface */
    private $authResult;
    /** @var Auth\StorageInterface */
    private $authStorage;
    /** @var UserStorage */
    private $dataStorage;
    /** @var ConfigurationInterface */
    protected $configuration;

    /**
     * AbstractServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param Auth\ResultInterface   $authResultPrototype
     * @param Auth\StorageInterface  $authStorage
     * @param UserStorage            $dataStorage
     */
    public function __construct(
        ConfigurationInterface $configuration,
        Auth\ResultInterface $authResultPrototype,
        Auth\StorageInterface $authStorage,
        UserStorage $dataStorage
    ) {
        $this->configuration = $configuration->getConfig('auth');
        $this->authResult = $authResultPrototype;
        $this->authStorage = $authStorage;
        $this->dataStorage = $dataStorage;
    }

    /**
     * Gets the auth storage instance. (e.g.: AuthSessionStorage)
     *
     * @return Auth\StorageInterface
     */
    protected function getAuthStorage() : Auth\StorageInterface
    {
        return $this->authStorage;
    }

    /**
     * Gets the data storage instance. (e.g.: UserStorage)
     *
     * @return UserStorage
     */
    protected function getDataStorage() : UserStorage
    {
        return $this->dataStorage;
    }

    /**
     * Gets a new instance of the auth result container.
     *
     * @return Auth\ResultInterface
     */
    protected function getNewAuthResultInstance() : Auth\ResultInterface
    {
        return clone $this->authResult;
    }

    /**
     * Authenticates the user.
     *
     * @param Auth\CredentialInterface $credential
     * @return Auth\ResultInterface
     */
    abstract public function authenticate(Auth\CredentialInterface $credential) : Auth\ResultInterface;

    /**
     * Sets the authenticated user.
     *
     * @param UserEntity $dataEntity
     * @return Auth\ServiceInterface
     */
    public function setIdentity(UserEntity $dataEntity) : Auth\ServiceInterface
    {
        $this->authStorage->setIdentity($dataEntity);

        return $this;
    }

    /**
     * Checks whether the user is authenticated or not.
     *
     * @return bool
     */
    public function hasIdentity() : bool
    {
        return $this->authStorage->hasIdentity();
    }

    /**
     * Gets the authenticated user's entity.
     *
     * @return UserEntity|null
     */
    public function getIdentity() : ? UserEntity
    {
        return $this->authStorage->getIdentity();
    }

    /**
     * Clears the session.
     *
     * @return Auth\ServiceInterface
     */
    public function clearIdentity() : Auth\ServiceInterface
    {
        $this->authStorage->clearIdentity();
        return $this;
    }
}
