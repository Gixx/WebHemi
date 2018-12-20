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

namespace WebHemi\Middleware\Security;

use Generator;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Http\ResponseInterface;
use WebHemi\Http\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;

/**
 * Class CSRFMiddleware.
 */
class CSRFMiddleware implements MiddlewareInterface
{
    /**
     * @var CSRFInterface
     */
    private $csrfAdapter;

    /**
     * AclMiddleware constructor.
     *
     * @param CSRFInterface $csrfAdapter
     */
    public function __construct(CSRFInterface $csrfAdapter)
    {
        $this->csrfAdapter = $csrfAdapter;
    }

    /**
     * A middleware is a callable. It can do whatever is appropriate with the Request and Response objects.
     * The only hard requirement is that a middleware MUST return an instance of \Psr\Http\Message\ResponseInterface.
     * Each middleware SHOULD invoke the next middleware and pass it Request and Response objects as arguments.
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @throws RuntimeException
     * @return void
     */
    public function __invoke(ServerRequestInterface&$request, ResponseInterface&$response) : void
    {
        // Get requests will not harm
        if ($request->getMethod() === 'GET') {
            return;
        }

        $csrfToken = '';

        foreach ($this->recursiveFind($request->getParsedBody(), CSRFInterface::SESSION_KEY) as $value) {
            $csrfToken = $value;
            break;
        }

        if (empty($csrfToken)) {
            throw new RuntimeException('CSRF token is missing from the request', 405);
        }

        $tokenTTL = $request->isXmlHttpRequest() ? null : CSRFInterface::SESSION_TTL_IN_SECONDS;
        $allowMultipleUse = $request->isXmlHttpRequest();
        $result = $this->csrfAdapter->verify($csrfToken, $tokenTTL, $allowMultipleUse);

        if (!$result) {
            throw new RuntimeException('The provided CSRF token is not valid or outdated.', 403);
        }
    }

    /**
     * Recursively search for a key in an Iterable data
     *
     * @param mixed $haystack
     * @param string $needle
     * @return Generator
     */
    protected function recursiveFind($haystack, string $needle) : Generator
    {
        $iterator  = new RecursiveArrayIterator($haystack);
        $recursive = new RecursiveIteratorIterator(
            $iterator,
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                yield $value;
            }
        }
    }
}
