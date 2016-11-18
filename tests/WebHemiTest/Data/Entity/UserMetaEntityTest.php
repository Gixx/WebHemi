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
namespace WebHemiTest\Data\Entity;

use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserMetaEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class UserMetaEntityTest.
 */
class UserMetaEntityTest extends TestCase
{
    /**
     * Tests if the UserMetaEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $entity = new UserMetaEntity();

        $this->assertInstanceOf(DataEntityInterface::class, $entity);

        $entity->setUserMetaId(123);
        $this->assertSame($entity->getUserMetaId(), $entity->getKeyData());

        $expectedKey = 567;
        $entity->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $entity->getUserMetaId());
        $this->assertSame($expectedKey, $entity->getKeyData());
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['userMetaId', 1, 1],
            ['userMetaId', 'someId', 'someId'],
            ['userId', 2, 2],
            ['userId', 'someUserId', 'someUserId'],
            ['metaKey', 'someKey', 'someKey'],
            ['metaData', 'someData', 'someData'],
        ];
    }

    /**
     * Tests if the UserMetaEntity instance has any property preset.
     *
     * @param string $attribute
     *
     * @dataProvider dataProvider
     */
    public function testNoInitValues($attribute)
    {
        $entity = new UserMetaEntity();

        $this->assertClassHasAttribute($attribute, UserMetaEntity::class);
        $this->assertAttributeEmpty($attribute, $entity);
    }

    /**
     * Tests if the UserMetaEntity has a specific setter method and it sets the value with the correct type.
     *
     * @param string $attribute
     * @param mixed  $parameter
     * @param mixed  $expectedData
     *
     * @dataProvider dataProvider
     */
    public function testSetters($attribute, $parameter, $expectedData)
    {
        $entity = new UserMetaEntity();
        $method = 'set' . ucfirst($attribute);

        $this->assertTrue(method_exists($entity, $method));

        $entity->{$method}($parameter);
        $this->assertAttributeEquals($expectedData, $attribute, $entity);
    }

    /**
     * Tests if the UserMetaEntity has a specific getter method and it gets the value with the correct type.
     *
     * @param string $attribute
     * @param mixed  $parameter
     * @param mixed  $expectedData
     *
     * @dataProvider dataProvider
     */
    public function testGetters($attribute, $parameter, $expectedData)
    {
        $entity = new UserMetaEntity();
        $methodName = ucfirst($attribute);
        $setMethod = 'set' . $methodName;
        $getMethod = 'get' . $methodName;

        $this->assertTrue(method_exists($entity, $setMethod));
        $this->assertTrue(method_exists($entity, $getMethod));

        $entity->{$setMethod}($parameter);
        $actualData = $entity->{$getMethod}();

        $this->assertEquals($expectedData, $actualData);
    }
}
