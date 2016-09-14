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
    /** @var array<FormElement> */
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

        // for simplicity in rendering (twig macro), we store it in an array.
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
     * Set unique identifier for the form.
     *
     * @param string $uniqueFormNamePostfix
     * @return AbstractForm
     */
    protected function setUniqueFormNamePostfix($uniqueFormNamePostfix)
    {
        $this->form[0]->setUniqueFormNamePostfix($uniqueFormNamePostfix);

        return $this;
    }

    /**
     * Sets form action.
     *
     * @param string $action
     * @return AbstractForm
     */
    protected function setAction($action)
    {
        $this->form[0]->setAttribute('action', $action);

        return $this;
    }

    /**
     * Sets form submit.
     *
     * @param string $method
     * @return AbstractForm
     */
    protected function setMethod($method = 'POST')
    {
        $this->form[0]->setAttribute('method', $method);

        return $this;
    }

    /**
     * Sets form autocomplete option.
     *
     * @param bool $autocomplete
     * @return AbstractForm
     */
    protected function setAutocomplete($autocomplete = true)
    {
        $this->form[0]->setAttribute('autocomplete', $autocomplete);

        return $this;
    }

    /**
     * Sets form encryption type.
     *
     * @param string $enctype
     * @return AbstractForm
     */
    protected function setEnctype($enctype = 'application/x-www-form-urlencoded')
    {
        $this->form[0]->setAttribute('enctype', $enctype);

        return $this;
    }

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
     * @return array<FormElement>
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

        // fake cotent to avoid phpmd warning until the real function logic is created...
        if (!empty($data)) {
            $this->isValid();
        }

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

        return ($key !== null && $key !== false);
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
