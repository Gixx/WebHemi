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
namespace WebHemi\Data\Entity;

use DateTime;

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
    /** @var DateTime */
    private $dateCreated;
    /** @var DateTime */
    private $dateModified;

    /**
     * @param int $applicationId
     *
     * @return ApplicationEntity
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
     * @return ApplicationEntity
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
     * @return ApplicationEntity
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
     * @return ApplicationEntity
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
     * @return ApplicationEntity
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
     * @param DateTime $dateCreated
     *
     * @return ApplicationEntity
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
     * @return ApplicationEntity
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
