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

namespace WebHemi\Form\Element\Html;

/**
 * Class HtmlElement.
 */
class HtmlElement extends AbstractElement
{
    public const HTML_ELEMENT_BUTTON = 'button';
    public const HTML_ELEMENT_FORM = 'form';
    public const HTML_ELEMENT_HIDDEN = 'hidden';
    public const HTML_ELEMENT_INPUT_CHECKBOX = 'inputCheckbox';
    public const HTML_ELEMENT_INPUT_FILE = 'inputFile';
    public const HTML_ELEMENT_INPUT_IMAGE = 'inputImage';
    public const HTML_ELEMENT_INPUT_PASSWORD = 'inputPassword';
    public const HTML_ELEMENT_INPUT_RADIO = 'inputRadio';
    public const HTML_ELEMENT_INPUT_TEXT = 'inputText';
    public const HTML_ELEMENT_RESET = 'reset';
    public const HTML_ELEMENT_SELECT = 'select';
    public const HTML_ELEMENT_SUBMIT = 'submit';
    public const HTML_ELEMENT_TEXTAREA = 'textarea';

    /**
     * @var array
     */
    protected $validTypes = [
        self::HTML_ELEMENT_BUTTON,
        self::HTML_ELEMENT_FORM,
        self::HTML_ELEMENT_HIDDEN,
        self::HTML_ELEMENT_INPUT_CHECKBOX,
        self::HTML_ELEMENT_INPUT_FILE,
        self::HTML_ELEMENT_INPUT_IMAGE,
        self::HTML_ELEMENT_INPUT_PASSWORD,
        self::HTML_ELEMENT_INPUT_RADIO,
        self::HTML_ELEMENT_INPUT_TEXT,
        self::HTML_ELEMENT_RESET,
        self::HTML_ELEMENT_SELECT,
        self::HTML_ELEMENT_SUBMIT,
        self::HTML_ELEMENT_TEXTAREA,
    ];
}
