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
declare(strict_types = 1);

namespace WebHemi\Adapter\Renderer\Twig;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class WebHemiTwigExtension
 */
class WebHemiTwigExtension extends Twig_Extension
{
    /** @var string */
    private $viewPath;

    /**
     * WebHemiTwigExtension constructor.
     *
     * @param string $viewPath
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Returns extension functions.
     *
     * @return array<Twig_SimpleFunction>
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('defined', array($this, 'isDefined')),
            new Twig_SimpleFunction('getStat', array($this, 'getStat')),
        ];
    }

    /**
     * Checks if a file exists on the view path.
     *
     * @param string $fileName
     * @return bool
     */
    public function isDefined(string $fileName) : bool
    {
        $fileName = str_replace('@Theme', $this->viewPath, $fileName);
        return file_exists($fileName);
    }

    /**
     * Returns render statistics.
     *
     * @return array
     */
    public function getStat() : array
    {
        return render_stat();
    }
}
