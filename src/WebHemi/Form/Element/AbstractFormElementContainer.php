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
namespace WebHemi\Form\Element;

use RuntimeException;

/**
 * Class AbstractFormElementContainer
 */
abstract class AbstractFormElementContainer implements FormElementContainerInterface
{
    /** @var FormElementInterface */
    protected $formElementPrototypes;

    /**
     * FormElementContainer constructor.
     *
     * @param FormElementInterface[] ...$formElementPrototypes
     */
    public function __construct(FormElementInterface ...$formElementPrototypes)
    {
        /** @var FormElementInterface $formElement */
        foreach ($formElementPrototypes as $formElement) {
            if (method_exists($formElement, 'resetTabIndex')) {
                $formElement->resetTabIndex();
            }
            $this->formElementPrototypes[$this->getBaseClassName($formElement)] = $formElement;
        }
    }

    /**
     * Searches for suitable form element and returns a cloned instance.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return FormElementInterface
     */
    public function __call($name, $arguments)
    {
        $classBaseName = substr($name, 3);

        if (!isset($this->formElementPrototypes[$classBaseName])) {
            throw new RuntimeException(sprintf('%s is not a valid form element.', $classBaseName));
        }

        /** @var FormElementInterface $formElement */
        $formElement = clone $this->formElementPrototypes[$classBaseName];

        // We have to be able to set the constructor parameters when we get the object via this container.
        if (isset($arguments[0])) {
            $formElement->setName($arguments[0]);
        }

        if (isset($arguments[1])) {
            $formElement->setLabel($arguments[1]);
        }

        if (isset($arguments[2])) {
            $formElement->setValue($arguments[2]);
        }

        return $formElement;
    }

    /**
     * Returns the base name of the class (no namespace).
     *
     * @param FormElementInterface $formElement
     *
     * @return string
     */
    protected function getBaseClassName(FormElementInterface $formElement)
    {
        $fullClassName = get_class($formElement);
        $namespaces = explode('\\', $fullClassName);
        return array_pop($namespaces);
    }
}
