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
namespace WebHemiTest\TestService;

use InvalidArgumentException;
use WebHemi\Environment\ServiceAdapter\Base\ServiceAdapter as EnvironmentManager;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;

/**
 * Class EnvironmentManager.
 */
class EmptyEnvironmentManager extends EnvironmentManager
{
    const APPLICATION_TYPE_DIRECTORY = 'directory';
    const APPLICATION_TYPE_DOMAIN = 'domain';

    const COOKIE_AUTO_LOGIN_PREFIX = 'atln';
    const COOKIE_SESSION_PREFIX = 'atsn';

    const DEFAULT_APPLICATION = 'website';
    const DEFAULT_APPLICATION_URI = '/';
    const DEFAULT_MODULE = 'Website';
    const DEFAULT_THEME = 'default';
    const DEFAULT_THEME_RESOURCE_PATH = '/resources/default_theme';

    const SESSION_SALT = 'WebHemiTestX';

    /** @var string */
    protected $requestUri;

    /**
     * ModuleManager constructor.
     *
     * @param ConfigInterface $configuration
     * @param array           $getData
     * @param array           $postData
     * @param array           $serverData
     * @param array           $cookieData
     * @param array           $filesData
     * @param array           $optionsData
     */
    public function __construct(
        ConfigInterface $configuration,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData,
        array $optionsData = []
    ) {
        $this->configuration = $configuration->getConfig('applications');
        $this->environmentData = [
            'GET'    => $getData,
            'POST'   => $postData,
            'SERVER' => $serverData,
            'COOKIE' => $cookieData,
            'FILES'  => $filesData,
        ];
        $this->applicationRoot = __DIR__.'/../TestDocumentRoot';
        $this->documentRoot = realpath($this->applicationRoot.'/');
        $this->applicationDomain = 'www.unittest.dev';
        $this->topDomain = 'unittest.dev';
        $this->selectedModule = 'Website';
        $this->selectedApplication = 'website';
        $this->selectedApplicationUri = '/';
        $this->requestUri = $serverData['REQUEST_URI'] ?? '/';
        $this->selectedTheme = 'default';
        $this->selectedThemeResourcePath = '/resources/vendor_themes/test_theme';
        $this->isHttps = isset($serverData['HTTPS']) && $serverData['HTTPS'] == 'on';
        $this->options = $optionsData;
        $this->url = 'http'.($this->isHttps ? 's' : '').'://'
            .$this->environmentData['SERVER']['HTTP_HOST']
            .$this->environmentData['SERVER']['REQUEST_URI'];
    }

    /**
     * @param string $documentRoot
     * @return EmptyEnvironmentManager
     */
    public function setDocumentRoot(string $documentRoot) : EmptyEnvironmentManager
    {
        $this->documentRoot = $documentRoot;

        return $this;
    }

    /**
     * @param string $application
     * @return EmptyEnvironmentManager
     */
    public function setSelectedApplication(string $application) : EmptyEnvironmentManager
    {
        $this->selectedApplication = $application;

        return $this;
    }

    /**
     * @param $uri
     * @return EmptyEnvironmentManager
     */
    public function setSelectedApplicationUri($uri) : EmptyEnvironmentManager
    {
        $this->selectedApplicationUri = $uri;

        return $this;
    }

    /**
     * @param $requestUri
     * @return EmptyEnvironmentManager
     */
    public function setRequestUri($requestUri) : EmptyEnvironmentManager
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function getRequestUri() : string
    {
        return $this->requestUri;
    }

    /**
     * @param $module
     * @return EmptyEnvironmentManager
     */
    public function setSelectedModule($module) : EmptyEnvironmentManager
    {
        $this->selectedModule = $module;

        return $this;
    }

    /**
     * @param $theme
     * @return EmptyEnvironmentManager
     */
    public function setSelectedTheme($theme) : EmptyEnvironmentManager
    {
        $this->selectedTheme = $theme;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourcePath() : string
    {
        if ($this->selectedTheme !== self::DEFAULT_THEME) {
            $this->selectedThemeResourcePath = '/resources/vendor_themes/'.$this->selectedTheme;
        } else {
            $this->selectedThemeResourcePath = '/resources/default_theme';
        }

        return $this->selectedThemeResourcePath;
    }
}
