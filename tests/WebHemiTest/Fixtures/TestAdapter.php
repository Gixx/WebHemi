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
namespace WebHemiTest\Fixtures;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Uri;
use WebHemi\Adapter\Http\GuzzleHttp\ServerRequest;
use WebHemi\Adapter\Http\GuzzleHttp\Response;

class TestAdapter
{
    /**
     * Returns the HTTP request.
     *
     * @return ServerRequest
     */
    public function getRequest()
    {
        $uri = new Uri('');
        $uri = $uri->withScheme('http')
            ->withHost('unittest.dev')
            ->withPort(80)
            ->withPath('/')
            ->withQuery('');

        return new ServerRequest(
            'GET',
            $uri,
            [],
            new LazyOpenStream('php://input', 'r+'),
            '1.1',
            [
                'HTTP_HOST'    => 'unittest.dev',
                'SERVER_NAME'  => 'unittest.dev',
                'REQUEST_URI'  => '/',
                'QUERY_STRING' => '',
            ]
        );
    }

    /**
     * Returns the response being sent.
     *
     * @return Response
     */
    public function getResponse()
    {
        return new Response(Response::STATUS_PROCESSING);
    }
}
