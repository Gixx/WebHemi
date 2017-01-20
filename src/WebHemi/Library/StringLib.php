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
declare(strict_types = 1);

namespace WebHemi\Library;

/**
 * Class String
 */
final class StringLib
{
    /**
     * Converts CamelCase text to under_score equivalent.
     *
     * @param string $input
     * @return string
     */
    public static function convertCamelCaseToUnderscore(string $input) : string
    {
        $input[0] = strtolower($input[0]);
        return strtolower(preg_replace('/([A-Z])/', '_\\1', $input));
    }

    /**
     * Converts under_score to CamelCase equivalent.
     *
     * @param string $input
     * @return string
     */
    public static function convertUnderscoreToCamelCase(string $input) : string
    {
        $input = preg_replace('/_([a-zA-Z0-9])/', '#\\1', $input);
        $parts = explode('#', $input);
        array_walk(
            $parts,
            function (&$value) {
                $value = ucfirst(strtolower($value));
            }
        );
        return implode($parts);
    }

    /**
     * Converts all non-alphanumeric and additional extra characters to underscore.
     *
     * @param string $input
     * @param string $extraCharacters
     * @return string
     */
    public static function convertNonAlphanumericToUnderscore(string $input, string $extraCharacters = '') : string
    {
        // Escape some characters that can affect badly the regular expression.
        $extraCharacters = str_replace(
            ['-', '[', ']', '(', ')', '/', '$', '^'],
            ['\\-', '\\[', '\\]', '\\(', '\\)', '\\/', '\\$', '\\^'],
            $extraCharacters
        );

        $output = preg_replace('/[^a-zA-Z0-9'.$extraCharacters.']/', '_', $input);

        while (strpos($output, '__') !== false) {
            $output = str_replace('__', '_', $output);
        }

        return trim($output, '_');
    }
}
