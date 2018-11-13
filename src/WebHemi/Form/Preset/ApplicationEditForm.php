<?php
/**
 * WebHemi.
 *
 * PHP version 7.1
 *
 * @copyright 2012 - 2018 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link http://www.gixx-web.com
 */
declare(strict_types = 1);

namespace WebHemi\Form\Preset;

/**
 * Class ApplicationEditForm
 *
 * @codeCoverageIgnore - only composes an object.
 */
class ApplicationEditForm extends ApplicationCreateForm
{
    /**
     * Initialize the adapter
     */
    protected function initAdapter()
    {
        $this->formAdapter->initialize('application', '[URL]', 'PUT');
    }
}
