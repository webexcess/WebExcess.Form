<?php
namespace WebExcess\Form\ViewHelpers\Iterator;

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
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use Neos\FluidAdaptor\Core\ViewHelper\Exception as ViewHelperException;

class ExplodeViewHelper extends AbstractViewHelper {

    /**
     * @var string
     */
    protected $method = 'explode';

    /**
     * Initialize the arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('content', 'string', 'String to be exploded by glue');
        $this->registerArgument('glue', 'string', 'String used as glue in the string to be exploded.', false, ',');
    }

    /**
     * Render method
     * @return mixed
     * @throws ViewHelperException
     */
    public function render() {
        $content = $this->arguments['content'];
        $glue = $this->resolveGlue();
        $output = call_user_func_array($this->method, [$glue, $content]);
        return $output;
    }

    /**
     * Detects the proper glue string to use for implode/explode operation
     *
     * @return string
     */
    protected function resolveGlue()
    {
        $glue = $this->arguments['glue'];
        if (false !== strpos($glue, ':') && 1 < strlen($glue)) {
            // glue contains a special type identifier, resolve the actual glue
            list ($type, $value) = explode(':', $glue);
            switch ($type) {
                case 'constant':
                    $glue = constant($value);
                    break;
                default:
                    $glue = $value;
            }
        }
        return $glue;
    }

}
