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

namespace WebHemi\Validator;

/**
 * Interface ValidatorCollectionInterface.
 */
interface ValidatorCollectionInterface
{
    /**
     * ValidatorCollection constructor.
     * @param ValidatorInterface ...$validatorInterfaces
     */
    public function __construct(ValidatorInterface ...$validatorInterfaces);

    /**
     * Adds a new validator to the collection
     *
     * @param ValidatorInterface $validator
     * @return void
     */
    public function addValidator(ValidatorInterface $validator) : void;

    /**
     * Returns a new instance of the selected validator
     *
     * @param string $validatorClass
     * @return ValidatorInterface
     */
    public function getValidator(string $validatorClass) : ValidatorInterface;
}
