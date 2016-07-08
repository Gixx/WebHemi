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
namespace WebHemi\Application;

use InvalidArgumentException;
use WebHemi\Config\ConfigInterface;

/**
 * Class EnvironmentManager.
 */
class EnvironmentManager
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

    const SESSION_SALT = 'WebHemi';

    /** @var ConfigInterface */
    private $config;
    /** @var string */
    private $url;
    /** @var string */
    private $subDomain;
    /** @var string */
    private $mainDomain;
    /** @var string */
    private $applicationDomain;
    /** @var string */
    private $documentRoot;
    /** @var string */
    private $selectedModule;
    /** @var string */
    private $selectedApplication;
    /** @var string */
    private $selectedApplicationUri;
    /** @var string */
    private $selectedTheme;
    /** @var string */
    private $selectedThemeResourcePath;
    /** @var array  */
    private $environmentData;

    /**
     * ModuleManager constructor.
     *
     * @param ConfigInterface $config
     * @param array           $getData
     * @param array           $postData
     * @param array           $serverData
     * @param array           $cookieData
     * @param array           $filesData
     */
    public function __construct(
        ConfigInterface $config,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData
    ) {
        $this->config = $config;
        $this->documentRoot = realpath(__DIR__.'/../../../');

        $this->environmentData = [
            'GET'    => $getData,
            'POST'   => $postData,
            'SERVER' => $serverData,
            'COOKIE' => $cookieData,
            'FILES'  => $filesData,
        ];

        $isHttps = isset($this->environmentData['SERVER']['HTTPS']) && $this->environmentData['SERVER']['HTTPS'];
        $this->url = 'http'.($isHttps ? 's' : '').'://'
            .$this->environmentData['SERVER']['HTTP_HOST']
            .$this->environmentData['SERVER']['REQUEST_URI']
            .$this->environmentData['SERVER']['QUERY_STRING'];

        $this->selectedModule = self::DEFAULT_MODULE;
        $this->selectedApplication = self::DEFAULT_APPLICATION;
        $this->selectedTheme = self::DEFAULT_THEME;
        $this->selectedThemeResourcePath = self::DEFAULT_THEME_RESOURCE_PATH;
        $this->selectedApplicationUri = self::DEFAULT_APPLICATION_URI;

        $this->secureSession();
        $this->setDomain();
        $this->selectModuleApplicationAndTheme();
    }

    /**
     * Gets the selected application.
     *
     * @return string
     */
    public function getSelectedApplication()
    {
        return $this->selectedApplication;
    }

    /**
     * Get the URI path for the selected application. Required for the RouterAdapter to work with directory-based
     * applications correctly.
     *
     * @return string
     */
    public function getSelectedApplicationUri()
    {
        return $this->selectedApplicationUri;
    }

    /**
     * Gets the selected module.
     *
     * @return string
     */
    public function getSelectedModule()
    {
        return $this->selectedModule;
    }

    /**
     * Gets the selected theme.
     *
     * @return string
     */
    public function getSelectedTheme()
    {
        return $this->selectedTheme;
    }

    /**
     * Gets the resource path for the selected theme.
     *
     * @return string
     */
    public function getResourcePath()
    {
        return $this->selectedThemeResourcePath;
    }

    /**
     * Gets environment data.
     *
     * @param string $key
     *
     * @return array
     */
    public function getEnvironmentData($key)
    {
        if (!isset($this->environmentData[$key])) {
            throw new InvalidArgumentException(sprintf('The "%s" is not a valid environment key.', $key));
        }

        return $this->environmentData[$key];
    }

    /**
     * Gets the template settings for a specific theme.
     *
     * @return ConfigInterface
     */
    public function getApplicationTemplateSettings()
    {
        return $this->config->getConfig('themes/'.$this->getSelectedTheme());
    }

    /**
     * Gets the routing settings for the selected module.
     *
     * @return ConfigInterface
     */
    public function getModuleRouteSettings()
    {
        return $this->config->getConfig('modules/'.$this->getSelectedModule().'/routing');
    }

    /**
     * Overwrite PHP settings to be more secure
     *
     * @codeCoverageIgnore
     */
    private function secureSession()
    {
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', '16');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');

        // hide session name
        session_name(self::COOKIE_SESSION_PREFIX.'-'.bin2hex(self::SESSION_SALT));
        // set session lifetime to 1 hour
        session_set_cookie_params(3600);
    }

    /**
     * Parses server data and tries to set domain information.
     */
    private function setDomain()
    {
        $domain = $this->environmentData['SERVER']['SERVER_NAME'];
        $subDomain = '';
        $urlParts = parse_url($this->url);

        // If the host is not an IP address, then check the sub-domain-based module names too
        if (!preg_match(
            '/^((\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/',
            $urlParts['host']
        )) {
            $domainParts = explode('.', $urlParts['host']);
            // @todo find out how to support complex TLDs like `.co.uk` or `.com.br`
            $tld = array_pop($domainParts);
            $domain = array_pop($domainParts).'.'.$tld;
            // the rest is the sub-domain
            $subDomain = implode('.', $domainParts);
        }

        // If no sub-domain presents, then it should be handled as 'www'
        if (empty($subDomain)) {
            $subDomain = 'www';
        }

        $this->subDomain = $subDomain;
        $this->mainDomain = $domain;
        $this->applicationDomain = $this->subDomain.'.'. $this->mainDomain;
    }

    /**
     * From the parsed domain data, selects the application, module and theme.
     *
     * It's the user's and the application's responsi
     */
    private function selectModuleApplicationAndTheme()
    {
        $urlParts = parse_url($this->url);
        $applications = $this->config->getData('applications');

        // Only the first segment is important (if exists).
        list($subDirectory) = explode('/', ltrim($urlParts['path'], '/'), 1);

        $applicationDataFixture = [
            'type' => self::APPLICATION_TYPE_DIRECTORY,
            'module' => self::DEFAULT_MODULE,
            'theme' => self::DEFAULT_THEME,
        ];

        // Run through the available application-modules to validate and find active module
        foreach ($applications as $applicationName => $applicationData) {
            // Don't risk, fix.
            $applicationData = array_merge($applicationDataFixture, $applicationData);

            if ((
                    $this->subDomain == 'www' &&
                    $applicationData['type'] == self::APPLICATION_TYPE_DIRECTORY &&
                    $applicationData['path'] == $subDirectory &&
                    !empty($subDirectory)
                ) ||
                (
                    $this->subDomain != 'www' &&
                    $applicationData['type'] == self::APPLICATION_TYPE_DOMAIN &&
                    $applicationData['path'] == $this->subDomain
                )
            ) {
                $this->selectedModule = $applicationData['module'];
                $this->selectedApplication = $applicationName;
                $this->selectedTheme = $applicationData['theme'];

                // It's not the environment ma
                if ($this->selectedTheme !== self::DEFAULT_THEME) {
                    $this->selectedThemeResourcePath = '/resources/vendor_themes/'.$this->selectedTheme;
                }

                $this->selectedApplicationUri = '/'.$subDirectory;
                break;
            }
        }
    }
}
