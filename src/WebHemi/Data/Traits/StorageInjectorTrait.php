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

namespace WebHemi\Data\Traits;

use WebHemi\Data\StorageInterface;
use WebHemi\Data\Storage;

/**
 * Trait StorageTrait
 */
trait StorageInjectorTrait
{
    /** @var StorageInterface[] */
    protected $dataStorages = [];

    /**
     * Add Storage instances to internal library.
     *
     * @param StorageInterface[] ...$dataStorages
     */
    protected function addStorageInstances(array $dataStorages) : void
    {
        foreach ($dataStorages as $instance) {
            if ($instance instanceof StorageInterface) {
                $storageClassFullName = get_class($instance);
                $classNamespacePath = explode('\\', $storageClassFullName);

                $storageClass = array_pop($classNamespacePath);

                $this->dataStorages[$storageClass] = $instance;
            }
        }
    }

    /**
     * Retrieves
     *
     * @param string $name
     * @param array $arguments
     * @return null|StorageInterface
     */
    public function __call(string $name, array $arguments) : ? StorageInterface
    {
        unset($arguments);

        $className = preg_replace('/^get/', '', $name);

        return $this->dataStorages[$className] ?? null;
    }
}
