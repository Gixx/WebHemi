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
declare(strict_types=1);

namespace WebHemi\Form\Html;

/**
 * Class Html5FormElement
 */
class Html5FormElement extends HtmlFormElement
{
    public const HTML_ELEMENT_DATALIST = 'datalist';
    public const HTML_ELEMENT_INPUT_COLOR = 'color';
    public const HTML_ELEMENT_INPUT_DATE = 'date';
    public const HTML_ELEMENT_INPUT_DATETIME = 'datetime';
    public const HTML_ELEMENT_INPUT_DATETIMELOCAL = 'datetime-local';
    public const HTML_ELEMENT_INPUT_EMAIL = 'email';
    public const HTML_ELEMENT_INPUT_MONTH = 'month';
    public const HTML_ELEMENT_INPUT_NUMBER = 'number';
    public const HTML_ELEMENT_INPUT_RANGE = 'range';
    public const HTML_ELEMENT_INPUT_SEARCH = 'search';
    public const HTML_ELEMENT_INPUT_TEL = 'tel';
    public const HTML_ELEMENT_INPUT_TIME = 'time';
    public const HTML_ELEMENT_INPUT_URL = 'url';
    public const HTML_ELEMENT_INPUT_WEEK = 'week';
    public const HTML_ELEMENT_KEYGEN = 'keygen';
    public const HTML_ELEMENT_OUTPUT = 'output';

    /** @var array */
    protected $validTypes = [
        self::HTML_ELEMENT_DATALIST,
        self::HTML_ELEMENT_INPUT_COLOR,
        self::HTML_ELEMENT_INPUT_DATE,
        self::HTML_ELEMENT_INPUT_DATETIME,
        self::HTML_ELEMENT_INPUT_DATETIMELOCAL,
        self::HTML_ELEMENT_INPUT_EMAIL,
        self::HTML_ELEMENT_INPUT_MONTH,
        self::HTML_ELEMENT_INPUT_NUMBER,
        self::HTML_ELEMENT_INPUT_RANGE,
        self::HTML_ELEMENT_INPUT_SEARCH,
        self::HTML_ELEMENT_INPUT_TEL,
        self::HTML_ELEMENT_INPUT_TIME,
        self::HTML_ELEMENT_INPUT_URL,
        self::HTML_ELEMENT_INPUT_WEEK,
        self::HTML_ELEMENT_KEYGEN,
        self::HTML_ELEMENT_OUTPUT,
    ];
}
