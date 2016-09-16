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
namespace WebHemi\Form\Element;

use InvalidArgumentException;
use RuntimeException;
use WebHemi\Form\Validator\FormValidatorInterface;

/**
 * Class FormElement
 */
final class FormElement extends AbstractFormElement
{
    /** @var array */
    private $options = [];
    /** @var array */
    private $optionGroups = [];

    /**
     * FormElement constructor.
     *
     * @param string $tagName
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct($tagName, $name = '', $label = '', $value = null)
    {
        $this->tagName = $tagName;
        $this->name = preg_replace('/[^a-z0-9]/', '_', strtolower($name));
        $this->label = $label;
        $this->value = $value;

        if (in_array($tagName, $this->tabIndexableTags)) {
            $this->attributes['tabindex'] = self::$tabIndex++;
        }
    }

    /**
     * Sets element name.
     *
     * @param string $name
     * @return FormElement
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the element name.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->name;

        if ($this->parentNode instanceof FormElementInterface) {
            $name = $this->parentNode->getName() . '[' . $this->name . ']';
        }

        if (count($this->options) > 1
            && $this->tagName  == self::TAG_SELECT
            && !empty($this->attributes['multiple'])
        ) {
            $name .= '[]';
        }

        return $name;
    }

    /**
     * Sets element label.
     *
     * @param string $label
     * @return FormElement
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Sets element value.
     *
     * @param mixed $value
     * @return FormElement
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets element Id.
     *
     * @return string
     */
    public function getId()
    {
        $name = $this->getName();
        $md5Match = [];

        // Rip off the unique form prefix to make possible to work with fixed CSS id selectors.
        if (preg_match('/^[a-z0-9]+\_(?P<md5>[a-f0-9]{32}).*$/', $name, $md5Match)) {
            $name = str_replace('_' . $md5Match['md5'], '', $name);
        }

        return 'id_' . trim(preg_replace('/[^a-z0-9]/', '_', $name), '_');
    }

    /**
     * Set label-value options for the element.
     *
     * @param array $options
     * @throws RuntimeException
     * @return FormElement
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option) {
            $checked = !empty($option['checked']);
            $group = !empty($option['group']) ? $option['group'] : 'Default';
            $this->setOption($option['label'], $option['value'], $checked, $group);
        }

        return $this;
    }

    /**
     * Checks if the element has value options.
     *
     * @return bool
     */
    public function hasOptions()
    {
        return !empty($this->options);
    }

    /**
     * Gets element value options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets label-value option for the element.
     *
     * @param string  $label
     * @param string  $value
     * @param boolean $checked
     * @param string  $group
     * @return FormElement
     */
    public function setOption($label, $value, $checked = false, $group = 'Default')
    {
        if (!in_array($this->tagName, $this->multiOptionTags)) {
            throw new RuntimeException(sprintf('Cannot set value options for `%s` element.', $this->tagName));
        }

        $option = &$this->options;

        // For <select> tag, the option groupping is allowed.
        if ($this->tagName == self::TAG_SELECT) {
            if (!isset($this->options[$group])) {
                $this->options[$group] = [];
            }

            $option = &$this->options[$group];

            $this->optionGroups[$group] = $group;
        }

        $option[$label] = [
            'label' => $label,
            'value' => $value,
            'checked' => $checked
        ];

        return $this;
    }

    /**
     * Checks if the Select box has groupped options.
     *
     * @return bool
     */
    public function isGrouppedSelect()
    {
        return count($this->optionGroups) > 1;
    }

    /**
     * Sets element attribute.
     *
     * @param string $key
     * @param mixed $value
     * @throws InvalidArgumentException
     * @return FormElement
     */
    public function setAttribute($key, $value)
    {
        if (!is_scalar($value)) {
            throw new InvalidArgumentException('Element attribute can hold scalar data only.');
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Sets multiple attributes.
     *
     * @param array $attributes
     * @return FormElement
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Gets element attribute.
     *
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            throw new RuntimeException(sprintf('Invalid attribute: `%s`', $name));
        }

        return $this->attributes[$name];
    }

    /**
     * Sets the element errors.
     *
     * @param array $errors
     * @return FormElement
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Sets the element validators.
     *
     * @param array<FormValidatorInterface> $validators
     * @return FormElement
     */
    public function setValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    /**
     * Adds validator to the form.
     *
     * @param FormValidatorInterface $validator
     * @return FormElement
     */
    public function addValidator(FormValidatorInterface $validator)
    {
        $this->validators[] = $validator;

        return $this;
    }

    /**
     * Validates element value.
     *
     * @param bool $reValidate
     * @return bool
     */
    public function isValid($reValidate = false)
    {
        if ($reValidate) {
            $this->errors = [];

            /** @var FormValidatorInterface $validator */
            foreach ($this->validators as $validator) {
                if (!$validator->validate($this->value)) {
                    $this->errors[] = $validator->getError();
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Sets the parent element.
     *
     * @param FormElementInterface $formElement
     * @throws RuntimeException
     * @return FormElementInterface
     */
    public function setParentNode(FormElementInterface $formElement)
    {
        $parentTagName = $formElement->getTagName();

        if (isset($this->mandatoryTagParents[$this->tagName])
            && !in_array($parentTagName, $this->mandatoryTagParents[$this->tagName])
        ) {
            throw new RuntimeException(
                sprintf(
                    'Cannot set `%s` as child element of `%s`.',
                    $this->tagName,
                    $parentTagName
                )
            );
        }

        $this->parentNode = $formElement;

        return $this;
    }

    /**
     * Set the child nodes for the element.
     *
     * @param array<FormElementInterface> $childNodes
     * @return FormElement
     */
    public function setChildNodes(array $childNodes)
    {
        foreach ($childNodes as $formElement) {
            $this->addChildNode($formElement);
        }

        return $this;
    }

    /**
     * Set child node for the element.
     *
     * @param FormElementInterface $childNode
     * @return FormElement
     */
    public function addChildNode(FormElementInterface $childNode)
    {
        $childNode->setParentNode($this);

        $this->childNodes[] = $childNode;

        return $this;
    }

    /**
     * Gets the child nodes of the element.
     *
     * @return array<FormElement>
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }
}
