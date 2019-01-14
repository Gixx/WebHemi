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

use DateTimeZone;
use InvalidArgumentException;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\DateTime;

/**
 * Class ApplicationEntityTest
 */
class ApplicationEntityTest extends AbstractEntityTestCase
{
    /** @var array  */
    protected $testData = [
        'id_application' => 1,
        'fk_domain' => 1,
        'path' => 'test path',
        'name' => 'test name',
        'title' => 'test title',
        'theme' => 'test theme',
        'locale' => 'en_GB',
        'timezone' => 'Europe/London',
        'introduction' => 'test introduction',
        'subject' => 'test subject',
        'description' => 'test description',
        'keywords' => 'test keywords',
        'copyright' => 'test copyright',
        'is_read_only' => 1,
        'is_enabled' => 0,
        'date_created' => '2016-04-26 23:21:19',
        'date_modified' => null
    ];

    /** @var array  */
    protected $expectedGetters = [
        'getApplicationId' => 1,
        'getDomainId' => 1,
        'getName' => 'test name',
        'getTitle' => 'test title',
        'getPath' => 'test path',
        'getTheme' => 'test theme',
        'getLocale' => 'en_GB',
        'getIntroduction' => 'test introduction',
        'getSubject' => 'test subject',
        'getDescription' => 'test description',
        'getKeywords' => 'test keywords',
        'getCopyright' => 'test copyright',
        'getTimeZone' => null,
        'getIsReadOnly' => true,
        'getIsEnabled' => false,
        'getDateCreated' => null,
        'getDateModified' => null
    ];

    /** @var array  */
    protected $expectedSetters = [
        'setApplicationId' => 1,
        'setDomainId' => 1,
        'setName' => 'test name',
        'setTitle' => 'test title',
        'setPath' => 'test path',
        'setTheme' => 'test theme',
        'setLocale' => 'en_GB',
        'setIntroduction' => 'test introduction',
        'setSubject' => 'test subject',
        'setDescription' => 'test description',
        'setKeywords' => 'test keywords',
        'setCopyright' => 'test copyright',
        'setTimeZone' => null,
        'setIsReadOnly' => true,
        'setIsEnabled' => false,
        'setDateCreated' => null
    ];

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $dateTimeZone = new DateTimeZone('Europe/London');
        $dateTime = new DateTime('2016-04-26 23:21:19');

        $this->expectedGetters['getTimeZone'] = $dateTimeZone;
        $this->expectedGetters['getDateCreated'] = $dateTime;

        $this->expectedSetters['setTimeZone'] = $dateTimeZone;
        $this->expectedSetters['setDateCreated'] = $dateTime;

        $this->entity = new ApplicationEntity();
    }

    /**
     * Test date modified setters and getters
     */
    public function testDateModified()
    {
        $entity = new ApplicationEntity();
        $this->assertEmpty($entity->getDateModified());

        $dateTime = new DateTime('2016-04-26 23:21:19');
        $entity->setDateModified($dateTime);

        $actualObject = var_export($entity->getDateModified(), true);
        $expectedObject = var_export($dateTime, true);
        $this->assertSame($expectedObject, $actualObject);
    }

    /**
     * Test invalid argument
     */
    public function testError()
    {
        $this->expectException(InvalidArgumentException::class);

        $data = ['non_existing_index' => 'data'];

        $entity = new ApplicationEntity();
        $entity->fromArray($data);
    }
}
