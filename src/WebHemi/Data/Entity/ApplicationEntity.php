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

namespace WebHemi\Data\Entity;

use WebHemi\DateTime;

/**
 * Class ApplicationEntity.
 */
class ApplicationEntity implements DataEntityInterface
{
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
    private $isEnabled;
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * Sets the value of the entity identifier.
     *
     * @param int $entityId
     * @return ApplicationEntity
     */
    public function setKeyData(int $entityId) : ApplicationEntity
    {
        $this->applicationId = $entityId;

        return $this;
    }

    /**
     * Gets the value of the entity identifier.
     *
     * @return null|int
     */
    public function getKeyData() : ? int
    {
        return $this->applicationId;
    }

    /**
     * @param int $applicationId
     * @return ApplicationEntity
     */
    public function setApplicationId(int $applicationId) : ApplicationEntity
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
     * @return ApplicationEntity
     */
    public function setName(string $name) : ApplicationEntity
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
     * @return ApplicationEntity
     */
    public function setTitle(string $title) : ApplicationEntity
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
     * @return ApplicationEntity
     */
    public function setDescription(string $description) : ApplicationEntity
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
     * @param bool $state
     * @return ApplicationEntity
     */
    public function setReadOnly(bool $state) : ApplicationEntity
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
     * @param bool $state
     * @return ApplicationEntity
     */
    public function setEnabled(bool $state) : ApplicationEntity
    {
        $this->isEnabled = $state;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnabled() : bool
    {
        return $this->isEnabled ?? false;
    }

    /**
     * @param DateTime $dateCreated
     * @return ApplicationEntity
     */
    public function setDateCreated(DateTime $dateCreated) : ApplicationEntity
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
     * @param DateTime $dateModified
     * @return ApplicationEntity
     */
    public function setDateModified(DateTime $dateModified) : ApplicationEntity
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
