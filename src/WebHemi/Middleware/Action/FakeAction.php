<?php

namespace WebHemi\Middleware\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataStorage\User\UserStorage;
use WebHemi\Middleware\MiddlewareInterface;

class FakeAction implements MiddlewareInterface
{
    /** @var UserStorage */
    private $userStorage;

    public function __construct(UserStorage $userStorage)
    {
        $this->userStorage = $userStorage;
    }

    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        /** @var UserEntity $userEntity */
        $userEntity = $this->userStorage->getUserById(1);

        $template = 'blog-list';
        $data = [
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
            ]
        ];

        $request = $request
            ->withAttribute('template', $template)
            ->withAttribute('data', $data);

        return $response;
    }
}
