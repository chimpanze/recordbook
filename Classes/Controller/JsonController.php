<?php
namespace RecordBook\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

use TYPO3\FLOW3\MVC\Controller\ActionController;
use \RecordBook\Domain\Model\Entry;
use \RecordBook\Domain\Model\User;

/**
 * Entry controller for the RecordBook package 
 *
 * @FLOW3\Scope("singleton")
 */
class JsonController extends ActionController {
	
	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Security\Authentication\AuthenticationManagerInterface
	 */
	protected $authenticationManager;
	
	/**
	 * @var \TYPO3\FLOW3\Security\AccountRepository
	 * @FLOW3\Inject
	 */
	protected $accountRepository;
	
	/**
	 * @FLOW3\Inject
	 * @var \RecordBook\Domain\Repository\EntryRepository
	 */
	protected $entryRepository;
	
	/**
	 * Retrieve Entrys as JSON
	 * 
	 * @param int $start
	 * @param int $end 
	 * @return string
	 */
	public function loadAction($start, $end) {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		$entries = $this->entryRepository->findByStartAndEnd($start, $end, $user);
		$jsonArray = array();		
		if($entries->count() > 0) {
			foreach($entries as $entry) {
				$color = '#0074CC';

				if($entry->getHoliday()) {
					$color = '#F89406';
				}

				if($entry->getSchool()) {
					$color = '#2F96B4';
				}
				$jsonArray[] = array(
				'title' => $entry->getWork(),
				'start' => $entry->getDate()->getTimestamp(),
				'id' => $this->persistenceManager->getIdentifierByObject($entry),
				'holiday' => $entry->getHoliday(),
				'school' => $entry->getSchool(),
				'duration' => $entry->getDuration(),
				'color' => $color
				);
			}
		}
		return json_encode($jsonArray);
	}
	
	/**
	 * Update Entry
	 * 
	 * @param string $id
	 * @param string $date 
	 * @return void
	 */
	public function dropAction($id, $date) {
		$entry = $this->entryRepository->findByIdentifier($id);
		$newDateTime = new \DateTime();
		$splittedDate = explode('.', $date);
		$newDateTime->setDate($splittedDate[2], $splittedDate[1], $splittedDate[0]);
		$entry->setDate($newDateTime);
		$this->entryRepository->update($entry);
		return 'OK';
	}
	
	/**
	 *
	 * @param string $id
	 * @param string $work
	 * @param string $date
	 * @param float $duration
	 * @param string $holiday
	 * @param string $school 
	 */
	public function updateAction($id, $work, $date, $duration, $holiday = '', $school = '') {
		$entry = $this->entryRepository->findByIdentifier($id);
		$newDateTime = new \DateTime();
		$splittedDate = explode('.', $date);
		$newDateTime->setDate($splittedDate[2], $splittedDate[1], $splittedDate[0]);
		
		$entry->setDate($newDateTime);
		$entry->setWork($work);
		$entry->setDuration((float)$duration);
		$entry->setHoliday($holiday == 'on' ? true : false);
		$entry->setSchool($school == 'on' ? true : false);
		
		$this->entryRepository->update($entry);
		return 'OK';
	}
	
	
}
?>