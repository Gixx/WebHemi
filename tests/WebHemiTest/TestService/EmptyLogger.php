<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemiTest\TestService;

use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Logger\ServiceInterface;

/**
 * Class EmptyLogger.
 */
class EmptyLogger implements ServiceInterface
{
    /**
     * EmptyLogger constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param string                 $section
     */
    public function __construct(ConfigurationInterface $configuration, string $section)
    {
        unset($configuration, $section);
    }

    /**
     * It will do nothing just avoid the `Scalar type declaration` fatal error for the void return type.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     * @return void
     */
    public function log($level, string $message, array $context = []) : void
    {
        unset($level, $message, $context);
        return;
    }
}
