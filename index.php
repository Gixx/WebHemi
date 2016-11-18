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
use WebHemi\Application\EnvironmentManager;
use WebHemi\Application\PipelineManager;
use WebHemi\Application\SessionManager;
use WebHemi\Application\Web\WebApplication as Application;
use WebHemi\Config;

require_once __DIR__.'/vendor/autoload.php';

$config = new Config\Config(require __DIR__.'/config/config.php');
// Set core objects
$environmentManager = new EnvironmentManager($config, $_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);

$pipelineManager = new PipelineManager($config->getConfig('middleware_pipeline'));
$pipelineManager->addModulePipeLine($environmentManager->getSelectedModule());

$sessionManager = new SessionManager($config->getConfig('session'));

$diAdapter = new DependencyInjectionAdapter($config->getConfig('dependencies'));
// Add core and module services to the DI adapter
$diAdapter->registerService(Config\ConfigInterface::class, $config)
    ->registerService(EnvironmentManager::class, $environmentManager)
    ->registerService(PipelineManager::class, $pipelineManager)
    ->registerService(SessionManager::class, $sessionManager)
    ->registerModuleServices('Global')
    ->registerModuleServices($environmentManager->getSelectedModule());

$app = new Application($diAdapter);
$app->run();
