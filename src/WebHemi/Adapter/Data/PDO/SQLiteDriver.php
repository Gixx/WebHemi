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

namespace WebHemi\Adapter\Data\PDO;

use PDO;
use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class SQLiteDriver.
 *
 * Extends PDO and implements the DataDriverInterface, so the Dependency Injector can reference it.
 */
class SQLiteDriver extends PDO implements DataDriverInterface
{
}
