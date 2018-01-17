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

use WebHemi\Form\Element\Html\HtmlElement;

/**
 * Class SimplePreset
 *
 * @codeCoverageIgnore - only composes an object.
 */
class SimplePreset extends AbstractPreset
{
    /**
     * Initialize and add elements to the form.
     *
     * @return void
     */
    protected function init() : void
    {
        $this->formAdapter->initialize('simple', '', 'POST');

        $submit = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_SUBMIT,
            'submit',
            'Submit'
        );

        $this->formAdapter->addElement($submit);
    }
}
