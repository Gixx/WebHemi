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
namespace WebHemiTest\TestService;

use WebHemi\Data\EntityInterface;

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
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return EntityInterface
     */
    public function setKeyData(int $entityId) : EntityInterface
    {
        $this->storage[$this->key] = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData() : int
    {
        return isset($this->key) ? $this->storage[$this->key] : null;
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
