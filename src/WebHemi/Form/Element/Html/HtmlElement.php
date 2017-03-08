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

namespace WebHemi\Form\Element\Html;

/**
 * Class HtmlElement.
 */
class HtmlElement extends AbstractElement
{
    public const HTML_ELEMENT_BUTTON = 'button';
    public const HTML_ELEMENT_FORM = 'form';
    public const HTML_ELEMENT_INPUT_CHECKBOX = 'checkbox';
    public const HTML_ELEMENT_INPUT_FILE = 'file';
    public const HTML_ELEMENT_INPUT_HIDDEN = 'hidden';
    public const HTML_ELEMENT_INPUT_IMAGE = 'IMAGE';
    public const HTML_ELEMENT_INPUT_PASSWORD = 'password';
    public const HTML_ELEMENT_INPUT_RADIO = 'radio';
    public const HTML_ELEMENT_INPUT_RESET = 'reset';
    public const HTML_ELEMENT_INPUT_SUBMIT = 'submit';
    public const HTML_ELEMENT_INPUT_TEXT = 'text';
    public const HTML_ELEMENT_SELECT = 'select';
    public const HTML_ELEMENT_TEXTAREA = 'textarea';

    /** @var array */
    protected $validTypes = [
        self::HTML_ELEMENT_BUTTON,
        self::HTML_ELEMENT_FORM,
        self::HTML_ELEMENT_INPUT_CHECKBOX,
        self::HTML_ELEMENT_INPUT_FILE,
        self::HTML_ELEMENT_INPUT_HIDDEN,
        self::HTML_ELEMENT_INPUT_IMAGE,
        self::HTML_ELEMENT_INPUT_PASSWORD,
        self::HTML_ELEMENT_INPUT_RADIO,
        self::HTML_ELEMENT_INPUT_RESET,
        self::HTML_ELEMENT_INPUT_SUBMIT,
        self::HTML_ELEMENT_INPUT_TEXT,
        self::HTML_ELEMENT_SELECT,
        self::HTML_ELEMENT_TEXTAREA,
    ];
}
