<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
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
 * Class ApplicationEditForm
 *
 * @codeCoverageIgnore - only composes an object.
 */
class ApplicationCreateForm extends AbstractPreset
{
    /**
     * Initialize the adapter
     */
    protected function initAdapter()
    {
        $this->formAdapter->initialize('application', '', 'POST');
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

        $csrf = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_HIDDEN,
            CSRFInterface::SESSION_KEY,
            ''
        );

        $name = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'name',
            'Application name'
        );
        $name->addValidator($this->validatorCollection->getValidator(Validator\NotEmptyValidator::class));

        $title = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'title',
            'Display name (title)'
        );
        $title->addValidator($this->validatorCollection->getValidator(Validator\NotEmptyValidator::class));

        $introduction = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_TEXTAREA,
            'introduction',
            'Introduction'
        );

        $subject = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_TEXTAREA,
            'subject',
            'Subject'
        );

        $description = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_TEXTAREA,
            'description',
            'Description'
        );

        $keywords = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'keywords',
            'Keywords'
        );

        $copyright = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_INPUT_TEXT,
            'copyright',
            'Copyright'
        );

        $cancel = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_RESET,
            'cancel',
            'Cancel'
        );

        $submit = $this->createElement(
            HtmlElement::class,
            HtmlElement::HTML_ELEMENT_SUBMIT,
            'submit',
            'Save'
        );

        $this->formAdapter
            ->addElement($csrf)
            ->addElement($name)
            ->addElement($title)
            ->addElement($introduction)
            ->addElement($subject)
            ->addElement($description)
            ->addElement($keywords)
            ->addElement($copyright)
            ->addElement($cancel)
            ->addElement($submit);
    }
}
