<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
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
$dependencyInjection->registerServiceInstance(DependencyInjectionInterface::class, $dependencyInjection)
    ->registerServiceInstance(ConfigurationInterface::class, $configuration)
    ->registerServiceInstance(EnvironmentInterface::class, $environment)
    ->registerServiceInstance(MiddlewarePipelineInterface::class, $middlewarePipeline)
    ->registerServiceInstance(SessionInterface::class, $session)
    ->registerModuleServices('Global')
    ->registerModuleServices('Website')
    ->registerModuleServices('Admin');

$diList = [];
$fullConfig = $configuration->getData('dependencies');

foreach ($fullConfig as $module => $dependencies) {
    if ($module == 'Cronjob') {
        continue;
    }
    foreach ($dependencies as $reference => $config) {
        $name = $reference;

        if (isset($config['class'])) {
            $name .= ' <span style="color:gray">('.$config['class'].')</span>';
        }

        $instance = '<span style="color:green">OK</span>';
        $additional = '';

        try {
            $obj = $dependencyInjection->get($reference);
            $objName = get_class($obj);

            if ($obj instanceof ApplicationInterface) {
                $obj->initSession();
            }

            if ($reference != $objName) {
                $name = $reference.' <span style="color:gray">('.$objName.')</span>';
            }
        } catch (Throwable $error) {
            $libdata = $dependencyInjection->getServiceConfiguration($reference);

            $instance = '<span style="color:red">Error</span>: '.$error->getMessage();
            $additional = '<ul>';
            $additional .= '<li><strong>File</strong> '.$error->getFile().'</li>'.PHP_EOL;
            $additional .= '<li><strong>Line</strong> '.$error->getLine().'</li>'.PHP_EOL;

            foreach ($libdata as $key => $value) {
                $additional .= '<li><strong>'.$key.'</strong> '.json_encode($value).'</li>'.PHP_EOL;
            }

            $additional .= '</ul>';
        }

        $diList[$name] = [
            'name' => $name,
            'status' => $instance,
            'additional' => $additional
        ];
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Dependency Injection tester</title>
        <style>
            tr {
                vertical-align: top;
            }

            tr:nth-child(2n) {
                background-color: #e8e8e8;
            }
        </style>
    </head>
    <body>
        <h1>Instantiate <?php echo count($diList); ?> objects with the DI adapter.</h1>
        <table>
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Info</th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($diList as $info) {
    echo '<tr><td>'.$info['name'].'</td><td>'.$info['status'].'</td><td>'.$info['additional'].'</td></tr>'.PHP_EOL;
}
?>
            </tbody>
        </table>
        <?php
            $stat = render_stat();
        ?>
        <p>Rendered in <?php echo $stat['duration']; ?> seconds.</p>
        <p>Used <?php echo $stat['memory_bytes']; ?> of memory.</p>
    </body>
</html>
