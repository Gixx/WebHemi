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

use WebHemi\DateTime;
use WebHemi\Environment\ServiceInterface as EnvironmentInterface;
use WebHemi\Renderer\HelperInterface;

/**
 * Class GetDatesHelper
 */
class GetDatesHelper implements HelperInterface
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
        return 'getDates';
    }

    /**
     * Should return the name of the helper.
     *
     * @return string
     */
    public static function getDefinition() : string
    {
        return '{{ getDates(order_by, limit) }}';
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
        return 'Returns the archive dates for the current application.';
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return array
     */
    public function __invoke() : array
    {
        /* TODO: tbd */
        $dates = [
            new DateTime('now'),
            new DateTime(mktime(12, 11, 10, 9, 18, 2017)),
            new DateTime(mktime(12, 11, 10, 8, 20, 2017)),
            new DateTime(mktime(12, 11, 10, 3, 15, 2016)),
        ];

        $archives = [];

        /** @var DateTime $date */
        foreach ($dates as $date) {
            $archives[] = [
                'url' => $date->format('Y-m'),
                'title' => $date->format('Y4B'),
                'total' => 0,
                'new' => 0
            ];
        }

        return $archives;
    }
}
