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

namespace WebHemi\Data\Connector;

use InvalidArgumentException;
use WebHemi\Data\ConnectorInterface;
use WebHemi\Data\MultiConnectorContainerInterface;

/**
 * Class MultiConnectorContainer
 */
class MultiConnectorContainer implements MultiConnectorContainerInterface
{
    /**
     * @var array
     */
    private $connectors = [];

    /**
     * MultiConnectorContainer constructor.
     *
     * @param ConnectorInterface[] ...$connectorInterfaces
     */
    public function __construct(ConnectorInterface ...$connectorInterfaces)
    {
        foreach ($connectorInterfaces as $connector) {
            /**
             * @var ConnectorInterface $connector
             */
            $this->connectors[$connector->getConnectorName()] = $connector;
        }
    }

    /**
     * Returns a Connector instance by name.
     *
     * @param  string $name
     * @throws InvalidArgumentException
     * @return ConnectorInterface
     */
    public function getConnectorByName(string $name) : ConnectorInterface
    {
        if (!isset($this->connectors[$name])) {
            throw new InvalidArgumentException(
                sprintf('%s is not a registered connector name.', $name),
                1000
            );
        }

        return clone $this->connectors[$name];
    }
}
