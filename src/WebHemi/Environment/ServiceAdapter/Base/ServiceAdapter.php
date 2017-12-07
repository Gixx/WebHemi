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

namespace WebHemi\Environment\ServiceAdapter\Base;

use Exception;
use InvalidArgumentException;
use LayerShifter\TLDExtract\Extract;
use LayerShifter\TLDExtract\Result;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface;

/**
 * Class ServiceAdapter.
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var Extract */
    protected $domainAdapter;
    /** @var string */
    protected $url;
    /** @var string */
    protected $subDomain;
    /** @var string */
    protected $mainDomain;
    /** @var string */
    protected $applicationDomain;
    /** @var string */
    protected $documentRoot;
    /** @var string */
    protected $applicationRoot;
    /** @var string */
    protected $selectedModule;
    /** @var string */
    protected $selectedApplication;
    /** @var string */
    protected $selectedApplicationUri;
    /** @var string */
    protected $selectedTheme;
    /** @var string */
    protected $selectedThemeResourcePath;
    /** @var array  */
    protected $environmentData;
    /** @var bool */
    protected $isHttps;
    /** @var array */
    protected $options = [];

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param array                  $getData
     * @param array                  $postData
     * @param array                  $serverData
     * @param array                  $cookieData
     * @param array                  $filesData
     * @param array                  $optionsData
     * @throws Exception
     */
    public function __construct(
        ConfigurationInterface $configuration,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData,
        array $optionsData
    ) {
        $this->configuration = $configuration->getConfig('applications');
        $this->domainAdapter = new Extract();
        $this->applicationRoot = realpath(__DIR__.'/../../../../../');
        // In case when the backend sources are out of the document root.
        $this->documentRoot = realpath($this->applicationRoot.'/');
        $this->options = $optionsData;

        if (isset($serverData['HTTP_REFERER'])) {
            $serverData['HTTP_REFERER'] = urldecode($serverData['HTTP_REFERER']);
        }

        $this->environmentData = [
            'GET'    => $getData,
            'POST'   => $postData,
            'SERVER' => $serverData,
            'COOKIE' => $cookieData,
            'FILES'  => $filesData,
        ];

        $this->isHttps = isset($this->environmentData['SERVER']['HTTPS']) && $this->environmentData['SERVER']['HTTPS'];
        $this->url = 'http'.($this->isHttps ? 's' : '').'://'
            .$this->environmentData['SERVER']['HTTP_HOST']
            .$this->environmentData['SERVER']['REQUEST_URI']; // contains also the query string

        $this->selectedModule = self::DEFAULT_MODULE;
        $this->selectedApplication = self::DEFAULT_APPLICATION;
        $this->selectedTheme = self::DEFAULT_THEME;
        $this->selectedThemeResourcePath = self::DEFAULT_THEME_RESOURCE_PATH;
        $this->selectedApplicationUri = self::DEFAULT_APPLICATION_URI;

        $this->setDomain()
            ->setApplication();
    }

    /**
     * Gets the document root path.
     *
     * @return string
     */
    public function getDocumentRoot() : string
    {
        return $this->documentRoot;
    }

    /**
     * Gets the application path.
     *
     * @return string
     */
    public function getApplicationRoot(): string
    {
        return $this->applicationRoot;
    }

    /**
     * Gets the application domain.
     *
     * @return string
     */
    public function getApplicationDomain() : string
    {
        return $this->applicationDomain;
    }

    /**
     * Gets the application SSL status.
     *
     * @return bool
     */
    public function isSecuredApplication() : bool
    {
        return $this->isHttps;
    }

    /**
     * Gets the selected application.
     *
     * @return string
     */
    public function getSelectedApplication() : string
    {
        return $this->selectedApplication;
    }

    /**
     * Get the URI path for the selected application. Required for the RouterAdapter to work with directory-based
     * applications correctly.
     *
     * @return string
     */
    public function getSelectedApplicationUri() : string
    {
        return $this->selectedApplicationUri;
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function getRequestUri() : string
    {
        return rtrim($this->environmentData['SERVER']['REQUEST_URI'], '/');
    }

    /**
     * Gets the selected module.
     *
     * @return string
     */
    public function getSelectedModule() : string
    {
        return $this->selectedModule;
    }

    /**
     * Gets the selected theme.
     *
     * @return string
     */
    public function getSelectedTheme() : string
    {
        return $this->selectedTheme;
    }

    /**
     * Gets the resource path for the selected theme.
     *
     * @return string
     */
    public function getResourcePath() : string
    {
        return $this->selectedThemeResourcePath;
    }

    /**
     * Gets the request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->environmentData['SERVER']['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Gets environment data.
     *
     * @param string $key
     * @return array
     */
    public function getEnvironmentData(string $key) : array
    {
        if (!isset($this->environmentData[$key])) {
            throw new InvalidArgumentException(sprintf('The "%s" is not a valid environment key.', $key));
        }

        return $this->environmentData[$key];
    }

    /**
     * Gets the client IP address.
     *
     * @return string
     */
    public function getClientIp() : string
    {
        $ipAddress = '';

        if (!empty($this->environmentData['SERVER']['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $this->environmentData['SERVER']['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($this->environmentData['SERVER']['REMOTE_ADDR'])) {
            $ipAddress = $this->environmentData['SERVER']['REMOTE_ADDR'];
        }

        return (string) $ipAddress;
    }

    /**
     * Gets the execution parameters (CLI).
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * Parses server data and tries to set domain information.
     *
     * @throws Exception
     * @return ServiceAdapter
     */
    private function setDomain() : ServiceAdapter
    {
        if ('dev' == getenv('APPLICATION_ENV')) {
            $this->domainAdapter->setExtractionMode(Extract::MODE_ALLOW_NOT_EXISTING_SUFFIXES);
        }

        /** @var Result $domainParts */
        $domainParts = $this->domainAdapter->parse($this->url);

        if (empty($domainParts->getSuffix())) {
            throw new Exception('This application does not support IP access');
        }

        // Redirecting to www when no subdomain is present
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE') && empty($domainParts->getSubdomain())) {
            $schema = 'http'.($this->isSecuredApplication() ? 's' : '').'://';
            $uri = $this->environmentData['SERVER']['REQUEST_URI'];
            header('Location: '.$schema.'www'.$domainParts->getFullHost().$uri);
            exit;
        }
        // @codeCoverageIgnoreEnd

        $this->subDomain = $domainParts->getSubdomain();
        $this->mainDomain = $domainParts->getHostname().'.'.$domainParts->getSuffix();
        $this->applicationDomain = $domainParts->getFullHost();

        return $this;
    }

    /**
     * Sets application related data.
     *
     * @throws Exception
     * @return ServiceAdapter
     */
    private function setApplication() : ServiceAdapter
    {
        // for safety purposes
        if (!isset($this->applicationDomain)) {
            $this->setDomain();
        }

        $urlParts = parse_url($this->url);
        list($subDirectory) = explode('/', ltrim($urlParts['path'], '/'), 2);

        $applications = $this->configuration->toArray();
        $aplicationNames = array_keys($applications);
        $selectedApplication = self::DEFAULT_APPLICATION;

        foreach ($aplicationNames as $applicationName) {
            if ($this->checkDirectoryIsValid($applicationName, $subDirectory)
                || $this->checkDomainIsValid($applicationName)
            ) {
                $selectedApplication = $applicationName;
                break;
            }
        }

        $applicationData = $applications[$selectedApplication];

        $this->selectedModule = $applicationData['module'] ?? self::DEFAULT_MODULE;
        $this->selectedApplication = $selectedApplication;
        $this->selectedTheme = $applicationData['theme'] ?? self::DEFAULT_THEME;
        $this->selectedApplicationUri = $applicationData['type'] == self::APPLICATION_TYPE_DIRECTORY
            ? '/'.$subDirectory
            : '/';

        // Final check for config and resources.
        if ($this->selectedTheme !== self::DEFAULT_THEME) {
            $this->selectedThemeResourcePath = '/resources/vendor_themes/'.$this->selectedTheme;
        }

        return $this;
    }

    /**
     * Checks from type, path it the current URI segment is valid.
     *
     * @param string $applicationName
     * @param string $subDirectory
     * @return bool
     */
    private function checkDirectoryIsValid(string $applicationName, string $subDirectory) : bool
    {
        $applications = $this->configuration->toArray();
        $applicationData = $applications[$applicationName];

        return $applicationName != 'website'
            && $this->applicationDomain == $applicationData['domain']
            && !empty($subDirectory)
            && $applicationData['type'] == self::APPLICATION_TYPE_DIRECTORY
            && $applicationData['path'] == '/'.$subDirectory;
    }

    /**
     * Checks from type and path if the domain is valid. If so, it sets the $subDirectory to the default.
     *
     * @param string $applicationName
     * @return bool
     */
    private function checkDomainIsValid(string $applicationName) : bool
    {
        $applications = $this->configuration->toArray();
        $applicationData = $applications[$applicationName];

        return $this->applicationDomain == $applicationData['domain']
            && $applicationData['type'] == self::APPLICATION_TYPE_DOMAIN;
    }
}
