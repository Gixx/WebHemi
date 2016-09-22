<?php

namespace WebHemi\Middleware\Action;

use WebHemi\DataEntity\User\UserEntity;
use WebHemi\DataStorage\User\UserStorage;
use WebHemi\Form\Web\TestForm;
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

        $form = new TestForm('test', '', 'POST');
        // Turn off aut complete feature.
        $form->setAutoComplete(false);
        // test data setter
        $form->setData((array)$this->request->getParsedBody());

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
            'postData' => var_export($this->request->getParsedBody(), true),
            'loginForm' => $form
        ];
    }
}
