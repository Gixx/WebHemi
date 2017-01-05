<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */

namespace WebHemi\Adapter\Log\Klogger;

use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;
use WebHemi\Adapter\Log\LogAdapterInterface;
use WebHemi\Config\ConfigInterface;

/**
 * Class KloggerAdapter
 */
class KloggerAdapter extends LogLevel implements LogAdapterInterface
{
    /** @var Logger */
    private $adapter;
    /** @var array */
    private $configuration;
    /** @var array */
    private $logLevel = [
        0 => self::DEBUG,
        1 => self::INFO,
        2 => self::NOTICE,
        3 => self::WARNING,
        4 => self::ERROR,
        5 => self::CRITICAL,
        6 => self::ALERT,
        7 => self::EMERGENCY,
    ];
    private $defaultLevel = self::WARNING;

    /**
     * LogAdapterInterface constructor.
     *
     * @param ConfigInterface $configuration
     * @param string          $section
     */
    public function __construct(ConfigInterface $configuration, $section)
    {
        $this->configuration = $configuration->getData('logging/'.$section);

        $logPath = $this->configuration['path'];
        $logLevel = $this->logLevel[$this->configuration['log_level']];
        $options = [
            'prefix' => $this->configuration['file_name'],
            'extension' => $this->configuration['file_extension'],
            'dateFormat' => $this->configuration['date_format']
        ];

        $this->adapter = new Logger($logPath, $logLevel, $options);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        if (is_numeric($level)) {
            $level = isset($this->logLevel[$level]) ? $this->logLevel[$level] : $this->defaultLevel;
        } elseif (!in_array($level, $this->logLevel)) {
            $level = $this->defaultLevel;
        }

        $this->adapter->log($level, $message, $context);
    }
}
