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

namespace WebHemi\Data;

/**
 * Interface StorageConnectorExchangeableInterface
 */
interface StorageConnectorExchangeableInterface extends StorageInterface
{
    /**
     * Sets a new connector for the storage
     *
     * @param ConnectorInterface $connector
     * @return StorageInterface
     */
    public function exchangeConnector(ConnectorInterface $connector) : StorageInterface;
}
