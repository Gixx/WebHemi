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
use WebHemi\Data\EntityInterface as DataEntityInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;

/**
 * Class AbstractEntityTest
 */
abstract class AbstractEntityTestClass extends TestCase
{
    /** @var string */
    protected $testTime = '2016-04-26 23:21:00';
    /** @var DataEntityInterface */
    protected $entityInstance;
    /** @var string */
    protected $entityClass;

    /**
     * Sets up the entity class name and instance for the test.
     */
    abstract public function setUpEntity();

    /**
     * Tests if the give entity implements the DataEntityInterface.
     */
    abstract public function testInstance();

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    abstract public function dataProvider();

    /**
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->setUpEntity();

        if (!isset($this->entityInstance)
            || !isset($this->entityClass)
            || get_class($this->entityInstance) != $this->entityClass
            || !$this->entityInstance instanceof DataEntityInterface
        ) {
            throw new IncompleteTestError('The entity is not initialized');
        }
    }

    /**
     * Tests if the entity instance has any property preset.
     *
     * @param string $attribute
     *
     * @dataProvider dataProvider
     */
    public function testNoInitValues($attribute)
    {
        $this->assertClassHasAttribute($attribute, $this->entityClass, 'Attribute name: '.$attribute);
        $this->assertAttributeEmpty($attribute, $this->entityInstance, 'Attribute name: '.$attribute);
    }

    /**
     * Tests if the entity has a specific setter method and it sets the value with the correct type.
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
        $method = 'set' . ucfirst(preg_replace('/^is/', '', $attribute));

        $this->assertTrue(method_exists($this->entityInstance, $method));

        $this->entityInstance->{$method}($parameter);
        $this->assertAttributeEquals($expectedData, $attribute, $this->entityInstance);

        if ($typeCheck) {
            if (is_bool($expectedData)) {
                $this->assertAttributeInternalType('boolean', $attribute, $this->entityInstance);
            } elseif (is_null($expectedData)) {
                $this->assertAttributeInternalType('null', $attribute, $this->entityInstance);
            } else {
                $this->assertAttributeInstanceOf(DateTime::class, $attribute, $this->entityInstance);
            }
        }
    }

    /**
     * Tests if the entity has a specific getter method and it gets the value with the correct type.
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
        $methodName = ucfirst(preg_replace('/^is/', '', $attribute));
        $setMethod = 'set' . $methodName;
        $getMethod = 'get' . $methodName;

        $this->assertTrue(method_exists($this->entityInstance, $setMethod));
        $this->assertTrue(method_exists($this->entityInstance, $getMethod));

        $this->entityInstance->{$setMethod}($parameter);
        $actualData = $this->entityInstance->{$getMethod}();

        $this->assertEquals($expectedData, $actualData);

        if ($typeCheck) {
            if (is_bool($expectedData)) {
                $this->assertInternalType('boolean', $actualData);
            } elseif (is_null($expectedData)) {
                $this->assertNull($actualData);
            } else {
                $this->assertInstanceOf(DateTime::class, $actualData);
            }
        }
    }
}
