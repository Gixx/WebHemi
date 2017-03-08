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

namespace WebHemi\Middleware\Action\Website;

use WebHemi\Form\Element\Html\Html5Element;
use WebHemi\Form\Element\Html\HtmlElement;
use WebHemi\Form\Element\Html\HtmlMultipleElement;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $form = $this->getTestForm();

        $data = [];

        if ($this->request->getMethod() == 'POST') {
            $data = $this->request->getParsedBody();
            $form->loadData($data);
        }

        return [
            'form' => $form,
            'data' => $data,
        ];
    }

    /**
     * @return HtmlForm
     */
    private function getTestForm()
    {
        // Test refactore Form elements
        $singleCheckbox = new HtmlElement(
            HtmlElement::HTML_ELEMENT_INPUT_CHECKBOX,
            'accept',
            'Accept terms of usage.',
            [true]
        );

        $multiCheckbox = new HtmlElement(
            HtmlElement::HTML_ELEMENT_INPUT_CHECKBOX,
            'my_locations',
            'I have already been...',
            ['uk', 'eu'],
            [
                'The United States' => 'us',
                'European Union' => 'eu',
                'United Kingdom' => 'uk'
            ]
        );

        $radioGroup = new HtmlElement(
            HtmlElement::HTML_ELEMENT_INPUT_RADIO,
            'curent_lang',
            'Current language',
            ['hu-HU'],
            [
                'English' => 'en-GB',
                'German' => 'de-DE',
                'Hungarian' => 'hu-HU'
            ]
        );

        $select = new HtmlMultipleElement(
            HtmlMultipleElement::HTML_MULTIPLE_ELEMENT_SELECT,
            'something',
            null,
            ['australia', 'india'],
            [
                'Default' => [
                    'Europe' => 'europe',
                    'America' => 'america',
                    'Australia' => 'australia'
                ],
                'Africa' => 'africa',
                'Asia' => 'asia',
                'Antarctica' => 'antarctica',
                'India' => 'india'
            ]
        );
        $select->setMultiple(true);

        $form = new HtmlForm('test', '', 'POST');
        $form
            ->addElement(
                new HtmlElement(
                    HtmlElement::HTML_ELEMENT_INPUT_HIDDEN,
                    'csrf',
                    null,
                    [md5('something')]
                )
            )
            ->addElement(
                new HtmlElement(
                    HtmlElement::HTML_ELEMENT_INPUT_TEXT,
                    'name',
                    'Login name',
                    ['Joker']
                )
            )
            ->addElement(
                new HtmlElement(
                    HtmlElement::HTML_ELEMENT_INPUT_PASSWORD,
                    'password',
                    'Password'
                )
            )
            ->addElement($singleCheckbox)
            ->addElement($multiCheckbox)
            ->addElement($radioGroup)
            ->addElement($select)
            ->addElement(
                new Html5Element(Html5Element::HTML5_ELEMENT_INPUT_NUMBER, 'num', 'Num', [4], [1, 16])
            )
            ->addElement(
                new Html5Element(Html5Element::HTML5_ELEMENT_INPUT_RANGE, 'range', 'Range', [4], [1, 6, 0.2])
            )
            ->addElement(
                new HtmlElement(HtmlElement::HTML_ELEMENT_INPUT_SUBMIT, 'submit', 'Submit')
            );

        return $form;
    }
}
