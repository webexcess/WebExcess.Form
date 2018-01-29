<?php
namespace WebExcess\Form\ViewHelpers\Form;

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
use Neos\Flow\Property\PropertyMapper;
use Neos\Flow\ResourceManagement\PersistentResource;
use Neos\FluidAdaptor\ViewHelpers\Form\AbstractFormFieldViewHelper;

class UploadedResourcesViewHelper extends AbstractFormFieldViewHelper
{

    /**
     * @var PropertyMapper
     * @Flow\Inject
     */
    protected $propertyMapper;

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    public function render($as = 'resources')
    {
        $this->templateVariableContainer->add($as, $this->getUploadedResource());
        $output = $this->renderChildren();
        $this->templateVariableContainer->remove($as);

        return $output;
    }

    protected function getUploadedResource()
    {
        if ($this->getMappingResultsForProperty()->hasErrors()) {
            return null;
        }
        $resourceObjects = $this->getPropertyValue();
        $return = array();

        if ($resourceObjects) {
            foreach ($resourceObjects as $resourceObject) {
                if ($resourceObject['resource'] instanceof PersistentResource) {
                    $return[] = $resourceObject;
                } else {
                    $return[] = $this->propertyMapper->convert($resourceObject['resource'], PersistentResource::class);
                }
            }
        }

        return $return;
    }
}
