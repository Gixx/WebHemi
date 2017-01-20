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

use WebHemi\Form\MultipleFormElementInterface;

/**
 * Class HtmlMultipleFormElement
 */
class HtmlMultipleFormElement extends AbstractHtmlFormElement implements MultipleFormElementInterface
{
    public const HTML_MULTIPLE_ELEMENT_INPUT_FILE = 'file';
    public const HTML_MULTIPLE_ELEMENT_SELECT = 'select';

    /** @var bool */
    private $isMultiple = false;
    /** @var array */
    protected $validTypes = [
        self::HTML_MULTIPLE_ELEMENT_INPUT_FILE,
        self::HTML_MULTIPLE_ELEMENT_SELECT,
    ];

    /**
     * Sets element to be multiple
     *
     * @param bool $isMultiple
     * @return MultipleFormElementInterface
     */
    public function setMultiple(bool $isMultiple) : MultipleFormElementInterface
    {
        $this->isMultiple = $isMultiple;

        return $this;
    }

    /**
     * Gets element multiple flag.
     *
     * @return bool
     */
    public function getMultiple() : bool
    {
        return $this->isMultiple;
    }
}
