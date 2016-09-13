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
        $hiddenCsrf = new FormElement(FormElement::TAG_INPUT_HIDDEN, 'csrf');
        $hiddenCsrf->setValue('Some CSRF test value');

        $fieldset = new FormElement(FormElement::TAG_FIELDSET, 'info');

        $legend = new FormElement(FormElement::TAG_LEGEND, 'info', 'Login form');

        $loginname = new FormElement(FormElement::TAG_INPUT_TEXT, 'username', 'Username');
        $loginname->setAttribute('placeholder', 'Your login name');

        $password = new FormElement(FormElement::TAG_INPUT_PASSWORD, 'password', 'Password');

        $checkboxRememberMe = new FormElement(FormElement::TAG_INPUT_CHECKBOX, 'remember_me', 'Remember Me');
        // For single-option radio boxes the ->setValue(1) is equals to this: ->setAttribute('checked', true);
        $checkboxRememberMe->setValue(1);

        $radioLanguage = new FormElement(FormElement::TAG_INPUT_RADIO, 'language', 'Select language');
        $radioLanguage->setValue('es')
            ->setOptions(
                [
                    ['label' => 'English', 'value' => 'en'],
                    ['label' => 'German', 'value' => 'de'],
                    ['label' => 'Spanish', 'value' => 'es'],
                ]
            );

        $checkboxTerms = new FormElement(FormElement::TAG_INPUT_CHECKBOX, 'terms', 'Terms and conditions');
        $checkboxTerms->setOptions(
            [
                ['label' => 'I am human.', 'value' => 'human'],
                ['label' => 'I have read the T&C.', 'value' => 'terms_read', 'checked' => false],
                ['label' => 'I want newsletters.', 'value' => 'terms_newsletter', 'checked' => true],
            ]
        );

        $fieldset->addChildNode($legend)
            ->addChildNode($loginname)
            ->addChildNode($password)
            ->addChildNode($checkboxRememberMe)
            ->addChildNode($radioLanguage)
            ->addChildNode($checkboxTerms);

        $submit = new FormElement(FormElement::TAG_BUTTON_SUBMIT, 'submit', 'Login');

        $this->addChildNode($hiddenCsrf)
            ->addChildNode($fieldset)
            ->addChildNode($submit);
    }
}
