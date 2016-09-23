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
 * Class InputElement.
 */
class InputElement extends AbstractElement
{
    /** @var string */
    protected $type = 'text';
    /** @var array */
    private $availableInputTypes = [
        'color',
        'date',
        'datetime',
        'datetime-local',
        'email',
        'image',
        'month',
        'number',
        'range',
        'search',
        'tel',
        'text',
        'time',
        'url',
        'week',
    ];

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
     * Sets input element type.
     *
     * @param string $type
     * @return InputElement
     */
    public function setType($type = 'text')
    {
        if (in_array($type, $this->availableInputTypes)) {
            $this->type = $type;
        }

        return $this;
    }
}
