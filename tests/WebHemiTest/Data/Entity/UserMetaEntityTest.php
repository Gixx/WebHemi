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

use WebHemi\Data\EntityInterface as DataEntityInterface;
use WebHemi\Data\Entity\User\UserMetaEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class UserMetaEntityTest.
 */
class UserMetaEntityTest extends AbstractEntityTestClass
{
    /**
     * Sets up the entity class name and instance for the test.
     */
    public function setUpEntity()
    {
        $this->entityInstance = new UserMetaEntity();
        $this->entityClass = UserMetaEntity::class;
    }

    /**
     * Tests if the UserMetaEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataEntityInterface::class, $this->entityInstance);

        $this->entityInstance->setUserMetaId(123);
        $this->assertSame($this->entityInstance->getUserMetaId(), $this->entityInstance->getKeyData());

        $expectedKey = 567;
        $this->entityInstance->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $this->entityInstance->getUserMetaId());
        $this->assertSame($expectedKey, $this->entityInstance->getKeyData());
    }

    /**
     * Data provider for the tests.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['userMetaId', 1, 1, false],
            ['userId', 2, 2, false],
            ['metaKey', 'someKey', 'someKey', false],
            ['metaData', 'someData', 'someData', false],
        ];
    }
}
