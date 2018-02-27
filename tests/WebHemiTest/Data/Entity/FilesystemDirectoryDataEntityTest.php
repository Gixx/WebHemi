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

use WebHemi\Data\Entity\FilesystemDirectoryDataEntity;

/**
 * Class FilesystemDirectoryDataEntityTest
 */
class FilesystemDirectoryDataEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_filesystem' => 1,
        'id_application' => 1,
        'id_filesystem_directory' => 1,
        'description' => 'test description',
        'directory_type' => 'test dir type',
        'proxy' => 'test proxy',
        'is_autoindex' => 1,
        'path' => 'test path',
        'basename' => 'test basenane',
        'uri' => 'test uri',
        'type' => 'test type',
        'title' => 'test title',
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getFilesystemId' => 1,
        'getApplicationId' => 1,
        'getFilesystemDirectoryId' => 1,
        'getDescription' => 'test description',
        'getDirectoryType' => 'test dir type',
        'getProxy' => 'test proxy',
        'getIsAutoIndex' => true,
        'getPath' => 'test path',
        'getBaseName' => 'test basenane',
        'getUri' => 'test uri',
        'getType' => 'test type',
        'getTitle' => 'test title',
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setFilesystemId' => 1,
        'setApplicationId' => 1,
        'setFilesystemDirectoryId' => 1,
        'setDescription' => 'test description',
        'setDirectoryType' => 'test dir type',
        'setProxy' => 'test proxy',
        'setIsAutoIndex' => true,
        'setPath' => 'test path',
        'setBaseName' => 'test basenane',
        'setUri' => 'test uri',
        'setType' => 'test type',
        'setTitle' => 'test title',
    ];

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->entity = new FilesystemDirectoryDataEntity();
    }
}
