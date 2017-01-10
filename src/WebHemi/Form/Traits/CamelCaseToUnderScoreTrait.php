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
namespace WebHemi\Form\Traits;

/**
 * Class CamelCaseToUnderScoreTrait.
 */
trait CamelCaseToUnderScoreTrait
{
    /**
     * Converts CamelCase text to under_score equivalent.
     *
     * @param $input
     * @return string
     */
    protected function camelCaseToUnderscore($input)
    {
        preg_match_all('/([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)/', $input, $matches);
        $return = $matches[0];

        foreach ($return as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $return);
    }
}
