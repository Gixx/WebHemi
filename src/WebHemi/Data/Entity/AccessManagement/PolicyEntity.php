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

namespace WebHemi\Data\Entity\AccessManagement;

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class PolicyEntity.
 */
class PolicyEntity implements EntityInterface
{
    /**
     * @var int
     */
    private $policyId;
    /**
     * @var int
     */
    private $resourceId;
    /**
     * @var int
     */
    private $applicationId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $method;
    /**
     * @var bool
     */
    private $isReadOnly;
    /**
     * @var DateTime
     */
    private $dateCreated;
    /**
     * @var DateTime
     */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param  int $entityId
     * @return PolicyEntity
     */
    public function setKeyData(int $entityId) : PolicyEntity
    {
        $this->policyId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->policyId;
    }

    /**
     * @param int $policyId
     * @return PolicyEntity
     */
    public function setPolicyId(int $policyId) : PolicyEntity
    {
        $this->policyId = $policyId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getPolicyId() : ? int
    {
        return $this->policyId;
    }

    /**
     * @param null|int $resource
     * @return PolicyEntity
     */
    public function setResourceId(? int $resource) : PolicyEntity
    {
        $this->resourceId = $resource;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getResourceId() : ? int
    {
        return $this->resourceId;
    }

    /**
     * @param null|int $applicationId
     * @return PolicyEntity
     */
    public function setApplicationId(? int $applicationId) : PolicyEntity
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getApplicationId() : ? int
    {
        return $this->applicationId;
    }

    /**
     * @param string $name
     * @return PolicyEntity
     */
    public function setName(string $name) : PolicyEntity
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName() : ? string
    {
        return $this->name;
    }

    /**
     * @param string $title
     * @return PolicyEntity
     */
    public function setTitle(string $title) : PolicyEntity
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->title;
    }

    /**
     * @param string $description
     * @return PolicyEntity
     */
    public function setDescription(string $description) : PolicyEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->description;
    }

    /**
     * @param null|string $method
     * @return PolicyEntity
     */
    public function setMethod(? string $method) : PolicyEntity
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMethod() : ? string
    {
        return $this->method;
    }

    /**
     * @param bool $state
     * @return PolicyEntity
     */
    public function setReadOnly(bool $state) : PolicyEntity
    {
        $this->isReadOnly = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly() : bool
    {
        return $this->isReadOnly ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return PolicyEntity
     */
    public function setDateCreated(DateTime $dateCreated) : PolicyEntity
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param null|DateTime $dateModified
     * @return PolicyEntity
     */
    public function setDateModified(? DateTime $dateModified) : PolicyEntity
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return $this->dateModified;
    }
}
