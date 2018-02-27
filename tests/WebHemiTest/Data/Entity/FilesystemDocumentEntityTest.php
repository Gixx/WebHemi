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

use WebHemi\Data\Entity\FilesystemDocumentEntity;
use WebHemi\DateTime;

/**
 * Class FilesystemDocumentEntityTest
 */
class FilesystemDocumentEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_filesystem_document' => 1,
        'fk_parent_revision' => 2,
        'fk_author' => 2,
        'content_revision' => 1,
        'content_lead' => 'test lead',
        'content_body' => 'test body',
        'date_created' => '2016-04-26 23:21:19',
        'date_modified' => '2016-04-26 23:21:19',
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getFilesystemDocumentId' => 1,
        'getParentRevisionId' => 2,
        'getAuthorId' => 2,
        'getContentRevision' => 1,
        'getContentLead' => 'test lead',
        'getContentBody' => 'test body',
        'getDateCreated' => null,
        'getDateModified' => null,
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setFilesystemDocumentId' => 1,
        'setParentRevisionId' => 2,
        'setAuthorId' => 2,
        'setContentRevision' => 1,
        'setContentLead' => 'test lead',
        'setContentBody' => 'test body',
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

        $this->entity = new FilesystemDocumentEntity();
    }
}
