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
namespace WebHemiTest\Data\Coupler;

use Exception;
use InvalidArgumentException;
use Prophecy\Argument;
use RuntimeException;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemiTest\Fixtures\EmptyCoupler;
use WebHemiTest\Fixtures\EmptyEntity;
use WebHemiTest\Fixtures\EmptyEntity2;
use WebHemiTest\InvokePrivateMethodTrait;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralCouplerTest. Tests the AbstractDataCoupler's methods mostly.
 */
class GeneralCouplerTest extends TestCase
{
    use InvokePrivateMethodTrait;
    use AssertTrait;

    /**
     * Test constructor.
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     */
    public function testConstructor()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();

        // Test constructor: The EmptyCoupler requires EmptyEntities
        $coupler = new EmptyCoupler($defaultAdapterInstance, $entityA, $entityB);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);

        // Test constructor: The EmptyCoupler requires EmptyEntities, changed order
        $coupler2 = new EmptyCoupler($defaultAdapterInstance, $entityB, $entityA);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler2);

        // Test constructor error:
        $applicationEntity = new ApplicationEntity();
        $this->setExpectedException(InvalidArgumentException::class);
        new EmptyCoupler($defaultAdapterInstance, $applicationEntity, $entityA);
    }

    /**
     * Test coupler methods.
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     */
    public function testCouplerFunctions()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();

        $coupler = new EmptyCoupler($defaultAdapterInstance, $entityA, $entityB);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);
        $this->assertInstanceOf(DataAdapterInterface::class, $coupler->getDataAdapter());

        $newEntity = $this->invokePrivateMethod($coupler, 'getNewEntityInstance', [EmptyEntity::class]);
        $this->assertInstanceOf(EmptyEntity::class, $newEntity);
        $this->assertFalse($entityA === $newEntity);

        $newEntity = $this->invokePrivateMethod($coupler, 'getNewEntityInstance', [EmptyEntity2::class]);
        $this->assertInstanceOf(EmptyEntity2::class, $newEntity);
        $this->assertFalse($entityB === $newEntity);
    }

    /**
     * Tests getEntityDependencies() method
     *
     * @covers \WebHemi\Data\Coupler\AbstractDataCoupler
     */
    public function testDependencyResolver()
    {
        $defaultAdapter = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn(1);
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn(1);

        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $entityA = new EmptyEntity();
        $entityB = new EmptyEntity2();
        $coupler = new EmptyCoupler($defaultAdapterInstance, $entityA, $entityB);

        $entityList = $coupler->getEntityDependencies($entityA);
        $this->assertEquals(3, count($entityList));
        $this->assertInstanceOf(EmptyEntity::class, $entityList[0]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[1]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[2]);

        $entityList = $coupler->getEntityDependencies($entityB);
        $this->assertEquals(2, count($entityList));
        $this->assertInstanceOf(EmptyEntity2::class, $entityList[0]);
        $this->assertInstanceOf(EmptyEntity2::class, $entityList[1]);

        $this->setExpectedException(RuntimeException::class);
        $applicationEntity = new ApplicationEntity();
        $coupler->getEntityDependencies($applicationEntity);
    }

    /**
     * Tests setDependency() method
     */
    public function testDependencySetter()
    {
        $defaultAdapter = $adapterProphecy = $this->prophesize(DataAdapterInterface::class);
        $defaultAdapter->setDataGroup(Argument::type('string'))->willReturn($adapterProphecy->reveal());
        $defaultAdapter->setIdKey(Argument::type('string'))->willReturn($adapterProphecy->reveal());
        $defaultAdapter->saveData(Argument::type('null'), Argument::type('array'))->will(
            function ($args) {
                return $args;
            }
        );

        /** @var DataAdapterInterface $defaultAdapterInstance */
        $defaultAdapterInstance = $defaultAdapter->reveal();

        $entityA = new EmptyEntity('empty_id_1');
        $entityA->setKeyData(1);
        $entityB = new EmptyEntity2('empty_id_2');
        $entityB->setKeyData(3);
        $coupler = new EmptyCoupler($defaultAdapterInstance, $entityA, $entityB);

        $saveDataArgs = $coupler->setDependency($entityA, $entityB);
        $expectedSaveData = [
            'empty_fk_1' => 1,
            'empty_fk_2' => 3,
        ];
        $this->assertNull($saveDataArgs[0]);
        $this->assertArraysAreSimilar($expectedSaveData, $saveDataArgs[1]);

        $coupler = new EmptyCoupler($defaultAdapterInstance, $entityA, $entityB);
        $applicationEntity = new ApplicationEntity();

        // Set dependency for wrong data entity #1
        try {
            $coupler->setDependency($applicationEntity, $entityB);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1002, $e->getCode());
        }

        // Set dependency for wrong data entity #2
        try {
            $coupler->setDependency($entityA, $applicationEntity);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1003, $e->getCode());
        }

        // Set dependency for self
        try {
            $coupler->setDependency($entityA, $entityA);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame(1004, $e->getCode());
        }
    }
}
