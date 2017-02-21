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
declare(strict_types = 1);

namespace WebHemi\Adapter\Renderer\Twig;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;
use WebHemi\Renderer\ThemeCheckTrait;

/**
 * Class TwigRendererAdapter.
 */
class TwigRendererAdapter implements RendererAdapterInterface
{
    /** @var Twig_Environment */
    private $adapter;
    /** @var ConfigInterface */
    private $configuration;
    /** @var EnvironmentManager */
    private $environmentManager;
    /** @var string */
    private $defaultViewPath;
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $templateResourcePath;
    /** @var string */
    private $applicationBaseUri;

    use ThemeCheckTrait;

    /**
     * RendererAdapterInterface constructor.
     *
     * @param ConfigInterface    $configuration
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ConfigInterface $configuration, EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
        $documentRoot = $environmentManager->getDocumentRoot();
        $selectedTheme = $environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $environmentManager->getResourcePath();

        if (!$configuration->has('themes/'.$selectedTheme)
            || !$this->checkSelectedThemeFeatures(
                $configuration->getConfig('themes/'.$selectedTheme),
                $environmentManager
            )
        ) {
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

        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')) {
            $this->adapter->addExtension(new TwigExtension());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     * @throws InvalidArgumentException
     * @return StreamInterface
     */
    public function render(string $template, array $parameters = []) : StreamInterface
    {
        if ($this->configuration->has('map/'.$template)) {
            $template = $this->configuration->getData('map/'.$template)[0];
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
        $parameters['document_root'] = $this->environmentManager->getDocumentRoot();
        $parameters['application_base_uri'] = $this->applicationBaseUri;

        $output = $this->adapter->render($template, $parameters);

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }
}
