<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi;

use DateTime as PHPDateTime;
use DateTimeZone;
use WebHemi\I18n\TimeZoneInterface;

/**
 * Class DateTime.
 *
 * Under php5-fpm the default DateTime object will be instantiated as TimeCopDateTime which makes issues in the
 * dependency injection adapter, and also fails unit tests. So the idea is to create this empty extension and
 * use this class instead of the default one or the TimeCopDateTime.
 */
class DateTime extends PHPDateTime implements TimeZoneInterface
{
    /**
     * @var string
     */
    private $timeZoneDataPath;
    /**
     * @var array
     */
    private $dateFormat = [];

    /**
     * DateTime constructor.
     *
     * @param string            $time
     * @param DateTimeZone|null $timeZone
     */
    public function __construct($time = 'now', DateTimeZone $timeZone = null)
    {
        if (is_numeric($time)) {
            $time = date('Y-m-d H:i:s', $time);
        }

        if (!$timeZone instanceof DateTimeZone) {
            $currentTimeZone = new DateTimeZone(date_default_timezone_get());
        } else {
            $currentTimeZone = $timeZone;
        }

        $this->timeZoneDataPath = __DIR__.'/I18n/TimeZone';
        $this->loadTimeZoneData($currentTimeZone->getName());

        parent::__construct($time, $currentTimeZone);
    }

    /**
     * Loads date format data regarding to the time zone.
     *
     * @param string $timeZone
     */
    public function loadTimeZoneData(string $timeZone) : void
    {
        $normalizedTimeZone = StringLib::convertNonAlphanumericToUnderscore($timeZone, '-');

        if (file_exists($this->timeZoneDataPath.'/'.$normalizedTimeZone.'.php')) {
            $this->dateFormat = include $this->timeZoneDataPath.'/'.$normalizedTimeZone.'.php';
        }
    }

    /**
     * Returns date formatted according to given format.
     *
     * @param  string $format
     * @return string
     * @link   http://php.net/manual/en/datetime.format.php
     */
    public function format($format)
    {
        if (isset($this->dateFormat[$format])) {
            $timestamp = $this->getTimestamp();
            $extraFormat = $this->dateFormat[$format];

            if (strpos($extraFormat, '%Q') !== false) {
                if ($this->dateFormat['ordinals']) {
                    $replace = date('jS', $timestamp);
                } else {
                    $replace = '%d.';
                }

                $extraFormat = str_replace('%Q', $replace, $extraFormat);
            }

            $dateString = strftime($extraFormat, $timestamp);
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
        return $this->format('Y-m-d H:i:s');
    }
}
