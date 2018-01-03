<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\Preset;

use WebHemi\Form\Element\Html\HtmlElement;

/**
 * Class ApplicationEditForm
 *
 * @codeCoverageIgnore - only composes an object.
 */
class ApplicationEditForm extends AbstractPreset
{
    /**
     * Initialize the adapter
     */
    protected function initAdapter()
    {
        $this->formAdapter->initialize('application', '[URL]/save', 'POST');
    }

    /**
     * Initialize and add elements to the form.
     *
     * @return void
     *
     * @codeCoverageIgnore - only composes an object.
     */
    protected function init() : void
    {
        $this->initAdapter();

        $name = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'name',
            'Application name'
        );

        $title = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'title',
            'Display name (title)'
        );

        $description = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_TEXTAREA,
            'description',
            'Description'
        );

        $enabled = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_CHECKBOX,
            'is_enabled',
            'Activated ?'
        );

        $submit = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_SUBMIT,
            'submit',
            'Save'
        );

        $this->formAdapter->addElement($name)
            ->addElement($title)
            ->addElement($description)
            ->addElement($enabled)
            ->addElement($submit);
    }
}
