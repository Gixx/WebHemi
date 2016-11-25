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
use WebHemi\Form\Element\FormElementContainerInterface;
use WebHemi\Form\Element\NestedElementInterface;
use WebHemi\Form\Traits\CamelCaseToUnderScoreTrait;
use WebHemi\Form\Traits\IteratorTrait;

/**
 * Class AbstractForm
 */
abstract class AbstractForm implements FormInterface, Iterator
{
    /** @var FormElementContainerInterface */
    private $formElementContainer;
    /** @var NestedElementInterface */
    protected $form;
    /** @var string */
    protected $salt;

    // The implementation of the Iterator interface.
    use IteratorTrait;
    // CamelCase to under_score converter
    use CamelCaseToUnderScoreTrait;

    /**
     * AbstractForm constructor.
     *
     * @param FormElementContainerInterface $formElementContainer
     * @param string                        $name
     * @param string                        $action
     * @param string                        $method
     */
    final public function __construct(
        FormElementContainerInterface $formElementContainer,
        $name = '',
        $action = '',
        $method = 'POST'
    ) {
        $this->formElementContainer = $formElementContainer;

        if (empty($name)) {
            $name = $this->camelCaseToUnderscore(get_called_class());
        }

        $this->form = $this->getFormContainer();
        $this->form->setName($name)
            ->setAttributes(
                [
                    'action' => $action,
                    'method' => strtoupper($method)
                ]
            );

        // For simplicity in rendering (twig macro), we store it in an array.
        $this->nodes[0] = &$this->form;
        // Set a default salt for the form name. If the AutoComplete attribute is 'off', it will be added to the form's
        // name attribute. The default salt will change every hour.
        $this->salt = md5(gmdate('YmdH'));

        $this->initForm();
    }

    /**
     * Returns the form element container.
     *
     * @return FormElementContainerInterface
     */
    protected function getFormElementContainer()
    {
        return $this->formElementContainer;
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
     * Sets form name.
     *
     * @param string $name
     * @return FormInterface
     */
    public function setName($name)
    {
        $this->form->setName($name);

        $formAttributes = $this->form->getAttributes();

        if (isset($formAttributes['autocomplete'])) {
            $this->setAutoComplete($formAttributes['autocomplete']);
        }
    }

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
    final public function setAction($action)
    {
        $this->setAttribute('action', $action);

        return $this;
    }

    /**
     * Sets form submit.
     *
     * @param string $method
     * @return AbstractForm
     */
    final public function setMethod($method = 'POST')
    {
        $this->setAttribute('method', $method);

        return $this;
    }

    /**
     * Sets form encoding type.
     *
     * @param string $encodingType
     * @return AbstractForm
     */
    final protected function setEnctype($encodingType = 'application/x-www-form-urlencoded')
    {
        $this->setAttribute('enctype', $encodingType);

        return $this;
    }

    /**
     * Sets form auto-complete option.
     *
     * @param bool $autoComplete
     * @return AbstractForm
     */
    final public function setAutoComplete($autoComplete = true)
    {
        $name = $this->form->getName(false);
        $md5Match = [];

        // Search for the unique form prefix.
        preg_match('/^[a-z0-9\_\-\[\]]+\_(?P<md5>[a-f0-9]{32}).*$/', $name, $md5Match);

        // When it's necessary, add/remove the salt to/from the name
        if ($autoComplete && !empty($md5Match)) {
            $name = str_replace('_'.$md5Match['md5'], '', $name);
        } elseif (!$autoComplete && empty($md5Match)) {
            $name = $name.'_'.$this->salt;
        }

        $this->form->setName($name);

        $this->setAttribute('autocomplete', $autoComplete);

        return $this;
    }

    /**
     * Sets specific form attributes.
     *
     * @param $name
     * @param $value
     */
    private function setAttribute($name, $value)
    {
        $attributes = $this->form->getAttributes();
        $attributes[$name] = $value;

        $this->form->setAttributes($attributes);
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
    protected function getNodes()
    {
        return $this->form->getNodes();
    }

    /**
     * Validates the form.
     *
     * @param bool $reValidate
     * @return bool
     */
    public function isValid($reValidate = false)
    {
        return $this->form->isValid($reValidate);
    }

    /**
     * Gets validation errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->form->getErrors();
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
