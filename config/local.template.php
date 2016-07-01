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

error_reporting(E_ALL);
ini_set('display_errors', 1);

return [
    'pdo' => [
        'dsn'      => 'mysql:dbname=webhemi;charset=utf8;hostname=127.0.0.1',
        'user'     => 'username',
        'password' => 'password',
        'options'  => [],
    ],
];
