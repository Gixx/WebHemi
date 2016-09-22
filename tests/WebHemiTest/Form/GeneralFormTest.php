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

use WebHemi\Form\Element\NestedElementInterface;
use WebHemi\Form\Element\Web;
use WebHemiTest\Fixtures\TestWebForm;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralFormTest.
 */
class GeneralFormTest extends TestCase
{
    /**
     * Tests constructor.
     */
    public function testConstructor()
    {
        $testForm = new TestWebForm('test_form');

        $this->assertTrue($testForm->isInitCalled);
        $this->assertSame('test_form', $testForm->getName());

        $this->assertInstanceOf(NestedElementInterface::class, $testForm->form);

        $attributes = $testForm->form->getAttributes();

        $this->assertSame('POST', $attributes['method']);
        $this->assertEmpty($attributes['action']);
    }

    /**
     * Tests simple validator. By default everything is valid.
     */
    public function testValidator()
    {
        $testForm = new TestWebForm('test_form');

        $this->assertTrue($testForm->isValid());
    }

    /**
     * Tests salt setter.
     */
    public function testSalt()
    {
        $salt = 'test';
        $testForm = new TestWebForm('test_form');

        $testForm->setNameSalt($salt);
        $this->assertSame(md5($salt), $testForm->salt);
    }

    /**
     * Tests attribute setter.
     */
    public function testAttributes()
    {
        $testForm = new TestWebForm('test_form');

        $attributes = $testForm->form->getAttributes();
        $this->assertSame('POST', $attributes['method']);
        $this->assertEmpty($attributes['action']);

        $testForm->setAction('some-url');
        $attributes = $testForm->form->getAttributes();
        $this->assertSame('POST', $attributes['method']);
        $this->assertSame('some-url', $attributes['action']);

        $testForm->setMethod('GET');
        $attributes = $testForm->form->getAttributes();
        $this->assertSame('GET', $attributes['method']);
        $this->assertSame('some-url', $attributes['action']);

        $testForm->doSetEnctype();
        $attributes = $testForm->form->getAttributes();
        $this->assertSame('GET', $attributes['method']);
        $this->assertSame('some-url', $attributes['action']);
        $this->assertSame('application/x-www-form-urlencoded', $attributes['enctype']);

        $testForm->doSetEnctype('multipart/form-data');
        $attributes = $testForm->form->getAttributes();
        $this->assertSame('GET', $attributes['method']);
        $this->assertSame('some-url', $attributes['action']);
        $this->assertSame('multipart/form-data', $attributes['enctype']);
    }

    /**
     * Tests auto-complete switcher.
     */
    public function testAutoCompete()
    {
        $salt = 'test';
        $testForm = new TestWebForm('test_form');

        $testForm->setNameSalt($salt);
        $this->assertSame('test_form', $testForm->getName());

        $testForm->setAutoComplete(false);
        $this->assertSame('test_form_'.md5($salt), $testForm->getName());

        $testForm->setAutoComplete(true);
        $this->assertSame('test_form', $testForm->getName());

        $testForm->setNameSalt($salt.'x');
        $testForm->setAutoComplete(false);
        $this->assertSame('test_form_'.md5($salt.'x'), $testForm->getName());

        $testForm->setAutoComplete(true);
        $this->assertSame('test_form', $testForm->getName());
    }
}
