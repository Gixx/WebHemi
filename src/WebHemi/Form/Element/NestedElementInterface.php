<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element;

/**
 * Interface NestedElementInterface.
 */
interface NestedElementInterface extends FormElementInterface
{
    /**
     * Set the child nodes for the element.
     *
     * @param array<FormElementInterface> $nodeElements
     * @return FormElementInterface
     */
    public function setNodes(array $nodeElements);

    /**
     * Checks if there are child elements.
     *
     * @return boolean
     */
    public function hasNodes();

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElementInterface>
     */
    public function getNodes();
}
