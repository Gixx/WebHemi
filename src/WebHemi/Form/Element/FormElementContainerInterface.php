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
namespace WebHemi\Form\Element;

/**
 * Interface FormElementContainerInterface
 */
interface FormElementContainerInterface
{
    /**
     * FormElementContainer constructor.
     *
     * @param FormElementInterface[] ...$formElementPrototypes
     */
    public function __construct(FormElementInterface ...$formElementPrototypes);
}
