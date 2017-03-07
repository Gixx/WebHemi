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

namespace WebHemi\Session;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationService;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationService $configuration
     */
    public function __construct(ConfigurationService $configuration);

    /**
     * Saves data back to session.
     */
    public function __destruct();

    /**
     * Starts a session.
     *
     * @param string $name
     * @param int    $timeOut
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     * @return ServiceInterface
     */
    public function start(
        string $name,
        int $timeOut = 3600,
        string $path = '/',
        ? string $domain = null,
        bool $secure = false,
        bool $httpOnly = false
    ) : ServiceInterface;

    /**
     * Regenerates session identifier.
     *
     * @return ServiceInterface
     */
    public function regenerateId() : ServiceInterface;

    /**
     * Sets session data.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $readOnly
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function set(string $name, $value, bool $readOnly = false) : ServiceInterface;

    /**
     * Gets session data.
     *
     * @param string $name
     * @throws RuntimeException
     * @return bool
     */
    public function has(string $name) : bool;

    /**
     * Gets session data.
     *
     * @param string $name
     * @param bool   $skipMissing
     * @throws RuntimeException
     * @return mixed
     */
    public function get(string $name, bool $skipMissing = true);

    /**
     * Deletes session data.
     *
     * @param string $name
     * @param bool   $forceDelete
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function delete(string $name, bool $forceDelete = false) : ServiceInterface;

    /**
     * Unlocks readOnly data.
     *
     * @param string $name
     * @return ServiceInterface
     */
    public function unlock(string $name) : ServiceInterface;

    /**
     * Returns the internal storage.
     *
     * @return array
     */
    public function toArray() : array;
}
