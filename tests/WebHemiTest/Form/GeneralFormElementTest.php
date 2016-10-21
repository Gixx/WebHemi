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
namespace WebHemiTest\Form;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use WebHemi\Form\Element\FormElementInterface;
use WebHemi\Form\Element\MultiOptionElementInterface;
use WebHemi\Form\Element\NestedElementInterface;
use WebHemi\Form\Element\Web;
use WebHemiTest\AssertTrait;
use WebHemiTest\Fixtures\TestWebForm;
use WebHemiTest\Fixtures\TestWebFormElement;
use WebHemiTest\Fixtures\TestFalseValidator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralFormElementTest.
 *
 * This test covers only those functions which was not tested by the GeneralFormTest
 */
class GeneralFormElementTest extends TestCase
{
    use AssertTrait;

    /**
     * Tests label getter / setter.
     */
    public function testLabel()
    {
        $element = new Web\TextElement('name');
        $this->assertEmpty($element->getLabel());
        $element->setLabel('Name');
        $this->assertSame('Name', $element->getLabel());
    }

    /**
     * Tests node getter / setter.
     */
    public function testNodes()
    {
        $element = new Web\FormElement('test');

        $this->assertInstanceOf(FormElementInterface::class, $element);
        $this->assertInstanceOf(NestedElementInterface::class, $element);
        $this->assertNotInstanceOf(MultiOptionElementInterface::class, $element);

        $this->assertFalse($element->hasNodes());
        $nestedElement = new Web\HiddenElement('hidden');
        $element->setNodes([$nestedElement]);
        $this->assertTrue($element->hasNodes());

        $parentNode = $nestedElement->getParentNode();
        $this->assertInstanceOf(NestedElementInterface::class, $parentNode);
        $this->assertTrue($parentNode === $element);

        $this->setExpectedException(RuntimeException::class);
        $notContainerElement = new Web\TextElement('test');
        $nestedElement = new Web\HiddenElement('hidden');
        $nestedElement->setParentNode($notContainerElement);
    }

    /**
     * Tests getting element type
     */
    public function testGetType()
    {
        $element = new Web\InputElement('test');
        $this->assertSame('text', $element->getType());

        $element->setType('some-fake-type');
        $this->assertSame('text', $element->getType());

        $element->setType('color');
        $this->assertSame('color', $element->getType());

        $this->setExpectedException(Exception::class);
        $element = new TestWebFormElement('no-type');
        $element->getType();
    }

    /**
     * Tests name getter.
     */
    public function testName()
    {
        $element1 = new Web\FieldSetElement('info');
        $element2 = new Web\TextElement('name');
        $element1->setNodes([$element2]);

        $this->assertSame('name', $element2->getName(false));
        $this->assertSame('info[name]', $element2->getName(true));
    }

    /**
     * Tests id getter.
     */
    public function testId()
    {
        $element = new Web\FormElement('test');
        $this->assertSame('id_test', $element->getId());

        $element->setName('test[with][array]');
        $this->assertSame('id_test_with_array', $element->getId());

        $element->setName('test_'.md5('test'));
        $this->assertSame('id_test', $element->getId());

        $element->setName('test_'.md5('test').'_[myData]');
        $this->assertSame('id_test_my_data', $element->getId());
    }

    /**
     * Tests getting attribute (also a non existing one).
     */
    public function testAttributesError1()
    {
        $element = new Web\InputElement('test');

        $expectedAttributes = ['class' => 'username'];
        $element->setAttributes($expectedAttributes);
        $this->assertArraysAreSimilar($expectedAttributes, $element->getAttributes());
        $this->assertSame('username', $element->getAttribute('class'));

        $this->setExpectedException(InvalidArgumentException::class);
        $element->getAttribute('non-exists');
    }

    /**
     * Tests setting non valid attribute.
     */
    public function testAttributesError2()
    {
        $element = new Web\InputElement('test');

        $this->setExpectedException(InvalidArgumentException::class);
        $element->setAttributes(['non-scalar' => ['test']]);
    }

    /**
     * Tests element validator.
     */
    public function testValidator()
    {
        $element = new Web\InputElement('test');
        $this->assertTrue($element->isValid());

        $errors = ['Some error', 'Some other error'];
        $element->setErrors($errors);
        $this->assertTrue($element->hasErrors());
        $this->assertArraysAreSimilar($errors, $element->getErrors());
        $this->assertFalse($element->isValid());
        $this->assertTrue($element->isValid(true));
    }

    /**
     * Tests validator for nested elements.
     */
    public function testNestedValidator()
    {
        $testForm = new TestWebForm(new Web\FormElementContainer(), 'test');

        $element1 = new Web\FieldSetElement('info');
        $element2 = new Web\TextElement('name');
        $element1->setNodes([$element2]);

        $testForm->doSetNodes([$element1]);

        $this->assertTrue($testForm->isValid(true));
        $this->assertEmpty($testForm->getErrors());

        $validators = [new TestFalseValidator()];

        $element2->setValidators($validators);
        $this->assertArraysAreSimilar($validators, $element2->getValidators());
        $element1->setNodes([$element2]);
        $testForm->doSetNodes([$element1]);

        $this->assertFalse($testForm->isValid(true));
        $this->assertArraysAreSimilar(['info' => ['name' => ['The data is not valid']]], $testForm->getErrors());
    }
}
