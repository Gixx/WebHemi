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
 * Class FakeRenderer.
 */
class FakeRenderer implements RendererAdapterInterface
{
    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @return StreamInterface
     */
    public function render($template, $parameters = [])
    {
        $output = '<h1>Hello world!</h1>';

        if (isset($parameters['exception'])) {
            /** @var \Exception $exp */
            $exp = $parameters['exception'];
            $output .= '<p>' . $exp->getMessage() . '</p>';
            $output .= '<p>' . $exp->getFile() . ' at line ' . $exp->getLine() . '</p>';
            $output .= '<pre>' . $exp->getTraceAsString() . '</pre>';
        } else {
            $output .= $parameters[0];
        }

        $output .= '<p>' . $template . '</p>';

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }
}
