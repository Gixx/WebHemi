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
namespace WebHemi\Form;

use Iterator;

abstract class AbstractForm implements FormInterface, Iterator
{
    /** @var FormElement[] */
    protected $form;

    /**
     * FormInterface constructor. Creates <FORM> element automatically.
     *
     * @param string $name
     * @param string $action
     * @param string $method
     */
    public function __construct($name, $action = '', $method = 'POST')
    {
        $form = new FormElement('form', $name);
        $form->setAttribute('action', $action)
            ->setAttribute('method', strtoupper($method));

        // for simplicity, we store it in an array.
        $this->form[0] = $form;

        $this->initForm();
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    abstract protected function initForm();

    /**
     * Adds a form element to the form.
     *
     * @param FormElement $formElement
     * @return AbstractForm
     */
    protected function addChildNode(FormElement $formElement)
    {
        $formElement->setParentNode($this->form[0]);

        $this->form[0]->addChildNode($formElement);

        return $this;
    }

    /**
     * Gets the form elements.
     *
     * @return FormElement[];
     */
    public function getChildNodes()
    {
        return $this->form[0]->getChildNodes();
    }

    /**
     * Validates the form.
     *
     * @return boolean
     */
    public function isValid()
    {
        $valid = true;

        // TODO: TBD

        return $valid;
    }

    /**
     * Sets form data.
     *
     * @param array $data
     * @return FormInterface
     */
    public function setData(array $data)
    {
        // TODO: TBD

        return $this;
    }

    /**
     * Returns the form data.
     *
     * @return array
     */
    public function getData()
    {
        // TODO: TBD
        return [];
    }

    /**
     * Return the current element.
     *
     * @return FormElement
     */
    final public function current()
    {
        return current($this->form);
    }

    /**
     * Moves the pointer forward to next element.
     *
     * @return void
     */
    final public function next()
    {
        next($this->form);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed
     */
    final public function key()
    {
        return key($this->form);
    }

    /**
     * Checks if current position is valid.
     *
     * @return boolean
     */
    final public function valid()
    {
        $key = key($this->form);

        return ($key !== NULL && $key !== FALSE);
    }

    /**
     * Rewinds the Iterator to the first element.
     *
     * @return void
     */
    final public function rewind()
    {
        reset($this->form);
    }
}
