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
namespace WebHemiTest\Validator;

use PHPUnit\Framework\TestCase;
use WebHemi\Validator\RangeValidator;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait;

/**
 * Class RangeValidatorTest
 */
class RangeValidatorTest extends TestCase
{
    use AssertArraysAreSimilarTrait;

    /**
     * Tests the constructor
     */
    public function testConstructor()
    {
        $validator = new RangeValidator([1,2,3]);

        $this->assertAttributeInternalType('array', 'availableValues', $validator);
        $this->assertAttributeSame(false, 'validateKeys', $validator);

        $validator = new RangeValidator([1,2,3], true);
        $this->assertAttributeSame(true, 'validateKeys', $validator);
    }

    /**
     * Tests the validate method
     */
    public function testValidator()
    {
        $validator = new RangeValidator([1,2,3]);

        $data = [];
        $result = $validator->validate($data);
        $this->assertTrue($result);
        $this->assertArraysAreSimilar($data, $validator->getValidData());

        $data = [1,3];
        $result = $validator->validate($data);
        $this->assertTrue($result);
        $this->assertArraysAreSimilar($data, $validator->getValidData());

        $expectedError = ['Some data is out of range: 5, 6'];
        $result = $validator->validate([1,3,5,6]);
        $this->assertFalse($result);
        $this->assertArraysAreSimilar($expectedError, $validator->getErrors());
    }

    /**
     * Tests the validate method
     */
    public function testValidatorForKeys()
    {
        $validator = new RangeValidator(['apple', 'pear', 'orange'], true);

        $data = ['apple' => 0, 'pear' => 1];
        $result = $validator->validate($data);
        $this->assertTrue($result);
        $this->assertArraysAreSimilar($data, $validator->getValidData());

        $expectedError = ['Some data is out of range: banana'];
        $data = ['apple' => 0, 'pear' => 1, 'banana' => 3];
        $result = $validator->validate($data);
        $this->assertFalse($result);
        $this->assertArraysAreSimilar($expectedError, $validator->getErrors());
    }
}
