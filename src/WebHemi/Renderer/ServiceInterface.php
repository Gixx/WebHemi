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

namespace WebHemi\Renderer;

use Psr\Http\Message\StreamInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * ServiceInterface constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param EnvironmentInterface   $environmentManager
     */
    public function __construct(ConfigurationInterface $configuration, EnvironmentInterface $environmentManager);

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     * @return StreamInterface
     */
    public function render(string $template, array $parameters = []) : StreamInterface;
}
