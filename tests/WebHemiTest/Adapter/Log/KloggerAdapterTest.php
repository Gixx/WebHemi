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

namespace WebHemiTest\Adapter\Log;

use WebHemi\Adapter\Log\LogAdapterInterface;
use WebHemi\Adapter\Log\Klogger\KloggerAdapter;
use WebHemi\Config\Config;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TwigRendererAdapterTest.
 */
class KloggerAdapterTest extends TestCase
{
    /** @var array */
    protected $config;
    /** @var  string */
    protected $dateToday;
    /** @var  string */
    protected $dateYesterday;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->dateToday = date('Y-m-d');
        $this->dateYesterday = date('Y-m-d', (time()-(60*60*24)));

        // Check also in the setup in case of unexpected exception would cut the execution flow and teardown not called.
        if (file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log')) {
            unlink(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log');
        }

        // To make sure that the test doesn't provide extra log files because of a midnight date change, we also check
        // for a "yesterday" log file.
        if (file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log')) {
            unlink(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log');
        }

        $this->config = require __DIR__.'/../../Fixtures/test_config.php';
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();

        if (file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log')) {
            unlink(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log');
        }

        // To make sure that the test doesn't provide extra log files because of a midnight date change, we also check
        // for a "yesterday" log file.
        if (file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log')) {
            unlink(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log');
        }
    }

    /**
     * Tests constructor
     */
    public function testConstructor()
    {
        $config = new Config($this->config);
        $testObj = new KloggerAdapter($config, 'unit');

        $this->assertInstanceOf(LogAdapterInterface::class, $testObj);
    }

    /**
     * Test log.
     */
    public function testLog()
    {
        $config = new Config($this->config);
        $testObj = new KloggerAdapter($config, 'unit');

        $testObj->log(3, 'Warning test');
        $testObj->log('notice', 'Notice test');
        $testObj->log(4332, 'Default numeric warning test');
        $testObj->log('some_level', 'Default string warning test');

        $logfileExists = file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log')
            || file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log');

        $this->assertTrue($logfileExists);

        if (file_exists(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log')) {
            $content = file(__DIR__.'/../../../../data/log/testlog-'.($this->dateToday).'.log');
        } else {
            $content = file(__DIR__.'/../../../../data/log/testlog-'.($this->dateYesterday).'.log');
        }

        $this->assertEquals(4, count($content));
        $this->assertNotFalse(strpos($content[1], '[notice] Notice test'));
        $this->assertNotFalse(strpos($content[3], '[warning] Default string warning test'));
    }
}
