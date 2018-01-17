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

namespace WebHemi;

use InvalidArgumentException;

/**
 * Class GeneralLib
 */
class GeneralLib
{
    /**
     * Collects and returns some information about the render time. First call will start start, the others will return.
     *
     * @return array
     */
    public static function renderStat() : array
    {
        static $stat;

        // Set timer
        if (!isset($stat)) {
            $stat = [
                'start_time' => microtime(true),
                'end_time' => null,
                'duration' => 0,
                'memory' => 0,
                'memory_bytes' => 0,
            ];

            return $stat;
        }

        // Get time
        $stat['end_time'] = microtime(true);
        $stat['duration'] = bcsub((string) $stat['end_time'], (string) $stat['start_time'], 4);

        // Memory peak
        $units = ['bytes', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max(memory_get_peak_usage(true), 0);
        $stat['memory_bytes'] = number_format($bytes).' bytes';

        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        $stat['memory'] = round($bytes, 2).' '.$units[$pow];

        return $stat;
    }

    /**
     * Merge config arrays in the correct way.
     * This rewrites the given key->value pairs and does not make key->array(value1, value2) like the
     * `array_merge_recursive` does.
     *
     * @throws InvalidArgumentException
     * @return array
     */
    public static function mergeArrayOverwrite()
    {
        if (func_num_args() < 2) {
            throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' needs two or more array arguments');
        }
        $arrays = func_get_args();
        $merged = [];

        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' encountered a non array argument');
            }
            if (!$array) {
                continue;
            }
            foreach ($array as $key => $value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = self::mergeArrayOverwrite($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}
