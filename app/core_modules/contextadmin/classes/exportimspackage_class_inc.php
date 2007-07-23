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

class exportimspackage extends dbTable
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
		$this->objConf = & $this->newObject('altconfig','config');
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
	function exportContent($contextcode)
	{
		//Retrieve data within context
		$courseData = $this->objIEUtils->getCourse($contextcode, 'new');
		//Course id
		$courseId = $courseData['0']['id'];
		//Course title
		$courseTitle = $courseData['0']['title'];
		//Create a temporary folder
		$tempDirectory = $this->objIEUtils->createTempDirectory($contextcode);
		//Write Schema files
		$writeSchemaFiles = $this->objIEUtils->writeSchemaFiles($tempDirectory);
		//Create resources folder
		$resourceFolder = $tempDirectory."/".$contextcode;
		//Retrieve Course Html
		$courseHtml = $this->objIEUtils->getCourseHtml($contextcode);
		//Retrieve Course Html images
		$courseImageNames = $this->objIEUtils->getImageNames($courseHtml);
//var_dump($courseImageNames);die;
		//Retrieve Course Html file links
		$courseResourceIds = $this->objIEUtils->getResourceIds($courseHtml);
//var_dump($courseResourceIds);die;
		//Re-write Course Html
		$courseHtml = $this->rebuildHtml($contextcode, $courseHtml, $courseImageNames, $courseResourceIds);
		//Write Images to specified directory (resources folder)
		if(count($courseImageNames) > 0)
			$imageNames = $this->objIEUtils->writeImages($courseImageNames, $resourceFolder);
		//Write Resources to specified directory (resources folder)
		if(count($courseResourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($courseResourceIds, $resourceFolder);
		//Write Course Html to specified directory (resources folder)
		$courseFilenames = $this->objIEUtils->writeHtmls($courseHtml, $tempDirectory, $courseTitle);
//var_dump($courseFilenames);die;
//var_dump($courseHtml);die;
		//Retrieve Html pages
		$htmlPages = $this->objIEUtils->getHtmlPages($contextcode);
		//Retrieve Html images
		$imageNames = $this->objIEUtils->getImageNames($htmlPages);
		//Retrieve Html file links
		$resourceIds = $this->objIEUtils->getResourceIds($htmlPages);
		//Re-write Htmls
		$htmlPages = $this->rebuildHtml($contextcode, $htmlPages, $imageNames, $resourceIds);
//var_dump($htmlPages);die;

		//Write Htmls to specified directory (resources folder)
		$htmlFilenames = $this->objIEUtils->writeHtmls($htmlPages, $resourceFolder);
		//Write Images to specified directory (resources folder)
		if(count($imageNames) > 0)
			$imageNames = $this->objIEUtils->writeImages($imageNames, $resourceFolder);
		//Write Resources to specified directory (resources folder)
		if(count($resourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($resourceIds, $resourceFolder);

		return TRUE;
	}

	function rebuildHtml($contextcode, $htmlPages, $courseImageNames, $courseResourceIds)
	{
		if(count($htmlPages) == 1)
		{
			$htmlPages = $this->changeImageSRC($contextcode, $htmlPages, $courseImageNames);
			//$htmlPages = $this->changeLinkUrl($contextcode, $htmlPages, $courseResourceIds);
			$htmlsModified = $htmlPages;
		}
		else
		{
			static $i = 0;
			foreach($htmlPages as $htmlPage)
			{
				$htmlPage = $this->changeImageSRC($contextcode, $htmlPage, $courseImageNames);
				//$htmlPage = $this->changeLinkUrl($htmlPage, $courseResourceIds);
				$htmlsModified[$i] = $htmlPage;
			}
		}

		return $htmlsModified;
	}

	function changeImageSRC($contextcode, $htmlPages, $courseImageNames)
	{
		$newLink = '"'.$contextcode;
		if(count($htmlPages) == 1)
		{
			foreach($courseImageNames as $courseImageName)
			{
				#Convert filename into regular expression
				$regex = '/'.$courseImageName.'/';
				#Find filename in html page if it exists
				preg_match_all($regex, $htmlPages, $matches, PREG_SET_ORDER);
				if($matches)
				{
					$newLink .= '/'.$courseImageName.'"';
					$regReplace = '/(".*'.$courseImageName.'.*?")/i';
					$htmlPages = preg_replace($regReplace, $newLink, $htmlPages);
					$htmlsModified = $htmlPages;
				}
			}
		}
		else
		{
			foreach($htmlPages as $htmlPage)
			{
				static $i = 0;
				foreach($courseImageNames as $courseImageName)
				{
					$regex = '/'.$courseImageName.'/';
					preg_match_all($regex, $htmlPage, $matches, PREG_SET_ORDER);
					if($matches)
					{
						$newLink .= '/'.$courseImageName.'"';
						$regReplace = '/(".*'.$courseImageName.'.*?")/i';
						$htmlPage = preg_replace($regReplace, $newLink, $htmlPage);
						$htmlsModified[$i] = $htmlPage;
					}

				}
			}
		}

		return $htmlsModified;
	}

	function changeLinkUrl($htmlPages, $courseResourceIds)
	{
		if(count($htmlPages) == 1)
		{

		}
		else
		{

		}

		return $htmlPages;
	}
/*	
	function rebuildHtml($htmlPages)
	{
		if(count($htmlPages) == 1)
		{
			#Rewrite images source in html
			$htmlPages = $this->changeImageSRC($htmlPages);
			#Rewrite links source in html
			$htmlPages = $this->changeLinkUrl($htmlPages);
		}
//		else
//		{
			
//		}
		
		return $htmlPages
	}
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