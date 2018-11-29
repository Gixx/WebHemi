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

use WebHemi\Data\Entity\FilesystemDirectoryEntity;
use WebHemi\DateTime;

/**
 * Class FilesystemDirectoryEntityTest
 */
class FilesystemDirectoryEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_filesystem_directory' => 1,
        'description' => 'test description',
        'directory_type' => 'test dir type',
        'proxy' => 'test proxy',
        'is_autoindex' => 1,
        'date_created' => '2016-04-26 23:21:19',
        'date_modified' => '2016-04-26 23:21:19',
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getFilesystemDirectoryId' => 1,
        'getDescription' => 'test description',
        'getDirectoryType' => 'test dir type',
        'getProxy' => 'test proxy',
        'getIsAutoIndex' => true,
        'getDateCreated' => null,
        'getDateModified' => null,
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setFilesystemDirectoryId' => 1,
        'setDescription' => 'test description',
        'setDirectoryType' => 'test dir type',
        'setProxy' => 'test proxy',
        'setIsAutoIndex' => true,
        'setDateCreated' => null,
        'setDateModified' => null,
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
        $this->expectedSetters['setDateCreated'] = $dateTime;
        $this->expectedSetters['setDateModified'] = $dateTime;

        $this->entity = new FilesystemDirectoryEntity();
    }
}
