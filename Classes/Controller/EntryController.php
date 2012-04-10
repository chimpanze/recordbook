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
class EntryController extends ActionController {

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
	 * @FLOW3\Inject
	 * @var \RecordBook\Domain\Repository\CsvRepository
	 */
	protected $csvRepository;
	
        /**
         * @FLOW3\Inject
         * @var \TYPO3\FLOW3\Resource\ResourceManager
         */
        protected $resourceManager;
	
	/**
	 * Shows a list of entries
	 *
	 * @return void
	 */
	public function indexAction() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		$this->view->assign('entries', $this->entryRepository->findByUser($user));
	}

	/**
	 * Shows a single entry object
	 *
	 * @param \RecordBook\Domain\Model\Entry $entry The entry to show
	 * @return void
	 */
	public function showAction(Entry $entry) {
		$this->view->assign('entry', $entry);
	}

	/**
	 * Shows a form for creating a new entry object
	 *
	 * @return void
	 */
	public function newAction() {
		$this->view->assign('newEntry', new \RecordBook\Domain\Model\Entry());
	}
	
/**
         * Initialize the create action
         *
         * @return void
         */
        public function initializeCreateAction() {
                $this->arguments['newEntry']->
                     getPropertyMappingConfiguration()
                     ->forProperty('date') // this line can be skipped in order to specify the format for all properties
                     ->setTypeConverterOption('TYPO3\FLOW3\Property\TypeConverter\DateTimeConverter',
                     \TYPO3\FLOW3\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd.m.Y');
        }

	/**
	 * Adds the given new entry object to the entry repository
	 *
	 * @param \RecordBook\Domain\Model\Entry $newEntry A new entry to add
	 * @return void
	 */
	public function createAction(\RecordBook\Domain\Model\Entry $newEntry) {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		$newEntry->setUser($user);
		$this->entryRepository->add($newEntry);
		$this->addFlashMessage('Neuer Eintrag erstellt.');
		$this->redirect('index');
	}

	/**
	 * Shows a form for editing an existing entry object
	 *
	 * @param \RecordBook\Domain\Model\Entry $entry The entry to edit
	 * @return void
	 */
	public function editAction(Entry $entry) {
		$this->view->assign('entry', $entry);
	}

	/**
	 * Updates the given entry object
	 *
	 * @param \RecordBook\Domain\Model\Entry $entry The entry to update
	 * @return void
	 */
	public function updateAction(Entry $entry) {
		$this->entryRepository->update($entry);
		$this->addFlashMessage('Updated the entry.');
		$this->redirect('index');
	}

	/**
	 * Removes the given entry object from the entry repository
	 *
	 * @param \RecordBook\Domain\Model\Entry $entry The entry to delete
	 * @return void
	 */
	public function deleteAction(Entry $entry) {
		$this->entryRepository->remove($entry);
		$this->addFlashMessage('Deleted a entry.');
		$this->redirect('index');
	}
	
	/**
	 * Upload Action
	 * 
	 * @param \RecordBook\Domain\Model\Csv $newCsv The csv to save
	 * @return void
	 */
	public function uploadAction(\RecordBook\Domain\Model\Csv $newCsv) {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		$newCsv->setUser($user);
		$newCsv->setDate(new \DateTime());
		$this->csvRepository->add($newCsv);
		$this->addFlashMessage('Datei wurde erfolgreich importiert!');
		$this->forward('import');
	}
	
	/**
	 * Import Action
	 * 
	 * @return void
	 */
	public function importAction() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		$csvs = $this->csvRepository->findByUser($user);
		$this->view->assign('csvs', $csvs);
		$this->view->assign('newCsv', new \RecordBook\Domain\Model\Csv());		
	}

	/**
	 * Import the CSV into the Database
	 * 
	 * @param \RecordBook\Domain\Model\Csv $csv 
	 */
	public function importCsvAction(\RecordBook\Domain\Model\Csv $csv) {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		
		$resource = $csv->getOriginalResource()->getResourcePointer();
		$csvFile = file_get_contents('resource://' . $resource);
		
		$lines = preg_split('/\r\n|\r|\n/', $csvFile);
		foreach($lines as $line) {
			$tmp_entry = array();
			$tmp_entry = str_getcsv($line);

			if(is_array($tmp_entry) && $tmp_entry[0] != null) {
				$newEntry = new \RecordBook\Domain\Model\Entry;
				$dateTime = new \DateTime();
				$splittedTime = explode('.', $tmp_entry[0]);
				$dateTime->setDate($splittedTime[2], $splittedTime[1], $splittedTime[0]);
				$dateTime->setTime(0, 0, 0);
				$newEntry->setDate($dateTime);
				$newEntry->setDuration((float)$tmp_entry[1]);
				$newEntry->setWork(utf8_encode($tmp_entry[2]));
				$newEntry->setUser($user);
				$this->entryRepository->add($newEntry);
			}
		}
		$this->addFlashMessage('Der Inhalt wurde importiert.');
		$this->forward('import');
	}
	
	/**
	 * 
	 */
	public function calendarAction() {
		
	}

}

?>