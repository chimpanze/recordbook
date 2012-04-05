<?php
namespace RecordBook\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A User
 *
 * @FLOW3\Scope("prototype")
 * @FLOW3\Entity
 */
class User extends \TYPO3\Party\Domain\Model\Person {

	/**
	 *
	 * @var \Doctrine\Common\Collections\Collection<\RecordBook\Domain\Model\Entry>
	 * @ORM\OneToMany(mappedBy="user")
	 * @ORM\OrderBy({"date" = "DESC"})
	 */
	protected $entries;
	
	/**
	 *
	 * @var string 
	 * @FLOW3\Validate(type="NotEmpty")
	 */
	protected $username;
	
	public function __construct() {
		parent::__construct();
		$this->entries = new \Doctrine\Common\Collections\ArrayCollection();		
	}
	
	public function addEntry(\RecordBook\Domain\Model\Entry $entry) {
		$this->entries->add($entry);
	}
	
	public function removeEntry(\RecordBook\Domain\Model\Entry $entry) {
		$this->entries->removeElement($entry);
	}
	
	public function getEntries() {
		return clone $this->entries;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

}
?>