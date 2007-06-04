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
	var $objConfig;

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
		$type = "new";
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode, $type);
		//Course id
		$courseId = $courseData['0']['id'];
		//Course context code
		$contextcode = $courseData['0']['contextcode'];
		//Create a temporary folder
		$tempDirectory = $this->createTempDirectory($contextcode);
		//Write Schema files
		$writeSchemaFiles = $this->writeSchemaFiles($tempDirectory);
		//Create resources folder
		$resourceFolder = $tempDirectory."/".$contextcode;
		//Write Images to specified directory (resources folder)
		$writeImages = $this->objIEUtils->writeImages($contextcode, $resourceFolder, $type);

/*
		//Retrieve html page data within context
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->writeKNGHtmls($courseData, $courseContent, $tempDirectory, $resourceFolder);
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
	 * Create a temporary folder
	 * 
	 * @param string $contextcode - context code of course
	 * @return string $tempFolder - location of temporary folder
	*/
	function createTempDirectory($contextcode)
	{
		//Temp folder location
		$tempFolder = "/tmp/".$contextcode;
		$resourceFolder = $tempFolder."/".$contextcode;
		//Check if folder exists and remove it
		$this->objIEUtils->recursive_remove_directory($tempFolder);
		$this->objIEUtils->recursive_remove_directory($resourceFolder);
		//Create directory
		if(!mkdir($tempFolder))
		{
			return "writeError";
		}
		//Create directory
		if(!mkdir($resourceFolder))
		{
			return "writeError";
		}
		//Change directory permissions
		if(!chmod($tempFolder,0777))
		{
			return "permissionError";
		}
		//Change directory permissions
		if(!chmod($resourceFolder,0777))
		{
			return "permissionError";
		}

		return $tempFolder;
	}

	/**
	 * Write Schema files
	 *
	 * @param string $tempDirectory - location of temporary folder
	 * @return TRUE - Successful execution
	*/
	function writeSchemaFiles($tempDirectory)
	{
		//Additional site root path locations
		//Schema filenames
		$fileEdu = "eduCommonsv1.1.xsd";
		$fileImscp = "imscp_v1p2.xsd";
		$fileImsmd = "imsmd_v1p2p4.xsd";
		//Schama files locations
		$schamas = $siterootpath."core_modules/contextadmin/ims/";
		$schamaspath1 = $schamas."eduCommonsv1.1.xsd";
		$schamaspath2 = $schamas."imscp_v1p2.xsd";
		$schamaspath3 = $schamas."imsmd_v1p2p4.xsd";
		//Write files to new directory
		//Write Schema files
		$fp = fopen($tempDirectory."/".$fileEdu,'w');
		if(fwrite($fp, file_get_contents($schamaspath1)) === FALSE)
		{
			return "writeError";
		}
		$fp = fopen($tempDirectory."/".$fileImscp,'w');
		if(fwrite($fp, file_get_contents($schamaspath2)) === FALSE)
		{
			return "writeError";
		}
		$fp = fopen($tempDirectory."/".$fileImsmd,'w');
		if(fwrite($fp, file_get_contents($schamaspath3)) === FALSE)
		{
			return "writeError";
		}
		fclose($fp);
		if(!chmod($tempDirectory,0777))
		{
			return "permissionError";
		}

		return TRUE;
	}

	/**
	 * Read images from Chisimba database
	 *
	 * @param
	 * @return TRUE - Successful execution
	*/
	function readChisimbaImages()
	{
	
		return TRUE;
	}

	/**
	 * Write images to IMS resource folder
	 *
	 * @param
	 * @return TRUE - Successful execution
	*/
	function writeChisimbaImages()
	{
	
		return TRUE;
	}

	/**
	 * Read htmls from Chisimba database
	 *
	 * @param
	 * @return TRUE - Successful execution
	*/
	function readChisimbaHtmls()
	{
	
		return TRUE;
	}

	/**
	 * Write htmls to IMS resource folder
	 *
	 * @param
	 * @return TRUE - Successful execution
	*/
	function writeChisimbaHtmls()
	{
	
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
		$tempDirectory = $this->createTempDirectory($contextcode);
		//Write Schema files
		$writeSchemaFiles = $this->writeSchemaFiles($tempDirectory);
		//Create resources folder
		$resourceFolder = $tempDirectory."/".$contextcode;
		//Write Images to specified directory (resources folder)
		$writeKNGImages = $this->objIEUtils->writeImages($contextcode, $resourceFolder);
		//Retrieve html page data within context
		$courseContent = $this->objIEUtils->getCourseContent($courseId);
		//Write Htmls to specified directory  (resources folder)
		$writeKNGHtmls = $this->writeKNGHtmls($courseData, $courseContent, $tempDirectory, $resourceFolder);
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
	 * Write htmls to IMS resource folder
	 *
	 * @param array $courseData
	 * @param array $courseContent 
	 * @param string $tempDirectory
	 * @param string $resourceFolder
	 * @return TRUE - Successful execution
	*/
	function writeKNGHtmls($courseData, $courseContent, $tempDirectory, $resourceFolder)
	{
		$homepage = $courseData['0']['about'];
		$filepath = $tempDirectory."/".$courseData['0']['contextcode'].".html";
		//Write home page to IMS folder
		//Open file to write
		$fp = fopen($filepath,'w');
		//Write contents of file
		fwrite($fp,$homepage);
		fclose($fp);
		for($i=0;$i<count($courseContent);$i++)
		{
			//Rename resource
			$filepath = $tempDirectory."/".$courseData['0']['contextcode']."/resource".$i.".html";
			//Store html filenames
			$htmlfilenames[$i] = $courseContent[$i]['0']['fullname']; 
			//Retrieve resource contents
			$contentsOfFile = $courseContent[$i]['0']['body'];
			//Open file to write
			$fp = fopen($filepath,'w');
			//Write contents of file
			fwrite($fp,$contentsOfFile);
			fclose($fp);
		}

		return $htmlfilenames;
	}

	/**
	 * Create imsmanifest.xml
	 *
	 * @param string $tempDirectory - path to /tmp directory
	 * @return filehandler $fp - file handler to write contents
	 */
	function createIMSManifestFile($tempDirectory)
	{
		$filePath = $tempDirectory."/imsmanifest.xml";
		//check if the xml file exist
		if(file_exists($filePath))
			//delete the xml file
			unlink($filePath);
       		//create the xml file
		$fp = fopen($filePath,'w');

		return $fp;
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