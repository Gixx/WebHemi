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

use WebHemi\Data\EntityInterface;
use WebHemi\DateTime;

/**
 * Class ApplicationEntity.
 */
class ApplicationEntity implements EntityInterface
{
    /** @var int */
    private $applicationId;
    /** @var string */
    private $name;
    /** @var string */
    private $title;
    /** @var string */
    private $description;
    /** @var string */
    private $path;
    /** @var string */
    private $theme;
    /** @var string */
    private $type;
    /** @var string */
    private $locale;
    /** @var string */
    private $timeZone;
    /** @var string */
    private $introduction;
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
     * @param null|string $description
     * @return ApplicationEntity
     */
    public function setDescription(? string $description) : ApplicationEntity
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
     * @param null|string $path
     * @return ApplicationEntity
     */
    public function setPath(? string $path) : ApplicationEntity
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath() : ? string
    {
        return $this->path;
    }

    /**
     * @param null|string $theme
     * @return ApplicationEntity
     */
    public function setTheme(? string $theme) : ApplicationEntity
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTheme() : ? string
    {
        return $this->theme;
    }

    /**
     * @param null|string $type
     * @return ApplicationEntity
     */
    public function setType(? string $type) : ApplicationEntity
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        return $this->type;
    }

    /**
     * @param null|string $locale
     * @return ApplicationEntity
     */
    public function setLocale(? string $locale) : ApplicationEntity
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocale() : ? string
    {
        return $this->locale;
    }

    /**
     * @param null|string $timeZone
     * @return ApplicationEntity
     */
    public function setTimeZone(? string $timeZone) : ApplicationEntity
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTimeZone() : ? string
    {
        return $this->timeZone;
    }

    /**
     * @param null|string $introduction
     * @return ApplicationEntity
     */
    public function setIntroduction(? string $introduction) : ApplicationEntity
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIntroduction() : ? string
    {
        return $this->introduction;
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
     * @param null|DateTime $dateModified
     * @return ApplicationEntity
     */
    public function setDateModified(? DateTime $dateModified) : ApplicationEntity
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
