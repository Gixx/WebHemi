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
declare(strict_types = 1);

namespace WebHemi\Acl;

use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * Checks if a User can access to a Resource in an Application
     *
     * @param UserEntity             $userEntity
     * @param ResourceEntity|null    $resourceEntity
     * @param ApplicationEntity|null $applicationEntity
     * @return bool
     */
    public function isAllowed(
        UserEntity $userEntity,
        ? ResourceEntity $resourceEntity = null,
        ? ApplicationEntity $applicationEntity = null
    ) : bool;
}
