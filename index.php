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

use WebHemi\Adapter\DependencyInjection\Auryn\AurynAdapter as DependencyInjectionAdapter;
use WebHemi\Application\Web\WebApplication as Application;
use WebHemi\Application\Config;

require_once(__DIR__ . '/vendor/autoload.php');

$config = require(__DIR__ . '/config/config.php');

$app = new Application(new DependencyInjectionAdapter(), new Config($config));
$app->setEnvironmentFromGlobals($_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
$app->run();
