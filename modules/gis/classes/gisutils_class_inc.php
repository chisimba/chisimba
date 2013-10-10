<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle gis utils elements
 * This object can be used elsewhere in the system 
 *
 * @author Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package gis
 * @access public
 */
class gisutils extends object
{
	public $objConfig;
	public $objLanguage;

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
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
 	 * Create zip file and put in files from $files array
 	 * 
 	 * @example 
	 * $filearr = glob('/var/www/sadata/*');
	 * echo packFilesZip('testerBig.zip', $filearr,  true, false);
	 *
	 * 
     */
	public function packFilesZip($zipFN, $files, $removepath=TRUE, $movefiles2zip=TRUE)
	{
		if (!extension_loaded('zip')) {
			throw new customException($this->objLanguage->languageText("mod_gis_nozipext", "gis"));
		}
		$zip = new ZipArchive();
		if ($zip->open($zipFN, ZIPARCHIVE::CREATE)!==TRUE) {
			log_debug("Zip pack Error: cannot open <$zipFN>\n");
			throw new customException($this->objLanguage->languageText("mod_gis_nozipcreate", "gis"));
		}
		foreach ($files as $f) {
			$localFN = $removepath ? basename($f) : $f;
			$zip->addFile($f, $localFN);
		}
		$zip->close();
		return $zipFN;
	}
	
	public function unPackFilesFromZip($zipfile, $dest)
	{
		$zip = new ZipArchive;
		if ($zip->open($zipfile) === TRUE) {
    		$zip->extractTo($dest);
    		$zip->close();
    		return TRUE;
		} else {
    		return FALSE;
		}
	}
}
?>