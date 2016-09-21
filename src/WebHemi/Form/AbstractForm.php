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
use WebHemi\Form\Element\NestedElementInterface;
use WebHemi\Form\Element\Traits\IteratorTrait;

/**
 * Class AbstractForm
 */
abstract class AbstractForm implements FormInterface, Iterator
{
    /** @var NestedElementInterface */
    protected $form;
    /** @var string */
    protected $name;
    /** @var string */
    protected $salt;

    /** @var string */
    protected $uniqueFormNamePostfix = '';

    // The implementation of the Iterator interface.
    use IteratorTrait;

    /**
     * AbstractForm constructor.
     *
     * @param string $name
     * @param string $action
     * @param string $method
     */
    final public function __construct($name, $action = '', $method = 'POST')
    {
        $this->form = $this->getFormContainer();
        $this->form->setName($name)
            ->setAttributes(
                [
                    'action' => $action,
                    'method' => strtoupper($method)
                ]
            );

        // For simplicity in rendering (twig macro), we store it in an array.
        $this->nodes[0] =& $this->form;
        // Set a default salt for the form name. If the AutoComplete attribute is 'off', it will be added to the form's
        // name attribute. The default salt will change every hour.
        $this->salt = md5(gmdate('YmdH'));

        $this->initForm();
    }

    /**
     * Returns the form container element. E.g.: for HTML forms it is the <form> tag.
     *
     * @return NestedElementInterface
     */
    abstract protected function getFormContainer();

    /**
     * Initialize form.
     *
     * @return void
     */
    abstract protected function initForm();

    /**
     * Gets form name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->form->getName();
    }

    /**
     * Set unique identifier for the form.
     *
     * @param string $salt
     * @return AbstractForm
     */
    public function setNameSalt($salt)
    {
        $this->salt = md5($salt);

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
        $attributes = $this->form->getAttributes();
        $attributes['action'] = $action;

        $this->form->setAttributes($attributes);

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
        $attributes = $this->form->getAttributes();
        $attributes['method'] = $method;

        $this->form->setAttributes($attributes);

        return $this;
    }

    /**
     * Sets form auto-complete option.
     *
     * @param bool $autoComplete
     * @return AbstractForm
     */
    public function setAutoComplete($autoComplete = true)
    {
        $name = $this->form->getName(false);
        $md5Match = [];

        // Search for the unique form prefix.
        preg_match('/^[a-z0-9]+\_(?P<md5>[a-f0-9]{32}).*$/', $name, $md5Match);

        // When it's necessary, add/remove the salt to/from the name
        if ($autoComplete && !empty($md5Match)) {
            $name = str_replace('_'.$md5Match['md5'], '', $name);
        } elseif (!$autoComplete && empty($md5Match)) {
            $name = $name.'_'.$this->salt;
        }

        $this->form->setName($name);

        $attributes = $this->form->getAttributes();
        $attributes['autocomplete'] = $autoComplete;

        $this->form->setAttributes($attributes);

        return $this;
    }

    /**
     * Sets form encoding type.
     *
     * @param string $encodingType
     * @return AbstractForm
     */
    protected function setEnctype($encodingType = 'application/x-www-form-urlencoded')
    {
        $attributes = $this->form->getAttributes();
        $attributes['enctype'] = $encodingType;

        $this->form->setAttributes($attributes);

        return $this;
    }

    /**
     * Adds a form element to the form.
     *
     * @param array<FormElementInterface> $nodes
     * @return AbstractForm
     */
    protected function setNodes(array $nodes)
    {
        $this->form->setNodes($nodes);

        return $this;
    }

    /**
     * Gets the form elements.
     *
     * @return array<FormElementInterface>
     */
    public function getNodes()
    {
        return $this->form->getNodes();
    }

    /**
     * Validates the form.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->form->isValid();
    }

    /**
     * Sets form data.
     *
     * @param array $data
     * @return FormInterface
     */
    public function setData(array $data)
    {
        if (isset($data[$this->form->getName()])) {
            $data = $data[$this->form->getName()];
        } elseif (isset($data[$this->form->getName(false)])) {
            $data = $data[$this->form->getName(false)];
        }

        $this->form->setValue($data);

        return $this;
    }

    /**
     * Returns the form data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->form->getValue();
    }
}
