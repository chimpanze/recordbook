<?php
namespace RecordBook;

use \TYPO3\FLOW3\Package\Package as BasePackage;
use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Package base class of the RecordBook package.
 *
 * @FLOW3\Scope("singleton")
 */
class Package extends BasePackage {

	public function boot(\TYPO3\FLOW3\Core\Bootstrap $bootstrap) {
		require(__DIR__ . '/../Resources/Private/PHP/tcpdf/config/lang/ger.php');
		require(__DIR__ . '/../Resources/Private/PHP/tcpdf/tcpdf.php');
	}
}
?>