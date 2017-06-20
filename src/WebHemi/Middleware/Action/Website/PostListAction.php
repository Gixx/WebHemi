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

/**
 * Class PostListAction.
 */
class PostListAction extends IndexAction
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
        $blogPosts = [];
        $currentMenu = '';
        $title = '';
        $params = $this->getRoutingParameters();

        if (isset($params['category'])) {
            $currentMenu = $params['category'];
            if ($params['category'] == 'posts') {
                $title = 'Posts';
                $blogPosts = [$this->database[1]];
            } elseif ($params['category'] == 'useful') {
                $title = 'Useful infos';
                $blogPosts = [$this->database[0]];
            } elseif ($params['category'] == 'events') {
                $title = 'Events';
                $blogPosts = [$this->database[2]];
            } elseif ($params['category'] == 'something') {
                $title = 'Something';
                $blogPosts = [$this->database[3]];
            }
        } elseif (isset($params['tag'])) {
            $currentMenu = $params['tag'];
            if ($params['tag'] == 'php') {
                $title = '#PHP';
                $blogPosts = [$this->database[0], $this->database[1]];
            } elseif ($params['tag'] == 'coding') {
                $title = '#Coding';
                $blogPosts = [$this->database[0], $this->database[1]];
            } elseif ($params['tag'] == 'munich') {
                $title = '#München';
                $blogPosts = [$this->database[2], $this->database[3]];
            }
        }

        return [
            'title' => $title,
            'activeMenu' => $currentMenu,
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
            'blogPosts' => $blogPosts,
        ];
    }
}
