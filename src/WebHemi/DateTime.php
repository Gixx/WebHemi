<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemi;

use DateTime as PHPDateTime;

/**
 * Class DateTime.
 *
 * Under php5-fpm the default DateTime object will be instantiated as TimeCopDateTime which makes issues in the
 * dependency injection adapter, and also fails unit tests. So the idea is to create this empty extension and
 * use this class instead of the default one or the TimeCopDateTime.
 */
class DateTime extends PHPDateTime
{
}
