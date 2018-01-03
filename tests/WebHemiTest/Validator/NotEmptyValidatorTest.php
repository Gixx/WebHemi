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
use WebHemi\Validator\NotEmptyValidator;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait;

/**
 * Class NotEmptyValidatorTest
 */
class NotEmptyValidatorTest extends TestCase
{
    use AssertArraysAreSimilarTrait;

    /**
     * Tests the validate method
     */
    public function testValidator()
    {
        $validator = new NotEmptyValidator();

        $expectedError = ['This field is mandatory and cannot be empty'];
        $result = $validator->validate([]);
        $this->assertFalse($result);
        $this->assertArraysAreSimilar($expectedError, $validator->getErrors());

        $result = $validator->validate([false]);
        $this->assertFalse($result);

        $result = $validator->validate([0 => 0]);
        $this->assertFalse($result);

        $result = $validator->validate(['key' => []]);
        $this->assertFalse($result);

        $result = $validator->validate(['true' => false]);
        $this->assertFalse($result);

        $result = $validator->validate([null]);
        $this->assertFalse($result);

        $result = $validator->validate(['                         ']);
        $this->assertFalse($result);


        $data = [1, 'key' => '       value', 'notempty' => [1,2,3], true];
        $expectedData = [1, 'key' => 'value', 'notempty' => [1,2,3], true];
        $result = $validator->validate($data);
        $this->assertTrue($result);
        $this->assertArraysAreSimilar($expectedData, $validator->getValidData());
    }
}
