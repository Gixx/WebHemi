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

namespace WebHemi\Renderer\ServiceAdapter\Twig;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\ServiceInterface;
use WebHemi\Renderer\Traits\GetSelectedThemeResourcePathTrait;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var Twig_Environment */
    private $adapter;
    /** @var string */
    private $defaultViewPath;
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $templateResourcePath;
    /** @var string */
    private $applicationBaseUri;

    use GetSelectedThemeResourcePathTrait;

    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var EnvironmentInterface */
    protected $environmentManager;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     */
    public function __construct(ConfigurationInterface $configuration, EnvironmentInterface $environmentManager)
    {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;

        $documentRoot = $environmentManager->getDocumentRoot();
        $selectedTheme = $environmentManager->getSelectedTheme();
        $selectedThemeResourcePath = $this->getSelectedThemeResourcePath(
            $selectedTheme,
            $configuration,
            $environmentManager
        );

        // Overwrite for later usage.
        $this->configuration = $configuration->getConfig('themes/'.$selectedTheme);

        $this->defaultViewPath = $documentRoot.EnvironmentInterface::DEFAULT_THEME_RESOURCE_PATH.'/view';
        $this->templateViewPath = $documentRoot.$selectedThemeResourcePath.'/view';
        $this->templateResourcePath = $selectedThemeResourcePath.'/static';
        $this->applicationBaseUri = $environmentManager->getSelectedApplicationUri();

        $loader = new Twig_Loader_Filesystem($this->templateViewPath);
        $loader->addPath($this->defaultViewPath, 'WebHemi');
        $loader->addPath($this->templateViewPath, 'Theme');

        $this->adapter = new Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $this->adapter->addExtension(new Twig_Extension_Debug());
        // @codeCoverageIgnoreStart
        //
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
