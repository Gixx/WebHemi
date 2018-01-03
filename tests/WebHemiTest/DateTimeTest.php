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
namespace WebHemiTest;

use DateTimeZone;
use PHPUnit\Framework\TestCase;
use WebHemi\DateTime;

/**
 * Class DateTimeTest.
 */
class DateTimeTest extends TestCase
{
    /**
     * Tests class constructor.
     */
    public function testConstructor()
    {
        $originalTimezone = date_default_timezone_get();

        $expectedTimestamp = mktime(12, 13, 14, 5, 6, 2017);
        $expectedTimeZone = date_default_timezone_get();
        $dateTime = new DateTime($expectedTimestamp);

        $this->assertSame($expectedTimestamp, $dateTime->getTimestamp());
        $this->assertSame($expectedTimeZone, $dateTime->getTimezone()->getName());

        date_default_timezone_set('Europe/London');
        $expectedTimeZone = 'Europe/London';
        $timeZone = new DateTimeZone($expectedTimeZone);

        $date = date('Y-m-d H:i:s', $expectedTimestamp);
        $dateTime = new DateTime($date, $timeZone);

        $this->assertSame($expectedTimestamp, $dateTime->getTimestamp());
        $this->assertSame($expectedTimeZone, $dateTime->getTimezone()->getName());

        date_default_timezone_set($originalTimezone);
    }

    /**
     * Data provider for checker test.
     *
     * @return array
     */
    public function checkerProvider()
    {
        $notThisDay = date('d') == 1 ? 2 : (date('d') - 1);
        $notThisMonth = date('m') == 1 ? 2 : (date('m') - 1);

        $today = time();
        $thisMonth = mktime(1, 2, 3, date('m'), $notThisDay, date('Y'));
        $thisYear =  mktime(1, 2, 3, $notThisMonth, 1, date('Y'));
        $notThisYear =  mktime(1, 2, 3, 1, 1, 2000);

        return [
            [date('Y-m-d', $today), true, true, true],
            [date('Y-m-d', $thisMonth), false, true, true],
            [date('Y-m-d', $thisYear), false, false, true],
            [date('Y-m-d', $notThisYear), false, false, false],
        ];
    }

    /**
     * Tests checker functions.
     *
     * @covers \WebHemi\DateTime::isToday()
     * @covers \WebHemi\DateTime::isCurrentMonth()
     * @covers \WebHemi\DateTime::isCurrentYear()
     * @dataProvider checkerProvider
     */
    public function testCheckers($date, $isToday, $isCurrentMonth, $isCurrentYear)
    {
        $originalTimezone = date_default_timezone_get();

        date_default_timezone_set('Europe/London');
        $timeZone = new DateTimeZone('Europe/London');
        $dateTime = new DateTime($date, $timeZone);

        $this->assertSame($isToday, $dateTime->isToday());
        $this->assertSame($isCurrentMonth, $dateTime->isCurrentMonth());
        $this->assertSame($isCurrentYear, $dateTime->isCurrentYear());

        date_default_timezone_set($originalTimezone);
    }

    /**
     * Tests format method.
     */
    public function testFormatter()
    {
        $originalTimezone = date_default_timezone_get();

        $timeZoneWithNoDataInWebhemi = new DateTimeZone('Africa/Abidjan');
        $timeZoneWithDataInWebhemi = new DateTimeZone('Europe/London');
        $timeZoneWithDataInWebhemiHu = new DateTimeZone('Europe/Budapest');


        $testDate = '2017-09-10 11:12:13';

        date_default_timezone_set('Africa/Abidjan');
        $dateTime = new DateTime($testDate, $timeZoneWithNoDataInWebhemi);
        $resultDate = $dateTime->format('BDT');
        // The expected result is a strange format, since we didn't have a definition for this named format.
        $expectedDate = '508SunGMT';
        $this->assertSame($expectedDate, $resultDate);

        date_default_timezone_set('Europe/London');
        $dateTime = new DateTime($testDate, $timeZoneWithDataInWebhemi);
        $resultDate = $dateTime->format('BDT');
        // The expected result is a translated.
        $expectedDate = '10th September, 11:12 am';
        $this->assertSame(strtolower($expectedDate), strtolower($resultDate));

        date_default_timezone_set('Europe/Budapest');
        $dateTime = new DateTime($testDate, $timeZoneWithDataInWebhemiHu);
        $resultDate = $dateTime->format('BDT');
        // The expected result is a translated.
        $expectedDate = 'September 10. 11:12';
        $this->assertSame($expectedDate, $resultDate);

        date_default_timezone_set($originalTimezone);
    }

    /**
     * Tests the toString method.
     */
    public function testToString()
    {
        $originalTimezone = date_default_timezone_get();

        date_default_timezone_set('Europe/London');
        $timeZone = new DateTimeZone('Europe/London');
        $timestamp = mktime(12, 13, 14, 5, 6, 2017);

        $dateTime = new DateTime($timestamp, $timeZone);

        $expectedDate = date('Y-m-d H:i:s', $timestamp);
        $currentDate = $dateTime->__toString();

        $this->assertSame($expectedDate, $currentDate);

        date_default_timezone_set($originalTimezone);
    }
}
