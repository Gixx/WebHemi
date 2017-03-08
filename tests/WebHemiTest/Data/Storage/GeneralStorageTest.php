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
namespace WebHemiTest\Data\Storage;

use InvalidArgumentException;
use Prophecy\Argument;
use WebHemi\Data\ConnectorInterface as DataAdapterInterface;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemiTest\TestService\EmptyStorage;
use WebHemiTest\TestService\EmptyEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class GeneralStorageTest. It tests the AbstractDataStorage's methods mostly.
 */
class GeneralStorageTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractStorage
     */
    public function testAbstractClassMethods()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn($defaultAdapter->reveal());

        $dataEntity = new EmptyEntity();

        /** @var DataAdapterInterface $defaultAdapterInstance */
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
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getConnector());
        $this->assertFalse($defaultAdapterInstance === $storage->getConnector());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(EmptyEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Tests saveEntity() method.
     */
    public function testSaveEntity()
    {
        $randNewId = rand(5, 100);

        // create a user with no Id.
        $userEntity = new UserEntity();
        $userEntity->setUserName('test')
            ->setEmail('test@foo.org');

        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn($defaultAdapter->reveal());
        $defaultAdapter->saveData(Argument::any(), Argument::type('array'))->will(
            function ($args) use ($randNewId) {
                if (is_null($args[0])) {
                    return $randNewId;
                } else {
                    return $args[0];
                }
            }
        );
        $defaultAdapter->getData(Argument::type('int'))->will(
            function ($args) use ($userEntity) {
                return [
                    'id_user' => $args[0],
                    'username' => $userEntity->getUserName(),
                    'email' => $userEntity->getEmail(),
                    'password' => 'some password',
                    'hash' => md5('salt'),
                    'is_active' => 1,
                    'is_enabled' => 1,
                    'date_created' => '2016-11-11 11:11:11',
                    'date_modified' => null
                ];
            }
        );

        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $storage = new UserStorage($defaultAdapterInstance, $userEntity);

        // save new entity assumes it will have a new ID.
        $actualResult = $storage->saveEntity($userEntity);
        $this->assertInstanceOf(UserStorage::class, $actualResult);
        $this->assertSame($randNewId, $userEntity->getKeyData());
        $this->assertSame('2016-11-11 11:11:11', $userEntity->getDateCreated()->format('Y-m-d H:i:s'));

        $this->expectException(InvalidArgumentException::class);
        $otherEntity = new EmptyEntity();
        $storage->saveEntity($otherEntity);
    }
}
