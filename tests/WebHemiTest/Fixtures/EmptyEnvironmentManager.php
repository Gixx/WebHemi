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
     * @param string $documentRoot
     * @return EmptyEnvironmentManager
     */
    public function setDocumentRoot(string $documentRoot) : EmptyEnvironmentManager
    {
        $this->documentRoot = $documentRoot;

        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentRoot() : string
    {
        return $this->documentRoot;
    }

    /**
     * @return string
     */
    public function getApplicationDomain() : string
    {
        return $this->applicationDomain;
    }

    /**
     * @return bool
     */
    public function isSecuredApplication() : bool
    {
        return $this->isHttps;
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
     * @return string
     */
    public function getSelectedApplication() : string
    {
        return $this->selectedApplication;
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
     * @return string
     */
    public function getSelectedApplicationUri() : string
    {
        return $this->selectedApplicationUri;
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
     * @return string
     */
    public function getSelectedModule() : string
    {
        return $this->selectedModule;
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
    public function getSelectedTheme() : string
    {
        return $this->selectedTheme;
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

    /**
     * @param $key
     * @return array
     */
    public function getEnvironmentData(string $key) : array
    {
        if (!isset($this->environmentData[$key])) {
            throw new InvalidArgumentException(sprintf('The "%s" is not a valid environment key.', $key));
        }

        return $this->environmentData[$key];
    }
}
