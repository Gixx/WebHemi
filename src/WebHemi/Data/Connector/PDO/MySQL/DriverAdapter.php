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
declare(strict_types = 1);

namespace WebHemi\Data\Connector\PDO\MySQL;

use PDO;
use WebHemi\Data\DriverInterface;

/**
 * Class DriverAdapter.
 *
 * Extends PDO and implements the DriverInterface, so the Dependency Injector can reference it.
 */
class DriverAdapter extends PDO implements DriverInterface
{
}
