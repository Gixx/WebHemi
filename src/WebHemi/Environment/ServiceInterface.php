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

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    public const APPLICATION_TYPE_DIRECTORY = 'directory';
    public const APPLICATION_TYPE_DOMAIN = 'domain';
    public const COOKIE_AUTO_LOGIN_PREFIX = 'atln';
    public const COOKIE_SESSION_PREFIX = 'atsn';
    public const DEFAULT_APPLICATION = 'website';
    public const DEFAULT_APPLICATION_URI = '/';
    public const DEFAULT_MODULE = 'Website';
    public const DEFAULT_THEME = 'default';
    public const DEFAULT_THEME_RESOURCE_PATH = '/resources/default_theme';
    public const SESSION_SALT = 'WebHemi';

    /**
     * ServiceInterface constructor.
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
    );

    /**
     * Gets the document root path.
     *
     * @return string
     */
    public function getDocumentRoot(): string;

    /**
     * Gets the application domain.
     *
     * @return string
     */
    public function getApplicationDomain(): string;

    /**
     * Gets the application SSL status.
     *
     * @return bool
     */
    public function isSecuredApplication(): bool;

    /**
     * Gets the selected application.
     *
     * @return string
     */
    public function getSelectedApplication(): string;

    /**
     * Get the URI path for the selected application. Required for the RouterAdapter to work with directory-based
     * applications correctly.
     *
     * @return string
     */
    public function getSelectedApplicationUri(): string;

    /**
     * Gets the request URI
     *
     * @return string
     */
    public function getRequestUri(): string;

    /**
     * Gets the selected module.
     *
     * @return string
     */
    public function getSelectedModule(): string;

    /**
     * Gets the selected theme.
     *
     * @return string
     */
    public function getSelectedTheme(): string;

    /**
     * Gets the resource path for the selected theme.
     *
     * @return string
     */
    public function getResourcePath(): string;

    /**
     * Gets the request method.
     *
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * Gets environment data.
     *
     * @param string $key
     * @return array
     */
    public function getEnvironmentData(string $key): array;

    /**
     * Gets the client IP address.
     *
     * @return string
     */
    public function getClientIp(): string;
}
