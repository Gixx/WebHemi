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
namespace WebHemi\Adapter\Renderer\Twig;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Config\ConfigInterface;

/**
 * Class TwigRendererAdapter.
 */
class TwigRendererAdapter implements RendererAdapterInterface
{
    private $adapter;
    /** @var ConfigInterface */
    private $config;
    /** @var string */
    private $templateViewPath;
    /** @var string */
    private $templateResourcePath;
    /** @var string */
    private $applicationBaseUri;

    /**
     * RendererAdapterInterface constructor.
     *
     * @param ConfigInterface $templateConfig
     * @param string          $templatePath
     * @param string          $applicationBaseUri
     */
    public function __construct(ConfigInterface $templateConfig, $templatePath, $applicationBaseUri)
    {
        $this->config = $templateConfig;
        $this->templateViewPath = realpath(__DIR__.'/../../../../../').$templatePath.'/view';
        $this->templateResourcePath = $templatePath.'/static';
        $this->applicationBaseUri = $applicationBaseUri;

        $loader = new Twig_Loader_Filesystem($this->templateViewPath);
        $this->adapter = new Twig_Environment($loader, array('debug' => true));
        $this->adapter->addExtension(new Twig_Extension_Debug());
    }

    /**
     * Renders the template for the output.
     *
     * @param string $template
     * @param array  $parameters
     *
     * @throws InvalidArgumentException
     *
     * @return StreamInterface
     */
    public function render($template, $parameters = [])
    {
        if ($this->config->has('map/'.$template)) {
            $template = $this->config->getData('map/'.$template);
        }

        if (!file_exists($this->templateViewPath.'/'.$template)) {
            throw new InvalidArgumentException(sprintf('Unable to render file: "%s". No such file.', $template));
        }

        // Tell the template where the resources are.
        $parameters['template_resource_path'] = $this->templateResourcePath;
        $parameters['application_base_uri'] = $this->applicationBaseUri;

        $output = $this->adapter->render($template, $parameters);

        // The ugliest shit ever. But that is how they made it... :/
        return \GuzzleHttp\Psr7\stream_for($output);
    }
}
