<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Data\Storage;

use InvalidArgumentException;
use WebHemi\Data\Query\QueryInterface;
use WebHemi\Data\Entity\EntityInterface;
use WebHemi\Data\Entity\EntitySet;

/**
 * Interface StorageInterface.
 */
interface StorageInterface
{
    /**
     * StorageInterface constructor.
     *
     * @param QueryInterface $queryAdapter
     * @param EntitySet $entitySetPrototype
     * @param EntityInterface[] ...$entityPrototypes
     */
    public function __construct(
        QueryInterface $queryAdapter,
        EntitySet $entitySetPrototype,
        EntityInterface ...$entityPrototypes
    );

    /**
     * @return QueryInterface
     */
    public function getQueryAdapter() : QueryInterface;
}
