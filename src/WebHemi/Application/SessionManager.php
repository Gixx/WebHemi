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
namespace WebHemi\Application;

use RuntimeException;
use WebHemi\Config\ConfigInterface;

/**
 * Class SessionManager.
 */
class SessionManager
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
     * SessionManager constructor.
     *
     * @param ConfigInterface $sessionConfig
     */
    public function __construct(ConfigInterface $sessionConfig)
    {
        $configuration = $sessionConfig->getData('session');

        $this->namespace = $configuration['namespace'];
        $this->cookiePrefix = $configuration['cookie_prefix'];
        $this->sessionNameSalt = $configuration['session_name_salt'];

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            ini_set('session.entropy_file', '/dev/urandom');
            ini_set('session.entropy_length', '16');
            ini_set('session.hash_function', $configuration['hash_function']);
            ini_set('session.use_only_cookies', (int) $configuration['use_only_cookies']);
            ini_set('session.use_cookies', (int) $configuration['use_cookies']);
            ini_set('session.use_trans_sid', (int) $configuration['use_trans_sid']);
            ini_set('session.cookie_httponly', (int) $configuration['cookie_http_only']);
            ini_set('session.save_path', $configuration['save_path']);
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
     * @codeCoverageIgnore
     */
    private function read()
    {
        if (isset($_SESSION[$this->namespace])) {
            $this->data = $_SESSION[$this->namespace];
        }
    }

    /**
     * Writes class property to PHP Session array.
     * @codeCoverageIgnore
     */
    private function write()
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
     * @codeCoverageIgnore
     */
    private function sessionStarted()
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
     * @return SessionManager
     */
    public function start($name, $timeOut = 3600, $path = '/', $domain = null, $secure = false, $httpOnly = false)
    {
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
     * @return SessionManager
     */
    public function regenerateId()
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
     * Gets session data.
     *
     * @param string $name
     * @param bool   $skipMissing
     * @throws RuntimeException
     * @return mixed
     */
    public function get($name, $skipMissing = true)
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
     * Sets session data.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $readOnly
     * @throws RuntimeException
     * @return SessionManager
     */
    public function set($name, $value, $readOnly = false)
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
     * Deletes session data.
     *
     * @param string $name
     * @param bool   $forceDelete
     * @throws RuntimeException
     * @return SessionManager
     */
    public function delete($name, $forceDelete = false)
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
     * @return SessionManager
     */
    public function unlock($name)
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
    public function toArray()
    {
        return $this->data;
    }
}
