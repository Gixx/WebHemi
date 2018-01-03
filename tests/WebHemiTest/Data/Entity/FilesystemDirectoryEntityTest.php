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
use WebHemi\Data\Entity\Filesystem\FilesystemDirectoryEntity;

/**
 * Class FilesystemDirectoryEntityTest.
 */
class FilesystemDirectoryEntityTest extends AbstractEntityTestClass
{
    /**
     * Sets up the entity class name and instance for the test.
     */
    public function setUpEntity()
    {
        $this->entityInstance = new FilesystemDirectoryEntity();
        $this->entityClass = FilesystemDirectoryEntity::class;
    }

    /**
     * Tests if the FilesystemEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataEntityInterface::class, $this->entityInstance);

        $this->entityInstance->setFilesystemDirectoryId(123);
        $this->assertSame($this->entityInstance->getFilesystemDirectoryId(), $this->entityInstance->getKeyData());

        $expectedKey = 567;
        $this->entityInstance->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $this->entityInstance->getFilesystemDirectoryId());
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
            ['filesystemDirectoryId', 1, 1, false],
            ['description','some description','some description', false],
            ['directoryType','some type','some type', false],
            ['proxy','some proxy','some proxy', false],
            ['isAutoIndex',true, true, true],
            ['dateCreated', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }
}
