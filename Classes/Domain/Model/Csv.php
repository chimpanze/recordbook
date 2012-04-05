<?php

namespace RecordBook\Domain\Model;

/* *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\FLOW3\Resource\Resource;

/**
 * A Csv
 *
 * @FLOW3\Scope("prototype")
 * @FLOW3\Entity
 */
class Csv {

	/**
	 * csv
	 * @ORM\ManyToOne(cascade={"persist", "merge"})
	 * @var \TYPO3\FLOW3\Resource\Resource
	 */
	protected $originalResource;

	/**
	 * user
	 * 
	 * @ORM\ManyToOne(inversedBy="csvs")
	 * @var \RecordBook\Domain\Model\User
	 */
	protected $user;

	/**
	 * date
	 *
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $date;

	/**
	 * Returns the user
	 *
	 * @return \RecordBook\Domain\Model\User $user
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Sets the user
	 *
	 * @param \RecordBook\Domain\Model\User $user
	 * @return void
	 */
	public function setUser(\RecordBook\Domain\Model\User $user) {
		$this->user = $user;
	}

	/**
	 * @param \TYPO3\FLOW3\Resource\Resource $originalResource
	 * @return void
	 */
	public function setOriginalResource(\TYPO3\FLOW3\Resource\Resource $originalResource) {
		$this->originalResource = $originalResource;
	}

	/**
	 * @return \TYPO3\FLOW3\Resource\Resource $originalResource
	 */
	public function getOriginalResource() {
		return $this->originalResource;
	}

	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $date
	 * @return void
	 */
	public function setDate($date) {
		$this->date = $date;
	}

}

?>