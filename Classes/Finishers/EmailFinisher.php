<?php
namespace WebExcess\Form\Finishers;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Form\Exception\FinisherException;
use Neos\FluidAdaptor\View\StandaloneView;
use Neos\SwiftMailer;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use WebExcess\Form\Service\FormHelperService;

class EmailFinisher extends AbstractFinisher
{
    const FORMAT_PLAINTEXT = 'plaintext';
    const FORMAT_HTML = 'html';

    /**
     * @var array
     */
    protected $defaultOptions = array(
        'recipientName' => '',
        'senderName' => '',
        'excludeFields' => array(),
        'format' => self::FORMAT_HTML,
        'testMode' => false,
        'debugMessage' => false,
    );

    /**
     * @Flow\Inject
     * @var FormHelperService
     */
    protected $formHelperService;

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @return void
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();

        $excludeFields = is_array($this->parseOption('excludeFields')) ? $this->parseOption('excludeFields') : explode(',', preg_replace('/\s+/', '', $this->parseOption('excludeFields')));

        $arguments = $formRuntime->getRequest()->getArguments();
        $argumentsTmp = array();
        foreach ($arguments as $argument => $value) {
            if (!in_array($argument, $excludeFields)) {
                $argumentsTmp[$argument] = $value;
            }
        }
        $formRuntime->getRequest()->setArguments($argumentsTmp);

        $formValues = array();
        foreach ($formRuntime->getRequest()->getArguments() as $key => $value) {
            $element = $formRuntime->getFormDefinition()->getElementByIdentifier($key);

            if ($element) {
                $formValues[$key]['label'] = $this->formHelperService->getTranslatedLabel('label', $element);

                if (is_array($value)) {
                    foreach ($value as $valueItem) {
                        $translatedValue = $this->formHelperService->getTranslatedLabel('options.' . $valueItem, $element);
                        $formValues[$key]['value'][] = $translatedValue == '' ? nl2br($valueItem) : nl2br($translatedValue);
                    }
                } else {
                    $translatedValue = $this->formHelperService->getTranslatedLabel('options.' . $value, $element);
                    $formValues[$key]['value'] = $translatedValue == '' ? nl2br($value) : nl2br($translatedValue);
                }
            }
        }

        $standaloneView = $this->initializeStandaloneView();
        $standaloneView->assign('formValues', $formValues);
        $standaloneView->assign('postValues', $formRuntime->getRequest()->getArguments());
        $message = $standaloneView->render();

        if ($this->parseOption('debugMessage')) {
            echo $message;
            exit;
        }

        $recipientAddress = null;
        if ($this->parseOption('recipientAddress') != '') {
            $recipientAddress = $this->parseOption('recipientAddress');
        } else {
            if ($this->parseOption('recipientAddressFallback')) {
                $recipientAddress = $this->parseOption('recipientAddressFallback');
            }
        }

        $subject = $this->parseOption('subject');
        $recipientName = $this->parseOption('recipientName');
        $senderAddress = $this->parseOption('senderAddress');
        $senderName = $this->parseOption('senderName');
        $replyToAddress = $this->parseOption('replyToAddress');
        $carbonCopyAddress = $this->parseOption('carbonCopyAddress');
        $blindCarbonCopyAddress = $this->parseOption('blindCarbonCopyAddress');
        $format = $this->parseOption('format');
        $testMode = $this->parseOption('testMode');

        if ($subject === null) {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if ($recipientAddress === null) {
            throw new FinisherException('The option "recipientAddress" must be set for the EmailFinisher.', 1327060200);
        }
        if ($senderAddress === null) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $mail = new SwiftMailer\Message();

        $mail
            ->setFrom(array($senderAddress => $senderName))
            ->setTo(array($recipientAddress => $recipientName))
            ->setSubject($subject);

        if ($replyToAddress !== null) {
            $mail->setReplyTo($replyToAddress);
        }

        if ($carbonCopyAddress !== null) {
            $mail->setCc($carbonCopyAddress);
        }

        if ($blindCarbonCopyAddress !== null) {
            $mail->setBcc($blindCarbonCopyAddress);
        }

        if ($format === self::FORMAT_PLAINTEXT) {
            $mail->setBody($message, 'text/plain');
        } else {
            $mail->setBody($message, 'text/html');
        }

        if ($testMode === true) {
            echo '<table style="margin-bottom: 30px;">
                <tr>
                    <td><i>Subject:</i></td>
                    <td>' . $subject . '</td>
                </tr>
                <tr>
                    <td><i>Sender (Email => Name):</i></td>
                    <td>' . $senderAddress . ' => ' . $senderName . '</td>
                </tr>
                <tr>
                    <td><i>Recipient (Email => Name):</i></td>
                    <td>' . $recipientAddress . ' => ' . $recipientName . '</td>
                </tr>
                <tr>
                    <td><i>Reply to address:</i></td>
                    <td>' . $replyToAddress . '</td>
                </tr>
                <tr>
                    <td><i>Carbon copy address:</i></td>
                    <td>' . $carbonCopyAddress . '</td>
                </tr>
                <tr>
                    <td><i>Blind carbon copy address:</i></td>
                    <td>' . $blindCarbonCopyAddress . '</td>
                </tr>
                <tr>
                    <td><i>Format:</i></td>
                    <td>' . $format . '</td>
                </tr>
            </table><hr style="margin-bottom: 30px;">';
            echo $message;
            exit;
        } else {
            $mail->send();
        }
    }

    /**
     * @return StandaloneView
     * @throws FinisherException
     */
    protected function initializeStandaloneView()
    {
        $standaloneView = new StandaloneView();
        if (!isset($this->options['templatePathAndFilename'])) {
            throw new FinisherException('The option "templatePathAndFilename" must be set for the EmailFinisher.', 1327058829);
        }
        $standaloneView->setTemplatePathAndFilename($this->options['templatePathAndFilename']);

        if (isset($this->options['partialRootPath'])) {
            $standaloneView->setPartialRootPath($this->options['partialRootPath']);
        }

        if (isset($this->options['layoutRootPath'])) {
            $standaloneView->setLayoutRootPath($this->options['layoutRootPath']);
        }

        if (isset($this->options['variables'])) {
            $standaloneView->assignMultiple($this->options['variables']);
        }

        return $standaloneView;
    }

    /**
     * Extends the functionality of the default parseOption() method
     * by making node-properties available
     *
     * @param string $optionName
     * @return mixed|string
     */
    protected function parseOption($optionName)
    {
        if (!isset($this->options[$optionName]) || $this->options[$optionName] === '') {
            if (isset($this->defaultOptions[$optionName])) {
                $option = $this->defaultOptions[$optionName];
            } else {
                return NULL;
            }
        } else {
            $option = $this->options[$optionName];
        }
        if (!is_string($option)) {
            return $option;
        }
        if (preg_match('/{node\.([^}]+)}/', $option, $matches)) {
            $renderingOptions = $this->finisherContext->getFormRuntime()->getRenderingOptions();
            if (isset($renderingOptions['node'])) {
                /** @var NodeInterface $node */
                $node = $renderingOptions['node'];
                if ($node->hasProperty($matches[1])) {
                    $property = $node->getProperty($matches[1]);
                    if (!empty($property)) {
                        return $property;
                    }
                }
            }
        }
        return parent::parseOption($optionName);
    }
}
