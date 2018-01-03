<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\TestService;

use WebHemi\Form\ElementInterface;
use WebHemi\Form\Preset\AbstractPreset;

/**
 * Class EmptyFormPreset
 */
class EmptyFormPreset extends AbstractPreset
{
    /**
     * Initialize.
     */
    protected function init() : void
    {
        return;
    }

    /**
     * Passes parameters to protected method to be able to test.
     *
     * @param string $class
     * @param string $type
     * @param string $name
     * @param string $label
     * @return ElementInterface
     */
    public function creatingTestElement(string $class, string $type, string $name, string $label) : ElementInterface
    {
        return $this->createElement($class, $type, $name, $label);
    }
}
