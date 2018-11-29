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

use DateTimeZone;
use WebHemi\DateTime;

/**
 * Class AbstractEntity
 */
class ApplicationEntity extends AbstractEntity
{
    /**
     * @var array
     */
    protected $container = [
        'id_application' => null,
        'name' => null,
        'title' => null,
        'introduction' => null,
        'subject' => null,
        'description' => null,
        'keywords' => null,
        'copyright' => null,
        'domain' => null,
        'path' => null,
        'theme' => null,
        'type' => null,
        'locale' => null,
        'timezone' => null,
        'is_read_only' => null,
        'is_enabled' => null,
        'date_created' => null,
        'date_modified' => null,
    ];

    /**
     * @param int $identifier
     * @return ApplicationEntity
     */
    public function setApplicationId(int $identifier) : ApplicationEntity
    {
        $this->container['id_application'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getApplicationId() : ? int
    {
        return !is_null($this->container['id_application'])
            ? (int) $this->container['id_application']
            : null;
    }

    /**
     * @param string $name
     * @return ApplicationEntity
     */
    public function setName(string $name) : ApplicationEntity
    {
        $this->container['name'] = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName() : ? string
    {
        return $this->container['name'];
    }

    /**
     * @param string $title
     * @return ApplicationEntity
     */
    public function setTitle(string $title) : ApplicationEntity
    {
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle() : ? string
    {
        return $this->container['title'];
    }

    /**
     * @param string $introduction
     * @return ApplicationEntity
     */
    public function setIntroduction(string $introduction) : ApplicationEntity
    {
        $this->container['introduction'] = $introduction;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIntroduction() : ? string
    {
        return $this->container['introduction'];
    }

    /**
     * @param string $subject
     * @return ApplicationEntity
     */
    public function setSubject(string $subject) : ApplicationEntity
    {
        $this->container['subject'] = $subject;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSubject() : ? string
    {
        return $this->container['subject'];
    }

    /**
     * @param string $description
     * @return ApplicationEntity
     */
    public function setDescription(string $description) : ApplicationEntity
    {
        $this->container['description'] = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription() : ? string
    {
        return $this->container['description'];
    }

    /**
     * @param string $keywords
     * @return ApplicationEntity
     */
    public function setKeywords(string $keywords) : ApplicationEntity
    {
        $this->container['keywords'] = $keywords;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getKeywords() : ? string
    {
        return $this->container['keywords'];
    }

    /**
     * @param string $copyright
     * @return ApplicationEntity
     */
    public function setCopyright(string $copyright) : ApplicationEntity
    {
        $this->container['copyright'] = $copyright;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCopyright() : ? string
    {
        return $this->container['copyright'];
    }

    /**
     * @param string $domain
     * @return ApplicationEntity
     */
    public function setDomain(string $domain) : ApplicationEntity
    {
        $this->container['domain'] = $domain;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDomain() : ? string
    {
        return $this->container['domain'];
    }

    /**
     * @param string $path
     * @return ApplicationEntity
     */
    public function setPath(string $path) : ApplicationEntity
    {
        $this->container['path'] = $path;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath() : ? string
    {
        return $this->container['path'];
    }

    /**
     * @param string $theme
     * @return ApplicationEntity
     */
    public function setTheme(string $theme) : ApplicationEntity
    {
        $this->container['theme'] = $theme;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTheme() : ? string
    {
        return $this->container['theme'];
    }

    /**
     * @param string $type
     * @return ApplicationEntity
     */
    public function setType(string $type) : ApplicationEntity
    {
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() : ? string
    {
        return $this->container['type'];
    }

    /**
     * @param string $locale
     * @return ApplicationEntity
     */
    public function setLocale(string $locale) : ApplicationEntity
    {
        $this->container['locale'] = $locale;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocale() : ? string
    {
        return $this->container['locale'];
    }

    /**
     * @param DateTimeZone $timeZone
     * @return ApplicationEntity
     */
    public function setTimeZone(DateTimeZone $timeZone) : ApplicationEntity
    {
        $this->container['timezone'] = $timeZone->getName();

        return $this;
    }

    /**
     * @return DateTimeZone|null
     */
    public function getTimeZone() : ? DateTimeZone
    {
        return !empty($this->container['timezone'])
            ? new DateTimeZone($this->container['timezone'])
            : null;
    }

    /**
     * @param bool $isReadonly
     * @return ApplicationEntity
     */
    public function setIsReadOnly(bool $isReadonly) : ApplicationEntity
    {
        $this->container['is_read_only'] = $isReadonly ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsReadOnly() : bool
    {
        return !empty($this->container['is_read_only']);
    }

    /**
     * @param bool $isEnabled
     * @return ApplicationEntity
     */
    public function setIsEnabled(bool $isEnabled) : ApplicationEntity
    {
        $this->container['is_enabled'] = $isEnabled ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled() : bool
    {
        return !empty($this->container['is_enabled']);
    }

    /**
     * @param DateTime $dateTime
     * @return ApplicationEntity
     */
    public function setDateCreated(DateTime $dateTime) : ApplicationEntity
    {
        $this->container['date_created'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateCreated() : ? DateTime
    {
        return !empty($this->container['date_created'])
            ? new DateTime($this->container['date_created'])
            : null;
    }

    /**
     * @param DateTime $dateTime
     * @return ApplicationEntity
     */
    public function setDateModified(DateTime $dateTime) : ApplicationEntity
    {
        $this->container['date_modified'] = $dateTime->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getDateModified() : ? DateTime
    {
        return !empty($this->container['date_modified'])
            ? new DateTime($this->container['date_modified'])
            : null;
    }
}
