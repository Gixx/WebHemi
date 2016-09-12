<?php

namespace WebHemi\Middleware\Action;

use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataStorage\User\UserStorage;
use WebHemi\Form\LoginForm;
use WebHemi\Middleware\AbstractMiddlewareAction;

/**
 * Class FakeAction
 *
 * @codeCoverageIgnore - only for test purposes
 */
class FakeAction extends AbstractMiddlewareAction
{
    /** @var UserStorage */
    private $userStorage;

    public function __construct(UserStorage $userStorage)
    {
        $this->userStorage = $userStorage;
    }

    public function getTemplateName()
    {
        return 'blog-list';
    }

    public function getTemplateData()
    {
        /** @var UserEntity $userEntity */
        $userEntity = $this->userStorage->getUserById(1);

        var_dump($_POST);

        return [
            'blogPosts' => [
                [
                    'title'       => 'Fake test 1',
                    'slug'        => 'fake_1',
                    'publishedAt' => time(),
                    'author'      => [
                        'name' => $userEntity->getUserName()
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
            'loginForm' => new LoginForm('test')
        ];
    }
}
