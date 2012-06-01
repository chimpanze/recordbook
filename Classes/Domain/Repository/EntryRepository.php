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
	
	/**
	 * Retrieve Entries by User and give it back as array aggregated in weeks
	 * @param \RecordBook\Domain\Model\User $user
	 * @return array
	 */
	public function findByUserAndAggregateInWeeks($user) {
		$query = $this->createQuery();
		
		$entries = $query->matching($query->equals('user', $user))->execute();
		
		/**
		 * dataArray[year][week][startDate/endDate/data][] = data 
		 */
		$dataArray = array();
		
		if($entries->count() > 0) {
			foreach($entries as $entry) {
				/* @var $date \DateTime */
				$date = $entry->getDate();
				$week = $date->format('W');
				$month = $date->format('m');
				$year = $date->format('Y');
				$weekRange = $this->weekRange($date->format('U'));
				$dataArray[$year][$month][$week]['startDate'] = \DateTime::createFromFormat('d.m.Y', $weekRange[0]);
				$dataArray[$year][$month][$week]['endDateFriday'] = \DateTime::createFromFormat('d.m.Y', $weekRange[2]);
				$dataArray[$year][$month][$week]['endDate'] = \DateTime::createFromFormat('d.m.Y', $weekRange[1]);
				$dataArray[$year][$month][$week]['data'][] = $entry;
			}
			ksort($dataArray);
			foreach($dataArray as &$array) {
				ksort($array);
				foreach($array as &$underArray) {
					ksort($underArray);
				}
			}
		}
		
		return $dataArray;
	}
	
	private function weekRange($ts) {
		$start = (date('w', $ts) == 0) ? $ts : strtotime('last monday', $ts);
		return array(date('d.m.Y', $start), date('d.m.Y', strtotime('next sunday', $start)), date('d.m.Y', strtotime('next friday', $start)));
	}

}
?>