<?php
/**
 * WebHemi.
 *
 * PHP version 7.0
 *
 * @copyright 2012 - 2017 Gixx-web (http://www.gixx-web.com)
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @link      http://www.gixx-web.com
 */
namespace WebHemi\Form\Element\Web;

/**
 * Class InputElement.
 */
class InputElement extends AbstractTabindexElement
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
