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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use WebHemi\Form\FormElementInterface;
use WebHemi\Form\FormInterface;
use WebHemi\Form\Html\HtmlForm;
use WebHemi\Form\Html\HtmlFormElement;
use WebHemiTest\AssertTrait;

/**
 * Class FormTest
 */
class HtmlFormTest extends TestCase
{
    use AssertTrait;

    /**
     * Tests HtmlForm constructor.
     */
    public function testConstructor()
    {
        $name = 'someForm';
        $action = 'some/action';

        $form = new HtmlForm($name, $action);
        $this->assertInstanceOf(FormInterface::class, $form);
        $this->assertAttributeEquals($name, 'name', $form);
        $this->assertAttributeEquals($action, 'action', $form);
        $this->assertAttributeEquals('POST', 'method', $form);

        $form = new HtmlForm($name, $action, 'GET');
        $this->assertAttributeEquals('GET', 'method', $form);
    }

    /**
     * Tests HtmlForm getter methods.
     */
    public function testGetters()
    {
        $name = 'someForm';
        $action = 'some/action';

        $form = new HtmlForm($name, $action);
        $this->assertSame($name, $form->getName());
        $this->assertSame($action, $form->getAction());
        $this->assertSame('POST', $form->getMethod());
        $this->assertEmpty($form->getElements());
        $this->assertInternalType('array', $form->getElements());
    }

    /**
     * Tests addElement method.
     */
    public function testAddElement()
    {
        $name = 'someForm';
        $action = 'some/action';

        $form = new HtmlForm($name, $action);
        $this->assertEmpty($form->getElements());

        $element = $this->prophesize(FormElementInterface::class);
        $element->getName()->willReturn('some_element');
        $element->setName(Argument::type('string'))->willReturn($element->reveal());
        /** @var FormElementInterface $elementInstance */
        $elementInstance = $element->reveal();

        $form->addElement($elementInstance);
        $elements = $form->getElements();
        $this->assertNotEmpty($elements);
        $this->assertSame(1, count($elements));

        $expectedName = $name.'[some_element]';

        $this->assertTrue(isset($elements[$expectedName]));
        $this->assertInstanceOf(FormElementInterface::class, $elements[$expectedName]);
    }

    public function testLoadData()
    {
        $name = 'someForm';
        $action = 'some/action';
        $form = new HtmlForm($name, $action);

        $element1Instance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_data');
        $element2Instance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_other_data');

        $form->addElement($element1Instance)
            ->addElement($element2Instance);

        $data1 = [];
        $expectedJsonArrray = [
            'name' => 'someForm',
            'action' => 'some/action',
            'method' => 'POST',
            'data' => [
                'id_some_form_some_data' => [],
                'id_some_form_some_other_data' => []
            ],
            'errors' => []

        ];
        $data2 = [
            'someForm' => [
                'some_data' => 1,
                'some_other_data' => [
                    'deep_index' => 2
                ],
                'non_existing_data' => 'do not care'
            ]
        ];

        $form->loadData($data1);
        $elements = $form->getElements();
        $this->assertEmpty($elements['someForm[some_data]']->getValues());
        $this->assertEmpty($elements['someForm[some_other_data]']->getValues());
        $this->assertSame(json_encode($expectedJsonArrray), json_encode($form));
        $this->assertSame($expectedJsonArrray, $form->jsonSerialize());

        $form->loadData($data2);
        $elements = $form->getElements();
        $this->assertArraysAreSimilar([1], $elements['someForm[some_data]']->getValues());
        $this->assertArraysAreSimilar(['deep_index' => 2], $elements['someForm[some_other_data]']->getValues());
    }
}
