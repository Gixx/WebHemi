<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\I18n;

/**
 * Interface TimeZoneInterface.
 */
interface TimeZoneInterface
{
    /**
     * Loads date format data regarding to the time zone.
     *
     * @param string $timeZone
     */
    public function loadTimeZoneData(string $timeZone) : void;
}
