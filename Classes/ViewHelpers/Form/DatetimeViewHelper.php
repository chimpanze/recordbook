<?php
namespace RecordBook\ViewHelpers\Form;

/*                                                                        *
 * This script belongs to the FLOW3 package "Conference".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Renders a textbox with a jQuery UI Datepicker functionality
 * @see http://docs.jquery.com/UI/Datepicker
 *
 */
class DatetimeViewHelper extends \TYPO3\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'input';

	/**
	 * Initialize the arguments.
	 *
	 * @return void

	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerTagAttribute('size', 'int', 'The size of the input field');
		$this->registerArgument('errorClass', 'string', 'CSS class to set if there are errors for this view helper', FALSE, 'f3-form-error');
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Renders the textbox, hiddenfield and required jQuery UI
	 * @see http://docs.jquery.com/UI/Datepicker
	 *
	 * @param string $previewDateFormat
	 * @param string $transmittedDateFormat
	 * @param string $initialDate
	 * @return string
	 * @api
	 */
	public function render($dateFormat = 'Y-m-d', $initialDate = 'today') {
		$name = $this->getName();
		$this->registerFieldNameForFormTokenGeneration($name);
		
		$this->tag->addAttribute('name', $name);

		$this->tag->addAttribute('type', 'text');
		$date = $this->getValue();
		if (!$date instanceof \DateTime) {
			$date = new \DateTime($initialDate);
		}

		$this->tag->addAttribute('value', $date->format($dateFormat));
		$this->setErrorClassAttribute();

		$content = $this->tag->render();
		return $content;
	}

	/**
	 * Converts the given PHP date format string to the equivalent jquery UI datepicker
	 * format
	 * @see http://www.php.net/manual/datetime.createfromformat.php
	 * @see http://docs.jquery.com/UI/Datepicker/formatDate
	 *
	 * @param string $dateFormat PHP date format string
	 * @return string jQuery datepicker date format string
	 */
	protected function phpDateFormatToDatePickerDateFormat($dateFormat) {
		$map = array(
			'j' => 'd',
			'd' => 'dd',
			'z' => 'o',
			'l' => 'DD',
			'n' => 'm',
			'm' => 'mm',
			'M' => 'M',
			'F' => 'MM',
			'Y' => 'yy',
				// time is not supported (yet):
			'H' => '00',
			'i' => '00',
			's' => '00',
			'u' => '0',
			'P' => '+00:00',
		);
		return strtr($dateFormat, $map);
	}
}

?>