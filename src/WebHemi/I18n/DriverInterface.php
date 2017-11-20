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

/**
 * Interface DriverInterface.
 */
interface DriverInterface
{
    /**
     * DriverInterface constructor.
     *
     * @param ServiceInterface $i18nService
     */
    public function __construct(ServiceInterface $i18nService);

    /**
     * Translates the given text.
     *
     * @param string $text
     * @return string
     */
    public function translate(string $text) : string;
}
