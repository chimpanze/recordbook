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
		$this->view->assign('users', $this->userRepository->findAll());
	}

	/**
	 * Shows a single user object
	 *
	 * @param \RecordBook\Domain\Model\User $user The user to show
	 * @return void
	 */
	public function showAction(User $user) {
		$this->view->assign('user', $user);
	}

	/**
	 * Shows a form for creating a new user object
	 *
	 * @return void
	 */
	public function newAction() {
	}

	/**
	 * Adds the given new user object to the user repository
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $password_repeat
	 * @param string $name
	 * @param string $email
	 * @return void 
	 */
	public function createAction($username, $password, $password_repeat, $name, $email) {
		$defaultRole = 'User';

		if($username == '' || strlen($username) < 3) {
			$this->addFlashMessage('Benutzername zu kurz oder leer.');
			$this->redirect('register');
		} else if($password == '' || $password != $password_repeat) {
			$this->addFlashMessage('Passwort zu kurz oder leer.');
			$this->redirect('register');
		} else {
			// create a account with password an add it to the accountRepository
			$account = $this->accountFactory->createAccountWithPassword($username, $password, array($defaultRole));
			
			$person = $this->createPerson($name, $email, $username);
			$account->setParty($person);
			
			$this->userRepository->add($person);
			$this->accountRepository->add($account);

			// add a message and redirect to the login form
			$this->addFlashMessage('Account created. Please login.');
			$this->redirect('index');
		}
		
		$this->redirect('index');
	}
	
/**
	 * Create a Person object with a given Name and E-Mail.
	 *
	 * @param string $name
	 * @param string $email
	 * @return Person
	 */
	protected function createPerson($name, $email, $username) {
		$user = new User();

		$user->setName($this->createName($name));
		$user->setUsername($username);

		$electronicAddress = new ElectronicAddress();
		$electronicAddress->setIdentifier($email);
		$electronicAddress->setType(ElectronicAddress::TYPE_EMAIL);
		$electronicAddress->setUsage(ElectronicAddress::USAGE_WORK);

		$user->setPrimaryElectronicAddress($electronicAddress);

		return $user;
	}
	
	/**
	 * Convert a given name into a PersonName.
	 *
	 * @param string $name
	 * @return PersonName
	 */
	protected function createName($name) {
		$firstnameAndLastname = explode(' ', $name, 2);
		$firstName = '';
		$lastName = '';
		if (isset($firstnameAndLastname[0])) {
			$firstName = $firstnameAndLastname[0];
		}
		if (isset($firstnameAndLastname[1])) {
			$lastName = $firstnameAndLastname[1];
		}

		return new PersonName('', $firstName, '', $lastName);
	}

	/**
	 * Shows a form for editing an existing user object
	 *
	 * @param \RecordBook\Domain\Model\User $user The user to edit
	 * @return void
	 */
	public function editAction(User $user) {
		$this->view->assign('user', $user);
	}

	/**
	 * Updates the given user object
	 *
	 * @param \RecordBook\Domain\Model\User $user The user to update
	 * @return void
	 */
	public function updateAction(User $user) {
		$this->userRepository->update($user);
		$this->addFlashMessage('Updated the user.');
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
	
	/**
	 * 
	 */
	public function loginAction() {
		
	}
	
	/**
	 * Authenticates an account by invoking the Provider based Authentication Manager.
	 *
	 * On successful authentication redirects to the list of posts, otherwise returns
	 * to the login screen.
	 *
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function authenticateAction() {
		try {
			$this->authenticationManager->authenticate();
			$this->redirect('index');
		} catch (\TYPO3\FLOW3\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Falscher Benutzername oder Passwort.');
			throw $exception;
		}
	}
	
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->addFlashMessage('Sie wurden ausgeloggt.');
		$this->redirect('index');
	}
	
	public function registerAction() {
		
	}

}

?>