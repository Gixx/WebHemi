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
use WebHemi\Adapter\DependencyInjection\Symfony\SymfonyAdapter as DependencyInjectionAdapter;
use WebHemi\Application\Web\WebApplication as Application;
use WebHemi\Config\Config;
use WebHemi\Middleware\Pipeline\Pipeline;

require_once __DIR__.'/vendor/autoload.php';

$config = new Config(require __DIR__.'/config/config.php');
$diAdapter = new DependencyInjectionAdapter($config->get('dependencies', Config::CONFIG_AS_OBJECT));
$pipeline = new Pipeline();

$app = new Application($diAdapter, $config, $pipeline);
$app->setEnvironmentData('GET', $_GET)
    ->setEnvironmentData('POST', $_POST)
    ->setEnvironmentData('SERVER', $_SERVER)
    ->setEnvironmentData('COOKIE', $_COOKIE)
    ->setEnvironmentData('FILES', $_FILES)
    ->run();
