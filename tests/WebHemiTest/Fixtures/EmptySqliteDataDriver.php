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
namespace WebHemiTest\Fixtures;

use PDO;
use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class EmptySqliteDataDriver
 */
class EmptySqliteDataDriver extends PDO implements DataDriverInterface
{
}
