<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\I18n\ServiceAdapter\Base\ServiceAdapter as I18nService;

/**
 * Class EmptyI18nService
 */
class EmptyI18nService extends I18nService
{
    /**
     * Sets the locale.
     *
     * @param string $locale
     * @return I18nService
     */
    public function setLocale(string $locale) : I18nService
    {
        $matches = [];

        if (preg_match('/^(?P<language>[a-z]+)_(?P<territory>[A-Z]+)(?:\.(?P<codeSet>.*))?/', $locale, $matches)) {
            $this->language = $matches['language'];
            $this->territory = $matches['territory'];
            $this->codeSet = $matches['codeSet'] ?? 'utf8';
            $this->locale = $locale;
        }

        return $this;
    }

    /**
     * Sets the time zone.
     *
     * @param string $timeZone
     * @return I18nService
     */
    public function setTimeZone(string $timeZone) : I18nService
    {
        $this->timeZone = $timeZone;

        return $this;
    }
}
