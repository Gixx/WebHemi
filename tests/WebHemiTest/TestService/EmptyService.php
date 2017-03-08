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
namespace WebHemiTest\TestService;

/**
 * Class EmptyService
 */
class EmptyService
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
        $this->storage[$key] = $keyData;
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

            if ($matches['type'] == 'set') {
                $this->storage[$property] = $arguments[0];
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
