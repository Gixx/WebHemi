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
use WebHemi\Data\Entity\Filesystem\FilesystemEntity;

/**
 * Class FilesystemEntityTest.
 */
class FilesystemEntityTest extends AbstractEntityTestClass
{
    /**
     * Sets up the entity class name and instance for the test.
     */
    public function setUpEntity()
    {
        $this->entityInstance = new FilesystemEntity();
        $this->entityClass = FilesystemEntity::class;
    }

    /**
     * Tests if the FilesystemEntity implements the DataEntityInterface.
     */
    public function testInstance()
    {
        $this->assertInstanceOf(DataEntityInterface::class, $this->entityInstance);

        $this->entityInstance->setFilesystemId(123);
        $this->assertSame($this->entityInstance->getFilesystemId(), $this->entityInstance->getKeyData());

        $expectedKey = 567;
        $this->entityInstance->setKeyData($expectedKey);
        $this->assertSame($expectedKey, $this->entityInstance->getFilesystemId());
        $this->assertSame($expectedKey, $this->entityInstance->getKeyData());
    }

    /**
     * Tests the getType() method
     */
    public function testGetType()
    {
        $entity = new FilesystemEntity();

        $this->assertSame(FilesystemEntity::TYPE_DOCUMENT, $entity->getType());

        $entity->setDocumentId(1);
        $this->assertSame(FilesystemEntity::TYPE_DOCUMENT, $entity->getType());
        $this->assertEmpty($entity->getFileId());
        $this->assertEmpty($entity->getLinkId());
        $this->assertEmpty($entity->getDirectoryId());

        $entity->setDirectoryId(1);
        $this->assertSame(FilesystemEntity::TYPE_DIRECTORY, $entity->getType());
        $this->assertEmpty($entity->getFileId());
        $this->assertEmpty($entity->getLinkId());
        $this->assertEmpty($entity->getDocumentId());

        $entity->setFileId(1);
        $this->assertSame(FilesystemEntity::TYPE_BINARY, $entity->getType());
        $this->assertEmpty($entity->getDirectoryId());
        $this->assertEmpty($entity->getLinkId());
        $this->assertEmpty($entity->getDocumentId());

        $entity->setLinkId(1);
        $this->assertSame(FilesystemEntity::TYPE_SYMLINK, $entity->getType());
        $this->assertEmpty($entity->getDirectoryId());
        $this->assertEmpty($entity->getFileId());
        $this->assertEmpty($entity->getDocumentId());
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
            ['filesystemId', 1, 1, false],
            ['applicationId', 1, 1, false],
            ['applicationId', null, null, true],
            ['categoryId', 1, 1, false],
            ['categoryId', null, null, true],
            ['parentId', 1, 1, false],
            ['parentId', null, null, true],
            ['documentId', 1, 1, false],
            ['documentId', null, null, true],
            ['fileId', 1, 1, false],
            ['fileId', null, null, true],
            ['linkId', 1, 1, false],
            ['linkId', null, null, true],
            ['directoryId', 1, 1, false],
            ['directoryId', null, null, true],
            ['path','/some/path','/some/path', false],
            ['baseName','file.html','file.html', false],
            ['title','some title','some title', false],
            ['description','some description','some description', false],
            ['isHidden',true, true, true],
            ['isReadOnly',true, true, true],
            ['isDeleted',true, true, true],
            ['dateCreated', $dateTest, $dateTest, true],
            ['datePublished', $dateTest, $dateTest, true],
            ['dateModified', $dateTest, $dateTest, true],
        ];
    }
}
