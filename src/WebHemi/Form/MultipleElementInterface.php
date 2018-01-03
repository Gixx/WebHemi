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

namespace WebHemi\Form;

/**
 * Interface MultipleElementInterface
 */
interface MultipleElementInterface extends ElementInterface
{
    /**
     * Sets element to be multiple
     *
     * @param bool $isMultiple
     * @return MultipleElementInterface
     */
    public function setMultiple(bool $isMultiple) : MultipleElementInterface;

    /**
     * Gets element multiple flag.
     *
     * @return bool
     */
    public function getMultiple() : bool;
}
