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
namespace WebHemi\Adapter\Renderer;

use Psr\Http\Message\StreamInterface;

/**
 * Interface RendererAdapterInterface.
 */
interface RendererAdapterInterface
{
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
