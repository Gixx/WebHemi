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
namespace WebHemi\Form\Element\Web;

use WebHemi\Form\Element\FormElementInterface;
use WebHemi\Form\Element\NestedElementInterface;

/**
 * Class FormElement.
 */
class FormElement extends AbstractElement implements NestedElementInterface
{
    /** @var array<FormElementInterface> */
    protected $nodes = [];

    /**
     * Set the child nodes for the element.
     *
     * @param array<FormElementInterface> $nodeElements
     * @return FormElement
     */
    public function setNodes(array $nodeElements)
    {
        /** @var NestedElementInterface $this */
        $this->nodes = [];

        foreach ($nodeElements as $nodeElement) {
            $this->addNode($nodeElement);
        }

        return $this;
    }

    /**
     * Set child node for the element.
     *
     * @param FormElementInterface $nodeElement
     * @return FormElement
     */
    protected function addNode(FormElementInterface $nodeElement)
    {
        $nodeElement->setParentNode($this);
        $this->nodes[$nodeElement->getName(false)] = $nodeElement;

        return $this;
    }

    /**
     * Checks if there are child elements.
     *
     * @return boolean
     */
    public function hasNodes()
    {
        return !empty($this->nodes);
    }

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElementInterface>
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Returns the element type.
     *
     * @return string
     */
    public function getType()
    {
        return 'form';
    }

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return FormElement
     */
    public function setValue($value)
    {
        $children = $this->getNodes();

        /**
         * @var string               $simpleName
         * @var FormElementInterface $child
         */
        foreach ($children as $simpleName => $child) {
            if (isset($value[$simpleName])) {
                $child->setValue($value[$simpleName]);
            }
        }

        return $this;
    }

    /**
     * Returns element value.
     *
     * @return mixed
     */
    public function getValue()
    {
        $children = $this->getNodes();
        $value = [];

        /**
         * @var string               $simpleName
         * @var FormElementInterface $child
         */
        foreach ($children as $simpleName => $child) {
            $value[$simpleName] = $child->getValue();
        }

        return $value;
    }
}
