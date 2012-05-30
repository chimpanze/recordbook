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
class ExportController extends ActionController {
	
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
	 * 
	 * @FLOW3\SkipCsrfProtection
	 */
	public function pdfAction() {
		$user = $this->getUser();
		$this->view->assign('entries', $this->entryRepository->findByUser($user));
	}
	
	/**
	 * 
	 * @FLOW3\SkipCsrfProtection
	 */
	public function xmlAction() {
		$user = $this->getUser();
		$this->view->assign('entries', $this->entryRepository->findByUserAndAggregateInWeeks($user));
		$this->controllerContext->getResponse()->setHeader('Content-Type', 'text/xml');
		$this->controllerContext->getResponse()->setHeader('Content-Disposition', 'attachment; filename=recordbook.xml');
	}
	
	/**
	 * Get user and return it
	 *
	 * @return \RecordBook\Domain\Model\User
	 */
	private function getUser() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$user = $account->getParty();
		return $user;
	}
}
?>