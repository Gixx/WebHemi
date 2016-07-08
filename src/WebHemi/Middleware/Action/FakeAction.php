<?php

namespace WebHemi\Middleware\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebHemi\Middleware\MiddlewareInterface;

class FakeAction implements MiddlewareInterface
{
    public function __invoke(ServerRequestInterface &$request, ResponseInterface $response)
    {
        $template = 'blog-list';
        $data = [
            'blogPosts' => [
                [
                    'title'       => 'Fake test 1',
                    'slug'        => 'fake_1',
                    'publishedAt' => time(),
                    'author'      => [
                        'name' => 'John Doe'
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
