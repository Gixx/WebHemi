<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Logger\ServiceAdapter\Klogger;

use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;
use WebHemi\Configuration\ServiceInterface as ConfigurationInterface;
use WebHemi\Logger\ServiceInterface;

/**
 * Class ServiceAdapter.
 */
class ServiceAdapter extends LogLevel implements ServiceInterface
{
    /**
     * @var string
     */
    protected $section;
    /**
     * @var Logger
     */
    private $adapter;
    /**
     * @var array
     */
    private $configuration;
    /**
     * @var array
     */
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
     * ServiceAdapter constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param string                 $section
     */
    public function __construct(ConfigurationInterface $configuration, string $section)
    {
        $this->configuration = $configuration->getData('logger/'.$section);

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
     * @param  mixed  $level
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    public function log($level, string $message, array $context = []) : void
    {
        if (is_numeric($level)) {
            $level = isset($this->logLevel[$level]) ? $this->logLevel[$level] : $this->defaultLevel;
        } elseif (!in_array($level, $this->logLevel)) {
            $level = $this->defaultLevel;
        }

        $this->adapter->log($level, $message, $context);
    }
}
