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

namespace WebHemi\Form\Html;

/**
 * Class Html5FormElement
 */
class Html5FormElement extends AbstractHtmlFormElement
{
    public const HTML5_ELEMENT_DATALIST = 'datalist';
    public const HTML5_ELEMENT_INPUT_COLOR = 'color';
    public const HTML5_ELEMENT_INPUT_DATE = 'date';
    public const HTML5_ELEMENT_INPUT_DATETIME = 'datetime';
    public const HTML5_ELEMENT_INPUT_DATETIMELOCAL = 'datetime-local';
    public const HTML5_ELEMENT_INPUT_EMAIL = 'email';
    public const HTML5_ELEMENT_INPUT_MONTH = 'month';
    public const HTML5_ELEMENT_INPUT_NUMBER = 'number';
    public const HTML5_ELEMENT_INPUT_RANGE = 'range';
    public const HTML5_ELEMENT_INPUT_SEARCH = 'search';
    public const HTML5_ELEMENT_INPUT_TEL = 'tel';
    public const HTML5_ELEMENT_INPUT_TIME = 'time';
    public const HTML5_ELEMENT_INPUT_URL = 'url';
    public const HTML5_ELEMENT_INPUT_WEEK = 'week';
    public const HTML5_ELEMENT_KEYGEN = 'keygen';
    public const HTML5_ELEMENT_OUTPUT = 'output';

    /** @var array */
    protected $validTypes = [
        self::HTML5_ELEMENT_DATALIST,
        self::HTML5_ELEMENT_INPUT_COLOR,
        self::HTML5_ELEMENT_INPUT_DATE,
        self::HTML5_ELEMENT_INPUT_DATETIME,
        self::HTML5_ELEMENT_INPUT_DATETIMELOCAL,
        self::HTML5_ELEMENT_INPUT_EMAIL,
        self::HTML5_ELEMENT_INPUT_MONTH,
        self::HTML5_ELEMENT_INPUT_NUMBER,
        self::HTML5_ELEMENT_INPUT_RANGE,
        self::HTML5_ELEMENT_INPUT_SEARCH,
        self::HTML5_ELEMENT_INPUT_TEL,
        self::HTML5_ELEMENT_INPUT_TIME,
        self::HTML5_ELEMENT_INPUT_URL,
        self::HTML5_ELEMENT_INPUT_WEEK,
        self::HTML5_ELEMENT_KEYGEN,
        self::HTML5_ELEMENT_OUTPUT,
    ];
}
