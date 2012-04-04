<?php

namespace RecordBook\Domain\Model;

/* *
 * This script belongs to the FLOW3 package "RecordBook".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;
use Doctrine\ORM\Mapping as ORM;

/**
 * A Entry
 *
 * @FLOW3\Scope("prototype")
 * @FLOW3\Entity
 */
class Entry {

	/**
	 * work
	 *
	 * @var string
	 * @FLOW3\Validate(type="Text")
	 * @FLOW3\Validate(type="StringLength", options={ "minimum"=1 })
	 * @ORM\Column(type="text")
	 */
	protected $work;

	/**
	 * date
	 *
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $date;

	/**
	 * duration
	 *
	 * @var float
	 */
	protected $duration;

	/**
	 * holiday
	 *
	 * @var boolean
	 */
	protected $holiday;

	/**
	 * school
	 *
	 * @var boolean
	 */
	protected $school;

	/**
	 * user
	 * 
	 * @ORM\ManyToOne(inversedBy="entries")
	 * @var \RecordBook\Domain\Model\User
	 */
	protected $user;

	/**
	 * Returns the work
	 *
	 * @return string $work
	 */
	public function getWork() {
		return $this->work;
	}

	/**
	 * Sets the work
	 *
	 * @param string $work
	 * @return void
	 */
	public function setWork($work) {
		$this->work = $work;
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

	/**
	 * Returns the duration
	 *
	 * @return int $duration
	 */
	public function getDuration() {
		return $this->duration;
	}

	/**
	 * Sets the duration
	 *
	 * @param int $duration
	 * @return void
	 */
	public function setDuration($duration) {
		$this->duration = $duration;
	}

	/**
	 * Returns the holiday
	 *
	 * @return boolean $holiday
	 */
	public function getHoliday() {
		return $this->holiday;
	}

	/**
	 * Sets the holiday
	 *
	 * @param boolean $holiday
	 * @return void
	 */
	public function setHoliday($holiday) {
		$this->holiday = $holiday;
	}

	/**
	 * Returns the boolean state of holiday
	 *
	 * @return boolean
	 */
	public function isHoliday() {
		return $this->getHoliday();
	}

	/**
	 * Returns the school
	 *
	 * @return boolean $school
	 */
	public function getSchool() {
		return $this->school;
	}

	/**
	 * Sets the school
	 *
	 * @param boolean $school
	 * @return void
	 */
	public function setSchool($school) {
		$this->school = $school;
	}

	/**
	 * Returns the boolean state of school
	 *
	 * @return boolean
	 */
	public function isSchool() {
		return $this->getSchool();
	}

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

}

?>