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

use InvalidArgumentException;
use RuntimeException;
use ArrayObject;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemiTest\TestService\EmptyDependencyInjectionContainer;
use WebHemiTest\TestService\EmptyService;
use WebHemiTest\TestService\TestMiddleware;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class GeneralAdapterTest.
 */
class GeneralAdapterTest extends TestCase
{
    /** @var Config */
    private $config;

    use AssertTrait;

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
     * Tests the registerModuleServices() method.
     */
    public function testRegisterModuleServices()
    {
        $serviceCount = 0;
        $adapter = new EmptyDependencyInjectionContainer($this->config);

        $this->assertAttributeCount($serviceCount, 'serviceLibrary', $adapter);

        $globalServices = $this->config->getData('dependencies/Global');
        $serviceCount += count(array_keys($globalServices));
        $adapter->registerModuleServices('Global');
        $this->assertAttributeCount($serviceCount, 'serviceLibrary', $adapter);

        $websiteServices = $this->config->getData('dependencies/Website');
        $serviceCount += count(array_keys($websiteServices));
        $adapter->registerModuleServices('Website');
        $this->assertAttributeCount($serviceCount, 'serviceLibrary', $adapter);

        $someAppServices = $this->config->getData('dependencies/SomeApp');
        $serviceCount += count(array_keys($someAppServices));
        $adapter->registerModuleServices('SomeApp');
        $this->assertAttributeCount($serviceCount, 'serviceLibrary', $adapter);

        $this->expectException(InvalidArgumentException::class);
        $adapter->registerModuleServices('NonExistingModule');
    }

    /**
     * Tests the serviceIsInitialized() method.
     */
    public function testServiceIsInitialized()
    {
        $adapter = new EmptyDependencyInjectionContainer($this->config);
        $adapter->registerModuleServices('SomeApp');

        $this->assertTrue($adapter->has('moreAlias'));
        $this->assertFalse($adapter->callServiceIsInitialized('moreAlias'));

        $adapter->get('moreAlias');
        $this->assertTrue($adapter->callServiceIsInitialized('moreAlias'));
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['alias', ArrayObject::class, ['input' => ['something', 2]], false],
            ['otherAlias', ArrayObject::class, ['input' => ['something', 2]], false],
            ['moreAlias', ArrayObject::class, ['input' => ['something', 2]], true],
            ['lastInherit', ArrayObject::class, ['input' => ['something', 2]], true],
            [EmptyService::class, EmptyService::class, ['input' => ['something', 2]], false],
        ];
    }

    /**
     * Tests how the adapter resolves the inheritance
     *
     * @param string $identifier
     * @param string $expectedClass
     *
     * @dataProvider dataProvider
     */
    public function testInheritance($identifier, $expectedClass, $expectedArgs, $expectedShared)
    {
        $moduleName = 'SomeApp';
        $adapter = new EmptyDependencyInjectionContainer($this->config);
        $adapter->registerModuleServices($moduleName);

        $actualClass = $adapter->callResolveServiceClassName($identifier, $moduleName);
        $actualArgs = $adapter->callResolveServiceArguments($identifier, $moduleName);
        $actualShared = $adapter->callResolveShares($identifier, $moduleName);

        $this->assertSame($expectedClass, $actualClass);
        $this->assertArraysAreSimilar($expectedArgs, $actualArgs);
        $this->assertSame($expectedShared, $actualShared);
    }

    /**
     * Tests the resolveServiceClassName() method.
     */
    public function testResolveServiceClassName()
    {
        $moduleName = 'Global';
        $adapter = new EmptyDependencyInjectionContainer($this->config);
        $adapter->registerModuleServices($moduleName);

        $expectedResult = TestMiddleware::class;

        $actualResult = $adapter->callResolveServiceClassName('pipe4', $moduleName);
        $this->assertSame($expectedResult, $actualResult);

        $this->expectException(RuntimeException::class);
        $adapter->callResolveServiceClassName('non-registered-service', $moduleName);
    }
}
