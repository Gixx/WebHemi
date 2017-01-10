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
namespace WebHemi\Form\Traits;

use WebHemi\Form\Element\FormElementInterface;

/**
 * Class IteratorTrait
 *
 * @codeCoverageIgnore - general implementation of the Iterator interface.
 */
trait IteratorTrait
{
    /** @var array<FormElementInterface> */
    protected $nodes = [];

    /**
     * Return the current element.
     *
     * @return FormElementInterface
     */
    public function current()
    {
        return current($this->nodes);
    }

    /**
     * Moves the pointer forward to next element.
     *
     * @return void
     */
    public function next()
    {
        next($this->nodes);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->nodes);
    }

    /**
     * Checks if current position is valid.
     *
     * @return boolean
     */
    public function valid()
    {
        $key = key($this->nodes);

        return ($key !== null && $key !== false);
    }

    /**
     * Rewinds the Iterator to the first element.
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->nodes);
    }
}
