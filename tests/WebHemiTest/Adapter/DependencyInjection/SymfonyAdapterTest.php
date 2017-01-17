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
namespace WebHemiTest\Adapter\DependencyInjection;

use ArrayIterator;
use ArrayObject;
use WebHemi\DateTime;
use DateTimeZone;
use RuntimeException;
use stdClass;
use Symfony\Component\DependencyInjection\Reference;
use WebHemi\Adapter\DependencyInjection\Symfony\SymfonyAdapter;
use WebHemi\Adapter\DependencyInjection\DependencyInjectionAdapterInterface;
use WebHemi\Config\Config;
use WebHemi\Config\ConfigInterface;
use WebHemiTest\AssertTrait;
use WebHemiTest\InvokePrivateMethodTrait;
use WebHemiTest\Fixtures\EmptyEntity;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class SymfonyAdapterTest.
 */
class SymfonyAdapterTest extends TestCase
{
    use AssertTrait;
    use InvokePrivateMethodTrait;

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $config = new Config([
            'applications' => [],
            'themes' => [],
            'modules' => [],
            'auth' => [],
            'middleware_pipeline' => [],
            'dependencies' => [],
        ]);
        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global');

        $this->assertInstanceOf(DependencyInjectionAdapterInterface::class, $adapter);
        $this->assertAttributeEmpty('configuration', $adapter);
    }

    /**
     * Tests initializer.
     */
    public function testInitContainer()
    {
        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        'alias' => [
                            'class' => DateTime::class
                        ],
                        ArrayObject::class => [],
                    ]
                ],
            ]
        );
        $adapter = new SymfonyAdapter($config);

        $this->assertInstanceOf(DependencyInjectionAdapterInterface::class, $adapter);
    }

    /**
     * Tests service registering.
     */
    public function testRegisterService()
    {
        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        // Alias with argument, no call.
                        'alias1' => [
                            'class' => DateTime::class,
                            'arguments' => [
                                '2016-04-05 01:02:03'
                            ]
                        ],
                        // No alias, no argument, no call.
                        stdClass::class => [],
                        // Alias with call and share, no argument.
                        'special' => [
                            'class'  => ArrayObject::class,
                            'calls'  => ['offsetSet' => ['date', 'alias1']],
                            'shared' => true
                        ]
                    ]
                ],
            ]
        );
        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global');

        // can't overwrite already registered alias.
        $adapter->registerService('alias1', DateTime::class);
        // register the same service with different alias and with default parameters.
        $adapter->registerService('alias2', DateTime::class);

        /** @var DateTime $actualDate */
        $actualDate = $adapter->get('alias1');
        $this->assertInstanceOf(DateTime::class, $actualDate);
        $this->assertEquals('2016-04-05 01:02:03', $actualDate->format('Y-m-d H:i:s'));

        /** @var DateTime $otherDateService */
        $otherDateService = $adapter->get('alias2');
        $this->assertNotEquals($actualDate->getTimestamp(), $otherDateService->getTimestamp());

        // Get a non-registered service being registered with default parameters.
        $serviceResult = $adapter->get(ArrayObject::class);
        $this->assertInstanceOf(ArrayObject::class, $serviceResult);

        // Get a service which called a method after initialization with another service as parameter.
        /** @var ArrayObject $arrayService */
        $arrayService = $adapter->get('special');
        $this->assertInstanceOf(ArrayObject::class, $arrayService);
        $this->assertTrue($arrayService->offsetExists('date'));
        $this->assertInstanceOf(DateTime::class, $arrayService->offsetGet('date'));
        $this->assertEquals('2016-04-05 01:02:03', $arrayService->offsetGet('date')->format('Y-m-d H:i:s'));
    }

    /**
     * Tests referencing.
     */
    public function testReferenceFinder()
    {
        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        // Tests both class and alias reference to listed but not yet registered services
                        'special1' => [
                            'class'  => ArrayObject::class,
                            'calls'  => [
                                'offsetSet' => ['date', 'alias1'],
                                'setIteratorClass' => [ArrayIterator::class]
                            ],
                            'shared' => true
                        ],
                        'alias1' => [
                            'class' => DateTime::class,
                            'arguments' => [
                                '2016-04-05 01:02:03'
                            ]
                        ],
                    ],
                    'Website' => [
                        'special2' => [
                            'class'  => ArrayObject::class,
                            'calls'  => [
                                'offsetSet' => ['iterator', ArrayIterator::class],
                            ],
                            'shared' => true
                        ],
                    ]
                ],
            ]
        );
        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global')
            ->registerModuleServices('Website');

        // Reference is not a string: returns the same.
        $actualResult = $this->invokePrivateMethod(
            $adapter,
            'getReferenceServiceIfAvailable',
            [155]
        );
        $this->assertInternalType('int', $actualResult);
        $this->assertSame(155, $actualResult);

        // Reference is a registered service.
        /** @var Reference $actualResult */
        $actualResult = $this->invokePrivateMethod(
            $adapter,
            'getReferenceServiceIfAvailable',
            ['special2']
        );
        $this->assertInstanceOf(Reference::class, $actualResult);

        // Reference is an existing but not registered class.
        /** @var Reference $actualResult */
        $actualResult = $this->invokePrivateMethod(
            $adapter,
            'getReferenceServiceIfAvailable',
            [DateTime::class]
        );
        $this->assertInstanceOf(Reference::class, $actualResult);
    }

    /**
     * Tests if shared services can have new arguments before instantiation.
     */
    public function testArgumentSetting()
    {
        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        'alias' => [
                            'class' => DateTime::class,
                            'shared' => true
                        ]
                    ]
                ],
            ]
        );

        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global');

        $timeZone = new DateTimeZone('Europe/Berlin');
        $adapter->setServiceArgument('alias', '2016-04-05 01:02:03')
            ->setServiceArgument('alias', $timeZone);

        /** @var DateTime $actualDate */
        $actualDate = $adapter->get('alias');
        $this->assertInstanceOf(DateTime::class, $actualDate);
        $this->assertEquals('2016-04-05 01:02:03', $actualDate->format('Y-m-d H:i:s'));

        // Set a new parameter after instantiate is forbidden.
        $timeZone = new DateTimeZone('Europe/London');
        $this->expectException(RuntimeException::class);
        $adapter->setServiceArgument('alias', $timeZone);
    }

    /**
     * Tests scalar argument
     */
    public function testScalarArgument()
    {
        $keyData = DateTime::class;

        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        'alias' => [
                            'class' => EmptyEntity::class,
                            'arguments' => [
                                'theKey',
                                $keyData
                            ]
                        ]
                    ]
                ],
            ]
        );

        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global');

        /** @var EmptyEntity $actualDate */
        $actualObject = $adapter->get('alias');
        $this->assertInstanceOf(EmptyEntity::class, $actualObject);
        $this->assertInstanceOf(DateTime::class, $actualObject->getKeyData());

        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        'alias' => [
                            'class' => EmptyEntity::class,
                            'arguments' => [
                                'theKey',
                                '!:'.$keyData
                            ]
                        ]
                    ]
                ],
            ]
        );

        $adapter = new SymfonyAdapter($config);
        $adapter->registerService(ConfigInterface::class, $config)
            ->registerModuleServices('Global');

        /** @var EmptyEntity $actualDate */
        $actualObject = $adapter->get('alias');
        $this->assertInstanceOf(EmptyEntity::class, $actualObject);
        $this->assertInternalType('string', $actualObject->getKeyData());
        $this->assertSame($keyData, $actualObject->getKeyData());
    }

    /**
     * Tests getServiceSetupData
     */
    public function testGetServiceSetupData()
    {
        $config = new Config(
            [
                'dependencies' => [
                    'Global' => [
                        'alias' => [
                            'class' => DateTime::class,
                            'shared' => true
                        ]
                    ]
                ],
            ]
        );

        $adapter = new SymfonyAdapter($config);

        $expectedResult = [
            'class' => 'someServiceClassName',
            'arguments' => [],
            'calls' => [],
            'shared' => false,
        ];

        $actualResult = $this->invokePrivateMethod($adapter, 'getServiceSetupData', ['someService', 'someServiceClassName']);
        $this->assertArraysAreSimilar($expectedResult, $actualResult);
    }
}
