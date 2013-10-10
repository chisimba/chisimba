<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle blog elements
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @author Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package swesos
 * @access public
 */
class gisops extends object
{
	public $objConfig;

	/**
     * Standard init function called by the constructor call of Object
     *
     * @param void
     * @return void
     * @access public
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->loadClass('href', 'htmlelements');
			$tt = $this->newObject('domtt', 'htmlelements');
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	public function uploadDataFile($featurebox = TRUE)
	{
		$this->loadClass('href', 'htmlelements');
		$this->loadClass('label', 'htmlelements');
		$objSelectFile = $this->newObject('selectfile', 'filemanager');
		$this->objUser = $this->getObject('user', 'security');

		$fileform = new form('uploaddatafile', $this->uri(array(
		'action' => 'uploaddatafile'
		)));

		//start a fieldset
		$filefieldset = $this->getObject('fieldset', 'htmlelements');
		$fileadd = $this->newObject('htmltable', 'htmlelements');
		$fileadd->cellpadding = 3;

		//file textfield
		$fileadd->startRow();
		$filelabel = new label($this->objLanguage->languageText('mod_gis_shpfile', 'gis') .':', 'input_file');

		$objSelectFile->name = 'shpzip';
		$objSelectFile->restrictFileList = array('zip');

		$fileadd->addCell($filelabel->show());
		$fileadd->addCell($objSelectFile->show());
		$fileadd->endRow();

		//end off the form and add the buttons
		$this->objIMButton = &new button($this->objLanguage->languageText('word_upload', 'system'));
		$this->objIMButton->setValue($this->objLanguage->languageText('word_upload', 'system'));
		$this->objIMButton->setToSubmit();
		$filefieldset->addContent($fileadd->show());
		$fileform->addToForm($filefieldset->show());
		$fileform->addToForm($this->objIMButton->show());
		$fileform = $fileform->show();

		if ($featurebox == TRUE) {
			$objFeatureBox = $this->getObject('featurebox', 'navigation');
			$ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_gis_uploadfile", "gis") , $fileform);
			return $ret;
		} else {
			return $fileform;
		}

	}




}
?>