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
namespace WebHemiTestX\Renderer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use WebHemi\Acl\ServiceAdapter\Base\ServiceAdapter as Acl;
use WebHemi\Acl\ServiceInterface as AclAdapterInterface;
use WebHemi\Auth\ServiceAdapter\Base\ServiceAdapter as Auth;
use WebHemi\Auth\ServiceInterface as AuthAdapterInterface;
use WebHemi\Environment\ServiceAdapter\Base\ServiceAdapter as EnvironmentManager;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemi\Configuration\ServiceInterface as ConfigInterface;
use WebHemi\Data\Entity\AccessManagement\ResourceEntity;
use WebHemi\Data\Entity\ApplicationEntity;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\AccessManagement\ResourceStorage;
use WebHemi\Data\Storage\ApplicationStorage;
use WebHemi\Renderer\Helper\IsAllowedHelper;

/**
 * Class IsAllowedHelperTest.
 */
class IsAllowedHelperTest extends TestCase
{
    /** @var array */
    protected $get = [];
    /** @var array */
    protected $post = [];
    /** @var array */
    protected $server;
    /** @var array */
    protected $cookie = [];
    /** @var array */
    protected $files = [];

    /** @var ConfigInterface */
    private $config;
    /** @var UserEntity */
    private $userEntity;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $configData = [
            'applications' => [
                'website' => [
                    'module' => 'Website',
                ],
                'admin' => [
                    'module' => 'Admin',
                ],
            ],
            'router' => [
                'Website' => [
                    'some_page' => [
                        'path'            => '/some/url[/]',
                        'middleware'      => 'SomeMiddleware'
                    ],
                    'some_other_page' => [
                        'path'            => '/some/url/{id:\d+}',
                        'middleware'      => 'SomeOtherMiddleware'
                    ],
                ],
                'Admin' => [
                    'some_admin_page' => [
                        'path'            => '/some/admin/page[/]',
                        'middleware'      => 'SomeMiddleware'
                    ],
                    'some_other_admin_page' => [
                        'path'            => '/some/other/admin/page[/]',
                        'middleware'      => 'SomeOtherMiddleware'
                    ],
                ],
            ],
        ];

        $this->config = new Config($configData);

