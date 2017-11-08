<?php
namespace WebExcess\Form\Finishers;

/*
 * This file is part of the WebExcess.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\AbstractFinisher;
use WebExcess\Form\Service\FormHelperService;
use WebExcess\Form\Domain\Repository\FormRepository;
use WebExcess\Form\Domain\Model\Form;

class SaveToDatabaseFinisher extends AbstractFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = array(
        'excludeFields' => array(),
        'dbLabels' => array(),
        'debug' => false
    );

    /**
     * @Flow\Inject
     * @var FormRepository
     */
    protected $formRepository;

    /**
     * @Flow\Inject
     * @var FormHelperService
     */
    protected $formHelperService;

    /**
     * @Flow\Inject
     * @var \Neos\Form\Persistence\YamlPersistenceManager
     */
    protected $yamlPersistenceManager;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\I18n\Service
     */
    protected $i18nService;


    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @return void
     * @throws \Neos\Form\Exception\FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $defaultLocale = $this->i18nService->getConfiguration()->getDefaultLocale();

        $formIdentifier = $formRuntime->getIdentifier();
        $formValues = $formRuntime->getFormState()->getFormValues();
        foreach ($formValues as $formKey => $formValue) {
            if (is_null($formValue)) {
                unset($formValues[$formKey]);
            }
        }

        // Exclude Fields
        $excludeFields = $this->parseOption('excludeFields');
        if (is_string($excludeFields)) {
            $excludeFields = explode(',', str_replace(' ', '', trim($excludeFields)));
        }

        // Get Label from Form
        if ($this->yamlPersistenceManager->exists($formIdentifier)) {
            $formSettings = $this->yamlPersistenceManager->load($formIdentifier);
            $formLabel = $formSettings['label'];

            // Get data
            $postValues = array();
            foreach ($formValues as $key => $value) {
                if (!in_array($key, $excludeFields)) {
                    $element = $formRuntime->getFormDefinition()->getElementByIdentifier($key);
                    if ($element) {
                        if (array_key_exists($key, $this->parseOption('dbLabels')) && $this->parseOption('dbLabels')[$key] != '') {
                            $postValues[$key]['label'] = $this->parseOption('dbLabels')[$key];
                        } else {
                            $postValues[$key]['label'] = $this->formHelperService->getTranslatedLabel('label', $element, $defaultLocale);
                        }

                        if (is_array($value)) {
                            foreach ($value as $valueItem) {
                                $translatedValue = $this->formHelperService->getTranslatedLabel('options.' . $valueItem, $element, $defaultLocale);
                                $postValues[$key]['value'][] = $translatedValue == '' ? nl2br($valueItem) : nl2br($translatedValue);
                            }
                        } else {
                            $translatedValue = $this->formHelperService->getTranslatedLabel('options.' . $value, $element, $defaultLocale);
                            $postValues[$key]['value'] = $translatedValue == '' ? nl2br($value) : nl2br($translatedValue);
                        }
                    }
                }
            }

            if ($this->parseOption('debug') == true) {
                \Neos\Flow\var_dump($postValues); exit;
            } else {
                if (!empty($postValues)) {
                    $formData = new Form();
                    $formData->setFormIdentifier($formIdentifier);
                    $formData->setFormLabel($formLabel);
                    $formData->setFormValues($postValues);
                    $formData->setCrdate(new \DateTime());
                    $this->formRepository->add($formData);
                    $this->persistenceManager->persistAll();
                }
            }
        }
    }
}
