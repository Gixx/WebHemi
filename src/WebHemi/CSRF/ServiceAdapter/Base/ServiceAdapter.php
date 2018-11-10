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
     * @return string
     */
    public function generate() : string
    {
        $extra = $this->getClientHash();

        try {
            $randomString = bin2hex(random_bytes(16));
            $randomString = str_pad(substr($randomString, 0, 32), 32, 'a', STR_PAD_LEFT);
        } catch (\Throwable $error) {
            $randomString = $this->getRandomString(32);
        }

        $token = base64_encode(time() . $extra . $randomString);

        $this->sessionManager->set(self::SESSION_KEY, $token);

        return $token;
    }

    /**
     * Check the CSRF token is valid.
     *
     * @param string $token
     * @param null|int $ttl
     * @param bool $multiple
     * @return bool
     */
    public function verify(string $token, ? int $ttl = null, bool $multiple = true) : bool
    {
        $sessionToken = $this->sessionManager->get(self::SESSION_KEY) ?? '';

        if (!$multiple) {
            $this->sessionManager->delete(self::SESSION_KEY);
        }

        $sessionToken = $this->decodeToken($sessionToken);
        $token = $this->decodeToken($token);

        return !((!empty($ttl) && $sessionToken['time'] + $ttl > time())
            || ($token['extra'] != $this->getClientHash())
            || ($sessionToken['randomString'] != $token['randomString'])
        );
    }

    /**
     * Decodes the given token.
     *
     * @param string $token
     * @return array
     */
    protected function decodeToken(string $token) : array
    {
        $token = base64_decode($token) ?: str_repeat('0', 82);

        return [
            'time' => substr($token, 0, 10),
            'extra' => substr($token, 10, 40),
            'randomString' => substr($token, 50),
        ];
    }

    /**
     * @return string
     */
    protected function getClientHash() : string
    {
        return sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Generate a random string
     *
     * @param int $length
     * @return string
     */
    protected function getRandomString(int $length) : string
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
