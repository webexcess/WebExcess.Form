<?php

namespace WebExcess\Form\FormElements;

/*
 * This file is part of the WebExcess.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Form\Core\Model\AbstractFormElement;
use TYPO3\Form\Core\Runtime\FormRuntime;
use TYPO3\Form\Core\Runtime\FormState;

class CollapseFormElement extends AbstractFormElement
{

    /**
     * Executed before the current element is outputted to the client
     *
     * @param FormRuntime $formRuntime
     * @return void
     */
    public function beforeRendering(FormRuntime $formRuntime)
    {
        $this->setCollapseClass($formRuntime->getFormState());
    }

    /**
     * Executed after the page containing the current element has been submitted
     *
     * @param FormRuntime $formRuntime
     * @param mixed $elementValue raw value of the submitted element
     * @return void
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue)
    {
        $properties = $this->getProperties();

        if ($properties['collapseField'] !== false) {
            $collapseFieldValue = $formRuntime->getFormState()->getFormValue($properties['collapseField']);

            if (!empty($this->getValidators())) {
                $formDefinition = $this->getRootForm();

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
     * @param FormState $formState
     */
    protected function setCollapseClass(FormState $formState)
    {
        $properties = $this->getProperties();
        $renderingOptions = $this->getRenderingOptions();
        $formDefinition = $this->getRootForm();

        if ($properties['collapseField'] !== false) {
            $defaultValue = $formState->getFormValue($properties['collapseField']) === null ? $formDefinition->getElementDefaultValueByIdentifier($properties['collapseField']) : $formState->getFormValue($properties['collapseField']);

            if (is_array($properties['collapseValue'])) {
                if (in_array($defaultValue, $properties['collapseValue'])) {
                    $this->setRenderingOption('collapseClassAttribute', $renderingOptions['containerVisibleClassAttribute']);
                } else {
                    $this->setRenderingOption('collapseClassAttribute', $renderingOptions['containerHiddenClassAttribute']);
                }
            } else {
                if ($defaultValue == $properties['collapseValue']) {
                    $this->setRenderingOption('collapseClassAttribute', $renderingOptions['containerVisibleClassAttribute']);
                } else {
                    $this->setRenderingOption('collapseClassAttribute', $renderingOptions['containerHiddenClassAttribute']);
                }
            }
        }
    }
}
