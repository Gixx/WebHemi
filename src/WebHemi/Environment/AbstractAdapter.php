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

namespace WebHemi\Environment;

use Exception;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Class AbstractAdapter.
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class AbstractAdapter implements ServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;
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
    abstract public function __construct(
        ConfigurationInterface $configuration,
        array $getData,
        array $postData,
        array $serverData,
        array $cookieData,
        array $filesData,
        array $optionsData
    );

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
     * Gets the full address
     *
     * @return string
     */
    public function getAddress() : string
    {
        return $this->url;
    }

    /**
     * Gets the request URI
     *
     * @return string
     */
    abstract public function getRequestUri() : string;

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
    abstract public function getRequestMethod(): string;

    /**
     * Gets environment data.
     *
     * @param string $key
     * @return array
     */
    abstract public function getEnvironmentData(string $key) : array;

    /**
     * Gets the client IP address.
     *
     * @return string
     */
    abstract public function getClientIp() : string;

    /**
     * Gets the execution parameters (CLI).
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }
}
