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

namespace WebHemi\Data\Query\SQLite;

use WebHemi\Data\Query\MySQL;

/**
 * Class QueryAdapter
 */
class QueryAdapter extends MySQL\QueryAdapter
{
    /**
     * @var string
     */
    protected static $statementPath = __DIR__.'/../MySQL/statements/*.sql';
}
