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

use Exception;
use WebHemi\Adapter\Http\ResponseInterface;
use WebHemi\Adapter\Http\ServerRequestInterface;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class TestActionMiddleware.
 */
class TestActionMiddleware extends AbstractMiddlewareAction
{
    /** @var boolean */
    private $shouldSimulateError;

    /**
     * TestActionMiddleware constructor.
     *
     * @param bool $shouldSimulateError
     */
    public function __construct($shouldSimulateError = false)
    {
        $this->shouldSimulateError = $shouldSimulateError;
    }

    /**
     * Gets template name.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'test-page';
    }

    /**
     * Gets template data
     *
     * @throws Exception
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        if ($this->shouldSimulateError) {
            throw new Exception('Simulated error');
        }

        return [];
    }
}
