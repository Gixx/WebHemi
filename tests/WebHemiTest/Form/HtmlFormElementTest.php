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
use InvalidArgumentException;
use WebHemi\Form\Html\HtmlFormElement;
use WebHemi\Form\Html\HtmlMultipleFormElement;
use WebHemiTest\AssertTrait;
use WebHemiTest\Fixtures\TestFalseValidator;
use WebHemiTest\Fixtures\TestTrueValidator;

/**
 * Class HtmlFormElementTest
 */
class HtmlFormElementTest extends TestCase
{
    use AssertTrait;

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $formElement = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_FILE, 'test');
        $this->assertSame('file', $formElement->getType());
        $this->assertSame('test', $formElement->getName());
        $this->assertSame('id_test', $formElement->getId());
        $this->assertSame('test', $formElement->getLabel());
        $this->assertEmpty($formElement->getValues());
        $this->assertEmpty($formElement->getValueRange());
        $this->assertEmpty($formElement->getErrors());

        $this->expectException(InvalidArgumentException::class);
        new HtmlMultipleFormElement(HtmlFormElement::HTML_ELEMENT_BUTTON, 'not-good');
    }

    /**
     * Tests the setName method which also sets the identifier.
     */
    public function testSetNameAndId()
    {
        $formElement = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_FILE, 'test');
        $this->assertSame('test', $formElement->getName());
        $this->assertSame('id_test', $formElement->getId());

        $formElement->setName('SomeCamelCaseName');
        $this->assertSame('some_camel_case_name', $formElement->getName());
        $this->assertSame('id_some_camel_case_name', $formElement->getId());

        $formElement->setName('SomeCamelCaseName[with-Dash][and-NestedName]');
        $this->assertSame('some_camel_case_name[with_dash][and_nested_name]', $formElement->getName());
        $this->assertSame('id_some_camel_case_name_with_dash_and_nested_name', $formElement->getId());

        $this->expectException(InvalidArgumentException::class);
        $formElement->setName('{--$#--}(%)');
    }

    /**
     * Tests simple setter and getter methods.
     */
    public function testSettersAndGetters()
    {
        $formElement = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'test');

        $result = $formElement->setLabel('My Label');
        $this->assertInstanceOf(HtmlFormElement::class, $result);
        $this->assertSame('My Label', $formElement->getLabel());

        $this->assertEmpty($formElement->getValueRange());
        $formElement->setValueRange([]);
        $this->assertEmpty($formElement->getValueRange());
        $this->assertInternalType('array', $formElement->getValueRange());

        $expectedData = ['my' => 'data', 'unit_test' => 'passed'];
        $formElement->setValueRange($expectedData);
        $this->assertInternalType('array', $formElement->getValueRange());
        $this->assertArraysAreSimilar($expectedData, $formElement->getValueRange());

        $this->assertEmpty($formElement->getValues());
        $expectedData = ['my' => 'values', 'unit_test' => 'passed'];
        $formElement->setValues($expectedData);
        $this->assertInternalType('array', $formElement->getValues());
        $this->assertArraysAreSimilar($expectedData, $formElement->getValues());

        $formElement = new HtmlMultipleFormElement(HtmlMultipleFormElement::HTML_MULTIPLE_ELEMENT_SELECT, 'test');
        $this->assertFalse($formElement->getMultiple());
        $formElement->setMultiple(true);
        $this->assertTrue($formElement->getMultiple());
        $formElement->setMultiple(false);
        $this->assertFalse($formElement->getMultiple());
    }

    /**
     * Test validation.
     */
    public function testValidator()
    {
        $trueValidator = new TestTrueValidator();
        $falseValidator = new TestFalseValidator();

        $formElement = new HtmlFormElement(HtmlFormElement::HTML_ELEMENT_INPUT_TEXT, 'test');
        $formElement->validate();
        $this->assertEmpty($formElement->getErrors());

        $formElement->addValidator($trueValidator);
        $formElement->validate();
        $this->assertEmpty($formElement->getErrors());

        $expectedErrors = [TestFalseValidator::class => ['The data is not valid']];
        $formElement->addValidator($falseValidator);
        $formElement->validate();
        $this->assertArraysAreSimilar($expectedErrors, $formElement->getErrors());

        $expectedErrors = [TestFalseValidator::class => ['The data is not valid'], 'something' => ['error']];
        $formElement->setError('something', 'error');
        $this->assertArraysAreSimilar($expectedErrors, $formElement->getErrors());
    }
}
