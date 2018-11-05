<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\CSRF\ServiceAdapter\Base;

use WebHemi\CSRF\ServiceInterface;
use WebHemi\Session\ServiceInterface as SessionInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    protected $sessionManager;

    /**
     * ServiceInterface constructor.
     *
     * @param SessionInterface $sessionManager
     */
    public function __construct(SessionInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Generate a CSRF token.
     *
     * @param string $key
     * @return string
     */
    public function generate(string $key) : string
    {
        $key = preg_replace('/[^a-zA-Z0-9]/', '', $key);

        $extra = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);

        try {
            $randomString = bin2hex(random_bytes(16));
        } catch (\Throwable $error) {
            $randomString = $this->randomString(32);
        }

        $token = base64_encode(time() . $extra . $randomString);

        $this->sessionManager->set(self::SESSION_PREFIX.'_'.$key, $token);

        return $token;
    }

    /**
     * Check the CSRF token is valid.
     *
     * @param string $key
     * @param string $token
     * @param null|int $ttl
     * @param bool $multiple
     * @return bool
     */
    public function verify(string $key, string $token, ? int $ttl = null, bool $multiple = false) : bool
    {
        $key = preg_replace('/[^a-zA-Z0-9]/', '', $key);

        $extra = sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        $token = base64_decode($this->sessionManager->get(self::SESSION_PREFIX.'_'.$key));
        $tokenTime = substr($token, 0, 10);
        $tokenExtra = substr($token, 10, 40);
        $tokenRandomString = substr($token, 40);

        if (!$multiple) {
            $this->sessionManager->delete(self::SESSION_PREFIX.'_'.$key);
        }

        return !((!empty($ttl) && $tokenTime + $ttl > time())
            || $extra !== $tokenExtra
            || strlen($tokenRandomString) !== 32
            || !ctype_xdigit($tokenRandomString)
        );
    }

    /**
     * Generate a random string
     *
     * @param int $length
     * @return string
     */
    protected function randomString(int $length) : string
    {
        $seed = 'abcdef0123456789';
        $max = strlen($seed) - 1;
        $string = '';

        for ($i = 0; $i < $length; ++$i) {
            $string .= $seed[intval(mt_rand(0.0, $max))];
        }

        return $string;
    }
}
