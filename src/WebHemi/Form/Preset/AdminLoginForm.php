<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\Preset;

use WebHemi\Form\Element\Html\HtmlElement;

/**
 * Class AdminLoginForm
 */
class AdminLoginForm extends AbstractPreset
{
    /**
     * Initialize and add elements to the form.
     *
     * @return void
     *
     * @codeCoverageIgnore - only composes an object.
     */
    protected function init() : void
    {
        $this->formAdapter->initialize('login', '', 'POST');

        $userName = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'identification',
            'Identification'
        );

        $password = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_PASSWORD,
            'password',
            'Password'
        );

        $submit = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_SUBMIT,
            'submit',
            'Login'
        );

        $this->formAdapter->addElement($userName)
            ->addElement($password)
            ->addElement($submit);
    }
}
