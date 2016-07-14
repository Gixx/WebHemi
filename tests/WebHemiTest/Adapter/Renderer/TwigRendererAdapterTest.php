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
namespace WebHemiTest\Adapter\Renderer;

use InvalidArgumentException;
use WebHemi\Adapter\Renderer\RendererAdapterInterface;
use WebHemi\Adapter\Renderer\Twig\TwigRendererAdapter;
use WebHemi\Config\Config;
use WebHemiTest\AssertTrait;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TwigRendererAdapterTest.
 */
class TwigRendererAdapterTest extends TestCase
{
    /** @var Config */
    protected $templateConfig;
    /** @var string */
    protected $templatePath;

    use AssertTrait;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $options = [
            "map" => [
                "test-page" => "unit/test.twig"
            ]
        ];

        $this->templateConfig = new Config($options);
        $this->templatePath = '/tests/WebHemiTest/Fixtures/test_theme';
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $resultObj = new TwigRendererAdapter($this->templateConfig, $this->templatePath);

        $this->assertInstanceOf(RendererAdapterInterface::class, $resultObj);
        $this->assertAttributeInstanceOf(\Twig_Environment::class, 'adapter', $resultObj);
    }

    /**
     * Test renderer.
     */
    public function testRenderer()
    {
        $adapterObj = new TwigRendererAdapter($this->templateConfig, $this->templatePath);

        $result = $adapterObj->render('test-page');
        $resultData = json_decode($result, true);

        $this->assertInternalType('array', $resultData);
        $this->assertTrue(isset($resultData['template_resource_path']));
        $this->assertEquals($this->templatePath.'/static', $resultData['template_resource_path']);
        $this->assertEquals('Hello World!', $resultData['message']);

        $result = $adapterObj->render('unit/test.twig');
        $resultDataOther = json_decode($result, true);

        $this->assertArraysAreSimilar($resultData, $resultDataOther);


        $this->setExpectedException(InvalidArgumentException::class);
        $adapterObj->render('some_non_existing_theme_map_file');
    }
}
