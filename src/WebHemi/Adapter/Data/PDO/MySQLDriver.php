<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Adapter\Data\PDO;

use PDO;
use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class MySQLDriver.
 *
 * Extends PDO and implements the DataDriverInterface, so the Dependency Injector can reference it.
 */
class MySQLDriver extends PDO implements DataDriverInterface
{
}
