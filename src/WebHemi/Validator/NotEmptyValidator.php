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
 * class NotEmptyValidator.
 */
class NotEmptyValidator implements ValidatorInterface
{
    /** @var array */
    private $errors;
    /** @var array */
    private $validData;

    /**
     * Validates data. Returns true when data is not empty.
     *
     * @param array $values
     * @return bool
     */
    public function validate(array $values) : bool
    {
        $isEmpty = true;

        foreach ($values as $key => $data) {
            if (is_string($data)) {
                $data = trim($data);
            }

            if (!empty($data)) {
                $isEmpty = false;
                $this->validData[$key] = $data;
            }
        }

        if ($isEmpty) {
            $this->errors[] = 'This field is mandatory and cannot be empty';
            return false;
        }

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
