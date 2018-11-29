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

namespace WebHemi\Form\Preset;

use InvalidArgumentException;
use WebHemi\Form\ElementInterface;
use WebHemi\Form\PresetInterface;
use WebHemi\Form\ServiceInterface;
use WebHemi\Validator\ValidatorCollection;

/**
 * Class AbstractPreset
 */
abstract class AbstractPreset implements PresetInterface
{
    /**
     * @var ServiceInterface
     */
    protected $formAdapter;
    /**
     * @var ElementInterface[]
     */
    private $elementPrototypes;
    /**
     * @var ValidatorCollection
     */
    protected $validatorCollection;

    /**
     * LogSelector constructor.
     *
     * @param ServiceInterface   $formAdapter
     * @param ValidatorCollection $validatorCollection
     * @param ElementInterface ...$elementPrototypes
     */
    public function __construct(
        ServiceInterface $formAdapter,
        ValidatorCollection $validatorCollection,
        ElementInterface ...$elementPrototypes
    ) {
        $this->formAdapter = $formAdapter;
        $this->validatorCollection = $validatorCollection;

        foreach ($elementPrototypes as $formElement) {
            $this->elementPrototypes[get_class($formElement)] = $formElement;
        }

        $this->init();
    }

    /**
     * Initialize and add elements to the form.
     *
     * @return void
     */
    abstract protected function init() : void;

    /**
     * Create a certain type of element.
     *
     * @param  string $class
     * @param  string $type
     * @param  string $name
     * @param  string $label
     * @return ElementInterface
     */
    protected function createElement(string $class, string $type, string $name, string $label) : ElementInterface
    {
        if (!isset($this->elementPrototypes[$class])) {
            throw new InvalidArgumentException(
                sprintf('%s is not injected into the %s preset', $class, get_called_class()),
                1000
            );
        }

        /**
         * @var ElementInterface $element
         */
        $element = clone $this->elementPrototypes[$class];

        $element->setType($type)
            ->setName($name)
            ->setLabel($label);

        return $element;
    }

    /**
     * Returns the initialized form.
     *
     * @return ServiceInterface
     */
    public function getPreset() : ServiceInterface
    {
        return $this->formAdapter;
    }
}
