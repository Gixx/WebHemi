<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Form;

use RuntimeException;
use WebHemi\Form\Element\Web;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralFormElementTest.
 *
 * This test covers only those functions which was not tested by the GeneralFormTest
 */
class GeneralFormElementContainerTest extends TestCase
{
    /**
     * Tests constructor with and without paramteres.
     */
    public function testConstructor()
    {
        $container = new Web\FormElementContainer();

        $this->assertInstanceOf(Web\FormElementContainer::class, $container);
        $this->assertAttributeEmpty('formElementPrototypes', $container);

        $container = new Web\FormElementContainer(new Web\TextElement());

        $this->assertAttributeNotEmpty('formElementPrototypes', $container);
    }

    /**
     * Test object getter.
     */
    public function testRetriever()
    {
        $textElement = new Web\TextElement('test');

        $container = new Web\FormElementContainer($textElement);

        $actualElement1 = $container->getTextElement('test');
        $this->assertInstanceOf(Web\TextElement::class, $actualElement1);
        $this->assertSame('test', $actualElement1->getName());
        $this->assertFalse($textElement === $actualElement1);

        $actualElement2 = $container->getTextElement('other');
        $this->assertInstanceOf(Web\TextElement::class, $actualElement2);
        $this->assertSame('other', $actualElement2->getName());
        $this->assertFalse($actualElement1 === $actualElement2);

        $actualElement3 = $container->getTextElement();
        $this->assertInstanceOf(Web\TextElement::class, $actualElement3);
        $this->assertAttributeEmpty('name', $actualElement3);
    }

    /**
     * Test object getter when parameters are provided.
     */
    public function testRetrieverWithArguments()
    {
        $container = new Web\FormElementContainer(
            new Web\TextElement(),
            new Web\InputElement()
        );

        $actualElement = $container->getTextElement('loginname', 'Login name', 'your name here');
        $this->assertInstanceOf(Web\TextElement::class, $actualElement);
        $this->assertSame('loginname', $actualElement->getName());
        $this->assertSame('Login name', $actualElement->getLabel());
        $this->assertSame('your name here', $actualElement->getValue());

        $actualElement = $container->getInputElement('email', 'Email');
        $this->assertSame('email', $actualElement->getName());
        $this->assertSame('Email', $actualElement->getLabel());
        $this->assertEmpty($actualElement->getValue());
    }

    /**
     * Tests error when try to get an object instance which was not added to the container.
     */
    public function testNotInContainer()
    {
        $container = new Web\FormElementContainer(
            new Web\FormElement(),
            new Web\TextElement(),
            new Web\PasswordElement()
        );

        $formElement = $container->getFormElement('test');
        $this->assertInstanceOf(Web\FormElement::class, $formElement);

        $this->setExpectedException(RuntimeException::class);
        $container->getHiddenElement();
    }
}
