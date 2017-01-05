<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Fixtures;

use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class TestMiddleware.
 */
class TestMiddleware implements MiddlewareInterface
{
    /** @var int */
    public static $counter = 0;
    /** @var array */
    public static $trace = [];

    public static $responseStatus;
    public static $responseBody;
    /** @var bool */
    private $isFinalMiddleware = false;

    /**
     * TestMiddleware constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        self::$trace[] = $name;

        if ($name == 'final') {
            $this->isFinalMiddleware = true;
        }
    }

    /**
     * Invokes the middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        self::$counter++;

        if (in_array($request->getMethod(), ['GET', 'POST']) && $this->isFinalMiddleware) {
            self::$responseStatus = $response->getStatusCode();
            self::$responseBody = $response->getBody()->__toString();
        }

        return $response;
    }
}
