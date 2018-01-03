<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Router;

use WebHemi\Router\Result\Result;

/**
 * Interface ProxyInterface.
 */
interface ProxyInterface
{
    // View a specific post
    const VIEW_POST = 'view-post';
    // List posts under a specific direcory url (e.g.: foo/org/ or foo/org/index.html)
    const LIST_POST = 'list-post';
    // List posts of a category (e.g.: category/name)
    const LIST_CATEGORY = 'list-category';
    // List posts of a tag (e.g.: tag/name)
    const LIST_TAG = 'list-tag';
    // List posts from a date date (e.g.: archive/2018-02)
    const LIST_ARCHIVE = 'list-archive';
    // List images under a specific gallery url (e.g.: foo/name/index.html)
    const LIST_GALLERY = 'list-gallery';
    // List files under a specific gallery url (e.g.: foo/name/index.html)
    const LIST_BINARY = 'list-binary';
    // List posts writtem by a user (e.g.: user/name/index.html)
    const LIST_USER = 'list-user';

    /**
     * Resolves the middleware class name for the application and URL.
     *
     * @param string $application
     * @param Result $routeResult
     * @return void
     */
    public function resolveMiddleware(string $application, Result&$routeResult) : void;
}
