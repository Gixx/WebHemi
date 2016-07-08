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
namespace WebHemi\Routing;

/**
 * Class Result.
 */
class Result
{
    const CODE_FOUND = 200;
    const CODE_NOT_FOUND = 404;
    const CODE_BAD_METHOD = 405;

    /** @var int */
    private $status;
    /** @var string */
    private $matchedMiddleware;
    /** @var array */
    private $statusReason = [
        self::CODE_FOUND => 'Resource found.',
        self::CODE_NOT_FOUND => 'The requested resource cannot be found.',
        self::CODE_BAD_METHOD => 'Bad request method was used by the client.'
    ];

    /**
     * Sets status code.
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Gets status code.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusReason()
    {
        return $this->statusReason[$this->status];
    }

    /**
     * Sets matched middleware.
     *
     * @param string $matchedMiddleware
     */
    public function setMatchedMiddleware($matchedMiddleware)
    {
        $this->matchedMiddleware = $matchedMiddleware;
    }

    /**
     * Gets matched middleware.
     *
     * @return string
     */
    public function getMatchedMiddleware()
    {
        return $this->matchedMiddleware;
    }
}
