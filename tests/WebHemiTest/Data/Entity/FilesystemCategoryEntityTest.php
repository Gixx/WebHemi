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
use WebHemi\Data\Entity\Filesystem\FilesystemCategoryEntity;

/**
 * Class FilesystemCategoryEntityTest.
 */
class FilesystemCategoryEntityTest extends AbstractEntityTestClass
{
    /**
     * Sets up the entity class name and instance for the test.
     */
    public function setUpEntity()
    {
        $this->entityInstance = new FilesystemCategoryEntity();
        $this->entityClass = FilesystemCategoryEntity::class;
    }

    /**
     * Tests if the FilesystemEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataEntityInterface::class, $this->entityInstance);

        $this->entityInstance->setFilesystemCategoryId(123);
        $this->assertSame($this->entityInstance->getFilesystemCategoryId(), $this->entityInstance->getKeyData());

        $expectedKey = 567;
        $this->entityInstance->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $this->entityInstance->getFilesystemCategoryId());
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
            ['filesystemCategoryId', 1, 1, false],
            ['applicationId', 1, 1, false],
            ['applicationId', null, null, true],
            ['name','some name','some name', false],
            ['title','some title','some title', false],
            ['description','some description','some description', false],
            ['dateCreated', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }
}
