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

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\ResponseInterface;
use WebHemi\Http\ClientInterface;

/**
 * Class HttpClient
 *
 * @codeCoverageIgnore - don't test third party library until this only a forwards the calls.
 */
class Client implements ClientInterface
{
    /** @var GuzzleClient */
    private $guzzleClient;

    /**
     * HttpClient constructor.
     */
    public function __construct()
    {
        $this->guzzleClient = new GuzzleClient();
    }

    /**
     * Posts data.
     *
     * @param string $url
     * @param array $data
     * @return ResponseInterface
     */
    public function post(string $url, array $data) : ResponseInterface
    {
        return $this->guzzleClient->post($url, $data);
    }
}
