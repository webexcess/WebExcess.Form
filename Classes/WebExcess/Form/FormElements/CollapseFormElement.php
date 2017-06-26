<?php
namespace WebExcess\Form\FormElements;

use TYPO3\Form\Core\Model\AbstractFormElement;
use TYPO3\Form\Core\Runtime\FormRuntime;

class CollapseFormElement extends AbstractFormElement
{

    /**
     * Executed after the page containing the current element has been submitted
     *
     * @param FormRuntime $formRuntime
     * @param mixed $elementValue raw value of the submitted element
     * @return void
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue)
    {
        if (!empty($this->getValidators())) {
            $properties = $this->getProperties();

            if ($properties['collapseField'] !== false) {
                $formDefinition = $this->getRootForm();
                $collapseFieldValue = $formRuntime->getFormState()->getFormValue($properties['collapseField']);

                if (is_array($properties['collapseValue'])) {
                    if (!in_array($collapseFieldValue, $properties['collapseValue'])) {
                        $formDefinition->getProcessingRule($this->getIdentifier())->getValidators()->removeAll($formDefinition->getProcessingRule($this->getIdentifier())->getValidators());
                    }
                } else {
                    if ($collapseFieldValue != $properties['collapseValue']) {
                        $formDefinition->getProcessingRule($this->getIdentifier())->getValidators()->removeAll($formDefinition->getProcessingRule($this->getIdentifier())->getValidators());
                    }
                }
            }
        }
    }

    /**
     * Get the global unique identifier of the element
     *
     * @return string
     */
    public function getCollapseFieldIdentifier()
    {
        $properties = $this->getProperties();
        $formDefinition = $this->getRootForm();
        $uniqueIdentifier = sprintf('%s-%s', $formDefinition->getIdentifier(), $properties['collapseField']);
        $uniqueIdentifier = preg_replace('/[^a-zA-Z0-9-_]/', '_', $uniqueIdentifier);
        return lcfirst($uniqueIdentifier);
    }

    /**
     * Get the global unique identifier of the element
     *
     * @return string
     */
    public function getCollapseClassAttribute()
    {
        $properties = $this->getProperties();
        $renderingOptions = $this->getRenderingOptions();
        $formDefinition = $this->getRootForm();

        if ($properties['collapseField'] !== false) {
            $defaultValue = $formDefinition->getElementDefaultValueByIdentifier($properties['collapseField']);

            if (is_array($properties['collapseValue'])) {
                if (in_array($defaultValue, $properties['collapseValue'])) {
                    return $renderingOptions['containerVisibleClassAttribute'];
                }
            } else {
                if ($defaultValue == $properties['collapseValue']) {
                    return $renderingOptions['containerVisibleClassAttribute'];
                }
            }
        }

        return $renderingOptions['containerHiddenClassAttribute'];
    }
}
