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

namespace WebHemi\Renderer;

use Psr\Http\Message\StreamInterface;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\I18n\ServiceInterface as I18nService;

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
     * @param I18nService            $i18nService
     */
    public function __construct(
        ConfigurationInterface $configuration,
        EnvironmentInterface $environmentManager,
        I18nService $i18nService
    );

    /**
     * Renders the template for the output.
     *
     * @param  string $template
     * @param  array  $parameters
     * @return StreamInterface
     */
    public function render(string $template, array $parameters = []) : StreamInterface;
}
