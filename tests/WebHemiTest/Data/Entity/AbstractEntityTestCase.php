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
namespace WebHemiTest\Data\Entity;

use PHPUnit\Framework\TestCase;
use WebHemi\Data\Entity\EntityInterface;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait;

/**
 * Class AbstractEntityTestCase
 */
abstract class AbstractEntityTestCase extends TestCase
{
    /** @var EntityInterface */
    protected $entity;

    /** @var array  */
    protected $testData = [];

    /** @var array  */
    protected $expectedGetters = [];

    /** @var array  */
    protected $expectedSetters = [];

    use AssertArraysAreSimilarTrait;

    /**
     * Tests setters by getting the same array back as the test data
     */
    public function testSetters()
    {
        foreach ($this->expectedSetters as $method => $parameter) {
            $this->entity->$method($parameter);
        }

        $actualResult = $this->entity->toArray();

        $this->assertArraysAreSimilar($this->testData, $actualResult, 'toArray');
    }

    /**
     * Tests getters by loading test data.
     */
    public function testGetters()
    {
        $this->entity->fromArray($this->testData);

        foreach ($this->expectedGetters as $method => $expectedResult) {
            //var_dump($this->testData, $expectedResult, $this->entity->$method());
            if (is_array($expectedResult)) {
                $this->assertArraysAreSimilar($expectedResult, $this->entity->$method(), $method);
            } elseif (is_object($expectedResult)) {
                $actualObject = var_export($this->entity->$method(), true);
                $expectedObject = var_export($expectedResult, true);
                $this->assertSame($expectedObject, $actualObject, $method);
            } else {
                $this->assertSame($expectedResult, $this->entity->$method(), $method);
            }
        }
    }
}
