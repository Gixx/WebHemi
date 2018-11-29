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

namespace WebHemi\Middleware\Action\Admin\ControlPanel\Settings;

use RuntimeException;
use WebHemi\Middleware\Action\AbstractMiddlewareAction;

/**
 * Class IndexAction
 */
class IndexAction extends AbstractMiddlewareAction
{
    /**
     * Gets template map name or template file path.
     *
     * @return string
     */
    public function getTemplateName() : string
    {
        return 'admin-control-panel-settings-index';
    }

    /**
     * Gets template data.
     *
     * @return array
     */
    public function getTemplateData() : array
    {
        throw new RuntimeException('Not Implemented', 501);
        return [];
    }
}
