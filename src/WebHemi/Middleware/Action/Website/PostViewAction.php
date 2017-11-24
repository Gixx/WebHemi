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

namespace WebHemi\Middleware\Action\Website;

use WebHemi\DateTime;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class PostViewAction
 */
class PostViewAction extends AbstractMiddlewareAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-post-view';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $routingParams = $this->getRoutingParameters();

        $content = 'Lorem ipsum dolor sit amet...';
        $testFile = __DIR__.'/../../../../../data/temp/markdownTest.md';

        if (file_exists($testFile)) {
            $content = file_get_contents(__DIR__.'/../../../../../data/temp/markdownTest.md');
        }

        return [
            'activeMenu' => '',
            'blogPost' => [
                'title'       => 'Hogy indítsuk jól a napot: egy finom, gőzőlgő tea esete',
                'summary'     => 'Jó tudni...',
                'category'    => ['useful' => 'Hasznos infók'],
                'tags'        => ['php' => 'PHP', 'coding' => 'Coding'],
                'illustration'=> '/data/upload/filesystem/images/Nature.jpg',
                'path'        => 'posts/view/a_perfect_day.html',
                'publishedAt' => new DateTime('now'),
                'location'    => 'München',
                'author'      => [
                    'name'   => 'Admin',
                    'username'=> 'admin',
                    'avatar' => '/data/upload/avatars/admin.png',
                    'mood'   => ['szeretve érzi magát', 'hugging'],
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
                                       vero eos et accusam et justo duo dolores et ea rebum.',
                'content'     => $content,
                'parameter'   => $routingParams
            ]
        ];
    }
}
