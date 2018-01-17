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

namespace WebHemi\Data;

/**
 * Interface DriverInterface.
 *
 * The purpose of this interface is to be able to reference to a general interface in the config::dependencies part.
 * In the settings/local/db.php this interface as an alias should define the proper database driver instance.
 */
interface DriverInterface
{
}
