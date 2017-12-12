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

namespace WebHemi\I18n;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager
    );

    /**
     * Gets the language.
     *
     * @return string
     */
    public function getLanguage() : string;

    /**
     * Gets the territory.
     *
     * @return string
     */
    public function getTerritory() : string;

    /**
     * Gets the Locale.
     *
     * @return string
     */
    public function getLocale() : string;

    /**
     * Gets the code set.
     *
     * @return string
     */
    public function getCodeSet() : string;

    /**
     * Sets the locale.
     *
     * @param string $locale
     * @return ServiceInterface
     */
    public function setLocale(string $locale);

    /**
     * Gets the time zone.
     *
     * @return string
     */
    public function getTimeZone() : string;

    /**
     * Sets the time zone.
     *
     * @param string $timeZone
     * @return ServiceInterface
     */
    public function setTimeZone(string $timeZone);
}
