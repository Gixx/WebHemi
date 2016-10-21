<?php
/**
 * WebHemi.
 *
 * PHP version 5.6
 *
 * @copyright 2012 - 2016 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element\Web;

/**
 * Class KeygenElement.
 */
class KeygenElement extends AbstractElement
{
    /** @var string */
    protected $type = 'keygen';

    /**
     * SelectElement constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($name = '', $label = '', $value = null)
    {
        parent::__construct($name, $label, $value);

        $this->setTabIndex();
    }

    /**
     * Resets the object when cloning.
     */
    public function __clone()
    {
        parent::__clone();

        $this->setTabIndex();
    }
}
