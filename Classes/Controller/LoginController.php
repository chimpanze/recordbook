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
class LoginController extends ActionController {
	
	/**
	 * @var \TYPO3\FLOW3\Security\Cryptography\HashService
	 * @FLOW3\Inject
	 */
	protected $hashService;
	
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
			$this->addFlashMessage('Benutzername zu kurz oder leer.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
			$this->redirect('register');
		} else if($password == '' || $password != $password_repeat) {
			$this->addFlashMessage('Passwort zu kurz oder leer.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
			$this->redirect('register');
		} else if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
			$this->addFlashMessage('E-Mail Adresse ist nicht valide.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
			$this->redirect('register');
		} else {
			// create a account with password an add it to the accountRepository
			$account = $this->accountFactory->createAccountWithPassword($username, $password, array($defaultRole));
			
			$person = $this->createPerson($name, $email, $username);
			$account->setParty($person);
			
			$this->userRepository->add($person);
			$this->accountRepository->add($account);

			// add a message and redirect to the login form
			$this->addFlashMessage('Account wurde erstellt. Logge dich ein!');
			$this->redirect('login');
		}
		
		$this->redirect('login');
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
			$this->redirect('index', 'Entry');
		} catch (\TYPO3\FLOW3\Security\Exception\AuthenticationRequiredException $exception) {
			$this->addFlashMessage('Falscher Benutzername oder Passwort.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
			throw $exception;
		}
	}
	
	/**
	 * Logout Action
	 * 
	 * @return void 
	 */
	public function logoutAction() {
		$this->authenticationManager->logout();
		$this->addFlashMessage('Sie wurden ausgeloggt.');
		$this->redirect('login');
	}
	
	/**
	 * Register Action
	 * 
	 * @return void 
	 */
	public function registerAction() {
	}
	
	/**
	 * Retrieve Password Action
	 * 
	 * @return void 
	 */
	public function passwordAction() {
		
	}
	
	/**
	 * Sends the user a new Password
	 * 
	 * @param string $username
	 * @param string $email
	 * @return void
	 */
	public function resetAction($username, $email) {
		$users = $this->userRepository->findByUsername($username);
		if($users->count() > 0) {
			$user = $users->getFirst();
			if($user->getPrimaryElectronicAddress()->getIdentifier() === $email) {
				$accounts = $this->accountRepository->findByParty($user);
				$account = $accounts->getFirst();
				$password = $this->createRandomPassword();
				$account->setCredentialsSource($this->hashService->hashPassword($password));
				$this->accountRepository->update($account);
				
				$mail = new \TYPO3\SwiftMailer\Message();
				$mail->setFrom(array('passwordreset@recordbookgenerator.com' => 'Berichtsheft Generator'))
					->setTo(array($email => $username))
					->setSubject('Passwort zurücksetzen')
					->setBody('Ein neues Passwort wurde gesetzt: '.$password)
					->send();
				
				$this->addFlashMessage('Ein neues Passwort wurde gesetzt.', 'Info', \TYPO3\FLOW3\Error\Message::SEVERITY_NOTICE);
			} else {
				$this->addFlashMessage('Die E-Mail Adresse stimmt nicht.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
			}
		} else {
			$this->addFlashMessage('Der Benutzer wurde nicht gefunden.', 'Fehler', \TYPO3\FLOW3\Error\Message::SEVERITY_ERROR);
		}
		$this->forward('password');
	}
	
	/**
	 * creates random password string
	 * 
	 * @return string 
	 */
	private function createRandomPassword() {
		$chars = array_merge(
			range(0,9),
			range('a','z'),
			range('A','Z'),
			array('!','@','$','%','^','&','*')
			);
		
		shuffle($chars);
		
		$password = '';
		for($i=0; $i<8; $i++) {
			$password .= $chars[$i];
		}
		return $password;
	}
	
}

?>