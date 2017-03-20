<?php
namespace WebExcess\Form\Domain\Repository;

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
use Neos\Flow\Persistence\Repository;
use Neos\Flow\Persistence\QueryInterface;

/**
 * @Flow\Scope("singleton")
 */
class FormRepository extends Repository {

    /**
     * @param string $formIdentifier
     * @param string $direction
     * @return QueryInterface
     */
    public function findByFormIdentifierSorted($formIdentifier, $direction = 'desc') {
        $query = $this->createQuery();
        
        $query->matching(
            $query->equals('formIdentifier', $formIdentifier)
        );
        $query->setOrderings(array(
            'crdate' => $direction == 'desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING
        ));

        return $query->execute();
    }
    
}
