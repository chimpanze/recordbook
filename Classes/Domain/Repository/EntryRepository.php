<?php
namespace RecordBook\Domain\Repository;

/*                                                                        *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use \RecordBook\Domain\Model\User;

/**
 * A repository for Entries
 *
 * @FLOW3\Scope("singleton")
 */
class EntryRepository extends \TYPO3\FLOW3\Persistence\Repository {
	
	public function __construct() {
		parent::__construct();
		$this->setDefaultOrderings(array('date' => \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_DESCENDING));
	}

	/**
	 * Retrieve Entries by Start Timestamp and End Timestamp
	 * 
	 * @param int $start
	 * @param int $end 
	 * @param \RecordBook\Domain\Model\User $user
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function findByStartAndEnd($start, $end, $user) {
		$query = $this->createQuery();
		
		$startDateTime = new \DateTime();
		$endDateTime = new \DateTime();
		
		$startDateTime->setTimestamp($start);
		$endDateTime->setTimestamp($end);
		
		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual('date', $startDateTime),
				$query->lessThanOrEqual('date', $endDateTime),
				$query->equals('user', $user)
				)			
			)->execute();
	}

}
?>