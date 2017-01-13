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
declare(strict_types=1);

namespace WebHemi\Adapter\Log;

use WebHemi\Config\ConfigInterface;

/**
 * Interface LogAdapterInterface
 */
interface LogAdapterInterface
{
    /**
     * LogAdapterInterface constructor.
     *
     * @param ConfigInterface $configuration
     * @param string          $section
     */
    public function __construct(ConfigInterface $configuration, string $section);

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level Either the numeric level (0-7) or the name of the level (debug ... emergency)
     * @param string $message
     * @param array  $context
     * @return void
     */
    public function log($level, string $message, array $context = []) : void;
}
