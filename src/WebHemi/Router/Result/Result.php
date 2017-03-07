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

namespace WebHemi\Router\Result;

use InvalidArgumentException;

/**
 * Class Result.
 */
class Result
{
    public const CODE_FOUND = 200;
    public const CODE_NOT_FOUND = 404;
    public const CODE_BAD_METHOD = 405;

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
     * Define clone behaviour.
     */
    public function __clone()
    {
        unset($this->status);
        unset($this->matchedMiddleware);
        unset($this->parameters);
    }

    /**
     * Sets status code.
     *
     * @param int $status
     * @throws InvalidArgumentException
     * @return Result
     */
    public function setStatus(int $status) : Result
    {
        if (!isset($this->statusReason[$status])) {
            throw new InvalidArgumentException(sprintf('The parameter "%s" is not a valid router status.', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Gets status code.
     *
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Gets reason for the status set.
     *
     * @return string
     */
    public function getStatusReason() : string
    {
        return $this->statusReason[$this->status] ?? '';
    }

    /**
     * Sets matched middleware.
     *
     * @param string $matchedMiddleware
     * @return Result
     */
    public function setMatchedMiddleware(string $matchedMiddleware)
    {
        $this->matchedMiddleware = $matchedMiddleware;

        return $this;
    }

    /**
     * Gets matched middleware.
     *
     * @return string
     */
    public function getMatchedMiddleware() : string
    {
        return $this->matchedMiddleware;
    }

    /**
     * Sets the parameters.
     *
     * @param array $parameters
     * @return Result
     */
    public function setParameters(array $parameters) : Result
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets the parameters.
     *
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
}
