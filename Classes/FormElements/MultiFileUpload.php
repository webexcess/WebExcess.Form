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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\Form\Core\Model\AbstractFormElement;
use Neos\Form\Core\Runtime\FormRuntime;
use Neos\Form\Validation\FileTypeValidator;

use Neos\Flow\Property\PropertyMapper;

class MultiFileUpload extends AbstractFormElement
{

    /**
     * @var PropertyMapper
     * @Flow\Inject
     */
    protected $propertyMapper;

    /**
     * @param FormRuntime $formRuntime
     * @param mixed $elementValue
     * @return void
     * @throws \Neos\Flow\Property\Exception
     * @throws \Neos\Flow\Security\Exception
     * @throws \Neos\Form\Exception\FormDefinitionConsistencyException
     */
    public function onSubmit(FormRuntime $formRuntime, &$elementValue)
    {
        if (is_array($elementValue)) {
            foreach ($elementValue as $fieldIdentifier => $fieldValue) {
                $persistentResource = $this->propertyMapper->convert($fieldValue['resource'], PersistentResource::class);

                if ($persistentResource instanceof PersistentResource) {
                    $fileTypeValidator = new FileTypeValidator(array('allowedExtensions' => $this->properties['allowedExtensions']));
                    $validate = $fileTypeValidator->validate($persistentResource);

                    if ($validate->hasErrors()) {
                        $processingRule = $this->getRootForm()->getProcessingRule($this->getIdentifier());
                        $processingRule->getProcessingMessages()->addError($validate->getFirstError());
                    }
                }
            }
        }
    }
}
