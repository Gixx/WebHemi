<?php
/**
 * WebHemi
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\DataStorage\User;

use WebHemi\DataStorage\AbstractDataStorage;

/**
 * Class UserMetaStorage
 * @package WebHemi\DataStorage\User
 */
class UserMetaStorage extends AbstractDataStorage
{
    /** @var string  */
    protected $dataGroup = 'user_meta';
    /** @var  string */
    protected $idKey = 'id_user_meta';
}
