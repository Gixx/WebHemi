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

use WebHemi\I18n\ServiceInterface;
use WebHemi\I18n\DriverInterface;

/**
 * Class EmptyI18nDriver
 */
class EmptyI18nDriver implements DriverInterface
{
    /** @var ServiceInterface */
    private $i18nService;
    /** @var array */
    public $dictionary = [];

    /**
     * DriverInterface constructor.
     *
     * @param ServiceInterface $i18nService
     */
    public function __construct(ServiceInterface $i18nService)
    {
        $this->i18nService = $i18nService;
    }

    /**
     * Translates the given text.
     *
     * @param string $text
     * @return string
     */
    public function translate(string $text) : string
    {
        return $this->dictionary[$text] ?? $text;
    }
}
