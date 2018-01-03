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
declare(strict_types = 1);

namespace WebHemi\Renderer\ServiceAdapter\Twig;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Throwable;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\I18n\ServiceInterface as I18nService;
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
    /** @var I18nService */
    protected $i18nService;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     * @param I18nService            $i18nService
     * @throws Throwable
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        I18nService $i18nService
    ) {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;
        $this->i18nService = $i18nService;

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
     * @throws Throwable
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

        // Merge mandatory Application data.
        $applicationParams['application'] = [
            'selectedModule' => $this->environmentManager->getSelectedModule(),
            'address' => $this->environmentManager->getAddress(),
            'topDomain' => $this->environmentManager->getTopDomain(),
            'domainName' => $this->environmentManager->getApplicationDomain(),
            'domainAddress' => 'http'.($this->environmentManager->isSecuredApplication() ? 's' : '').'://'
                .$this->environmentManager->getApplicationDomain(),
            'resourcePath' => $this->templateResourcePath,
            'baseUri' => $this->applicationBaseUri,
            'currentUri' => $this->environmentManager->getRequestUri(),
            'documentRoot' => $this->environmentManager->getDocumentRoot(),
            'language' => $this->i18nService->getLanguage(),
            'locale' => $this->i18nService->getLocale(),
        ];

        $parameters = merge_array_overwrite($parameters, $applicationParams);

        $output = $this->adapter->render($template, $parameters);

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }
}
