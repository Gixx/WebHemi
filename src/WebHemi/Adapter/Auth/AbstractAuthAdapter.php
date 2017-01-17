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

namespace WebHemi\Adapter\Auth;

use WebHemi\Auth\Result;
use WebHemi\Auth\AuthStorageInterface;
use WebHemi\Config\ConfigInterface;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Storage\DataStorageInterface;

/**
 * Class AbstractAuthAdapter
 */
abstract class AbstractAuthAdapter implements AuthAdapterInterface
{
    /** @var Result */
    private $authResult;
    /** @var AuthStorageInterface */
    private $authStorage;
    /** @var DataStorageInterface */
    private $dataStorage;
    /** @var ConfigInterface */
    protected $configuration;

    /**
     * AbstractAuthAdapter constructor.
     *
     * @param ConfigInterface      $configuration
     * @param Result               $authResultPrototype
     * @param AuthStorageInterface $authStorage
     * @param DataStorageInterface $dataStorage
     */
    public function __construct(
        ConfigInterface $configuration,
        Result $authResultPrototype,
        AuthStorageInterface $authStorage,
        DataStorageInterface $dataStorage
    ) {
        $this->configuration = $configuration->getConfig('auth');
        $this->authResult = $authResultPrototype;
        $this->authStorage = $authStorage;
        $this->dataStorage = $dataStorage;
    }

    /**
     * Gets the auth storage instance. (e.g.: AuthSessionStorage)
     *
     * @return AuthStorageInterface
     */
    protected function getAuthStorage() : AuthStorageInterface
    {
        return $this->authStorage;
    }

    /**
     * Gets the data storage instance. (e.g.: UserStorage)
     *
     * @return DataStorageInterface
     */
    protected function getDataStorage() : DataStorageInterface
    {
        return $this->dataStorage;
    }

    /**
     * Gets a new instance of the auth result container.
     *
     * @return Result
     */
    protected function getAuthResult() : Result
    {
        return clone $this->authResult;
    }

    /**
     * Authenticates the user.
     *
     * @return Result
     */
    abstract public function authenticate() : Result;

    /**
     * Sets the authenticated user.
     *
     * @param DataEntityInterface $dataEntity
     * @return AuthAdapterInterface
     */
    public function setIdentity(DataEntityInterface $dataEntity) : AuthAdapterInterface
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
     * @return DataEntityInterface|null
     */
    public function getIdentity() : ?DataEntityInterface
    {
        return $this->authStorage->getIdentity();
    }

    /**
     * Clears the session.
     *
     * @return AuthAdapterInterface
     */
    public function clearIdentity() : AuthAdapterInterface
    {
        $this->authStorage->clearIdentity();
        return $this;
    }
}
