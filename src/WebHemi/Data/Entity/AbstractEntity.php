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

use InvalidArgumentException;

/**
 * Class AbstractEntity
 */
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var array
     */
    protected $container = [];

    /**
     * @var array
     */
    protected $referenceContainer = [];

    /**
     * Set reference entity.
     *
     * @param string $referenceName
     * @param EntityInterface $referenceEntity
     * @return bool
     */
    public function setReference(string $referenceName, EntityInterface $referenceEntity): bool
    {
        if (!\array_key_exists($referenceName, $this->referenceContainer)) {
            return false;
        }

        $this->referenceContainer[$referenceName] = $referenceEntity;

        return true;
    }

    /**
     * Get reference entity by key
     *
     * @param string $referenceName
     * @return EntityInterface|null
     */
    public function getReference(string $referenceName): ? EntityInterface
    {
        if (isset($this->referenceContainer[$referenceName])
            && $this->referenceContainer[$referenceName] instanceof EntityInterface
        ) {
            return $this->referenceContainer[$referenceName];
        }

        return null;
    }

    /**
     * Returns entity data as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->container;
    }

    /**
     * Fills entity from an arrray.
     *
     * @param array $arrayData
     */
    public function fromArray(array $arrayData): void
    {
        foreach ($arrayData as $key => $value) {
            if (!\array_key_exists($key, $this->container)) {
                throw new InvalidArgumentException(
                    sprintf('"%s" is not defined in '.static::class, $key),
                    1001
                );
            }

            $this->container[$key] = $value;
        }
    }
}
