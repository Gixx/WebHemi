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

use RuntimeException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Session\ServiceAdapter\Base\ServiceAdapter as SessionManager;
use WebHemi\Session\ServiceInterface;

/**
 * Class EmptySessionManager.
 */
class EmptySessionManager extends SessionManager
{
    /** @var string */
    public $namespace;
    /** @var string */
    public $cookiePrefix;
    /** @var string */
    public $sessionNameSalt;
    /** @var array */
    public $readOnly = [];
    /** @var array */
    public $data = [];
    /** @var array */
    public $session;
    /** @var string */
    public $sessionName;
    /** @var string */
    public $sessionId;
    /** @var int */
    public $sessionIdCounter = 1;

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
    }

    /**
     * Saves data back to session.
     */
    public function __destruct()
    {
        $this->write();
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
        $this->data = $this->session[$this->namespace];
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
        $this->session[$this->namespace] = $this->data;
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
        return !empty($this->session);
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

        unset($timeOut, $path, $domain, $secure, $httpOnly);

        $this->session[$this->namespace]['started'] = true;
        $this->sessionName = $this->cookiePrefix.'-'.bin2hex($name.$this->sessionNameSalt);
        $this->sessionId = 'unittest_'.md5($this->namespace.$this->sessionIdCounter);

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

        $this->sessionIdCounter++;
        $this->sessionId = 'unittest_'.md5($this->namespace.$this->sessionIdCounter);

        return $this;
    }

    /**
     * Returns the session id.
     *
     * @return string
     */
    public function getSessionId() : string
    {
        if (!$this->sessionStarted()) {
            throw new RuntimeException('Cannot retrieve session identifier. Session is not started yet.', 1010);
        }

        return $this->sessionId;
    }
}
