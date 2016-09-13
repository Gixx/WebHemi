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

/**
 * Class LoginForm
 */
class LoginForm extends AbstractForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    protected function initForm()
    {
        $fieldset = new FormElement(FormElement::TAG_FIELDSET, 'info');

        $legend = new FormElement(FormElement::TAG_LEGEND, 'info', 'Login information 2');
        $loginname = new FormElement(FormElement::TAG_INPUT_TEXT, 'username', 'Username');
        $password = new FormElement(FormElement::TAG_INPUT_PASSWORD, 'password', 'Password');

        $fieldset->addChildNode($legend)
            ->addChildNode($loginname)
            ->addChildNode($password);

        $submit = new FormElement(FormElement::TAG_BUTTON_SUBMIT, 'submit', 'Login');

        $this->addChildNode($fieldset)
            ->addChildNode($submit);
    }
}
