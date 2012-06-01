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
		$entries = $this->entryRepository->findByUserAndAggregateInWeeks($user);
		$name = $user->getName();
		$tcpdf = new \TCPDF();

		// set document information
		$tcpdf->SetCreator('PDF_CREATOR');
		$tcpdf->SetAuthor($name);
		$tcpdf->SetTitle('Berichtsheft');
		$tcpdf->SetSubject('Berichtsheft');
		$tcpdf->SetKeywords('');

		// set default header data
		$tcpdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

		// set header and footer fonts
		$tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$tcpdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$tcpdf->SetAutoPageBreak(TRUE, 0.5);

		//set image scale factor
		$tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$tcpdf->setLanguageArray('de');

		$tcpdf->setPrintHeader(false);
		$tcpdf->setPrintFooter(false);

		// ---------------------------------------------------------

		// set font
		$tcpdf->SetFont('freesans', '', 8);

		foreach($entries as $year => $yearly) {

			foreach($yearly as $month => $monthly) {

				$tcpdf->AddPage();

				// HEADER
				$begin = mktime(0,0,0,$month,1,$year);
				$end = mktime(0,0,0,$month+1,0,$year);

				$htmlcontent = '<div style="text-align:right;">';
				$htmlcontent .= '<h2>Auszubildender: '.$name.'</h2>';
				$htmlcontent .= 'Für den Monat vom '.date('d.m.Y',$begin).' bis '.date('d.m.Y',$end).'<br />';
				$htmlcontent .= '</div>';
				$htmlcontent .= '<table border="1" cellpadding="5">';
				$htmlcontent .= '<tr>';
				$htmlcontent .= '<th width="70%" bgcolor="#a0a0a0"><strong>Arbeit</strong></th>';
				$htmlcontent .= '<th width="30%" bgcolor="#a0a0a0"><strong>Datum</strong></th>';
			// $htmlcontent .= '<th width="15%" bgcolor="#a0a0a0"><strong>Stunden</strong></th>';
				$htmlcontent .= '</tr>';

				$hours_week = 0;

				//Reports
				foreach ( $monthly as $week => $weekly) {
					$firstWeekDay = $weekly['startDate']->getTimestamp();

					$htmlcontent .= '<tr>';
					$htmlcontent .= '<td width="70%" bgcolor="#dbdbdb">Woche '.$week.'</td>';
					$htmlcontent .= '<td width="30%" bgcolor="#dbdbdb">'.$weekly['startDate']->format('d.m.Y').' bis zum '.$weekly['endDate']->format('d.m.Y').'</td>';
					//$htmlcontent .= '<td width="15%" bgcolor="#dbdbdb"></td>';
					$htmlcontent .= '</tr>';


				foreach($weekly['data'] as $report) {
					$htmlcontent .= '<tr>';
					$htmlcontent .= '<td width="100%" colspan="2">'.nl2br($report->getWork()).'</td>';
					//$htmlcontent .= '<td width="15%">'.$report['hours'].'</td>';
					$htmlcontent .= '</tr>';
					$hours_week += $report->getDuration();
				}
				}

				/* $htmlcontent .= '<tr>';
				$htmlcontent .= '<td width="100%" colspan="3"><p align="right">Stunden gesamt: '.$hours_week.'</p></td>';
				$htmlcontent .= '</tr>';*/
				$htmlcontent .= '</table>';


				// FOOTER
				$htmlcontent .= '<h3>Unterschriften</h3>
				<table border="0" cellpadding="2" cellspacing="3">
				<tr>
					<td width="49%"><br /><br /><br /><br /><hr noshade size="1" /></td>
					<td width="49%"><br /><br /><br /><br /><hr noshade size="1" /></td>
				</tr>
				<tr>
					<td width="49%"><div align="center" style="font-size:25px;">Auszubildender</div></td>
					<td width="49%"><div align="center" style="font-size:25px;">Ausbilder</div></td>
				</tr>
				</table>';

				$tcpdf->writeHTML($htmlcontent, true, 0, true, 0);
			}
		}


//		$this->controllerContext->getResponse()->setHeader('Content-Type', 'application/pdfl');
//		$this->controllerContext->getResponse()->setHeader('Content-Disposition', 'attachment; filename=recordbook.pdf');
		return $tcpdf->Output('complete.pdf', 'D');
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