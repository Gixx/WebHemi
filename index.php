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
use WebHemi\Application\ServiceAdapter\Base\ServiceAdapter as Application;
use WebHemi\Application\ServiceInterface as ApplicationInterface;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Configuration;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\DependencyInjection\ServiceAdapter\Symfony\ServiceAdapter as DependencyInjection;
use WebHemi\DependencyInjection\ServiceInterface as DependencyInjectionInterface;
use WebHemi\Environment\ServiceAdapter\Base\ServiceAdapter as Environment;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\MiddlewarePipeline\ServiceAdapter\Base\ServiceAdapter as MiddlewarePipeline;
use WebHemi\MiddlewarePipeline\ServiceInterface as MiddlewarePipelineInterface;
use WebHemi\Session\ServiceAdapter\Base\ServiceAdapter as Session;
use WebHemi\Session\ServiceInterface as SessionInterface;

require_once __DIR__.'/vendor/autoload.php';

// Set up core objects
$configurationData = require_once __DIR__.'/config/config.php';
// Get the service class names from the configuration, so no need to hardcode them.
// These global dependency definitions are mandatory and MUST exist
$applicationClass = $configurationData['dependencies']['Global'][ApplicationInterface::class]['class'];
$configurationClass = $configurationData['dependencies']['Global'][ConfigurationInterface::class]['class'];
$dependencyInjectionClass = $configurationData['dependencies']['Global'][DependencyInjectionInterface::class]['class'];
$environmentClass = $configurationData['dependencies']['Global'][EnvironmentInterface::class]['class'];
$middlewarePipelineClass = $configurationData['dependencies']['Global'][MiddlewarePipelineInterface::class]['class'];
$sessionClass = $configurationData['dependencies']['Global'][SessionInterface::class]['class'];
/** @var Configuration $configuration */
$configuration = new $configurationClass($configurationData);
/** @var Environment $environment */
$environment = new $environmentClass($configuration, $_GET, $_POST, $_SERVER, $_COOKIE, $_FILES, []);
/** @var MiddlewarePipeline $middlewarePipeline */
$middlewarePipeline = new $middlewarePipelineClass($configuration);
$middlewarePipeline->addModulePipeLine($environment->getSelectedModule());
/** @var Session $session */
$session = new $sessionClass($configuration);
/** @var DependencyInjection $dependencyInjection */
$dependencyInjection = new $dependencyInjectionClass($configuration);
// Add core and module services to the DI adapter
$dependencyInjection->registerServiceInstance(ConfigurationInterface::class, $configuration)
    ->registerServiceInstance(EnvironmentInterface::class, $environment)
    ->registerServiceInstance(MiddlewarePipelineInterface::class, $middlewarePipeline)
    ->registerServiceInstance(SessionInterface::class, $session)
    ->registerServiceInstance(DependencyInjectionInterface::class, $dependencyInjection)
    ->registerModuleServices('Global')
    ->registerModuleServices($environment->getSelectedModule());

/** @var Application $application */
$application = new $applicationClass($dependencyInjection);
$application
    ->initSession()
    ->run()
    ->renderOutput();
