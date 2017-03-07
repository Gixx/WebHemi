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
use WebHemi\Application\ServiceInterface as ApplicationInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\MiddlewarePipeline\ServiceInterface as MiddlewarePipelineInterface;
use WebHemi\Session\ServiceInterface as SessionInterface;

require_once __DIR__.'/vendor/autoload.php';

// Set up core objects
$configurationData = require_once __DIR__.'/config/config.php';
// Get the service class names from the configuration, so no need to hardcode them.
$applicationClass = $configurationData['dependencies']['Global'][ApplicationInterface::class]['class'];
$configurationClass = $configurationData['dependencies']['Global'][ConfigurationInterface::class]['class'];
$dependencyInjectionClass = $configurationData['dependencies']['Global'][DependencyInjectionInterface::class]['class'];
$environmentClass = $configurationData['dependencies']['Global'][EnvironmentInterface::class]['class'];
$middlewarePipelineClass = $configurationData['dependencies']['Global'][MiddlewarePipelineInterface::class]['class'];
$sessionClass = $configurationData['dependencies']['Global'][SessionInterface::class]['class'];

/** @var ConfigurationInterface $configuration */
$configuration = new $configurationClass($configurationData);
/** @var EnvironmentInterface $environment */
$environment = new $environmentClass($configuration, $_GET, $_POST, $_SERVER, $_COOKIE, $_FILES);
/** @var MiddlewarePipelineInterface $middlewarePipeline */
$middlewarePipeline = new $middlewarePipelineClass($configuration);
$middlewarePipeline->addModulePipeLine($environment->getSelectedModule());
/** @var SessionInterface $session */
$session = new $sessionClass($configuration);
/** @var DependencyInjectionInterface $dependencyInjection */
$dependencyInjection = new $dependencyInjectionClass($configuration);
// Add core and module services to the DI adapter
$dependencyInjection->registerService(ConfigurationInterface::class, $configuration)
    ->registerService(EnvironmentInterface::class, $environment)
    ->registerService(MiddlewarePipelineInterface::class, $middlewarePipeline)
    ->registerService(SessionInterface::class, $session)
    ->registerModuleServices('Global')
    ->registerModuleServices($environment->getSelectedModule());

/** @var ApplicationInterface $application */
$application = new $applicationClass($dependencyInjection);
$application->run();
