<?php
namespace RecordBook\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

use TYPO3\FLOW3\MVC\Controller\ActionController;
use \RecordBook\Domain\Model\Entry;

/**
 * Entry controller for the RecordBook package 
 *
 * @FLOW3\Scope("singleton")
 */
class EntryController extends ActionController {

	/**
	 * @FLOW3\Inject
	 * @var \RecordBook\Domain\Repository\EntryRepository
	 */
	protected $entryRepository;

	/**
	 * Shows a list of entries
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('entries', $this->entryRepository->findAll());
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
	}

	/**
	 * Adds the given new entry object to the entry repository
	 *
	 * @param \RecordBook\Domain\Model\Entry $newEntry A new entry to add
	 * @return void
	 */
	public function createAction(Entry $newEntry) {
		$this->entryRepository->add($newEntry);
		$this->addFlashMessage('Created a new entry.');
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
	 * @return void
	 */
	public function uploadAction() {
	}
	
	/**
	 * Import Action
	 * 
	 * @return void
	 */
	public function importAction() {
		
	}

}

?>