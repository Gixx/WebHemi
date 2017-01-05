<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Adapter\Data\InMemory;

use WebHemi\Adapter\Data\DataDriverInterface;

/**
 * Class InMemoryDriver.
 *
 * Implements the DataDriverInterface, so the Dependency Injector can reference it.
 *
 * @codeCoverageIgnore - simple array setter/getter.
 */
class InMemoryDriver implements DataDriverInterface
{
    /** @var array */
    private $data = [];

    /**
     * InMemoryDriver constructor.
     *
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
