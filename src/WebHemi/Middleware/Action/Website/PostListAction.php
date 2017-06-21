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
        } elseif (isset($params['date'])) {
            $currentMenu = $params['date'];
            if ($params['date'] == '2017-05') {
                $title = 'Archive: 2017 May';
                $blogPosts = [$this->database[0], $this->database[1], $this->database[2]];
            } elseif ($params['date'] == '2017-06') {
                $title = 'Archive: 2017 June';
                $blogPosts = [$this->database[3]];
            }
        }

        return [
            'title' => $title,
            'activeMenu' => $currentMenu,
            'blogPosts' => $blogPosts,
        ];
    }
}
