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

use InvalidArgumentException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var ConfigurationInterface */
    private $configuration;
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
    /** @var bool */
    private $isHttps;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param array                  $getData
     * @param array                  $postData
     * @param array                  $serverData
     * @param array                  $cookieData
     * @param array                  $filesData
     */
    public function __construct(
        ConfigurationInterface $configuration,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData
    ) {
        $this->configuration = $configuration->getConfig('applications');
        $this->documentRoot = realpath(__DIR__.'/../../../../../');

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
            ->selectModuleApplicationAndTheme();
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
     * Parses server data and tries to set domain information.
     *
     * @return ServiceAdapter
     */
    private function setDomain() : ServiceAdapter
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

        // If no sub-domain presents, then it should be handled as the default sub-domain set for the 'website'
        if (empty($subDomain)) {
            $subDomain = $this->configuration->getData('website/path')[0];
        }

        $this->subDomain = $subDomain;
        $this->mainDomain = $domain;
        $this->applicationDomain = $this->subDomain.'.'.$this->mainDomain;

        // Redirecting when the app domain is not equal to the server data
        // @codeCoverageIgnoreStart
        if (!defined('PHPUNIT_WEBHEMI_TESTSUITE')
            && $this->environmentData['SERVER']['HTTP_HOST'] != $this->applicationDomain
        ) {
            $schema = 'http'.($this->isSecuredApplication() ? 's' : '').'://';
            $uri = $this->environmentData['SERVER']['REQUEST_URI'];
            header('Location: '.$schema.$this->applicationDomain.$uri);
            exit;
        }
        // @codeCoverageIgnoreEnd

        return $this;
    }

    /**
     * From the parsed domain data, selects the application, module and theme.
     *
     * @return ServiceAdapter
     */
    private function selectModuleApplicationAndTheme() : ServiceAdapter
    {
        $urlParts = parse_url($this->url);
        $applications = $this->configuration->toArray();

        // Only the first segment is important (if exists).
        list($subDirectory) = explode('/', ltrim($urlParts['path'], '/'), 2);

        $applicationDataFixture = [
            'type' => self::APPLICATION_TYPE_DIRECTORY,
            'module' => self::DEFAULT_MODULE,
            'theme' => self::DEFAULT_THEME,
        ];

        // Run through the available application-modules to validate and find active module
        foreach ($applications as $applicationName => $applicationData) {
            // Don't risk, fix.
            $applicationData = array_merge($applicationDataFixture, $applicationData);

            if ($this->checkDirectoryIsValid($applicationName, $applicationData, $subDirectory)
                || $this->checkDomainIsValid($applicationName, $applicationData, $subDirectory)
            ) {
                $this->selectedModule = $applicationData['module'];
                $this->selectedApplication = (string) $applicationName;
                $this->selectedTheme = $applicationData['theme'];

                $this->selectedApplicationUri = '/'.$subDirectory;
                break;
            }
        }

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
     * @param array  $applicationData
     * @param string $subDirectory
     * @return bool
     */
    private function checkDirectoryIsValid(string $applicationName, array $applicationData, string $subDirectory) : bool
    {
        return $this->subDomain == $this->configuration->getData('website/path')[0]
            && $applicationName != 'website'
            && !empty($subDirectory)
            && $applicationData['type'] == self::APPLICATION_TYPE_DIRECTORY
            && $applicationData['path'] == $subDirectory;
    }

    /**
     * Checks from type and path if the domain is valid. If so, it sets the $subDirectory to the default.
     *
     * @param string $applicationName
     * @param array  $applicationData
     * @param string $subDirectory
     * @return bool
     */
    private function checkDomainIsValid(string $applicationName, array $applicationData, string&$subDirectory) : bool
    {
        $isSubdomain = $applicationName == 'website'
            || (
                $this->subDomain != $this->configuration->getData('website/path')[0]
                && $applicationData['type'] == self::APPLICATION_TYPE_DOMAIN
                && $applicationData['path'] == $this->subDomain
            );

        // If this method get called and will return TRUE, it means the $subDirectory paramtere will be used only for
        // setting the right selectedApplicationUri. To avoid complexity, we change it here. Doesn't matter.
        if ($isSubdomain) {
            $subDirectory = '';
        }

        return $isSubdomain;
    }
}
