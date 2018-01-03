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
 * Interface MultiConnectorContainerInterface
 */
interface MultiConnectorContainerInterface
{
    /**
     * MultiConnectorContainerInterface constructor.
     *
     * @param ConnectorInterface[] ...$connectorInterfaces
     */
    public function __construct(ConnectorInterface ...$connectorInterfaces);

    /**
     * Returns a Connector instance by name.
     *
     * @param string $name
     * @return ConnectorInterface
     */
    public function getConnectorByName(string $name) : ConnectorInterface;
}
