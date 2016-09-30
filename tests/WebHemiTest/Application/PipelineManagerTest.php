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
namespace WebHemiTest\Application;

use ArrayObject;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use RuntimeException;
use WebHemi\Config\Config;
use WebHemi\Middleware\DispatcherMiddleware;
use WebHemi\Middleware\FinalMiddleware;
use WebHemi\Application\PipelineManager as Pipeline;
use WebHemi\Middleware\RoutingMiddleware;
use WebHemiTest\AssertTrait;
use WebHemiTest\Fixtures\TestActionMiddleware;
use WebHemiTest\Fixtures\TestMiddleware;
use WebHemiTest\InvokePrivateMethodTrait;

/**
 * Class PipelineTest.
 */
class PipelineTest extends TestCase
{
    protected $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    public function setUp()
    {
        parent::setUp();

        $this->config = [
            ['service' => FinalMiddleware::class, 'priority' => 1],
            ['service' => TestMiddleware::class, 'priority' => 50],
            ['service' => 'someAlias', 'priority' => -3],
            ['service' => 'someNoPriorityAlias'],
            ['service' => TestActionMiddleware::class, 'priority' => 100],
        ];
    }

    /**
     * Tests the getPipelineList() method.
     */
    public function testGetList()
    {
        $pipeline = new Pipeline(new Config($this->config));
        $pipeline->start();

        $expectedPipeline = [
            'someAlias',
            RoutingMiddleware::class,
            TestMiddleware::class,
            'someNoPriorityAlias',
            DispatcherMiddleware::class,
            TestActionMiddleware::class
        ];
        $actualPipeline = $pipeline->getPipelineList();
        $this->assertArraysAreSimilar($actualPipeline, $expectedPipeline);
    }

    /**
     * Tests the getPipelineList() method.
     */
    public function testErrorOfCallNextWhenNotStarted()
    {
        $pipeline = new Pipeline(new Config($this->config));

        $this->setExpectedException(RuntimeException::class, '', 1003);
        $pipeline->next();
    }

    /**
     * Tests the getPipelineList() method.
     */
    public function testErrorOfCheckMiddlewareWhenStarted()
    {
        $pipeline = new Pipeline(new Config($this->config));
        $pipeline->start();

        $this->setExpectedException(RuntimeException::class, '', 1000);
        $this->invokePrivateMethod($pipeline, 'checkMiddleware', ['newService']);
    }

    /**
     * Tests the getPipelineList() method.
     */
    public function testErrorOfCheckMiddlewareWithQueuedService()
    {
        $pipeline = new Pipeline(new Config($this->config));

        $this->setExpectedException(RuntimeException::class, '', 1001);
        $this->invokePrivateMethod($pipeline, 'checkMiddleware', [TestActionMiddleware::class]);
    }

    /**
     * Tests the getPipelineList() method.
     */
    public function testErrorOfCheckMiddlewareWithWrongInstance()
    {
        $pipeline = new Pipeline(new Config($this->config));

        $this->setExpectedException(RuntimeException::class, '', 1002);
        $this->invokePrivateMethod($pipeline, 'checkMiddleware', [DateTime::class]);
    }
}
