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

namespace WebHemi\Validator;

/**
 * class RangeValidator.
 */
class RangeValidator implements ValidatorInterface
{
    /** @var array  */
    private $availableValues;
    /** @var bool */
    private $validateKeys;
    /** @var array */
    private $errors;
    /** @var array */
    private $validData;

    /**
     * RangeValidator constructor.
     *
     * @param array $availableValues
     * @param bool  $validateKeys
     */
    public function __construct(array $availableValues, bool $validateKeys = false)
    {
        $this->availableValues = array_values($availableValues);
        $this->validateKeys = $validateKeys;
    }

    /**
     * Validates data.
     *
     * @param array $values
     * @return bool
     */
    public function validate(array $values) : bool
    {
        $data = $this->validateKeys ? array_keys($values) : array_values($values);
        $diff = array_diff($data, $this->availableValues);

        if (!empty($diff)) {
            $this->errors[] = sprintf("Some data is out of range: %s", implode(', ', $diff));
            return false;
        }

        $this->validData = $values;

        return true;
    }

    /**
     * Retrieve valid data.
     *
     * @return array
     */
    public function getValidData() : array
    {
        return $this->validData;
    }

    /**
     * Gets errors from validation.
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
