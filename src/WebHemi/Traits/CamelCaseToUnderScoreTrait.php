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

namespace WebHemi\Traits;

/**
 * Class CamelCaseToUnderScoreTrait.
 */
trait CamelCaseToUnderScoreTrait
{
    /**
     * Converts CamelCase text to under_score equivalent.
     *
     * @param string $input
     * @return string
     */
    protected function camelCaseToUnderscore(string $input) : string
    {
        preg_match_all('/([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)/', $input, $matches);
        $output = $matches[0];

        foreach ($output as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $output);
    }
}
