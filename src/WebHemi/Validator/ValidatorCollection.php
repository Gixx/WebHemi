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

namespace WebHemi\Validator;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Class ValidatorCollection.
 */
class ValidatorCollection implements ValidatorCollectionInterface
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * ValidatorCollection constructor.
     * @param ValidatorInterface ...$validatorInterfaces
     */
    public function __construct(ValidatorInterface ...$validatorInterfaces)
    {
        foreach ($validatorInterfaces as $validator) {
            $this->addValidator($validator);
        }
    }

    /**
     * Adds a new validator to the collection
     *
     * @param ValidatorInterface $validator
     * @return void
     */
    public function addValidator(ValidatorInterface $validator) : void
    {
        if (!$validator instanceof ValidatorInterface) {
            $valueType = is_object($validator) ? get_class($validator) : gettype($validator);

            throw new InvalidArgumentException(
                sprintf(
                    __METHOD__.' requires parameter 1 to be an instance of ValidatorInterface, %s given.',
                    $valueType
                ),
                1002
            );
        }

        $offset = get_class($validator);

        $this->container[$offset] = $validator;
    }

    /**
     * Returns a new instance of the selected validator
     *
     * @param string $validatorClass
     * @return ValidatorInterface
     */
    public function getValidator(string $validatorClass) : ValidatorInterface
    {
        if (empty($validatorClass)
            || !class_exists($validatorClass)
            || !in_array(ValidatorInterface::class, class_implements($validatorClass))
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    __METHOD__.' requires parameter 1 to be a valid class that implements the %s, %s given.',
                    ValidatorInterface::class,
                    gettype($validatorClass)
                ),
                1003
            );
        }

        return clone $this->container[$validatorClass];
    }
}
