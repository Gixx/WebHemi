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

use WebHemi\DateTime;

/**
 * Class DomainEntity
 */
class DomainEntity extends AbstractEntity
{
    /**
     * DomainEntity constructor.
     */
    public function __construct()
    {
        $this->container = [
            'id_domain' => null,
            'schema' => null,
            'domain' => null,
            'title' => null,
            'is_default' => null,
            'is_read_only' => null,
            'date_created' => null,
            'date_modified' => null
        ];
    }

    /**
     * @param int $identifier
     * @return DomainEntity
     */
    public function setDomainId(int $identifier) : DomainEntity
    {
        $this->container['id_domain'] = $identifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDomainId() : ? int
    {
        return $this->container['id_domain'] !== null
            ? (int) $this->container['id_domain']
            : null;
    }

    /**
     * @param string $schema
     * @return DomainEntity
     */
    public function setSchema(string $schema) : DomainEntity
    {
        $this->container['schema'] = $schema;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSchema() : ? string
    {
        return $this->container['schema'];
    }

    /**
     * @param string $domain
     * @return DomainEntity
     */
    public function setDomain(string $domain) : DomainEntity
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
     * @param string $title
     * @return DomainEntity
     */
    public function setTitle(string $title) : DomainEntity
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
     * @param bool $isDefault
     * @return DomainEntity
     */
    public function setIsDefault(bool $isDefault) : DomainEntity
    {
        $this->container['is_default'] = $isDefault ? 1 : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsDefault() : bool
    {
        return !empty($this->container['is_default']);
    }

    /**
     * @param bool $isReadonly
     * @return DomainEntity
     */
    public function setIsReadOnly(bool $isReadonly) : DomainEntity
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
     * @param DateTime $dateTime
     * @return DomainEntity
     */
    public function setDateCreated(DateTime $dateTime) : DomainEntity
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
     * @return DomainEntity
     */
    public function setDateModified(DateTime $dateTime) : DomainEntity
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
