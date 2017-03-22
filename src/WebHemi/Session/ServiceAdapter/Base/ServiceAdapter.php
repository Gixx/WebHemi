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

namespace WebHemi\Session\ServiceAdapter\Base;

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Session\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var string */
    private $namespace;
    /** @var string */
    private $cookiePrefix;
    /** @var string */
    private $sessionNameSalt;
    /** @var array */
    private $readOnly = [];
    /** @var array */
    private $data = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $configuration = $configuration->getData('session');

        $this->namespace = $configuration['namespace'];
        $this->cookiePrefix = $configuration['cookie_prefix'];
        $this->sessionNameSalt = $configuration['session_name_salt'];

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            ini_set('session.entropy_file', '/dev/urandom');
            ini_set('session.entropy_length', '16');
            ini_set('session.hash_function', (string) $configuration['hash_function']);
            ini_set('session.use_only_cookies', (string) $configuration['use_only_cookies']);
            ini_set('session.use_cookies', (string) $configuration['use_cookies']);
            ini_set('session.use_trans_sid', (string) $configuration['use_trans_sid']);
            ini_set('session.cookie_httponly', (string) $configuration['cookie_http_only']);
            ini_set('session.save_path', (string) $configuration['save_path']);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Saves data back to session.
     */
    public function __destruct()
    {
        $this->write();

        // @codeCoverageIgnoreStart
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            session_write_close();
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Reads PHP Session array to class property.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    private function read() : void
    {
        if (isset($_SESSION[$this->namespace])) {
            $this->data = $_SESSION[$this->namespace];
        }
    }

    /**
     * Writes class property to PHP Session array.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    private function write() : void
    {
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            return;
        }

        $_SESSION[$this->namespace] = $this->data;
    }

    /**
     * Check whether the session has already been started.
     *
     * @return bool
     *
     * @codeCoverageIgnore
     */
    private function sessionStarted() : bool
    {
        // For unit test we give controllable result.
        if (defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            return $this->namespace == 'TEST';
        }

        return session_status() === PHP_SESSION_ACTIVE;
    }

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
    ) : ServiceInterface {
        if ($this->sessionStarted()) {
            throw new RuntimeException('Cannot start session. Session is already started.', 1000);
        }

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            session_name($this->cookiePrefix.'-'.bin2hex($name.$this->sessionNameSalt));
            session_set_cookie_params($timeOut, $path, $domain, $secure, $httpOnly);
            session_start();
        }
        // @codeCoverageIgnoreEnd

        $this->read();

        return $this;
    }

    /**
     * Regenerates session identifier.
     *
     * @return ServiceInterface
     */
    public function regenerateId() : ServiceInterface
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot regenerate session identifier. Session is not started yet.', 1001);
        }

        // first save data.
        $this->write();

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            if (!session_regenerate_id(true)) {
                throw new RuntimeException('Cannot regenerate session identifier. Unknown error.', 1002);
            }
        }
        // @codeCoverageIgnoreEnd

        return $this;
    }

    /**
     * Sets session data.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $readOnly
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function set(string $name, $value, bool $readOnly = false) : ServiceInterface
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot set session data. Session is not started yet.', 1005);
        }

        if (isset($this->readOnly[$name])) {
            throw new RuntimeException('Unable to overwrite data. Permission denied.', 1006);
        }

        if ($readOnly) {
            $this->readOnly[$name] = $name;
        }

        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Checks whether a session data exists or not.
     *
     * @param string $name
     * @throws RuntimeException
     * @return bool
     */
    public function has(string $name) : bool
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot set session data. Session is not started yet.', 1009);
        }

        return isset($this->data[$name]);
    }

    /**
     * Gets session data.
     *
     * @param string $name
     * @param bool   $skipMissing
     * @throws RuntimeException
     * @return mixed
     */
    public function get(string $name, bool $skipMissing = true)
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot set session data. Session is not started yet.', 1003);
        }

        if (isset($this->data[$name])) {
            return $this->data[$name];
        } elseif ($skipMissing) {
            return null;
        }

        throw new RuntimeException('Cannot retrieve session data. Data is not set', 1004);
    }

    /**
     * Deletes session data.
     *
     * @param string $name
     * @param bool   $forceDelete
     * @throws RuntimeException
     * @return ServiceInterface
     */
    public function delete(string $name, bool $forceDelete = false) : ServiceInterface
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot delete session data. Session is not started.', 1007);
        }

        if (!$forceDelete && isset($this->readOnly[$name])) {
            throw new RuntimeException('Unable to delete data. Permission denied.', 1008);
        }

        // hide errors if data not exists.
        unset($this->readOnly[$name]);
        unset($this->data[$name]);

        return $this;
    }

    /**
     * Unlocks readOnly data.
     *
     * @param string $name
     * @return ServiceInterface
     */
    public function unlock(string $name) : ServiceInterface
    {
        if (isset($this->readOnly[$name])) {
            unset($this->readOnly[$name]);
        }

        return $this;
    }

    /**
     * Returns the internal storage.
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->data;
    }
}
