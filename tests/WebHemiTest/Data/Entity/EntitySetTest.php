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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\EntitySet;

/**
 * Class EntitySetTest
 */
class EntitySetTest extends TestCase
{
    /**
     * Tests the offsetExists
     */
    public function testOffsetExists()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entitySet = new EntitySet();
        $entitySet->offsetSet(1, $entityA);

        $this->assertFalse($entitySet->offsetExists(0));
        $this->assertTrue(isset($entitySet[1]));

        try {
            $result = isset($entitySet['index']);
            unset($result);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1000, $exception->getCode());
        }
    }

    /**
     * Tests the offsetGet
     */
    public function testOffsetGet()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entitySet = new EntitySet();
        $entitySet[] = $entityA;

        $actualResult = $entitySet[0];
        $this->assertTrue($entityA === $actualResult);

        try {
            $result = $entitySet['index'];
            unset($result);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1001, $exception->getCode());
        }
    }

    /**
     * Tests the offsetSet method
     */
    public function testOffsetSet()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entityB = new ApplicationEntity();
        $entityB->setApplicationId(2);

        $entitySet = new EntitySet();
        $entitySet->offsetSet(0, $entityA);
        $entitySet[] = $entityB;

        try {
            $entitySet->offsetSet('index', $entityA);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1002, $exception->getCode());
        }

        try {
            $entitySet[] = 'test text';
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1003, $exception->getCode());
        }
    }

    /**
     * Tests the offsetUnset method
     */
    public function testOffsetUnset()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entityB = new ApplicationEntity();
        $entityB->setApplicationId(2);

        $entitySet = new EntitySet();
        $entitySet->offsetSet(0, $entityA);
        $entitySet[] = $entityB;

        $this->assertTrue(isset($entitySet[1]));
        $entitySet->offsetUnset(1);
        $this->assertFalse(isset($entitySet[1]));

        $this->assertFalse(isset($entitySet[100]));
        unset($entitySet[100]);

        try {
            unset($entitySet['cat']);
        } catch (Throwable $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame(1004, $exception->getCode());
        }
    }

    /**
     * Tests the key method
     */
    public function testKey()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entityB = new ApplicationEntity();
        $entityB->setApplicationId(2);

        $entitySet = new EntitySet();
        $entitySet->offsetSet(0, $entityA);
        $entitySet[] = $entityB;

        foreach ($entitySet as $index => $data) {
            $this->assertTrue(is_int($index));
            $this->assertInstanceOf(ApplicationEntity::class, $data);
        }
    }

    /**
     * Tests the toArray and merge methods
     */
    public function testArray()
    {
        $entityA = new ApplicationEntity();
        $entityA->setApplicationId(1);

        $entityB = new ApplicationEntity();
        $entityB->setApplicationId(2);

        $entityC = new ApplicationEntity();
        $entityC->setApplicationId(3);

        $entityD = new ApplicationEntity();
        $entityD->setApplicationId(4);

        $entitySetA = new EntitySet();
        $entitySetA[] = $entityA;
        $entitySetA[] = $entityB;

        $entitySetB = new EntitySet();
        $entitySetB[] = $entityC;
        $entitySetB[] = $entityD;

        $entitySetA->merge($entitySetB);

        $actualArray = $entitySetA->toArray();
        $expectedArray = [
            $entityA,
            $entityB,
            $entityC,
            $entityD
        ];

        $this->assertSame(count($actualArray), count($expectedArray));

        foreach ($actualArray as $index => $actualEntity) {
            $actual = var_export($actualEntity, true);
            $expected = var_export($expectedArray[$index], true);

            $this->assertSame($actual, $expected);
        }
    }
}
