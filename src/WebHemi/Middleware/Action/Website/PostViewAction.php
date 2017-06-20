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

use WebHemi\Http\ServerRequestInterface;
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
        $routingParams = $this->request->getAttribute(ServerRequestInterface::REQUEST_ATTR_ROUTING_PARAMETERS);

        $content = 'Lorem ipsum dolor sit amet...';
        $testFile = __DIR__.'/../../../../../data/temp/markdownTest.md';

        if (file_exists($testFile)) {
            $content = file_get_contents(__DIR__.'/../../../../../data/temp/markdownTest.md');
        }

        return [
            'activeMenu' => '',
            'categories' => [
                ['url' => 'posts',      'title' => 'Posts',        'icon' => 'chrome_reader_mode',      'new' => 1],
                ['url' => 'useful',     'title' => 'Useful infos', 'icon' => 'perm_device_information', 'new' => 0],
                ['url' => 'events',     'title' => 'Events',       'icon' => 'event_note',              'new' => 0],
                ['url' => 'something',  'title' => 'Something',    'icon' => null,                      'new' => 8],
            ],
            'tags' => [
                ['url' => 'php',    'title' => 'PHP',    'total' => 132, 'new' =>  1],
                ['url' => 'coding', 'title' => 'Coding', 'total' => 132, 'new' =>  0],
                ['url' => 'munich', 'title' => 'Munich', 'total' => 132, 'new' => 85]
            ],
            'blogPost' => [
                'title'       => 'Fake test',
                'publishedAt' => time(),
                'author'      => [
                    'name' => 'Some User'
                ],
                'content'     => $content,
                'parameter'   => $routingParams
            ]
        ];
    }
}
