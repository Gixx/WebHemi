<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Data\Entity;

use WebHemi\DateTime;
use WebHemi\Data\EntityInterface as DataEntityInterface;
use WebHemi\Data\Entity\AccessManagement\PolicyEntity;

/**
 * Class PolicyEntityTest.
 */
class PolicyEntityTest extends AbstractEntityTestClass
{
    /**
     * Sets up the entity class name and instance for the test.
     */
    public function setUpEntity()
    {
        $this->entityInstance = new PolicyEntity();
        $this->entityClass = PolicyEntity::class;
    }

    /**
     * Tests if the PolicyEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataEntityInterface::class, $this->entityInstance);

        $this->entityInstance->setPolicyId(123);
        $this->assertSame($this->entityInstance->getPolicyId(), $this->entityInstance->getKeyData());

        $expectedKey = 567;
        $this->entityInstance->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $this->entityInstance->getPolicyId());
        $this->assertSame($expectedKey, $this->entityInstance->getKeyData());
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
            ['policyId', 1, 1, false],
            ['applicationId', 1, 1, false],
            ['applicationId', null, null, true],
            ['resourceId', 1, 1, false],
            ['resourceId', null, null, true],
            ['name','some name','some name', false],
            ['title','some title','some title', false],
            ['description','some description','some description', false],
            ['isReadOnly',true, true, true],
            ['dateCreated', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }
}
