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

namespace WebHemi\I18n\ServiceAdapter\Base;

use InvalidArgumentException;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\I18n\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter implements ServiceInterface
{
    /** @var ConfigurationInterface */
    protected $configuration;
    /** @var EnvironmentInterface */
    protected $environmentManager;
    /** @var string */
    protected $language;
    /** @var string */
    protected $territory;
    /** @var string */
    protected $codeSet;
    /** @var string */
    protected $locale;
    /** @var string */
    protected $timeZone;

    /**
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager
    ) {
        $this->configuration = $configuration;
        $this->environmentManager = $environmentManager;

        $currentApplicationConfig = $configuration
            ->getConfig('applications')
            ->getData($environmentManager->getSelectedApplication());

        $this->setLocale($currentApplicationConfig['locale']);
        $this->setTimeZone($currentApplicationConfig['timezone']);
    }

    /**
     * Gets the language.
     *
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Sets the language.
     *
     * @param string $language
     */
    protected function setLanguage(string $language) : void
    {
        $this->language = $language;

        putenv('LANGUAGE='.$language);
        putenv('LANG='.$language);
        setlocale(LC_TIME, '');
    }

    /**
     * Gets the territory.
     *
     * @return string
     */
    public function getTerritory() : string
    {
        return $this->territory;
    }

    /**
     * Gets the Locale.
     *
     * @return string
     */
    public function getLocale() : string
    {
        return $this->locale;
    }

    /**
     * Gets the code set.
     *
     * @return string
     */
    public function getCodeSet() : string
    {
        return $this->codeSet;
    }

    /**
     * Sets the locale.
     *
     * @param string $locale
     * @throws InvalidArgumentException
     * @return ServiceAdapter
     */
    public function setLocale(string $locale) : ServiceAdapter
    {
        $matches = [];

        if (preg_match('/^(?P<language>[a-z]+)_(?P<territory>[A-Z]+)(?:\.(?P<codeSet>.*))?/', $locale, $matches)) {
            $language = $matches['language'];
            $this->setLanguage($language);

            $this->territory = $matches['territory'];

            $this->codeSet = $matches['codeSet'] ?? 'UTF-8';
            $localeCodeSet = strtolower(str_replace('-', '', $this->codeSet));

            $composedLocale = $language.'_'.$this->territory.'.'.$localeCodeSet;

            putenv('LC_ALL='.$composedLocale);
            setlocale(LC_ALL, $composedLocale);
            setlocale(LC_MESSAGES, $composedLocale);
            setlocale(LC_CTYPE, $composedLocale);

            $this->locale = $composedLocale;
        } else {
            throw new InvalidArgumentException(sprintf('Invalid locale: %s', $locale), 1000);
        }

        return $this;
    }

    /**
     * Sets the time zone.
     *
     * @param string $timeZone
     * @return ServiceAdapter
     */
    public function setTimeZone(string $timeZone) : ServiceAdapter
    {
        $this->timeZone = $timeZone;

        date_default_timezone_set($timeZone);

        return $this;
    }

    /**
     * Gets the time zone.
     *
     * @return string
     */
    public function getTimeZone() : string
    {
        return $this->timeZone;
    }
}
