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

use DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\User\UserGroupEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class UserGroupEntityTest.
 */
class UserGroupEntityTest extends TestCase
{
    /** @var string */
    private $testTime = '2016-04-26 23:21:00';

    /**
     * Tests if the UserGroupEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $entity = new UserGroupEntity();

        $this->assertInstanceOf(DataEntityInterface::class, $entity);

        $entity->setUserGroupId(123);
        $this->assertSame($entity->getUserGroupId(), $entity->getKeyData());

        $expectedKey = 567;
        $entity->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $entity->getUserGroupId());
        $this->assertSame($expectedKey, $entity->getKeyData());
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        $dateTest = new DateTime($this->testTime);

        return [
            ['userGroupId', 1, 1, false],
            ['userGroupId', 'someId', 'someId', false],
            ['name','some name','some name', false],
            ['title','some title','some title', false],
            ['description','some description','some description', false],
            ['isReadOnly',1, true, true],
            ['dateCreated', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }

    /**
     * Tests if the UserGroupEntity instance has any property preset.
     *
     * @param string $attribute
     *
     * @dataProvider dataProvider
     */
    public function testNoInitValues($attribute)
    {
        $entity = new UserGroupEntity();

        $this->assertClassHasAttribute($attribute, UserGroupEntity::class);
        $this->assertAttributeEmpty($attribute, $entity);
    }

    /**
     * Tests if the UserGroupEntity has a specific setter method and it sets the value with the correct type.
     *
     * @param string $attribute
     * @param mixed  $parameter
     * @param mixed  $expectedData
     * @param bool   $typeCheck
     *
     * @dataProvider dataProvider
     */
    public function testSetters($attribute, $parameter, $expectedData, $typeCheck)
    {
        $entity = new UserGroupEntity();
        $method = 'set' . ucfirst(preg_replace('/^is/', '', $attribute));

        $this->assertTrue(method_exists($entity, $method));

        $entity->{$method}($parameter);
        $this->assertAttributeEquals($expectedData, $attribute, $entity);

        if ($typeCheck) {
            if (is_bool($expectedData)) {
                $this->assertAttributeInternalType('boolean', $attribute, $entity);
            } else {
                $this->assertAttributeInstanceOf(DateTime::class, $attribute, $entity);
            }
        }
    }

    /**
     * Tests if the UserGroupEntity has a specific getter method and it gets the value with the correct type.
     *
     * @param string $attribute
     * @param mixed  $parameter
     * @param mixed  $expectedData
     * @param bool   $typeCheck
     *
     * @dataProvider dataProvider
     */
    public function testGetters($attribute, $parameter, $expectedData, $typeCheck)
    {
        $entity = new UserGroupEntity();
        $methodName = ucfirst(preg_replace('/^is/', '', $attribute));
        $setMethod = 'set' . $methodName;
        $getMethod = 'get' . $methodName;

        $this->assertTrue(method_exists($entity, $setMethod));
        $this->assertTrue(method_exists($entity, $getMethod));

        $entity->{$setMethod}($parameter);
        $actualData = $entity->{$getMethod}();

        $this->assertEquals($expectedData, $actualData);

        if ($typeCheck) {
            if (is_bool($expectedData)) {
                $this->assertInternalType('boolean', $actualData);
            } else {
                $this->assertInstanceOf(DateTime::class, $actualData);
            }
        }
    }
}
