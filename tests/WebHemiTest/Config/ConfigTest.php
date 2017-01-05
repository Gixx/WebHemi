<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemiTest\Config;

use InvalidArgumentException;
use WebHemi\Config\Config;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ConfigTest.
 */
class ConfigTest extends TestCase
{
    /** @var  array */
    protected $testConfig;
    /** @var  array */
    protected $processedConfig;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->testConfig = [
            'A' => [
                'A1' => [
                    'A11' => [
                        'some_key' => 'some data',
                        'some other key' => 'some other data',
                    ],
                    'A12' => [
                        'some_key_of_a12' => 'some data',
                        'some other key' => [
                            'A12x4' => 'deepest level',
                        ]
                    ],
                ],
                'A2' => null,
            ],
            'B' => 'data',
            'C' => [
                'C1' => [
                    'some_key' => 'some data',
                    'some other key' => 'some other data',
                ],
            ],
        ];

        $this->processConfig('', $this->testConfig);
    }

    /**
     * Processes the config into a one dimensional array.
     *
     * @param $path
     * @param $config
     */
    protected function processConfig($path, $config)
    {
        if (!is_array($config)) {
            return;
        }

        foreach ($config as $key => $value) {
            $this->processedConfig[$path.$key] = $value;

            if (is_array($value) && !empty($value)) {
                $this->processConfig($path.$key.'/', $value);
            }
        }
    }

    /**
     * Test constructor with empty array.
     */
    public function testEmptyConfig()
    {
        $config = new Config([]);

        $this->assertAttributeEmpty('pathMap', $config);
        $this->assertAttributeEmpty('rawConfig', $config);
    }

    /**
     * Test constructor with test config.
     */
    public function testConfigProcess()
    {
        $config = new Config($this->testConfig);

        $this->assertAttributeEquals($this->testConfig, 'rawConfig', $config);
        $this->assertAttributeEquals($this->processedConfig, 'pathMap', $config);
    }

    /**
     * Test the processed config.
     */
    public function testPathMap()
    {
        $config = new Config($this->testConfig);

        $this->assertFalse($config->has('NonExistingKey'));
        $this->assertTrue($config->has('A/A1/A11'));
        $this->assertFalse($config->has('A/A1/A11/'));
        $this->assertArraysAreSimilar($config->getData('A/A1/A11'), $this->testConfig['A']['A1']['A11']);

        $this->assertEquals('deepest level', $config->getData('A/A1/A12/some other key/A12x4'));

        $this->assertArraysAreSimilar($config->getData('C/C1'), $this->testConfig['A']['A1']['A11']);

        $subConfig = $config->getConfig('A/A1/A11');
        $this->assertInstanceOf(Config::class, $subConfig);
        $this->assertFalse($config === $subConfig);
        $this->assertArraysAreSimilar($subConfig->toArray(), $this->testConfig['A']['A1']['A11']);
    }

    /**
     * Test whether the instance throws exception for invalid path.
     *
     * @throws InvalidArgumentException
     */
    public function testExceptionData()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = new Config($this->testConfig);

        $this->assertFalse($config->getData('NonExistingKey'));
    }

    /**
     * Test whether the instance throws exception for invalid path.
     *
     * @throws InvalidArgumentException
     */
    public function testExceptionConfig()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = new Config($this->testConfig);

        $this->assertFalse($config->getConfig('NonExistingKey'));
    }
}