        $this->userEntity = new UserEntity();
        $this->userEntity->setKeyData(1);
    }

    /**
     * Tests the helper invoke
     */
    public function testHelperWithResourceName()
    {
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $environmentProphecy->getSelectedApplication()->willReturn('website');
        $environmentManager = $environmentProphecy->reveal();

        $aclProphecy = $this->prophesize(AclAdapterInterface::class);
        $aclProphecy->isAllowed(Argument::any(), Argument::any(), Argument::any())->will(
            function ($arguments) {
                /** @var UserEntity $userEntity */
                $userEntity = $arguments[0];
                /** @var ResourceEntity $resourceEntity */
                $resourceEntity = $arguments[1];
                /** @var ApplicationEntity $applicationEntity */
                $applicationEntity = $arguments[2];

                if (is_null($userEntity)
                    || is_null($resourceEntity)
                    || is_null($applicationEntity)
                ) {
                    return false;
                }

                return ($userEntity->getKeyData() === 1
                    && $resourceEntity->getKeyData() === $applicationEntity->getKeyData()
                );
            }
        );
        $aclAdapter = $aclProphecy->reveal();

        $user = $this->userEntity;
        $authProphecy = $this->prophesize(AuthAdapterInterface::class);
        $authProphecy->getIdentity()->will(
            function () use ($user) {
                return $user;
            }
        );
        $authAdapter = $authProphecy->reveal();

        $resourceStorage = $this->getMockBuilder(ResourceStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResourceByName'])
            ->getMock();
        $resourceStorage->method('getResourceByName')
            ->will($this->returnCallback([$this, 'resourceStorageGetResourceByName']));

        $applicationStorage = $this->getMockBuilder(ApplicationStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getApplicationByName'])
            ->getMock();
        $applicationStorage->method('getApplicationByName')
            ->will($this->returnCallback([$this, 'applicationStorageGetApplicationByName']));


        /**
         * @var EnvironmentManager $environmentManager
         * @var Acl $aclAdapter
         * @var Auth $authAdapter
         * @var ResourceStorage $resourceStorage
         * @var ApplicationStorage $applicationStorage
         */
        $helper = new IsAllowedHelper(
            $this->config,
            $environmentManager,
            $aclAdapter,
            $authAdapter,
            $resourceStorage,
            $applicationStorage
        );

        // result = (resourceId == applicationId)
        // 1, 1 => true
        $this->assertTrue($helper('SomeMiddleware'));
        // 2, 1 => false
        $this->assertFalse($helper('SomeOtherMiddleware'));
        // 1, 2 => false
        $this->assertFalse($helper('SomeMiddleware', 'admin'));
        // 2, 2 => true
        $this->assertTrue($helper('SomeOtherMiddleware', 'admin'));
        // null, 1 => false
        $this->assertFalse($helper('SomeNotDefinedMiddleware'));
        // null, null => false
        $this->assertFalse($helper('SomeNotDefinedMiddleware', 'fake_app'));
    }

    /**
     * Tests the helper invoke with url aliases
     */
    public function testHelperWithUrlAliases()
    {
        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $environmentProphecy->getSelectedApplication()->willReturn('website');
        $environmentManager = $environmentProphecy->reveal();

        $aclProphecy = $this->prophesize(AclAdapterInterface::class);
        $aclProphecy->isAllowed(Argument::any(), Argument::any(), Argument::any())->will(
            function ($arguments) {
                /** @var UserEntity $userEntity */
                $userEntity = $arguments[0];
                /** @var ResourceEntity $resourceEntity */
                $resourceEntity = $arguments[1];
                /** @var ApplicationEntity $applicationEntity */
                $applicationEntity = $arguments[2];

                if (is_null($userEntity)
                    || is_null($resourceEntity)
                    || is_null($applicationEntity)
                ) {
                    return false;
                }

                return ($userEntity->getKeyData() === 1
                    && $resourceEntity->getKeyData() === $applicationEntity->getKeyData()
                );
            }
        );
        $aclAdapter = $aclProphecy->reveal();

        $user = $this->userEntity;
        $authProphecy = $this->prophesize(AuthAdapterInterface::class);
        $authProphecy->getIdentity()->will(
            function () use ($user) {
                return $user;
            }
        );
        $authAdapter = $authProphecy->reveal();

        $resourceStorage = $this->getMockBuilder(ResourceStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResourceByName'])
            ->getMock();
        $resourceStorage->method('getResourceByName')
            ->will($this->returnCallback([$this, 'resourceStorageGetResourceByName']));

        $applicationStorage = $this->getMockBuilder(ApplicationStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getApplicationByName'])
            ->getMock();
        $applicationStorage->method('getApplicationByName')
            ->will($this->returnCallback([$this, 'applicationStorageGetApplicationByName']));

        /**
         * @var EnvironmentManager $environmentManager
         * @var Acl $aclAdapter
         * @var Auth $authAdapter
         * @var ResourceStorage $resourceStorage
         * @var ApplicationStorage $applicationStorage
         */
        $helper = new IsAllowedHelper(
            $this->config,
            $environmentManager,
            $aclAdapter,
            $authAdapter,
            $resourceStorage,
            $applicationStorage
        );

        // result = (resourceId == applicationId)
        // 1, 1 => true
        $this->assertTrue($helper('some_page'));
        // 2, 1 => false
        $this->assertFalse($helper('some_other_page'));
        // 1, 2 => false
        $this->assertFalse($helper('some_admin_page', 'admin'));
        // 2, 2 => true
        $this->assertTrue($helper('some_other_admin_page', 'admin'));
        // null, 1 => false
        $this->assertFalse($helper('non-existing_page'));
        // null, null => false
        $this->assertFalse($helper('non-existing_page', 'fake_app'));
    }

    /**
     * Since the PHPUnit Prophecy has issues with nullable return types, we use Mocking instead of prophecy...
     *
     * @param $applicationName
     * @return null|ApplicationEntity
     */
    public function applicationStorageGetApplicationByName($applicationName)
    {
        if ($applicationName == 'website') {
            $applicationEntity = new ApplicationEntity();
            $applicationEntity->setKeyData(1)
                ->setName('website');

            return $applicationEntity;
        } elseif ($applicationName == 'admin') {
            $applicationEntity = new ApplicationEntity();
            $applicationEntity->setKeyData(2)
                ->setName('admin');

            return $applicationEntity;
        }
        return null;
    }

    /**
     * Since the PHPUnit Prophecy has issues with nullable return types, we use Mocking instead of prophecy...
     *
     * @param $resourceName
     * @return null|ResourceEntity
     */
    public function resourceStorageGetResourceByName($resourceName)
    {
        if ($resourceName == 'SomeMiddleware') {
            $resourceEntity = new ResourceEntity();
            $resourceEntity->setKeyData(1)
                ->setName('SomeMiddleware');

            return $resourceEntity;
        } elseif ($resourceName == 'SomeOtherMiddleware') {
            $resourceEntity = new ResourceEntity();
            $resourceEntity->setKeyData(2)
                ->setName('SomeOtherMiddleware');

            return $resourceEntity;
        }
        return null;
    }

    /**
     * Tests helper when no user is defined.
     */
    public function testNoUser()
    {
        $config = new Config([]);

        $environmentProphecy = $this->prophesize(EnvironmentManager::class);
        $environmentManager = $environmentProphecy->reveal();

        $aclProphecy = $this->prophesize(AclAdapterInterface::class);
        $aclAdapter = $aclProphecy->reveal();

        $authAdapter = $this->getMockBuilder(Auth::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIdentity'])
            ->getMock();
        $authAdapter->method('getIdentity')
            ->willReturn(null);

        $resourceProphecy = $this->prophesize(ResourceStorage::class);
        $resourceStorage = $resourceProphecy->reveal();

        $applicationProphecy = $this->prophesize(ApplicationStorage::class);
        $applicationStorage = $applicationProphecy->reveal();

        /**
         * @var EnvironmentManager $environmentManager
         * @var Acl $aclAdapter
         * @var Auth $authAdapter
         * @var ResourceStorage $resourceStorage
         * @var ApplicationStorage $applicationStorage
         */
        $helper = new IsAllowedHelper(
            $config,
            $environmentManager,
            $aclAdapter,
            $authAdapter,
            $resourceStorage,
            $applicationStorage
        );

        $this->assertFalse($helper('Anything, since the user is not logged in, should return FALSE.'));
    }
}
