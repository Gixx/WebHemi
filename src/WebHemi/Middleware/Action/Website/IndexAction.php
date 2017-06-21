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

use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction.
 */
class IndexAction extends AbstractMiddlewareAction
{
    /** @var array */
    protected $database = [];

    /**
     * IndexAction constructor.
     */
    public function __construct()
    {
        $this->database = [
            [
                'title'       => 'How to start a perfect day',
                'summary'     => 'Good to know...',
                'category'    => ['useful' => 'Useful infos'],
                'tags'        => ['php' => 'PHP', 'coding' => 'Coding'],
                'illustration'=> '/data/upload/filesystem/images/Nature.jpg',
                'path'        => 'posts/view/a_perfect_day.html',
                'publishedAt' => time(),
                'location'    => 'München',
                'author'      => [
                    'name'   => 'Admin',
                    'username'=> 'admin',
                    'avatar' => '/data/upload/avatars/admin.png',
                    'mood'   => ['feels cozy', 'hugging'],
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
                                       vero eos et accusam et justo duo dolores et ea rebum.'
            ],
            [
                'title'       => 'Just an idea',
                'summary'     => null,
                'category'    => ['posts' => 'Posts'],
                'tags'        => ['php' => 'PHP', 'coding' => 'Coding'],
                'illustration'=> null,
                'path'        => 'notepad/just_an_idea.html',
                'publishedAt' => time(),
                'location'    => null,
                'author'      => [
                    'name'   => 'Gabor',
                    'username'=> 'gabor',
                    'avatar' => '/data/upload/avatars/gabor.png',
                    'mood'   => null,
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
                                       vero eos et accusam et justo duo dolores et ea rebum.'
            ],
            [
                'title'       => 'An Owl',
                'category'    => ['events' => 'Events'],
                'tags'        => ['munich' => 'Munich'],
                'illustration'=> '/data/upload/filesystem/images/Owl.jpg',
                'path'        => 'nature/birds/notes/an_owl.html',
                'publishedAt' => time(),
                'location'    => 'München',
                'author'      => [
                    'name'   => 'Amadeus',
                    'username'=> 'a.madeus',
                    'avatar' => '/data/upload/avatars/a.madeus.png',
                    'mood'   => ['feels cozy', 'hugging'],
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
                                       vero eos et accusam et justo duo dolores et ea rebum.'
            ],
            [
                'title'       => 'The new Spiderman movie',
                'summary'     => 'I didn\'t see it yet',
                'category'    => ['something' => 'Something'],
                'tags'        => ['munich' => 'Munich'],
                'illustration'=> '/data/upload/filesystem/images/Spider.jpg',
                'path'        => 'nature/arthropods/spidey.html',
                'publishedAt' => time(),
                'location'    => 'München',
                'author'      => [
                    'name'   => 'Amadeus',
                    'username'=> 'a.madeus',
                    'avatar' => '/data/upload/avatars/a.madeus.png',
                    'mood'   => ['feels cozy', 'hugging'],
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At
                                       vero eos et accusam et justo duo dolores et ea rebum.'
            ]
        ];
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        return [
            'activeMenu' => '',
            'blogPosts' => $this->database,
            'fixPost' => [
                'title'       => 'Welcome to my blog!',
                'summary'     => null,
                'category'    => null,
                'tags'        => null,
                'illustration'=> null,
                'path'        => 'welcome.html',
                'publishedAt' => time(),
                'location'    => null,
                'author'      => [
                    'name'   => 'Gabor',
                    'username'=> 'gabor',
                    'avatar' => '/data/upload/avatars/gabor.png',
                    'mood'   => null,
                ],
                'contentLead' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod 
                                       tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At 
                                       vero eos et accusam et justo duo dolores et ea rebum.'
            ],
        ];
    }
}
