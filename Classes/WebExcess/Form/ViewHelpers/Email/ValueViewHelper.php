<?php
namespace WebExcess\Form\ViewHelpers\Email;

/*
 * This file is part of the WebExcess.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class ValueViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @param array|string $property
     * @return string the rendered form head
     */
    public function render($value)
    {
        if (is_array($value)) {
            return '- ' . implode('<br />- ', $value);
        }

        return $value;
    }
}
