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

}
?>