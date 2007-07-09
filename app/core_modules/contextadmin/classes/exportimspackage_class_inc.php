<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class exportIMSPackage that manages 
 * the export of IMS specification content
 * @package exportimspackage
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Jarrett Jordaan
 * The process for export IMS specification content is:
 * 
 * 
 */

class exportimspackage extends object 
{
	/**
	 * @var object $objConfig
	*/
	public $objConfig;

	/**
	 * @var object $objIEUtils
	*/
	public $objIEUtils;

	/**
	 * The constructor
	*/
	function init()
	{
		$this->objConfig = & $this->newObject('altconfig','config');
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
		$this->objIMSTools = & $this->newObject('imstools','contextadmin');
	}

	/**
	 * Controls the process for exporting Chisimba package
	 * to conform to IMS specification
	 * Calls all necessary functions an does error checking
	 * 
	 * @param string $contextcode - context code of course
	 * @return TRUE - Successful execution
	*/
	function exportChisimbaContent($contextcode)
	{
//var_dump($contextcode);die;
		$type = "new";
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode, $type);
		//Course id
//var_dump($courseData);die;
		$courseId = $courseData['0']['id'];
//var_dump($courseId);die;
		//Create a temporary folder
		$tempDirectory = $this->objIEUtils->createTempDirectory($contextcode);
		//Write Schema files
		$writeSchemaFiles = $this->objIEUtils->writeSchemaFiles($tempDirectory);
		//Create resources folder
		$resourceFolder = $tempDirectory."/".$contextcode;
		//Write Images to specified directory (resources folder)
		$writeImages = $this->objIEUtils->writeImages($contextcode, $resourceFolder, $type);
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->objIEUtils->writeKNGHtmls($courseData, $contextcode, $resourceFolder, 'new');

		$writeImages = $this->objIEUtils->writeImages($contextcode, $resourceFolder, $type);
//var_dump($writeImages);die;
//var_dump($resourceFolder);die;
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->objIEUtils->writeKNGHtmls($courseData, $contextcode, $resourceFolder, 'new');
//var_dump($writeKNGHtmls);die;


/*
		//Merge filenames
		$filelist = array_merge($writeKNGHtmls, $writeKNGImages);
		//Instantiate imsmanifest.xml creation
		$fp = $this->createIMSManifestFile($tempDirectory);
		//Get the xml data 
		$contents = $this->createIMSManifestContent($courseData, $filelist, $tempDirectory);
		//Write to imsmanifest.xml
		fwrite($fp,$contents);
		//close the file
		fclose($fp);
*/
		return TRUE;
	}

	/**
	 * Controls the process for exporting KNG package
	 * to conform to IMS specification
	 * Calls all necessary functions an does error checking
	 * 
	 * @param $contextcode
	 * @return TRUE - Successful execution
	*/
	function exportKNGContent($contextcode)
	{
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode);
		//Course id
		$courseId = $courseData['0']['id'];
		//Course context code
		$contextcode = $courseData['0']['contextcode'];
		//Create a temporary folder
		$tempDirectory = $this->objIEUtils->createTempDirectory($contextcode);
		//Write Schema files
		$writeSchemaFiles = $this->objIEUtils->writeSchemaFiles($tempDirectory);
		//Create resources folder
		$resourceFolder = $tempDirectory."/".$contextcode;
		//Write Images to specified directory (resources folder)
		$writeKNGImages = $this->objIEUtils->writeImages($contextcode, $resourceFolder);
		//Retrieve html page data within context
		$courseContent = $this->objIEUtils->getCourseContent($courseId);
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->objIEUtils->writeKNGHtmls($courseData, $courseContent, $tempDirectory);
		//Merge filenames
		$filelist = array_merge($writeKNGHtmls, $writeKNGImages);
		//Instantiate imsmanifest.xml creation
		$fp = $this->objIEUtils->createIMSManifestFile($tempDirectory);
		//Get the xml data 
		$contents = $this->createIMSManifestContent($courseData, $filelist, $tempDirectory);
		//Write to imsmanifest.xml
		fwrite($fp,$contents);
		//close the file
		fclose($fp);

/*
		//Create xml file to store subpage information to be used by Chisimba
		$xml = $this->objIEUtils->createKNGXML($courseData);
		//Specify xml file location
		$subpages = $tempDirectory."/subpages.xml";
		//Open file for writing
		$fp = fopen($subpages,'w');
		fwrite($fp, $xml);
		fclose($fp);
*/
		//Zip temporary folder
		//$this->zipAndDownload($contextcode, $tempDirectory);

		return TRUE;
	}



	/**
	 * Retrieve imsmanifest.xml contents
	 *
	 * @param array $courseData
	 * @param array $filelist
	 * @return string $imsmanifest
	 */
	function createIMSManifestContent($courseData, $filelist, $tempDirectory)
	{
		
		//$imsmanifest = $this->imsSkeleton($filelist, $dirlist)->saveXML();
		$imsmanifest = $this->objIMSTools->moodle($courseData, $filelist, $tempDirectory);
		//$imsmanifest = $this->ECIETool($filelist, $dirlist, $fileLoc);

		return $imsmanifest;
	}

	/**
	 * Zip IMS package
	 *
	 * @param string $contextcode
	 * @param string $tempDirectory
	 */
	function zipAndDownload($contextcode, $tempDirectory)
	{
		require_once "File/Archive.php";
		require_once "Cache/Lite.php"; 
		File_Archive::extract($tempDirectory,File_Archive::toArchive($contextcode."-ims.zip",File_Archive::toOutput()));

		return TRUE;
	}

}
?>