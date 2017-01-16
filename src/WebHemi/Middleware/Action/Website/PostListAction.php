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
declare(strict_types=1);

namespace WebHemi\Middleware\Action\Website;

use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class PostListAction
 */
class PostListAction extends AbstractMiddlewareAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-post-list';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        return [
            'blogPosts' => [
                [
                    'title'       => 'Fake test 1',
                    'slug'        => 'fake_1',
                    'publishedAt' => time(),
                    'author'      => [
                        'name' => 'Amadeus'
                    ],
                    'content'     => 'Lorem ipsum dolor sit amet...'
                ],
                [
                    'title'       => 'Fake test 2',
                    'slug'        => 'fake_2',
                    'publishedAt' => time(),
                    'author'      => [
                        'name' => 'Jane Doe'
                    ],
                    'content'     => 'Lorem ipsum dolor sit amet...'
                ]
            ],
        ];
    }
}
