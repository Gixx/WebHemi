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
namespace WebHemi\Data\Entity\AccessManagement;

use DateTime;
use WebHemi\Data\Entity\DataEntityInterface;

/**
 * Class PolicyEntity.
 */
class PolicyEntity implements DataEntityInterface
{
    /** @var int */
    private $policyId;
    /** @var int */
    private $resourceId;
    /** @var int */
    private $applicationId;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var bool */
    private $isReadOnly;
    /** @var bool */
    private $isAllowed;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return PolicyEntity
     */
    public function setKeyData($entityId)
    {
        $this->policyId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return int
     */
    public function getKeyData()
    {
        return $this->policyId;
    }

    /**
     * @param int $policyId
     *
     * @return PolicyEntity
     */
    public function setPolicyId($policyId)
    {
        $this->policyId = $policyId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPolicyId()
    {
        return $this->policyId;
    }

    /**
     * @param int $resource
     *
     * @return PolicyEntity
     */
    public function setResourceId($resource)
    {
        $this->resourceId = $resource;

        return $this;
    }

    /**
     * @return int
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @param int $applicationId
     *
     * @return PolicyEntity
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param string $name
     *
     * @return PolicyEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $title
     *
     * @return PolicyEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $description
     *
     * @return PolicyEntity
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param bool $state
     *
     * @return PolicyEntity
     */
    public function setReadOnly($state)
    {
        $this->isReadOnly = (bool) $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * @param bool $state
     *
     * @return PolicyEntity
     */
    public function setAllowed($state)
    {
        $this->isAllowed = (bool) $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowed()
    {
        return $this->isAllowed;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return PolicyEntity
     */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateModified
     *
     * @return PolicyEntity
     */
    public function setDateModified(DateTime $dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }
}
