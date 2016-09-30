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

/**
 * Class SessionManager.
 *
 * @codeCoverageIgnore - Not testing session (yet).
 */
class SessionManager
{
    /** @var string */
    private $namespace = '_webhemi';
    /** @var string */
    private $cookiePrefix = 'atsn';
    /** @var string */
    private $sessionNameSalt = 'WebHemi';
    /** @var array */
    private $readonly = [];
    /** @var array */
    private $data = [];

    /**
     * SessionManager constructor.
     */
    public function __construct()
    {
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '16');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.save_path', __DIR__.'/../../../data/session/');
    }

    /**
     * Saves data back to session.
     */
    public function __destruct()
    {
        $_SESSION[$this->namespace] = $this->data;
        session_write_close();
    }

    /**
     * Check whether the session has already been started.
     *
     * @return bool
     */
    private function sessionStarted()
    {
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

        session_name($this->cookiePrefix.'-'.bin2hex($name . $this->sessionNameSalt));
        session_set_cookie_params($timeOut, $path, $domain, $secure, $httpOnly);
        session_start();

        if (isset($_SESSION[$this->namespace])) {
            $this->data = $_SESSION[$this->namespace];
        }

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
        $_SESSION[$this->namespace] = $this->data;

        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Cannot regenerate session identifier. Unknown error.', 1002);
        }

        return $this;
    }

    /**
     * Sets session data.
     *
     * @param string $name
     * @param mixed  $value
     * @param bool   $readonly
     * @throws RuntimeException
     * @return SessionManager
     */
    public function set($name, $value, $readonly = false)
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot set session data. Session is not started yet.', 1003);
        }

        if (isset($this->readonly[$name])) {
            throw new RuntimeException('Unable to overwrite data. Permission denied.', 1004);
        }

        if ($readonly) {
            $this->readonly[$name] = $name;
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
            throw new RuntimeException('Cannot delete session data. Session is not started.', 1005);
        }

        if (!$forceDelete && isset($this->readonly[$name])) {
            throw new RuntimeException('Unable to delete data. Permission denied.', 1006);
        }

        // hide errors if data not exists.
        unset($this->readonly[$name]);
        unset($this->data[$name]);

        return $this;
    }

    /**
     * Unlocks readonly data.
     *
     * @param string $name
     * @return SessionManager
     */
    public function unlock($name)
    {
        if (isset($this->readonly[$name])) {
            unset($this->readonly[$name]);
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
