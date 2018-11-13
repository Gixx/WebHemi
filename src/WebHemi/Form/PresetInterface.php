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

namespace WebHemi\Form;

use WebHemi\Validator\ValidatorCollection;

/**
 * Interface PresetInterface
 */
interface PresetInterface
{
    /**
     * PresetInterface constructor.
     *
     * @param ServiceInterface   $formAdapter
     * @param ValidatorCollection $validatorCollection
     * @param ElementInterface ...$elementPrototypes
     */
    public function __construct(
        ServiceInterface $formAdapter,
        ValidatorCollection $validatorCollection,
        ElementInterface ...$elementPrototypes
    );

    /**
     * Returns the initialized form.
     *
     * @return ServiceInterface
     */
    public function getPreset() : ServiceInterface;
}
