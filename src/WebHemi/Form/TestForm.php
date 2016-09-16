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
namespace WebHemi\Form;

use WebHemi\Form\Element\FormElement;

/**
 * Class TestForm
 *
 * @codeCoverageIgnore - only for test purposes
 */
class TestForm extends AbstractForm
{
    /**
     * Initialize form.
     *
     * @return void
     */
    protected function initForm()
    {
        // The best way to avoid autocomplete fields is to give unique name to the fields on every page load.
        $this->setNameSalt(time());
        // Change some default attributes.
        $this->setAutocomplete(false);


        // Hidden field.
        $hiddenCsrf = new FormElement(FormElement::TAG_INPUT_HIDDEN, 'csrf');
        $hiddenCsrf->setValue('Some CSRF test value');

        // Fieldsets
        $fieldset1 = $this->getMainFieldset();
        $fieldset2 = $this->getLocationFieldset();

        // Submit button.
        $submit = new FormElement(FormElement::TAG_BUTTON_SUBMIT, 'submit', 'Login');

        // Assign elements and the fieldset to the form
        $this->addChildNode($hiddenCsrf)
            ->addChildNode($fieldset1)
            ->addChildNode($fieldset2)
            ->addChildNode($submit);
    }

    /**
     * Gets main fieldset
     *
     * @return FormElement
     */
    private function getMainFieldset()
    {
        // Fieldset
        $fieldset = new FormElement(FormElement::TAG_FIELDSET, 'info');
        // Legend for the fieldset.
        $legend = new FormElement(FormElement::TAG_LEGEND);
        $legend->setLabel('Login form');

        // Text input with custom attribute
        $loginname = new FormElement(FormElement::TAG_INPUT_TEXT, 'username', 'Username');
        $loginname->setAttribute('placeholder', 'Your login name');

        // Password input
        $password = new FormElement(FormElement::TAG_INPUT_PASSWORD, 'password', 'Password');

        // Single-option checkbox.
        // For single-option radio boxes the ->setValue(1) is equals to this: ->setAttribute('checked', true);
        $checkboxRememberMe = new FormElement(FormElement::TAG_INPUT_CHECKBOX, 'remember_me', 'Remember Me');
        $checkboxRememberMe->setValue(1);

        // Radio group.
        $radioLanguage = new FormElement(FormElement::TAG_INPUT_RADIO, 'language', 'Select language');
        $radioLanguage->setValue('es')
            ->setOptions(
                [
                    ['label' => 'English', 'value' => 'en'],
                    ['label' => 'German', 'value' => 'de'],
                    ['label' => 'Spanish', 'value' => 'es'],
                ]
            );

        // Checkbox group.
        $checkboxTerms = new FormElement(FormElement::TAG_INPUT_CHECKBOX, 'terms', 'Terms and conditions');
        $checkboxTerms->setOptions(
            [
                ['label' => 'I am human.', 'value' => 'human'],
                ['label' => 'I have read the T&C.', 'value' => 'terms_read', 'checked' => false],
                ['label' => 'I want newsletters.', 'value' => 'terms_newsletter', 'checked' => true],
            ]
        );

        // Assign elements to the fieldset
        $fieldset->addChildNode($legend)
            ->addChildNode($loginname)
            ->addChildNode($password)
            ->addChildNode($checkboxRememberMe)
            ->addChildNode($radioLanguage)
            ->addChildNode($checkboxTerms);

        return $fieldset;
    }

    /**
     * Gets location fieldset.
     *
     * @return FormElement
     */
    private function getLocationFieldset()
    {
        // Fieldset
        $fieldset = new FormElement(FormElement::TAG_FIELDSET, 'location');
        // Legend for the fieldset.
        $legend = new FormElement(FormElement::TAG_LEGEND);
        $legend->setLabel('Location');

        // Select box with no multi selection and with no option groups
        $select1 = new FormElement(FormElement::TAG_SELECT, 'country', 'I live in:');
        $select1->setOptions(
            [
                ['label' => 'Hungary', 'value' => 'hu'],
                ['label' => 'Germany', 'value' => 'de', 'checked' => true],
                ['label' => 'Austria', 'value' => 'at'],
            ]
        );

        // Select box with no multi selection and WITH option groups
        $select2 = new FormElement(FormElement::TAG_SELECT, 'dream', 'I\'d like live in:');
        $select2->setOptions(
            [
                ['label' => 'Anywhere', 'value' => '*'],
                ['label' => 'Hungary', 'value' => 'hu', 'group' => 'Europe'],
                ['label' => 'USA', 'value' => 'us', 'group' => 'America'],
                ['label' => 'Germany', 'value' => 'de', 'group' => 'Europe'],
                ['label' => 'Austria', 'value' => 'at', 'group' => 'Europe'],
                ['label' => 'Canada', 'value' => 'ca', 'group' => 'America'],
                ['label' => 'Switzerland', 'value' => 'ch', 'group' => 'Europe', 'checked' => true],
                ['label' => 'France', 'value' => 'fr', 'group' => 'Europe'],
                ['label' => 'Spain', 'value' => 'es', 'group' => 'Europe'],
            ]
        );

        // Select box WITH multi selection. The element is build with constructors.
        $select3 = new FormElement(FormElement::TAG_SELECT, 'past', 'I\'ve already lived in:');
        $select3->setAttribute('multiple', true)
            ->setAttribute('size', 10);

        $select3Group1 = new FormElement(FormElement::TAG_OPTION_GROUP);
        $select3Group1->setLabel('Europe');

        $option1 = new FormElement(FormElement::TAG_OPTION);
        $option1->setValue('hu')->setLabel('Hungary')->setAttribute('selected', true);

        $option2 = new FormElement(FormElement::TAG_OPTION);
        $option2->setValue('de')->setLabel('Germany')->setAttribute('selected', true);

        $option3 = new FormElement(FormElement::TAG_OPTION);
        $option3->setValue('ch')->setLabel('Switzerland');

        $option4 = new FormElement(FormElement::TAG_OPTION);
        $option4->setValue('fr')->setLabel('France');

        $select3Group1->setChildNodes([$option1, $option2, $option3, $option4]);

        $select3Group2 = new FormElement(FormElement::TAG_OPTION_GROUP);
        $select3Group2->setLabel('America');

        $option5 = new FormElement(FormElement::TAG_OPTION);
        $option5->setValue('us')->setLabel('USA');

        $option6 = new FormElement(FormElement::TAG_OPTION);
        $option6->setValue('ca')->setLabel('Canada');

        $select3Group2->setChildNodes([$option5, $option6]);

        $select3Group3 = new FormElement(FormElement::TAG_OPTION_GROUP);
        $select3Group3->setLabel('Asia');

        $option7 = new FormElement(FormElement::TAG_OPTION);
        $option7->setValue('jp')->setLabel('Japan');

        $select3Group3->setChildNodes([$option7]);

        $select3->setChildNodes([$select3Group1, $select3Group2, $select3Group3]);

        // Assign elements to the fieldset
        $fieldset->addChildNode($legend)
            ->addChildNode($select1)
            ->addChildNode($select2)
            ->addChildNode($select3);

        return $fieldset;
    }
}
