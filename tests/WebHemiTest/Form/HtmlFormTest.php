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
namespace WebHemiTest\Form;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use RuntimeException;
use WebHemi\Form\ElementInterface as FormElementInterface;
use WebHemi\Form\ServiceInterface as FormInterface;
use WebHemi\Form\ServiceAdapter\Base\ServiceAdapter as HtmlForm;
use WebHemi\Form\Element\Html\HtmlElement as HtmlFormElement;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait as AssertTrait;
use WebHemiTest\TestService\TestFalseValidator;
use WebHemiTest\TestService\TestTrueValidator;

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
        $name = 'some_form';
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
     * Tests the initialize method.
     */
    public function testInit()
    {
        $name = 'some_form';
        $action = 'some/action';
        $method = 'GET';

        $form = new HtmlForm();
        $this->assertInstanceOf(FormInterface::class, $form);
        $this->assertAttributeEmpty('name', $form);
        $this->assertAttributeEmpty('action', $form);
        $this->assertAttributeEquals('POST', 'method', $form);

        $form->initialize($name, $action, $method);
        $this->assertAttributeEquals($name, 'name', $form);
        $this->assertAttributeEquals($action, 'action', $form);
        $this->assertAttributeEquals($method, 'method', $form);

        $this->expectException(RuntimeException::class);
        $form->initialize($name, $action, $method);
    }

    /**
     * Tests HtmlForm getter methods.
     */
    public function testGetters()
    {
        $name = 'some_form';
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
        $name = 'some_form';
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

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1001);
        $form->addElement($elementInstance);
    }

    /**
     * Tests getElement method.
     */
    public function testGetElement()
    {
        $name = 'some_form';
        $action = 'some/action';

        $form = new HtmlForm($name, $action);
        $this->assertEmpty($form->getElements());

        $element = $this->prophesize(FormElementInterface::class);
        $element->getName()->willReturn('some_element');
        $element->setName(Argument::type('string'))->willReturn($element->reveal());
        /** @var FormElementInterface $elementInstance */
        $elementInstance = $element->reveal();

        $form->addElement($elementInstance);
        $actualElement = $form->getElement('some_element');
        $this->assertTrue($elementInstance === $actualElement);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1002);
        $form->getElement('non_existing');
    }

    /**
     * Tests loadData() method.
     */
    public function testLoadData()
    {
        $name = 'some_form';
        $action = 'some/action';
        $form = new HtmlForm($name, $action);

        $element1Instance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_data');
        $element2Instance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_other_data');

        $form->addElement($element1Instance)
            ->addElement($element2Instance);

        $data1 = [];
        $expectedJsonArrray = [
            'name' => 'some_form',
            'action' => 'some/action',
            'method' => 'POST',
            'data' => [
                'id_some_form_some_data' => [],
                'id_some_form_some_other_data' => []
            ],
            'errors' => []

        ];
        $data2 = [
            'some_form' => [
                'some_data' => 1,
                'some_other_data' => [
                    'deep_index' => 2
                ],
                'non_existing_data' => 'do not care'
            ]
        ];

        $form->loadData($data1);
        $elements = $form->getElements();

        $this->assertEmpty($elements['some_form[some_data]']->getValues());
        $this->assertEmpty($elements['some_form[some_other_data]']->getValues());
        $this->assertSame(json_encode($expectedJsonArrray), json_encode($form));
        $this->assertSame($expectedJsonArrray, $form->jsonSerialize());

        $form->loadData($data2);
        $elements = $form->getElements();
        $this->assertArraysAreSimilar([1], $elements['some_form[some_data]']->getValues());
        $this->assertArraysAreSimilar(['deep_index' => 2], $elements['some_form[some_other_data]']->getValues());
    }

    /**
     * Tests the validate() method.
     */
    public function testValidator()
    {
        $name = 'some_form';
        $action = 'some/action';
        $form = new HtmlForm($name, $action);

        $trueValidator = new TestTrueValidator();
        $falseValidator = new TestFalseValidator();

        // Add element with validator
        $firstElementInstance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_data_1');
        $firstElementInstance->addValidator($trueValidator);
        $form->addElement($firstElementInstance);
        $validateResult = $form->validate();
        $this->assertTrue($validateResult);

        // Add element without validator
        $secondElementInstance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_data_2');
        $form->addElement($secondElementInstance);
        $validateResult = $form->validate();
        $this->assertTrue($validateResult);

        // Add element with validator that will fail
        $thirdElementInstance = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'some_data_3');
        $thirdElementInstance->addValidator($falseValidator);
        $form->addElement($thirdElementInstance);
        $validateResult = $form->validate();
        $this->assertFalse($validateResult);
    }
}
