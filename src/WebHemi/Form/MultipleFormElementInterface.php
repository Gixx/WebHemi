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

namespace WebHemi\Form;

/**
 * Interface MultipleFormElementInterface
 */
interface MultipleFormElementInterface extends FormElementInterface
{
    /**
     * Sets element to be multiple
     *
     * @param bool $isMultiple
     * @return MultipleFormElementInterface
     */
    public function setMultiple(bool $isMultiple) : MultipleFormElementInterface;

    /**
     * Gets element multiple flag.
     *
     * @return bool
     */
    public function getMultiple() : bool;
}
