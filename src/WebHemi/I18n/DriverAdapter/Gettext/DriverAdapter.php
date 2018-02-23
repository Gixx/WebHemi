<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\I18n\DriverAdapter\Gettext;

use RuntimeException;
use WebHemi\I18n\ServiceInterface;
use WebHemi\I18n\DriverInterface;

/**
 * Interface DriverInterface.
 *
 * @codeCoverageIgnore - uses PHP extension
 */
class DriverAdapter implements DriverInterface
{
    /**
     * @var string
     */
    private $translationPath;
    /**
     * @var ServiceInterface
     */
    private $i18nService;
    /**
     * @var string
     */
    private $textDomain;

    /**
     * DriverInterface constructor.
     *
     * @param ServiceInterface $i18nService
     */
    public function __construct(ServiceInterface $i18nService)
    {
        $this->i18nService = $i18nService;
        $this->translationPath = realpath(__DIR__.'/../../Translation');
        $this->textDomain = 'messages';

        if (!extension_loaded('gettext') || !function_exists('bindtextdomain')) {
            throw new RuntimeException('The gettext module is not loaded!');
        }

        $this->initDriver();
    }

    /**
     * Initializes the driver.
     */
    private function initDriver()
    {
        bind_textdomain_codeset($this->textDomain, $this->i18nService->getCodeSet());
        bindtextdomain($this->textDomain, $this->translationPath);
        textdomain($this->textDomain);
    }

    /**
     * Translates the given text.
     *
     * @param  string $text
     * @return string
     */
    public function translate(string $text) : string
    {
        return gettext($text);
    }
}
