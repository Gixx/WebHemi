<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Fixtures;

use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class EmptyEntity.
 */
class EmptyEntity implements DataEntityInterface
{
    /** @var array */
    private $storage = [];
    /** @var string */
    private $key;

    /**
     * Gets the value of the entity identifier.
     *
     * @return mixed
     */
    public function getKeyData()
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
