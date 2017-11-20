<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi;

use DateTime as PHPDateTime;
use DateTimeZone;

/**
 * Class DateTime.
 *
 * Under php5-fpm the default DateTime object will be instantiated as TimeCopDateTime which makes issues in the
 * dependency injection adapter, and also fails unit tests. So the idea is to create this empty extension and
 * use this class instead of the default one or the TimeCopDateTime.
 */
class DateTime extends PHPDateTime
{
    /**
     * TODO take translation data from I18n.
     */

    /** @var string */
    private $defaultTimezone;
    /** @var array */
    private $timezoneToLanguage = [
        'Europe/Budapest' => 'hu',
        'Europe/London' => 'en'
    ];
    /** @var array */
    private $formatLanguage = [
        'Y2MD' => [
            'default' => '%y-%m-%d',
            'en' => '%m-%d-%y',
            'hu' => '%y-%m-%d',
        ],
        'Y4MD' => [
            'default' => '%Y-%m-%d',
            'en' => '%m-%d-%Y',
            'hu' => '%Y-%m-%d'
        ],
        'Y4M' => [
            'default' => '%Y-%m',
            'en' => '%m-%d',
            'hu' => '%Y-%m'
        ],
        'Y4B' => [
            'default' => '%Y. %B',
            'en' => '%B, %Y',
            'hu' => '%Y. %B'
        ],
        'MD' => [
            'default' => '%m-%d',
            'en' => '%m-%d',
            'hu' => '%m-%d'
        ],
        'Y4BD' => [
            'default' => '%m. %B %Y',
            'en' => '%m. %B %Y',
            'hu' => '%Y. %B %d.'
        ],
        'BD' => [
            'default' => '%B %d.',
            'en' => '%B %d',
            'hu' => '%B %d.'
        ],
        'T' => [
            'default' => '%H:%M',
            'en' => '%H:%M',
            'hu' => '%H:%M',
        ],
        'TS' => [
            'default' => '%H:%M:%S',
            'en' => '%H:%M:%S',
            'hu' => '%H:%M:%S',
        ],
        'MDT' => [
            'default' => '%m %d., %H:%M',
            'en' => '%m %d, %H:%M',
            'hu' => '%m %d., %H:%M'
        ],
        'BDT' => [
            'default' => '%B %d., %H:%M',
            'en' => '%B %d, %H:%M',
            'hu' => '%B %d., %H:%M'
        ],
        'Y4BDT' => [
            'default' => '%m. %B %Y, %H:%M',
            'en' => '%m. %B %Y, %H:%M',
            'hu' => '%Y. %B %d.,  %H:%M'
        ],
        'Y4BDTS' => [
            'default' => '%m. %B %Y, %H:%M:%S',
            'en' => '%m. %B %Y, %H:%M:%S',
            'hu' => '%Y. %B %d.,  %H:%M:%S'
        ],
        'Y4MDTS' => [
            'default' => '%Y-%m-%d %H:%M:%S',
            'en' => '%m-%d-%Y %H:%M:%S',
            'hu' => '%Y-%m-%d %H:%M:%S'
        ],
    ];

    /**
     * DateTime constructor.
     *
     * @param string $time
     * @param DateTimeZone|null $timezone
     */
    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if (is_numeric($time)) {
            $time = date('Y-m-d H:i:s', $time);
        }

        $this->defaultTimezone = date_default_timezone_get();

        if (empty($timezone)) {
            $timezone = new DateTimeZone($this->defaultTimezone);
        }

        parent::__construct($time, $timezone);
    }

    /**
     * Returns date formatted according to given format.
     * @param string $format
     * @return string
     * @link http://php.net/manual/en/datetime.format.php
     */
    public function format($format)
    {
        if (isset($this->formatLanguage[$format])) {
            $timestamp = $this->getTimestamp();
            $language = $this->timezoneToLanguage[$this->defaultTimezone] ?? 'default';
            $dateString = strftime($this->formatLanguage[$format][$language], $timestamp);
        } else {
            $dateString = parent::format($format);
        }

        return $dateString;
    }

    /**
     * Checks if stored timestamp belong to current day.
     *
     * @return bool
     */
    public function isToday() : bool
    {
        return date('Ymd', $this->getTimestamp()) == date('Ymd');
    }

    /**
     * Checks if stored timestamp belong to current month.
     *
     * @return bool
     */
    public function isCurrentMonth() : bool
    {
        return date('Ym', $this->getTimestamp()) == date('Ym');
    }

    /**
     * Checks if stored timestamp belong to current year.
     *
     * @return bool
     */
    public function isCurrentYear() : bool
    {
        return date('Y', $this->getTimestamp()) == date('Y');
    }

    /**
     * Returns the date with the default format.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->format('Y4MDTs');
    }
}
