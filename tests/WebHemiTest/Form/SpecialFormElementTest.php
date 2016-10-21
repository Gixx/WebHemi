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
use WebHemi\Form\Element\Web;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class SpecialFormElementTest.
 */
class SpecialFormElementTest extends TestCase
{
    use AssertTrait;

    /**
     * Tests single checkbox (enable/disble or true/false switch)
     */
    public function testSingleCheckbox()
    {
        $element = new Web\CheckboxElement('test');

        $this->assertSame(0, $element->getValue());

        $element->setValue("0");
        $this->assertSame(0, $element->getValue());

        $element->setValue(0);
        $this->assertSame(0, $element->getValue());

        $element->setValue(false);
        $this->assertSame(0, $element->getValue());

        $element->setValue('');
        $this->assertSame(0, $element->getValue());

        $element->setValue(null);
        $this->assertSame(0, $element->getValue());

        // !!!!
        $element->setValue('null');
        $this->assertSame(1, $element->getValue());

        $element->setValue(1);
        $this->assertSame(1, $element->getValue());

        $element->setValue(true);
        $this->assertSame(1, $element->getValue());

        $element->setValue(100);
        $this->assertSame(1, $element->getValue());

        $element->setValue('some value');
        $this->assertSame(1, $element->getValue());
    }

    /**
     * Tests checkbox group.
     */
    public function testMultiCheckbox()
    {
        $element = new Web\CheckboxElement('test');
        $element->setOptions(
            [
                ['label' => 'Germany', 'value' => 'de'],
                ['label' => 'United Kingdom', 'value' => 'uk'],
                ['label' => 'Austria', 'value' => 'at'],
                ['label' => 'Switzerland', 'value' => 'ch'],
            ]
        );

        $this->assertEmpty($element->getValue());

        // !!! always return array
        $element->setValue('de');
        $this->assertArraysAreSimilar(['de'], $element->getValue());

        $element->setValue(['de', 'at']);
        $this->assertArraysAreSimilar(['de', 'at'], $element->getValue());

        // !!!! original option order
        $element->setValue(['ch', 'at']);
        $this->assertArraysAreSimilar(['at', 'ch'], $element->getValue());
    }

    /**
     * Test multi-option element.
     */
    public function testMultiOptions()
    {
        $select = new Web\SelectElement('country', 'Country');
        $this->assertFalse($select->hasOptions());

        $select->setOptions(
            [
                ['label' => 'Hungary', 'value' => 'hu'],
                ['label' => 'Germany', 'value' => 'de', 'checked' => true, 'attributes' => ['class' => 'red']],
                ['label' => 'Austria', 'value' => 'at'],
            ]
        );
        $expected = [
            'Default' => [
                'Hungary' => ['label' => 'Hungary', 'value' => 'hu', 'checked' => false, 'attributes' => []],
                'Germany' => [
                    'label' => 'Germany',
                    'value' => 'de',
                    'checked' => true,
                    'attributes' => ['class' => 'red']
                ],
                'Austria' => ['label' => 'Austria', 'value' => 'at', 'checked' => false, 'attributes' => []],
            ]
        ];

        $this->assertTrue($select->hasOptions());
        $this->assertArraysAreSimilar($expected, $select->getOptions());
        $this->assertFalse($select->isGroupedSelect());

        $select->setOptions(
            [
                ['label' => 'Hungary', 'value' => 'hu', 'group' => 'Hungarian'],
                ['label' => 'Germany', 'value' => 'de', 'group' => 'German'],
                ['label' => 'Austria', 'value' => 'at', 'group' => 'German'],
            ]
        );
        $select->setValue('at');
        $expected = [
            'Hungarian' => [
                'Hungary' => ['label' => 'Hungary', 'value' => 'hu', 'checked' => false, 'attributes' => []],
            ],
            'German' => [
                'Germany' => ['label' => 'Germany', 'value' => 'de', 'checked' => false, 'attributes' => []],
                'Austria' => ['label' => 'Austria', 'value' => 'at', 'checked' => true, 'attributes' => []],
            ]
        ];
        $this->assertTrue($select->hasOptions());
        $this->assertArraysAreSimilar($expected, $select->getOptions());
        $this->assertTrue($select->isGroupedSelect());

        $this->assertFalse(strpos($select->getName(), '[]') > 0);

        $select->setAttributes(['multiple' => true]);
        $this->assertTrue(strpos($select->getName(), '[]') > 0);
    }

    /**
     * Test multi-option value setter/getter.
     */
    public function testMultiSelectValue()
    {
        $element = new Web\CheckboxElement('test');
        $element->setOptions(
            [
                ['label' => 'Hungary', 'value' => 'hu', 'group' => 'Hungarian'],
                ['label' => 'Germany', 'value' => 'de', 'group' => 'German'],
                ['label' => 'Austria', 'value' => 'at', 'group' => 'German'],
            ]
        );
        $element->setValue('de');
        $this->assertArraysAreSimilar(['de'], $element->getValue());

        $element->setValue(['hu', 'at']);
        $this->assertArraysAreSimilar(['hu', 'at'], $element->getValue());

        $element->setValue(['hu' => 0, 'de' => 1, 'at' => 1]);
        $this->assertArraysAreSimilar(['de', 'at'], $element->getValue());
    }

    /**
     * Tests tabindex incrementation in constructor for some elements.
     */
    public function testTabindexIncrement()
    {
        // Hidden elements don't have tabindex.
        $element = new Web\HiddenElement('test');
        try {
            $element->getAttribute('tabindex');
        } catch (Exception $e) {
            $this->assertSame(0, $e->getCode());
        }

        $element = new Web\FileElement('file');
        $element->resetTabIndex()
            ->setTabIndex();

        $this->assertSame(1, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(2, $element2->getAttribute('tabindex'));

        $element = new Web\KeygenElement('keygen');
        $this->assertSame(3, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(4, $element2->getAttribute('tabindex'));

        $element = new Web\TextareaElement('textarea');
        $this->assertSame(5, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(6, $element2->getAttribute('tabindex'));

        $element = new Web\PasswordElement('password');
        $this->assertSame(7, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(8, $element2->getAttribute('tabindex'));

        $element = new Web\ButtonElement('button');
        $this->assertSame(9, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(10, $element2->getAttribute('tabindex'));

        $element = new Web\RadioElement('radio');
        $this->assertSame(11, $element->getAttribute('tabindex'));

        $element2 = clone $element;
        $this->assertSame(12, $element2->getAttribute('tabindex'));
    }
}
