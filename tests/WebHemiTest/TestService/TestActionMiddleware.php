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
namespace WebHemiTest\TestService;

use Exception;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class TestActionMiddleware.
 */
class TestActionMiddleware extends AbstractMiddlewareAction
{
    /** @var boolean */
    private $shouldSimulateError;
    /** @var int */
    private $errorCode;

    /**
     * TestActionMiddleware constructor.
     *
     * @param bool $shouldSimulateError
     * @param int  $errorCode
     */
    public function __construct($shouldSimulateError = false, $errorCode = 1)
    {
        $this->shouldSimulateError = $shouldSimulateError;
        $this->errorCode = $errorCode;
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
            throw new Exception('Simulated error', $this->errorCode);
        }

        return [];
    }
}
