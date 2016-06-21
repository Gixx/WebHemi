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
namespace WebHemiTest\DataStorage;

use Prophecy\Argument;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemiTest\Fixtures\EmptyStorage;
use WebHemiTest\Fixtures\EmptyEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralStorageTest. It tests the AbstractDataStorage's methods mostly.
 */
class GeneralStorageTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @covers \WebHemi\DataStorage\AbstractDataStorage
     */
    public function testAbstractClassMethods()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        $dataEntity = new EmptyEntity();

        $defaultAdapterInstance = $defaultAdapter->reveal();

        $storage = new EmptyStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(EmptyStorage::class, $storage);
        $this->assertFalse($storage->initialized());

        // If no default values for the properties are given, then these should be empty after the init().
        $this->assertAttributeEmpty('dataGroup', $storage);
        $this->assertAttributeEmpty('idKey', $storage);

        $storage->setDataGroup('someGroup');
        $storage->setIdKey('someId');
        $storage->init();

        $this->assertTrue($storage->initialized());
        $this->assertAttributeEquals('someGroup', 'dataGroup', $storage);
        $this->assertAttributeEquals('someId', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(EmptyEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }
}
