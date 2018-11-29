<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Data\Entity\EntityInterface;

/**
 * Class EmptyEntity.
 */
class EmptyEntity implements EntityInterface
{
    /** @var array */
    public $storage = [];
    /** @var string */
    public $key;

    /**
     * EmptyEntity constructor.
     *
     * @param string $key
     * @param mixed  $keyData
     */
    public function __construct($key = null, $keyData = null)
    {
        $this->key = $key;
        $this->storage[$this->key] = $keyData;
    }

    /**
     * Returns entity data as an array.
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->storage;
    }

    /**
     * Fills entity from an arrray.
     *
     * @param array $arrayData
     */
    public function fromArray(array $arrayData) : void
    {
        $this->storage = $arrayData;
    }

    /**
     * Handle getters and setters.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $matches = [];
        $return = null;

        if (preg_match('/^(?P<type>(get|set))(?P<property>.+)$/', $name, $matches)) {
            $property = lcfirst($matches['property']);
            $value = $arguments[0];

            if ($matches['type'] == 'set') {
                $this->storage[$property] = $value;
                $return = true;
            } else {
                if (isset($this->storage[$property])) {
                    $return = $this->storage[$property];
                }
            }
        }

        return $return;
    }
}
