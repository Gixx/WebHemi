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

namespace WebHemi\Form\Element\Html;

/**
 * Class Html5Element.
 */
class Html5Element extends AbstractElement
{
    public const HTML5_ELEMENT_DATALIST = 'datalist';
    public const HTML5_ELEMENT_INPUT_COLOR = 'inputColor';
    public const HTML5_ELEMENT_INPUT_DATE = 'inputDate';
    public const HTML5_ELEMENT_INPUT_DATETIME = 'inputDateTime';
    public const HTML5_ELEMENT_INPUT_DATETIMELOCAL = 'inputDateTimeLocal';
    public const HTML5_ELEMENT_INPUT_EMAIL = 'inputEmail';
    public const HTML5_ELEMENT_INPUT_MONTH = 'inputMonth';
    public const HTML5_ELEMENT_INPUT_NUMBER = 'inputNumber';
    public const HTML5_ELEMENT_INPUT_RANGE = 'inputRange';
    public const HTML5_ELEMENT_INPUT_SEARCH = 'inputSearch';
    public const HTML5_ELEMENT_INPUT_TEL = 'inputTel';
    public const HTML5_ELEMENT_INPUT_TIME = 'inputTime';
    public const HTML5_ELEMENT_INPUT_URL = 'inputUrl';
    public const HTML5_ELEMENT_INPUT_WEEK = 'inputWeek';
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
