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

use InvalidArgumentException;

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
        self::CODE_FOUND      => 'Resource found.',
        self::CODE_NOT_FOUND  => 'The requested resource cannot be found.',
        self::CODE_BAD_METHOD => 'Bad request method was used by the client.'
    ];
    /** @var array */
    private $parameters;

    /**
     * Sets status code.
     *
     * @param int $status
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setStatus($status)
    {
        if (!isset($this->statusReason[$status])) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" is not a valid routing status.', $status));
        }

        $this->status = $status;

        return $this;
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

    /**
     * Gets reason for the status set.
     *
     * @return false|string
     */
    public function getStatusReason()
    {
        return isset($this->statusReason[$this->status]) ? $this->statusReason[$this->status] : false;
    }

    /**
     * Sets matched middleware.
     *
     * @param string $matchedMiddleware
     *
     * @return $this
     */
    public function setMatchedMiddleware($matchedMiddleware)
    {
        $this->matchedMiddleware = $matchedMiddleware;

        return $this;
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

    /**
     * Sets the parameters.
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets the parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
