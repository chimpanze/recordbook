<?php
namespace RecordBook\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

use TYPO3\FLOW3\MVC\Controller\ActionController;
use \RecordBook\Domain\Model\User;
use TYPO3\Party\Domain\Model\ElectronicAddress;
use TYPO3\Party\Domain\Model\PersonName;
use TYPO3\FLOW3\Security\Account;

/**
 * User controller for the RecordBook package 
 *
 * @FLOW3\Scope("singleton")
 */
class UserController extends ActionController {
	
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
	 * @var \TYPO3\FLOW3\Security\AccountFactory
	 * @FLOW3\Inject
	 */
	protected $accountFactory;

	/**
	 * @FLOW3\Inject
	 * @var \RecordBook\Domain\Repository\UserRepository
	 */
	protected $userRepository;

	/**
	 * Shows a list of users
	 *
	 * @return void
	 */
	public function indexAction() {
		$account = $this->authenticationManager->getSecurityContext()->getAccount();
		$this->view->assign('account', $account);
	}

	/**
	 * Shows a form for editing an existing user object
	 *
	 * @param TYPO3\FLOW3\Security\Account $account The user to edit
	 * @return void
	 */
	public function editAction(Account $account) {
		$this->view->assign('account', $account);
	}

	/**
	 * Updates the given user object
	 *
	 * @param TYPO3\FLOW3\Security\Account $account The user to update
	 * @return void
	 */
	public function updateAction(Account $account) {
		$this->accountRepository->update($account);
		$this->addFlashMessage('Benutzerdaten gespeichert.');
		$this->redirect('index');
	}

	/**
	 * Removes the given user object from the user repository
	 *
	 * @param \RecordBook\Domain\Model\User $user The user to delete
	 * @return void
	 */
	public function deleteAction(User $user) {
		$this->userRepository->remove($user);
		$this->addFlashMessage('Deleted a user.');
		$this->redirect('index');
	}
	
}

?>