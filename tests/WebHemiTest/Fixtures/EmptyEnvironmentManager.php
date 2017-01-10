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
namespace WebHemiTest\Fixtures;

use InvalidArgumentException;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

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

    const SESSION_SALT = 'WebHemiTest';

    /** @var ConfigInterface */
    protected $configuration;
    /** @var string */
    protected $documentRoot = __DIR__;
    /** @var string */
    protected $applicationDomain = 'www.unittest.dev';
    /** @var string */
    protected $selectedModule = 'Website';
    /** @var string */
    protected $selectedApplication = 'website';
    /** @var string */
    protected $selectedApplicationUri = '/';
    /** @var string */
    protected $requestUri = '/';
    /** @var string */
    protected $selectedTheme = 'default';
    /** @var string */
    protected $selectedThemeResourcePath = '/resources/vendor_themes/test_theme';
    /** @var array  */
    protected $environmentData = [];
    /** @var bool */
    protected $isHttps = false;

    /**
     * ModuleManager constructor.
     *
     * @param ConfigInterface $configuration
     * @param array           $getData
     * @param array           $postData
     * @param array           $serverData
     * @param array           $cookieData
     * @param array           $filesData
     */
    public function __construct(
        ConfigInterface $configuration,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData
    ) {
        $this->configuration = $configuration->getConfig('applications');
        $this->environmentData = [
            'GET'    => $getData,
            'POST'   => $postData,
            'SERVER' => $serverData,
            'COOKIE' => $cookieData,
            'FILES'  => $filesData,
        ];
    }

    /**
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->documentRoot;
    }

    /**
     * @return string
     */
    public function getApplicationDomain()
    {
        return $this->applicationDomain;
    }

    /**
     * @return bool
     */
    public function isSecuredApplication()
    {
        return $this->isHttps;
    }

    /**
     * @param $application
     * @return $this
     */
    public function setSelectedApplication($application)
    {
        $this->selectedApplication = $application;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedApplication()
    {
        return $this->selectedApplication;
    }

    /**
     * @param $uri
     * @return $this
     */
    public function setSelectedApplicationUri($uri)
    {
        $this->selectedApplicationUri = $uri;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedApplicationUri()
    {
        return $this->selectedApplicationUri;
    }

    /**
     * @param $requestUri
     * @return $this
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @param $module
     * @return $this
     */
    public function setSelectedModule($module)
    {
        $this->selectedModule = $module;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedModule()
    {
        return $this->selectedModule;
    }

    /**
     * @param $theme
     * @return $this
     */
    public function setSelectedTheme($theme)
    {
        $this->selectedTheme = $theme;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedTheme()
    {
        return $this->selectedTheme;
    }

    /**
     * @return string
     */
    public function getResourcePath()
    {
        return $this->selectedThemeResourcePath;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getEnvironmentData($key)
    {
        if (!isset($this->environmentData[$key])) {
            throw new InvalidArgumentException(sprintf('The "%s" is not a valid environment key.', $key));
        }

        return $this->environmentData[$key];
    }
}
