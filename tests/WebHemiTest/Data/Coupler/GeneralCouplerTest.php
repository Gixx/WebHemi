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
namespace WebHemiTest\Data\Coupler;

use InvalidArgumentException;
use Prophecy\Argument;
use RuntimeException;
use WebHemi\Adapter\Data\DataAdapterInterface;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemiTest\Fixtures\EmptyCoupler;
use WebHemiTest\Fixtures\EmptyEntity;
use WebHemiTest\InvokePrivateMethodTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class GeneralCouplerTest. Tests the AbstractDataCoupler's methods mostly.
 */
class GeneralCouplerTest extends TestCase
{
    use InvokePrivateMethodTrait;

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

        $entity = new EmptyEntity();

        // Test constructor: The EmptyCoupler requires EmptyEntities
        $coupler = new EmptyCoupler($defaultAdapterInstance, $entity, $entity);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);

        // Test constructor error:
        $applicationEntity = new ApplicationEntity();
        $this->setExpectedException(InvalidArgumentException::class);
        new EmptyCoupler($defaultAdapterInstance, $applicationEntity, $entity);
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

        $entity = new EmptyEntity();

        $coupler = new EmptyCoupler($defaultAdapterInstance, $entity, $entity);
        $this->assertInstanceOf(EmptyCoupler::class, $coupler);
        $this->assertInstanceOf(DataAdapterInterface::class, $coupler->getDataAdapter());

        $newEntity = $this->invokePrivateMethod($coupler, 'getNewEntityInstance', [EmptyEntity::class]);
        $this->assertInstanceOf(EmptyEntity::class, $newEntity);
        $this->assertFalse($entity === $newEntity);
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

        $entity = new EmptyEntity();
        $applicationEntity = new ApplicationEntity();
        $coupler = new EmptyCoupler($defaultAdapterInstance, $entity, $entity);

        $entityList = $coupler->getEntityDependencies($entity);
        $this->assertEquals(3, count($entityList));
        $this->assertInstanceOf(EmptyEntity::class, $entityList[0]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[1]);
        $this->assertInstanceOf(EmptyEntity::class, $entityList[2]);

        $this->setExpectedException(RuntimeException::class);
        $coupler->getEntityDependencies($applicationEntity);
    }
}
