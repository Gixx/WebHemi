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
namespace WebHemiTest\Form;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WebHemi\Form\ElementInterface as FormElementInterface;
use WebHemi\Form\ServiceInterface as FormInterface;
use WebHemi\Form\PresetInterface as FormPresetInterface;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Form\Element\Html\HtmlElement as HtmlFormElement;
use WebHemi\Form\Element\Html\Html5Element as Html5FormElement;
use WebHemi\Form\Element\Html\HtmlMultipleElement as MultipleHtmlFormElement;
use WebHemiTest\TestService\EmptyFormPreset;

/**
 * Class FormPresetTest
 */
class FormPresetTest extends TestCase
{
    /**
     * Tests constructor.
     */
    public function testContructor()
    {
        $htmlElement = new HtmlFormElement();
        $html5Element = new Html5FormElement();
        $htmlMutipleElement = new MultipleHtmlFormElement();

        $formPreset = new EmptyFormPreset(new HtmlForm(), $htmlElement);

        $this->assertInstanceOf(FormPresetInterface::class, $formPreset);
        $this->assertInstanceOf(FormInterface::class, $formPreset->getPreset());

        $expectedArray = [
            HtmlFormElement::class => $htmlElement
        ];

        $this->assertAttributeCount(count($expectedArray), 'elementPrototypes', $formPreset);

        $formPreset = new EmptyFormPreset(new HtmlForm(), $htmlElement, $htmlMutipleElement, $html5Element);

        $expectedArray = [
            HtmlFormElement::class => $htmlElement,
            MultipleHtmlFormElement::class => $htmlMutipleElement,
            Html5FormElement::class => $html5Element
        ];

        $this->assertAttributeCount(count($expectedArray), 'elementPrototypes', $formPreset);
    }

    /**
     * Tests element initializer.
     */
    public function testElementCreator()
    {
        $formPreset = new EmptyFormPreset(new HtmlForm(), new HtmlFormElement());
        $element = $formPreset->creatingTestElement(
            HtmlFormElement::class,
            HtmlFormElement::HTML_ELEMENT_BUTTON,
            'test_button',
            'Test Button'
        );

        $this->assertInstanceOf(FormElementInterface::class, $element);
        $this->assertSame(HtmlFormElement::HTML_ELEMENT_BUTTON, $element->getType());

        $this->expectException(InvalidArgumentException::class);
        $formPreset->creatingTestElement(
            Html5FormElement::class,
            Html5FormElement::HTML5_ELEMENT_DATALIST,
            'test_datalist',
            'Test Datalist'
        );
    }
}
