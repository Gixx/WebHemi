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
namespace WebHemiTest\Data\Storage;

use Prophecy\Argument;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Storage\User\UserMetaStorage;
use WebHemi\Data\Entity\User\UserMetaEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class UserMetaStorageTest.
 */
class UserMetaStorageTest extends TestCase
{
    private $defaultAdapter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        $this->defaultAdapter = $defaultAdapter;
    }

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Storage\AbstractDataStorage
     */
    public function testStorageInit()
    {
        $dataEntity = new UserMetaEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserMetaStorage($defaultAdapterInstance, $dataEntity);

        $this->assertInstanceOf(UserMetaStorage::class, $storage);
        $this->assertTrue($storage->initialized());

        $this->assertAttributeEquals('webhemi_user_meta', 'dataGroup', $storage);
        $this->assertAttributeEquals('id_user_meta', 'idKey', $storage);

        // objects are not the same --> cloned.
        $this->assertInstanceOf(DataAdapterInterface::class, $storage->getDataAdapter());
        $this->assertFalse($defaultAdapterInstance === $storage->getDataAdapter());

        // objects are not the same --> cloned.
        $this->assertInstanceOf(UserMetaEntity::class, $storage->createEntity());
        $this->assertFalse($dataEntity === $storage->createEntity());
    }

    /**
     * Test the getUserMetaById method.
     */
    public function testGetUserMetaById()
    {
        $data = [
            [
                'id_user_meta' => 1,
                'fk_user' => 1,
                'meta_key' => 'body',
                'meta_data' => 'sporty',
            ],
            [
                'id_user_meta' => 2,
                'fk_user' => 1,
                'meta_key' => 'phone',
                'meta_data' => '+49 176 1234 5678',
            ],
        ];

        $this->defaultAdapter
            ->getData(Argument::type('int'))
            ->will(
                function ($args) use ($data) {
                    if (in_array($args[0], [1, 2])) {
                        return $data[($args[0] - 1)];
                    }

                    return false;
                }
            );

        $dataEntity = new UserMetaEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserMetaStorage($defaultAdapterInstance, $dataEntity);


        /** @var UserMetaEntity $actualResult */
        $actualResult = $storage->getUserMetaById(1);
        $this->assertInstanceOf(UserMetaEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[0]['meta_key'], $actualResult->getMetaKey());
        $this->assertEquals($data[0]['meta_data'], $actualResult->getMetaData());

        /** @var UserMetaEntity $actualResult */
        $actualResult = $storage->getUserMetaById(2);
        $this->assertInstanceOf(UserMetaEntity::class, $actualResult);
        $this->assertFalse($dataEntity === $actualResult);
        $this->assertEquals($data[1]['meta_key'], $actualResult->getMetaKey());
        $this->assertEquals($data[1]['meta_data'], $actualResult->getMetaData());

        /** @var bool $actualResult */
        $actualResult = $storage->getUserMetaById(3);
        $this->assertFalse($actualResult);
    }

    /**
     * Test the getUserByEmail method.
     */
    public function testGetUserMetaForUserId()
    {
        $data = [
            [
                'id_user_meta' => 1,
                'fk_user' => 1,
                'meta_key' => 'body',
                'meta_data' => 'sporty',
            ],
            [
                'id_user_meta' => 2,
                'fk_user' => 1,
                'meta_key' => 'phone',
                'meta_data' => '+49 176 1234 5678',
            ],
        ];

        $this->defaultAdapter
            ->getDataSet(Argument::type('array'))
            ->will(
                function ($args) use ($data) {
                    if ($args[0]['fk_user'] == 1) {
                        return $data;
                    }

                    return false;
                }
            );

        $dataEntity = new UserMetaEntity();
        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $this->defaultAdapter->reveal();
        $storage = new UserMetaStorage($defaultAdapterInstance, $dataEntity);


        /** @var UserMetaEntity[] $actualResult */
        $actualResult = $storage->getUserMetaForUserId(1);
        $this->assertInternalType('array', $actualResult);
        $this->assertSame(2, count($actualResult));
        $this->assertInstanceOf(UserMetaEntity::class, $actualResult[0]);
        $this->assertInstanceOf(UserMetaEntity::class, $actualResult[1]);
        $this->assertEquals('phone', $actualResult[1]->getMetaKey());
        $this->assertEquals('+49 176 1234 5678', $actualResult[1]->getMetaData());

        /** @var bool $actualResult */
        $actualResult = $storage->getUserMetaForUserId(2);
        $this->assertFalse($actualResult);
    }
}
