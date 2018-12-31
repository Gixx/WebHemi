<?php
/**
 * WebHemi.
 *
 * PHP version 7.2
 *
 * @copyright 2012 - 2019 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Renderer\Helper;

use WebHemi\Auth\ServiceInterface as AuthInterface;
use WebHemi\Data\Entity\UserEntity;
use WebHemi\Renderer\HelperInterface;

/**
 * Class GetCurrentUserHelper
 */
class GetCurrentUserHelper implements HelperInterface
{
    /**
     * @var AuthInterface
     */
    private $authAdapter;

    /**
     * Should return the name of the helper.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getName() : string
    {
        return 'getCurrentUser';
    }

    /**
     * Should return the name of the helper.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getDefinition() : string
    {
        return '{% set user = getCurrentUser() %}';
    }

    /**
     * Should return a description text.
     *
     * @return             string
     * @codeCoverageIgnore - plain text
     */
    public static function getDescription() : string
    {
        return 'Returns the current logged in user information.';
    }

    /**
     * Gets helper options for the render.
     *
     * @return             array
     * @codeCoverageIgnore - empty array
     */
    public static function getOptions() : array
    {
        return [];
    }

    /**
     * IsAllowedHelper constructor.
     *
     * @param AuthInterface $authAdapter
     */
    public function __construct(
        AuthInterface $authAdapter
    ) {
        $this->authAdapter = $authAdapter;
    }

    /**
     * A renderer helper should be called with its name.
     *
     * @return null|UserEntity
     */
    public function __invoke() : ? UserEntity
    {
        /**
         * @var UserEntity $userEntity
         */
        return $this->authAdapter->getIdentity();
    }
}
