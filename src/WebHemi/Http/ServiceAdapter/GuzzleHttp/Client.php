<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use WebHemi\Http\ClientInterface;

/**
 * Class HttpClient
 *
 * @codeCoverageIgnore - don't test third party library until this only a forwards the calls.
 */
class Client implements ClientInterface
{
    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * HttpClient constructor.
     */
    public function __construct()
    {
        $this->guzzleClient = new GuzzleClient();
    }

    /**
     * Get data.
     *
     * @param string $url
     * @param array $data
     * @return PsrResponseInterface
     */
    public function get(string $url, array $data) : PsrResponseInterface
    {
        $queryData = empty($data)
            ? []
            : [
                'query' => $data
            ];

        return $this->request('GET', $url, $queryData);
    }

    /**
     * Posts data.
     *
     * @param string $url
     * @param array $data
     * @return PsrResponseInterface
     */
    public function post(string $url, array $data) : PsrResponseInterface
    {
        $formData = [];

        if (!empty($data)) {
            $formData['multipart'] = [];

            foreach ($data as $key => $value) {
                $formData['multipart'][] = [
                    'name' => (string) $key,
                    'contents' => (string) $value
                ];
            }
        }

        return $this->request('POST', $url, $formData);
    }

    /**
     * Request an URL with data.
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return PsrResponseInterface
     */
    private function request(string $method, string $url, array $options) : PsrResponseInterface
    {
        try {
            $response = $this->guzzleClient->request($method, $url, $options);
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        }

        return $response;
    }
}
