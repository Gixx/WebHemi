<?php

namespace WebHemi\Middleware\Action;

use WebHemi\Application\SessionManager;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Form\FormInterface;
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
    /** @var TestForm */
    private $loginForm;
    /** @var SessionManager */
    private $session;

    public function __construct(UserStorage $userStorage, FormInterface $loginForm, SessionManager $session)
    {
        $this->userStorage = $userStorage;
        $this->loginForm = $loginForm;
        $this->session = $session;
    }

    public function getTemplateName()
    {
        return 'blog-list';
    }

    public function getTemplateData()
    {
        /** @var UserEntity $userEntity */
        $userEntity = $this->userStorage->getUserById(1);

        // Give special name
        $this->loginForm->setName('login');
        // Turn off aut complete feature.
        $this->loginForm->setAutoComplete(false);
        // test data setter
        $this->loginForm->setData((array)$this->request->getParsedBody());

        if (!empty($this->request->getParsedBody())) {
            $this->session->set('session', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            $this->session->regenerateId();
        }

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
            'session' => var_export($this->session->toArray(), true),
            'loginForm' => $this->loginForm
        ];
    }
}
