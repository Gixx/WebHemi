<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\Preset;

use WebHemi\CSRF\ServiceInterface as CSRFInterface;
use WebHemi\Form\Element\Html\HtmlElement;
use WebHemi\Validator;

/**
 * Class AdminLoginForm
 *
 * @codeCoverageIgnore - only composes an object.
 */
class AdminLoginForm extends AbstractPreset
{
    /**
     * Initialize and add elements to the form.
     *
     * @return void
     */
    protected function init() : void
    {
        $this->formAdapter->initialize('login', '', 'POST');

        $csrf = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_HIDDEN,
            CSRFInterface::SESSION_KEY,
            ''
        );

        $userName = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'identification',
            'Identification'
        );
        $userName->addValidator($this->validatorCollection->getValidator(Validator\NotEmptyValidator::class));

        $password = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_PASSWORD,
            'password',
            'Password'
        );
        $password->addValidator($this->validatorCollection->getValidator(Validator\NotEmptyValidator::class));

        $submit = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_SUBMIT,
            'submit',
            'Login'
        );

        $this->formAdapter
            ->addElement($csrf)
            ->addElement($userName)
            ->addElement($password)
            ->addElement($submit);
    }
}
