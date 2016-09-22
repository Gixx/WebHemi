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
namespace WebHemiTest\Fixtures;

use WebHemi\Form\AbstractForm;
use WebHemi\Form\Element\Web;
use WebHemi\Form\Element\NestedElementInterface;

/**
 * Class TestWebForm.
 *
 * @method NestedElementInterface doGetFormContainer()
 * @method TestWebForm doSetEnctype(string $string = '')
 *
 * @property NestedElementInterface $form
 * @property string $name
 * @property string $salt
 */
class TestWebForm extends AbstractForm
{
    public $isInitCalled = false;

    /**
     * Returns the form container element. E.g.: for HTML forms it is the <form> tag.
     *
     * @return Web\FormElement
     */
    protected function getFormContainer()
    {
        return new Web\FormElement();
    }

    /**
     * Initialize form.
     *
     * @return void
     */
    protected function initForm()
    {
        $this->isInitCalled = true;
    }

    /**
     * Open all closed method
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        // converts doSomeMethod() into someMethod()
        $name = lcfirst(substr($name, 2));

        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
    }

    /**
     * Reach all closed properties.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }
}
