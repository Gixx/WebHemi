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

namespace WebHemi\Http\ServiceAdapter\GuzzleHttp;

use GuzzleHttp\Psr7\ServerRequest as GuzzleServerRequest;
use InvalidArgumentException;
use WebHemi\Http\ServerRequestInterface;

/**
 * Class ServerRequest.
 */
class ServerRequest extends GuzzleServerRequest implements ServerRequestInterface
{
    /**
     * @var null|array|object
     */
    private $parsedBody;

    /**
     * Checks if it is an XML HTTP Request (Ajax) or not.
     *
     * @return bool
     */
    public function isXmlHttpRequest() : bool
    {
        $serverParams = $this->getServerParams();

        return (
            isset($serverParams['HTTP_X_REQUESTED_WITH'])
            && $serverParams['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'
        );
    }

    /**
     * The Guzzle does not handle the php://input for DELETE, PUT, PATCH etc Request methods. So we need to do it.
     *
     * @return array|null|object
     */
    public function getParsedBody()
    {
        if (empty($this->parsedBody)) {
            $this->parsedBody = $this->parseInput();
        }

        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $new = clone $this;
        $new->parsedBody = $data;

        return $new;
    }

    /**
     * Parse
     *
     * @return array|object|null
     */
    protected function parseInput()
    {
        $parsedInput = null;
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return null;
        }

        $serverParams = $this->getServerParams();
        $contentType = explode(';', ($serverParams['HTTP_CONTENT_TYPE'] ?? 'plain/text'))[0];

        switch (strtolower($contentType)) {
            case 'application/json':
            case 'application/x-json':
                $parsedInput = json_decode($input, true);
                break;

            case 'application/x-www-form-urlencoded':
                parse_str($input, $parsedInput);
                break;

            case 'multipart/form-data':
                // TODO write own parser to support PUT and DELETE methods' multipart/form-data
                throw new InvalidArgumentException('Unsopported request content type.');
                break;
        }

        return $parsedInput;
    }
}
