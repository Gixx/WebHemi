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
namespace WebHemi\Adapter\Renderer\Twig;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

/**
 * Class TwigRendererAdapter.
 */
class TwigRendererAdapter implements RendererAdapterInterface
{
    private $adapter;
    /** @var ConfigInterface */
    private $configuration;
    /** @var string */
    private $defaultViewPath;
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $templateResourcePath;
    /** @var string */
    private $applicationBaseUri;

    /**
     * RendererAdapterInterface constructor.
     *
     * @param ConfigInterface    $configuration
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ConfigInterface $configuration, EnvironmentManager $environmentManager)
    {
        $documentRoot = $environmentManager->getDocumentRoot();
        $selectedTheme = $environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $environmentManager->getResourcePath();

        if (!$configuration->has('themes/'.$selectedTheme)) {
            $selectedTheme = EnvironmentManager::DEFAULT_THEME;
            $selectedThemeResourcePath = EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH;
        }

        $this->configuration = $configuration->getConfig('themes/'.$selectedTheme);

        $this->defaultViewPath = $documentRoot.EnvironmentManager::DEFAULT_THEME_RESOURCE_PATH.'/view';
        $this->templateViewPath = $documentRoot.$selectedThemeResourcePath.'/view';
        $this->templateResourcePath = $selectedThemeResourcePath.'/static';
        $this->applicationBaseUri = $environmentManager->getSelectedApplicationUri();

        $loader = new Twig_Loader_Filesystem($this->templateViewPath);
        $loader->addPath($this->defaultViewPath, 'WebHemi');
        $loader->addPath($this->templateViewPath, 'Theme');
        $this->adapter = new Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $this->adapter->addExtension(new Twig_Extension_Debug());

        $viewPath = $this->templateViewPath;
        // @codeCoverageIgnoreStart
        // link a core function into template level
        $function = new Twig_SimpleFunction('defined', function ($fileName) use ($viewPath) {
            $fileName = str_replace('@Theme', $viewPath, $fileName);
            return file_exists($fileName);
        });
        $this->adapter->addFunction($function);
        // @codeCoverageIgnoreEnd
    }

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @throws InvalidArgumentException
     *
     * @return StreamInterface
     */
    public function render($template, $parameters = [])
    {
        if ($this->configuration->has('map/'.$template)) {
            $template = $this->configuration->getData('map/'.$template);
        }

        if (!file_exists($this->templateViewPath.'/'.$template)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to render file: "%s". No such file: %s.',
                    $template,
                    $this->templateViewPath.'/'.$template
                )
            );
        }

        // Tell the template where the resources are.
        $parameters['template_resource_path'] = $this->templateResourcePath;
        $parameters['application_base_uri'] = $this->applicationBaseUri;

        $output = $this->adapter->render($template, $parameters);

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }
}
