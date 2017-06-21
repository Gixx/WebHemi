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
 * Class GetTagsHelper
 */
class GetTagsHelper implements HelperInterface
{
    /** @var EnvironmentInterface */
    private $environmentManager;

    /**
     * GetTagsHelper constructor.
     *
     * @param EnvironmentInterface $environmentManager
     */
    public function __construct(EnvironmentInterface $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getName() : string
    {
        return 'getTags';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getTags(order_by, limit) }}';
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
     * Should return a description text.
     *
     * @return string
     */
    public static function getDescription() : string
    {
        return 'Returns the tags for the current application.';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        /* TODO: tbd */

        return [
            ['url' => 'php',    'title' => 'PHP',    'total' => 132, 'new' =>  1],
            ['url' => 'coding', 'title' => 'Coding', 'total' => 132, 'new' =>  0],
            ['url' => 'munich', 'title' => 'Munich', 'total' => 132, 'new' => 85]
        ];
    }
}
