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
namespace WebHemiTest\Data\Entity;

use WebHemi\DateTime;
use WebHemi\Data\Entity\DataEntityInterface;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class ResourceEntityTest.
 */
class ResourceEntityTest extends TestCase
{
    /** @var string */
    private $testTime = '2016-04-26 23:21:00';

    /**
     * Tests if the ResourceEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $entity = new ResourceEntity();

        $this->assertInstanceOf(DataEntityInterface::class, $entity);

        $entity->setResourceId(123);
        $this->assertSame($entity->getResourceId(), $entity->getKeyData());

        $expectedKey = 567;
        $entity->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $entity->getResourceId());
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
            ['resourceId', 1, 1, false],
            ['name','Some name','Some name', false],
            ['title','some title','some title', false],
            ['description','some description','some description', false],
            ['isReadOnly',true, true, true],
            ['dateCreated', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }

    /**
     * Tests if the ResourceEntity instance has any property preset.
     *
     * @param string $attribute
     *
     * @dataProvider dataProvider
     */
    public function testNoInitValues($attribute)
    {
        $entity = new ResourceEntity();

        $this->assertClassHasAttribute($attribute, ResourceEntity::class);
        $this->assertAttributeEmpty($attribute, $entity);
    }

    /**
     * Tests if the ResourceEntity has a specific setter method and it sets the value with the correct type.
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
        $entity = new ResourceEntity();
        $method = 'set' . ucfirst(preg_replace('/^is/', '', $attribute));

        $this->assertTrue(method_exists($entity, $method));

        $entity->{$method}($parameter);
        $this->assertAttributeEquals($expectedData, $attribute, $entity);

        if ($typeCheck) {
            if (is_bool($expectedData)) {
                $this->assertAttributeInternalType('boolean', $attribute, $entity);
            } elseif (is_null($expectedData)) {
                $this->assertAttributeSame(null, $attribute, $entity);
            } else {
                $this->assertAttributeInstanceOf(DateTime::class, $attribute, $entity);
            }
        }
    }

    /**
     * Tests if the ResourceEntity has a specific getter method and it gets the value with the correct type.
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
        $entity = new ResourceEntity();
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
            } elseif (is_null($expectedData)) {
                $this->assertAttributeSame(null, $attribute, $entity);
            } else {
                $this->assertInstanceOf(DateTime::class, $actualData);
            }
        }
    }
}
