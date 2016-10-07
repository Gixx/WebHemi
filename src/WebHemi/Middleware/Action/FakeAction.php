<?php

namespace WebHemi\Middleware\Action;

use WebHemi\Application\SessionManager;
use WebHemi\Data\Entity\User\UserEntity;
use WebHemi\Data\Entity\User\UserGroupEntity;
use WebHemi\Data\Storage\User\UserStorage;
use WebHemi\Data\Coupler\UserToGroupCoupler;
use WebHemi\Data\Coupler\UserToPolicyCoupler;
use WebHemi\Data\Coupler\UserGroupToPolicyCoupler;
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
    /** @var UserToPolicyCoupler */
    private $userToPolicyCoupler;
    /** @var UserToGroupCoupler */
    private $userToGroupCoupler;
    /** @var UserGroupToPolicyCoupler */
    private $userGroupToPolicyCoupler;
    /** @var TestForm */
    private $loginForm;
    /** @var SessionManager */
    private $session;

    public function __construct(
        UserStorage $userStorage,
        FormInterface $loginForm,
        SessionManager $session,
        UserToPolicyCoupler $userToPolicyCoupler,
        UserToGroupCoupler $userToGroupCoupler,
        UserGroupToPolicyCoupler $userGroupToPolicyCoupler
    ) {
        $this->userStorage = $userStorage;
        $this->loginForm = $loginForm;
        $this->session = $session;
        $this->userToPolicyCoupler = $userToPolicyCoupler;
        $this->userToGroupCoupler = $userToGroupCoupler;
        $this->userGroupToPolicyCoupler = $userGroupToPolicyCoupler;
    }

    public function getTemplateName()
    {
        return 'blog-list';
    }

    public function getTemplateData()
    {
        /** @var UserEntity $userEntity */
        $userEntity = $this->userStorage->getUserById(1);

        $policies = $this->userToPolicyCoupler->getEntityDependencies($userEntity);
        $groups = $this->userToGroupCoupler->getEntityDependencies($userEntity);
        $groupPolicies = [];

        /** @var UserGroupEntity $group */
        foreach ($groups as $group) {
            $groupPolicies[$group->getKeyData()] = $this->userGroupToPolicyCoupler->getEntityDependencies($group);
        }


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
            'user' => var_export($userEntity, true),
            'policy' => var_export($policies, true),
            'group' => var_export($groups, true),
            'grouppolicy' => var_export($groupPolicies, true),
            'loginForm' => $this->loginForm
        ];
    }
}
