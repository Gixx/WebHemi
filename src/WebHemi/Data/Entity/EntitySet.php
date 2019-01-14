<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Entity;

use ArrayAccess;
use InvalidArgumentException;
use Iterator;

/**
 * Class EntitySet.
 *
 * We have some strictness additions to the original ArrayAccess and Iterator interfaces.
 * A) The offset must be Integer.
 * B) The value must be an object that implements the EntityInterface.
 */
class EntitySet implements ArrayAccess, Iterator
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * Checks whether an offset exists.
     *
     * @param int $offset
     * @throws InvalidArgumentException
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        if (!\is_int($offset)) {
            throw new InvalidArgumentException(
                sprintf(__METHOD__.' requires parameter 1 to be integer, %s given.', gettype($offset)),
                1000
            );
        }

        return isset($this->container[$offset]);
    }

    /**
     * Returns the value at an offset.
     *
     * @param int $offset
     * @throws InvalidArgumentException
     * @return null|EntityInterface
     */
    public function offsetGet($offset): ? EntityInterface
    {
        if (!\is_int($offset)) {
            throw new InvalidArgumentException(
                sprintf(__METHOD__.' requires parameter 1 to be integer, %s given.', gettype($offset)),
                1001
            );
        }

        return $this->container[$offset] ?? null;
    }

    /**
     * Sets a value for an offset. It appends it if offset is Null.
     *
     * @param null|int $offset
     * @param EntityInterface $value
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value) : void
    {
        if ($offset === null) {
            $offset = empty($this->container) ? 0 : max(array_keys($this->container)) + 1;
        }

        if (!\is_int($offset)) {
            throw new InvalidArgumentException(
                sprintf(__METHOD__.' requires parameter 1 to be integer, %s given.', gettype($offset)),
                1002
            );
        }

        if (!$value instanceof EntityInterface) {
            $valueType = is_object($value) ? get_class($value) : gettype($value);

            throw new InvalidArgumentException(
                sprintf(__METHOD__.' requires parameter 2 to be an instance of EntityInterface, %s given.', $valueType),
                1003
            );
        }

        $this->container[$offset] = $value;
    }

    /**
     * Deletes a record from the container at an offset. It will not reorder the container.
     *
     * @param mixed $offset
     * @throws InvalidArgumentException
     */
    public function offsetUnset($offset) : void
    {
        if (!\is_int($offset)) {
            throw new InvalidArgumentException(
                sprintf(__METHOD__.' requires parameter 1 to be integer, %s given.', gettype($offset)),
                1004
            );
        }

        if ($this->offsetExists($offset)) {
            unset($this->container[$offset]);
        }
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind() : void
    {
        reset($this->container);
    }

    /**
     * Returns the current element.
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * Returns the key of the current element.
     *
     * @return int|mixed|null|string
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * Moves forward to next element.
     */
    public function next() : void
    {
        next($this->container);
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid() : bool
    {
        return key($this->container) !== null;
    }

    /**
     * Return the raw container
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->container;
    }

    /**
     * Merges another EntitySet into the current container.
     *
     * @param EntitySet $entitySet
     */
    public function merge(EntitySet $entitySet) : void
    {
        $this->container = array_merge($this->container, $entitySet->toArray());
    }
}
