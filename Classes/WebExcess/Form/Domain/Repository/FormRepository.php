<?php
namespace WebExcess\Form\Domain\Repository;

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
use TYPO3\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class FormRepository extends Repository {

    /**
     * @param string $formIdentifier
     * @param string $direction
     * @return \TYPO3\Flow\Persistence\QueryResultInterface
     */
    public function findByFormIdentifierSorted($formIdentifier, $direction = 'desc') {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('formIdentifier', $formIdentifier)
        );
        $query->setOrderings(array(
            'crdate' => $direction == 'desc' ? \TYPO3\Flow\Persistence\QueryInterface::ORDER_DESCENDING : \TYPO3\Flow\Persistence\QueryInterface::ORDER_ASCENDING
        ));

        return $query->execute();
    }

}
