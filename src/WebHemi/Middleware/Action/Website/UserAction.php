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

use RuntimeException;
use WebHemi\Data\Storage\User;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class UserAction
 */
class UserAction extends AbstractMiddlewareAction
{
    /** @var User\UserStorage */
    private $userStorage;
    /** @var User\UserMetaStorage */
    private $userMetaStorage;

    /**
     * UserAction constructor.
     *
     * @param User\UserStorage $userStorage
     * @param User\UserMetaStorage $userMetaStorage
     */
    public function __construct(User\UserStorage $userStorage, User\UserMetaStorage $userMetaStorage)
    {
        $this->userStorage = $userStorage;
        $this->userMetaStorage = $userMetaStorage;
    }

    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'website-user';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        $parameters = $this->getRoutingParameters();
        $userName = $parameters['username'] ?? '';

        $user = $this->userStorage->getUserByUserName($userName);

        if (!$user) {
            throw new RuntimeException(sprintf('User with name "%s" not found.', $userName), 404);
        }

        $userMeta = $this->userMetaStorage->getUserMetaForUserId($user->getUserId(), true);

        return [
            'user' => $user,
            'userMeta' => $userMeta
        ];
    }
}
