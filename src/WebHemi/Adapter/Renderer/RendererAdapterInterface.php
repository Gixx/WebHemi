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
namespace WebHemi\Adapter\Renderer;

use Psr\Http\Message\StreamInterface;
use WebHemi\Application\EnvironmentManager;
use WebHemi\Config\ConfigInterface;

/**
 * Interface RendererAdapterInterface.
 */
interface RendererAdapterInterface
{
    /**
     * RendererAdapterInterface constructor.
     *
     * @param ConfigInterface    $templateConfig
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(ConfigInterface $templateConfig, EnvironmentManager $environmentManager);

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return StreamInterface
     */
    public function render($template, $parameters = []);
}
