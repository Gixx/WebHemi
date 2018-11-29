<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\DependencyInjection;

use ArrayObject;
use InvalidArgumentException;
use RuntimeException;
use Throwable;
use WebHemi\DateTime;
use WebHemi\DependencyInjection\ServiceAdapter\Base\ServiceAdapter as BaseAdapter;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionAdapterInterface;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;
use WebHemiTest\TestService\EmptyEntity;
use WebHemiTest\TestService\EmptyService;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseAdapterTest.
 */
class BaseAdapterTest extends TestCase
{
    /** @var Config */
    private $config;

    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../test_config.php';
        $this->config = new Config($config);
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $adapter = new BaseAdapter($this->config);
        $adapter->registerServiceInstance(ConfigInterface::class, $this->config)
            ->registerModuleServices('Global');

        $this->assertInstanceOf(DependencyInjectionAdapterInterface::class, $adapter);
        $this->assertAttributeInstanceOf(ConfigInterface::class, 'configuration', $adapter);
    }

    /**
     * Tests initializer.
     */
    public function testInitContainer()
    {
        $adapter = new BaseAdapter($this->config);

        $this->assertInstanceOf(DependencyInjectionAdapterInterface::class, $adapter);
        // The identifier is an instantiable class
        $this->assertTrue($adapter->has(ArrayObject::class));
        // The identifier is an alias and not registered yet
        $this->assertFalse($adapter->has('actionOk'));

        $adapter->registerModuleServices('Global');
        // The identifier is an alias and registered
        $this->assertTrue($adapter->has('actionOk'));

        $adapter->get('actionBad');
        // The identifier is an alias and the service is initialized already
        $this->assertTrue($adapter->has('actionBad'));

        $this->assertFalse($adapter->has('someSuperName'));
        $adapter->registerServiceInstance('someSuperName', new EmptyService());
        $this->assertTrue($adapter->has('someSuperName'));
    }

    /**
     * Tests service registering.
     */
    public function testRegisterService()
    {
        $adapter = new BaseAdapter($this->config);
        $adapter->registerModuleServices('Website')
            ->registerModuleServices('SomeApp');

        /** @var DateTime $actualDate */
        $actualDate = $adapter->get('alias1');
        $this->assertInstanceOf(DateTime::class, $actualDate);
        $this->assertEquals('2016-04-05 01:02:03', $actualDate->format('Y-m-d H:i:s'));

        // Get a non-registered service being registered with default parameters.
        $serviceResult = $adapter->get(ArrayObject::class);
        $this->assertInstanceOf(ArrayObject::class, $serviceResult);

        // Get a service which called a method after initialization with another service as parameter.
        /** @var ArrayObject $arrayService */
        $arrayService = $adapter->get('special');
        $this->assertInstanceOf(ArrayObject::class, $arrayService);
        $this->assertTrue($arrayService->offsetExists('date'));
        $this->assertInstanceOf(DateTime::class, $arrayService->offsetGet('date'));
        $this->assertEquals('2016-04-05 01:02:03', $arrayService->offsetGet('date')->format('Y-m-d H:i:s'));

        try {
            $adapter->registerServiceInstance('custom_identifier', 'not object');
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1001, $exception->getCode());
        }

        // With get a non registered service, will be able to test the registerServiceToContainer() method's exception
        $this->expectException(InvalidArgumentException::class);
        $adapter->get('something-not-existing');
    }

    /**
     * Tests scalar argument
     */
    public function testScalarArgument()
    {
        $keyData = DateTime::class;

        $adapter = new BaseAdapter($this->config);
        $adapter->registerModuleServices('OtherApp');

        /** @var EmptyEntity $actualDate */
        $actualObject = $adapter->get('aliasWithReference');
        $this->assertInstanceOf(EmptyService::class, $actualObject);
        $this->assertInstanceOf(DateTime::class, $actualObject->getTheKey());

        try {
            // In the config this reference belongs to a service which has a parameter that is not marked as literal
            // but the alias cannot be identified as a class/service, so it must throw an error. This is the expected
            // behavior, and not the one in the Symfony adapter...
            $adapter->get('aliasWithFalseReference');
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        }


        /** @var EmptyEntity $actualDate */
        $actualObject = $adapter->get('aliasWithLiteral');
        $this->assertInstanceOf(EmptyService::class, $actualObject);
        $this->assertInternalType('string', $actualObject->getTheKey());
        $this->assertSame($keyData, $actualObject->getTheKey());
    }

    /**
     * Tests error
     */
    public function testInstantiateError()
    {
        $adapter = new BaseAdapter($this->config);
        $adapter->registerModuleServices('Website');

        try {
            $adapter->get('ThisWillHurt');
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        }
    }
}
