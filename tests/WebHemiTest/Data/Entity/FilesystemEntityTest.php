<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Data\Entity;

use WebHemi\Data\Entity\FilesystemEntity;
use WebHemi\DateTime;

/**
 * Class FilesystemEntityTest
 */
class FilesystemEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_filesystem' => 1,
        'fk_application' => 1,
        'fk_category' => 1,
        'fk_parent_node' => 1,
        'fk_filesystem_document' => 1,
        'fk_filesystem_file' => 1,
        'fk_filesystem_directory' => 1,
        'fk_filesystem_link' => 1,
        'path' => 'test path',
        'basename' => 'test basename',
        'uri' => 'test uri',
        'title' => 'test title',
        'description' => 'test description',
        'is_hidden' => 0,
        'is_read_only' => 1,
        'is_deleted' => 0,
        'date_created' => '2016-04-26 23:21:19',
        'date_modified' => '2016-04-26 23:21:19',
        'date_published' => '2016-04-26 23:21:19',
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getFilesystemId' => 1,
        'getApplicationId' => 1,
        'getCategoryId' => 1,
        'getParentNodeId' => 1,
        'getFilesystemDocumentId' => 1,
        'getFilesystemFileId' => 1,
        'getFilesystemDirectoryId' => 1,
        'getFilesystemLinkId' => 1,
        'getPath' => 'test path',
        'getBaseName' => 'test basename',
        'getUri' => 'test uri',
        'getTitle' => 'test title',
        'getDescription' => 'test description',
        'getIsHidden' => false,
        'getIsReadOnly' => true,
        'getIsDeleted' => false,
        'getDateCreated' => null,
        'getDateModified' => null,
        'getDatePublished' => null,
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setFilesystemId' => 1,
        'setApplicationId' => 1,
        'setCategoryId' => 1,
        'setParentNodeId' => 1,
        'setFilesystemDocumentId' => 1,
        'setFilesystemFileId' => 1,
        'setFilesystemDirectoryId' => 1,
        'setFilesystemLinkId' => 1,
        'setPath' => 'test path',
        'setBaseName' => 'test basename',
        'setUri' => 'test uri',
        'setTitle' => 'test title',
        'setDescription' => 'test description',
        'setIsHidden' => false,
        'setIsReadOnly' => true,
        'setIsDeleted' => false,
        'setDateCreated' => null,
        'setDateModified' => null,
        'setDatePublished' => null,
    ];

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $dateTime = new DateTime('2016-04-26 23:21:19');

        $this->expectedGetters['getDateCreated'] = $dateTime;
        $this->expectedGetters['getDateModified'] = $dateTime;
        $this->expectedGetters['getDatePublished'] = $dateTime;
        $this->expectedSetters['setDateCreated'] = $dateTime;
        $this->expectedSetters['setDateModified'] = $dateTime;
        $this->expectedSetters['setDatePublished'] = $dateTime;

        $this->entity = new FilesystemEntity();
    }

    /**
     * Data provider for the testGetType();
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            [null, null, null, null, FilesystemEntity::TYPE_DOCUMENT],
            [1, null, null, null, FilesystemEntity::TYPE_DOCUMENT],
            [null, 1, null, null, FilesystemEntity::TYPE_BINARY],
            [23, 3, null, null, FilesystemEntity::TYPE_BINARY],
            [null, 43, 32, null, FilesystemEntity::TYPE_BINARY],
            [null, 325, null, 543, FilesystemEntity::TYPE_BINARY],
            [3, 45, 343, null, FilesystemEntity::TYPE_BINARY],
            [78, 23, null, 2356, FilesystemEntity::TYPE_BINARY],
            [23, 88, 463, 5345, FilesystemEntity::TYPE_BINARY],
            [null, null, 1, null, FilesystemEntity::TYPE_DIRECTORY],
            [1, null, 4, null, FilesystemEntity::TYPE_DIRECTORY],
            [null, null, 4, 2, FilesystemEntity::TYPE_DIRECTORY],
            [2, null, 567, 32, FilesystemEntity::TYPE_DIRECTORY],
            [null, null, null, 3, FilesystemEntity::TYPE_SYMLINK],
            [2, null, null, 32, FilesystemEntity::TYPE_SYMLINK],
        ];
    }

    /**
     * Tests the getType method
     *
     * @param null|int $docId
     * @param null|int $fileId
     * @param null|int $dirId
     * @param null|int $linkId
     * @param string $expectedResult
     *
     * @dataProvider dataProvider
     */
    public function testGetType($docId, $fileId, $dirId, $linkId, $expectedResult)
    {
        $this->testData['fk_filesystem_document'] = $docId;
        $this->testData['fk_filesystem_file'] = $fileId;
        $this->testData['fk_filesystem_directory'] = $dirId;
        $this->testData['fk_filesystem_link'] = $linkId;

        $entity = new FilesystemEntity();
        $entity->fromArray($this->testData);

        $this->assertSame($expectedResult, $entity->getType());
    }
}
