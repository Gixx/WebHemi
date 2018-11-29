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

use WebHemi\Data\Entity\FilesystemCategoryEntity;
use WebHemi\DateTime;

/**
 * Class FilesystemCategoryEntityTest
 */
class FilesystemCategoryEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_filesystem_category' => 1,
        'fk_application' => 1,
        'name' => 'test name',
        'title' => 'test title',
        'description' => 'test description',
        'item_order' => 'test order',
        'date_created' => '2016-04-26 23:21:19',
        'date_modified' => '2016-04-26 23:21:19',
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getFilesystemCategoryId' => 1,
        'getApplicationId' => 1,
        'getName' => 'test name',
        'getTitle' => 'test title',
        'getDescription' => 'test description',
        'getItemOrder' => 'test order',
        'getDateCreated' => null,
        'getDateModified' => null,
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setFilesystemCategoryId' => 1,
        'setApplicationId' => 1,
        'setName' => 'test name',
        'setTitle' => 'test title',
        'setDescription' => 'test description',
        'setItemOrder' => 'test order',
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

        $this->entity = new FilesystemCategoryEntity();
    }
}
