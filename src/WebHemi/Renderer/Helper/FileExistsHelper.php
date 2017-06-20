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

namespace WebHemi\Renderer\Helper;

use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;

/**
 * Class FileExistsHelper.
 */
class FileExistsHelper implements HelperInterface
{
    /** @var string */
    private $applicationRoot;

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'fileExists';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{% if fileExists("fileName") %}';
    }

    /**
     * Should return a description text.
     *
     * @return string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Checks if the given filepath exists under the application root.';
    }

    /**
     * Gets helper options for the render.
     *
     * @return array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * DefinedHelper constructor.
     *
     * @param EnvironmentInterface   $environmentManager
     */
    public function __construct(EnvironmentInterface $environmentManager)
    {
        $this->applicationRoot = $environmentManager->getApplicationRoot();
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return bool
     */
    public function __invoke() : bool
    {
        $fileName = trim(func_get_args()[0], '/');

        return file_exists($this->applicationRoot.'/'.$fileName);
    }
}
