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
namespace WebHemiTest\Ftp;

use PHPUnit\Framework\TestCase;
use WebHemi\Configuration\ServiceAdapter\Base\ServiceAdapter as Config;
use WebHemiTest\TestService\EmptyFtpAdapter;
use WebHemiTest\TestExtension\AssertArraysAreSimilarTrait;
use WebHemiTest\TestExtension\InvokePrivateMethodTrait;

/**
 * Class FtpAdapterTest
 */
class FtpAdapterTest extends TestCase
{
    /** @var Config */
    protected $config;

    use AssertArraysAreSimilarTrait;
    use InvokePrivateMethodTrait;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        $path = realpath(__DIR__ . '/../TestDocumentRoot/data/temp');

        // generate files
        for ($i = 0; $i < 19; $i++) {
            if ($i == 0) {
                $fileName = 'a.log';
            } else {
                $fileName = sprintf('a.(%d).log', $i);
            }

            if (!file_exists($path.'/'.$fileName)) {
                file_put_contents($path.'/'.$fileName, 'test');
            }
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../test_config.php';
        $this->config = new Config($config);
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $path = realpath(__DIR__ . '/../TestDocumentRoot/data/temp');

        // Do some cleanup
        $files = glob($path.'/*.log'); //get all file names
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Tests the constructor
     */
    public function testConstructor()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $this->assertAttributeEmpty('options', $adapter);

        $expectedPath = realpath(__DIR__.'/../../../src/WebHemi/Ftp/ServiceAdapter');
        $this->assertAttributeSame($expectedPath, 'localPath', $adapter);
    }

    /**
     * Tests option related methods.
     */
    public function testOptions()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $this->assertEmpty($adapter->getOption('not-exists'));
        $this->assertSame('test', $adapter->getOption('not-exists', 'test'));

        $adapter->setOption('not-exists', 'test2');
        $this->assertSame('test2', $adapter->getOption('not-exists', 'test'));

        $options = ['key1' => 'value1', 'key2' => 'value2'];
        $adapter->setOptions($options);
        $this->assertSame('value2', $adapter->getOption('key2', 'something'));
    }

    /**
     * Tests the setLocalPath() method.
     */
    public function testSetLocalPath()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $expectedPath = realpath(__DIR__.'/../../../src/WebHemi/Ftp/ServiceAdapter').'/';
        $adapter->setLocalPath('');
        $this->assertAttributeSame($expectedPath, 'localPath', $adapter);
        $this->assertSame($expectedPath, $adapter->getLocalPath());

        $expectedPath = realpath(__DIR__.'/../../../src/WebHemi/Ftp/ServiceAdapter/Base');
        $adapter->setLocalPath('Base');
        $this->assertAttributeSame($expectedPath, 'localPath', $adapter);
        $this->assertSame($expectedPath, $adapter->getLocalPath());

        $expectedPath = '/tmp';
        $adapter->setLocalPath('/tmp');
        $this->assertAttributeSame($expectedPath, 'localPath', $adapter);
        $this->assertSame($expectedPath, $adapter->getLocalPath());
    }

    /**
     * Tests exception #1
     */
    public function testLocalPathNotExists()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1003);

        $adapter->setLocalPath('/something');
    }

    /**
     * Tests exception #2
     */
    public function testLocalPathNotDirectory()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1003);

        $adapter->setLocalPath(__FILE__);
    }

    /**
     * Tests exception #3
     */
    public function testLocalPathNotReadable()
    {
        $system = exec('uname -a');

        if (empty($system) || strpos($system, 'boot2docker') !== false || !is_dir('/root')) {
            $this->markTestSkipped(
                'This test is not available on this environment.'
            );
        }

        $adapter = new EmptyFtpAdapter($this->config);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1004);

        $adapter->setLocalPath('/root');
    }

    /**
     * Tests exception #4
     */
    public function testLocalPathNotWritable()
    {
        $system = exec('uname -a');

        if (empty($system) || strpos($system, 'boot2docker') !== false || !is_dir('/etc')) {
            $this->markTestSkipped(
                'This test is not available on this environment.'
            );
        }

        $adapter = new EmptyFtpAdapter($this->config);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1005);

        $adapter->setLocalPath('/etc');
    }

    /**
     * Tests the checkLocalFile() method
     */
    public function testCheckLocalFile()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $localFile = realpath(__DIR__ . '/../TestDocumentRoot/testfile.txt');
        $expectedResult = 'testfile.txt';

        $adapter->testLocalFile($localFile, false);
        $this->assertSame($expectedResult, $localFile);

        $expectedResult = 'testfile.(3).txt';
        $adapter->testLocalFile($localFile, true);
        $this->assertSame($expectedResult, $localFile);

        $path = realpath(__DIR__ . '/../TestDocumentRoot/data/temp');
        $adapter->setLocalPath($path);

        $localFile = 'a.log';
        $expectedResult = 'a.(19).log';
        $adapter->testLocalFile($localFile, true);
        $this->assertSame($expectedResult, $localFile);
        file_put_contents($path.'/'.$localFile, 'test');

        $localFile = 'a.log';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(1009);
        $adapter->testLocalFile($localFile, true);
    }

    /**
     * Tests the getOctalChmod() method.
     */
    public function testGetOctalChmod()
    {
        $adapter = new EmptyFtpAdapter($this->config);

        $input = '-rwxr-xr-x';
        $expectedResult = '0755';
        $actualResult = $adapter->convertOctalChmod($input);
        $this->assertSame($expectedResult, $actualResult);

        $input = '-rw-r--r--';
        $expectedResult = '0644';
        $actualResult = $adapter->convertOctalChmod($input);
        $this->assertSame($expectedResult, $actualResult);
    }
}
