<?php
# security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
//  end security check
/**
 * The class importIMSPackage that manages 
 * the import of IMS specification content into Chisimba
 * @package importimspackage
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Jarrett Jordaan
 * The process for import IMS specification content is:
 * 
 * 
 */

class importIMSPackage extends dbTable
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
	 * @var object $objDBContext
	*/
	public $objDBContext;
	/**
	 * @var object $objDebug - debugging flag to display information
	*/
	public $objDebug;
	/**
	 * @var object $objError - error flag to return errors
	*/
	public $objError;
	/**
	 * @var object $fileMod - file modification flag to modify file types
	*/
	public $fileMod;
	/**
	 * @var object $pageIds - holds the page id's corresponding to a file name
	*/
	public $pageIds;
	public $courseId;
	public $contentBasePath;
	public $courseContentBasePath;
	public $contextCode;
	public $courseTitle;
	public $courseContentPath;
	public $imagesLocation;
	public $docsLocation;
	public $filesLocation;
	public $folder;//relative path of temporary folder
	public $fileLocations;//relative paths to all files
	public $imsmanifestLocation;//relative paths to IMS manifest file
	public $simpleXmlObj;//simplexml object to access xml file
	public $domDocumentObj;//dom document object to access xml file
	public $xpathObj;//xpath object to access xml file
	public $newCourse;//all course data needed to create course
	public $resourceFileNames;//the filenames of resources, including type
	public $alldata;//all data needed to add file to database
	public $imageIds;
	public $fileIds;
	public $resourceIds;
	public $chapterIds;
	public $chapterId;

	/**
	 * The constructor
	*/
	function init()
	{
		// Load Filemanager class.
		$this->objIndex = $this->getObject('indexfileprocessor', 'filemanager');
		// Load System classes.
		$this->objConf = $this->newObject('altconfig','config');
        	$this->objDBContext = $this->newObject('dbcontext', 'context');
        	$this->objUser = $this->getObject('user', 'security');
		// Load Inner classes.
		$this->objIEUtils = $this->newObject('importexportutils','contextadmin');
		// Load Chapter Classes
		$this->objChapters = $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters = $this->getObject('db_contextcontent_contextchapter','contextcontent');
		// Load context classes
        	$this->objContentPages = $this->getObject('db_contextcontent_pages','contextcontent');
	        $this->objContentOrder = $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles = $this->getObject('db_contextcontent_titles','contextcontent');
		// Initialize Flags
		$this->fileMod = FALSE;
		$this->objDebug = FALSE;
		$this->objError = TRUE;
	}
	
	/**
	 * Controls the process for import IMS specification content,
	 * either as eduCommons or MIT packages
	 * 
	 * @param $_FILES global - uploaded file
	 * @param string $package
	 * @param string $choice - the selected course (when importing into existing course)
	 * @param boolean $createdCourse - whether or not to create a course
	 * 
	 * @return none
	 * 
	*/
	function importIMScontent($FILES, $package, $choice = '', $createCourse = '')
	{
		$this->fileMod = FALSE;
		$this->objIEUtils->fileModOff();
		$this->courseId = '';
		if($package == 'default')
			return $this->defaultPackage($FILES, $choice, $createCourse);
		else if($package == 'mit')
			return $this->mitPackage($FILES, $choice, $createCourse);
		else if($package == 'exe')
			return $this->exePackage($FILES, $choice, $createCourse);
		else
			return 'unknownPackage';
	}

	/**
	 * Controls the process for import IMS specification content,
	 * specifically of eXe packages
	 * 
	 * @param $_FILES global - uploaded file
	 * @param string $choice - the selected course (when importing into existing course)
	 * @param boolean $createdCourse - whether or not to create a course
	 * 
	 * @return TRUE - Successful execution
	 * 
	*/
	function exePackage($FILES, $choice = '', $createCourse = '')
	{
		// Check archive type
		if(!isset($FILES) || $FILES['upload']['type'] != 'application/zip')
			if($this->objError)
				return  "zipFileError";
		// Check if upload needs to forced
		if($createCourse == TRUE)
			$folder = $this->unzipIMSFile($FILES);
		else
			$folder = $this->unzipIMSFile($FILES, TRUE);
		if(!isset($folder) || $folder == 'unzipError')
			if($this->objError)
				return  "unzipError";
		// Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
		if(!isset($fileNames))
			if($this->objError)
				return  "fileReadError";
		// Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
			if($this->objError)
				return  "fileReadError";
		// Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
			if($this->objError)
				return  "imsReadError";
		// Read imsmanifest.xml file
		// Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj) || $simpleXmlObj == 'simpleXmlError')
			if($this->objError)
				return  "simpleXmlError";
		// Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj) || $domDocumentObj == 'domReadError')
			if($this->objError)
				return  "domReadError";
		// Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
			if($this->objError)
				return  "xpathSetError";
		// Retrieve all resource files in package
		$allPageLocation = $this->locateAllMITFiles($simpleXmlObj);
		if(!isset($allPageLocation))
			if($this->objError)
				return  "dataRetrievalError";
		// Retrieve all relative file paths
		$allLocations = $this->objIEUtils->list_dir_files($folder, 1);
		if(!isset($allLocations))
			if($this->objError)
				return  "dataRetrievalError";
		// Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
		if(!isset($fileNames))
			if($this->objError)
				return  "dataRetrievalError";
		// Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj,'exe');
		if(!isset($courseData) || $courseData == 'courseReadError')
			if($this->objError)
				return  "courseReadError";
		// Change context
		if($createCourse == FALSE)
			$courseData['contextcode'] = $choice;
		// Initialize all locations
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		if(!isset($init))
			if($this->objError)
				return  "initializeError";
		// Check to see if course needs to be created
		if($createCourse == TRUE)
		{
			$courseCreated = $this->objIEUtils->createCourseInChisimba($courseData);
			$this->courseId = $courseCreated;
			if(!isset($courseCreated) || $courseCreated == 'courseWriteError')
				if($this->objError)
					return  "courseWriteError";
		}
		else
		{
			$this->contextCode = $choice;
		}
		//Retrieve Html pages
		$htmlPages = $this->objIEUtils->getHtmlPages('', $allPageLocation, $folder, $this->docsLocation);
		if(!isset($htmlPages))
			if($this->objError)
				return  "dataRetrievalError";
		// Retrieve organizations
		$structure = $this->getStructure($simpleXmlObj);
		if(!isset($structure))
			if($this->objError)
				return  "noStructureError";
		// Retrieve menu titles for content within html pages
		$menuTitles = $this->getMenuTitles($htmlPages);
		if(!isset($menuTitles))
			if($this->objError)
				return  "dataRetrievalError";
		// Write Html data into Chisimba usrfiles directory
		$htmlFilenames = $this->objIEUtils->writeFiles($htmlPages, $this->docsLocation, '', 'html');
		if(!isset($htmlFilenames))
			if($this->objError)
				return  "writeResourcesError";
		// Write Image data into Chisimba usrfiles directory
		$writeImages = $this->writeMITImages('', $allLocations, $fileNames);
		if(!isset($writeImages))
			if($this->objError)
				return  "writeResourcesError";
		//Write Files to Chisimba usrfiles directory
		$writeFiles = $this->writeMITFiles('', $allLocations, $fileNames);
		if(!isset($writeFiles))
			if($this->objError)
				return  "writeResourcesError";
		// Load html data into Chisimba
		$pageIds = $this->loadToChisimbaFromContent($htmlPages, $allPageLocation, $structure, $menuTitles);
		if(!isset($pageIds))
			if($this->objError)
				return  "uploadError";
		// Load image data into Chisimba
		$uploadToChisimba = $this->uploadToChisimba();
		if(!isset($uploadToChisimba))
			if($this->objError)
				return  "uploadError";
		// Load file data into Chisimba
		$uploadFilesToChisimba = $this->uploadToChisimba($this->filesLocation);
		if(!isset($uploadFilesToChisimba))
			if($this->objError)
				return  "uploadError";
		$rebuildHtml = $this->rebuildMITHtml($menuTitles, $fileNames, $allPageLocation);
		if(!isset($rebuildHtml))
			if($this->objError)
				return  "rebuildHtmlError";
		// Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);

		return TRUE;
	}

	/**
	 * Controls the process for import IMS specification content,
	 * specifically of MIT packages
	 * 
	 * @param $_FILES global - uploaded file
	 * @param string $choice - the selected course (when importing into existing course)
	 * @param boolean $createdCourse - whether or not to create a course
	 * 
	 * @return TRUE - Successful execution
	 * 
	*/
	function mitPackage($FILES, $choice = '', $createCourse = '')
	{
		// Check archive type
		if(!isset($FILES) || $FILES['upload']['type'] != 'application/zip')
			if($this->objError)
				return  "zipFileError";
		// Check if upload needs to forced
		if($createCourse == TRUE)
			$folder = $this->unzipIMSFile($FILES);
		else
			$folder = $this->unzipIMSFile($FILES, TRUE);
		if(!isset($folder) || $folder == 'unzipError')
			if($this->objError)
				return  "unzipError";
		// Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
		if(!isset($fileNames))
			if($this->objError)
				return  "fileReadError";
		// Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
			if($this->objError)
				return  "fileReadError";
		// Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
			if($this->objError)
				return  "imsReadError";
		// Read imsmanifest.xml file
		// Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj) || $simpleXmlObj == 'simpleXmlError')
			if($this->objError)
				return  "simpleXmlError";
		// Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj) || $domDocumentObj == 'domReadError')
			if($this->objError)
				return  "domReadError";
		// Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
			if($this->objError)
				return  "xpathSetError";
		// Retrieve all resource files in package
		$allFilesLocation = $this->locateAllMITFiles($simpleXmlObj);
		// Retrieve all .xml files
		$xmlFilesLocation = $this->locateAllXmlFiles($xpathObj);
		// Retrieve all files
		$allLocations = $this->objIEUtils->list_dir_files($folder, 1);
		// Load all .xml files
		$newFolder = $folder.'/'.preg_replace("/.zip/","",$FILES['upload']['name']);
		$allXmlPackageData = $this->loadAllXmlFiles($newFolder, $xmlFilesLocation);
		// Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj,'mit');
		if(!isset($courseData) || $courseData == 'courseReadError')
			if($this->objError)
				return  "courseReadError";
		// Change context
		if($createCourse == FALSE)
			$courseData['contextcode'] = $choice;
		// Initialize all locations
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		if(!isset($init))
			if($this->objError)
				return  "initializeError";
		// Check to see if course needs to be created
		if($createCourse == TRUE)
		{
			$courseCreated = $this->objIEUtils->createCourseInChisimba($courseData);
			$this->courseId = $courseCreated;
			if(!isset($courseCreated) || $courseCreated == 'courseWriteError')
				if($this->objError)
					return  "courseWriteError";
		}
		else
			$this->contextCode = $choice;
		// Write Resources
		// Retrieve Html locations and move to usrfiles
		$mitHtmlsPath = $this->getMITHtmls($newFolder, $allXmlPackageData);
		//Write Images to Chisimba usrfiles directory
		$writeImages = $this->writeMITImages('', $allLocations, $fileNames);
		//Write Files to Chisimba usrfiles directory
		$writeFiles = $this->writeMITFiles('', $allLocations, $fileNames);
		// Load html data into Chisimba
		$loadData = $this->loadToChisimba($mitHtmlsPath, $allXmlPackageData, 'Y');
		if(!isset($loadData))
			if($this->objError)
				return  "loadDataError";
		// Load image data into Chisimba
		$uploadToChisimba = $this->uploadToChisimba();
		if(!isset($uploadToChisimba))
			if($this->objError)
				return  "uploadError";
		// Load file data into Chisimba
		$uploadFilesToChisimba = $this->uploadToChisimba($this->filesLocation);
		if(!isset($uploadFilesToChisimba))
			if($this->objError)
				return  "uploadError";
		// Rebuild html images and url links
		$allFileNames = $this->objIEUtils->list_dir_files($folder,0);
		$rebuildHtml = $this->rebuildMITHtml($loadData, $allFileNames, $allFilesLocation);
		if(!isset($rebuildHtml))
			if($this->objError)
				return  "rebuildHtmlError";
		// Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);

		return TRUE;
	}

	/**
	 * Controls the process for import IMS specification content,
	 * specifically eduCommons packages
	 * 
	 * @param $_FILES global - uploaded file
	 * @param string $choice - the selected course (when importing into existing course)
	 * @param boolean $createdCourse - whether or not to create a course
	 * 
	 * @return TRUE - Successful execution
	 * 
	*/
	function defaultPackage($FILES, $choice = '', $createCourse = '')
	{
		// Check archive type
		if(!isset($FILES) || $FILES['upload']['type'] != 'application/zip')
			if($this->objError)
				return  "zipFileError";
		// Check if upload needs to forced
		if($createCourse == TRUE)
			// Retrieve temp folder
			$folder = $this->unzipIMSFile($FILES);
		else
			$folder = $this->unzipIMSFile($FILES, TRUE);
		if(!isset($folder) || $folder == 'unzipError')
			if($this->objError)
				return  "unzipError";
		// Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
		if(!isset($fileNames))
			if($this->objError)
				return  "fileReadError";
		// Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
			if($this->objError)
				return  "fileReadError";
		// Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
			if($this->objError)
				return  "imsReadError";
		// Read imsmanifest.xml file
		// Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj) || $simpleXmlObj == 'simpleXmlError')
			if($this->objError)
				return  "simpleXmlError";
		// Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj) || $domDocumentObj == 'domReadError')
			if($this->objError)
				return  "domReadError";
		// Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
			if($this->objError)
				return  "xpathSetError";
		// Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj);
		if(!isset($courseData) || $courseData == 'courseReadError')
			if($this->objError)
				return  "courseReadError";
		// Change context
		if($createCourse == FALSE)
			$courseData['contextcode'] = $choice;
		// Initialize all locations
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		if(!isset($init))
			if($this->objError)
				return  "initializeError";
		// Create course
		if($createCourse == TRUE)
		{
			$courseCreated = $this->objIEUtils->createCourseInChisimba($courseData);
			$this->courseId = $courseCreated;
			if(!isset($courseCreated) || $courseCreated == 'courseWriteError')
				if($this->objError)
					return  "courseWriteError";
		}
		else
			$this->contextCode = $choice;
		// Write Resources
		$writeData = $this->writeResources($simpleXmlObj, $folder, $courseData);
		if(!isset($writeData))
			if($this->objError)
				return  "writeResourcesError";
		// Get organizations
		$structure = $this->getStructure($simpleXmlObj);
		if(!isset($structure))
			if($this->objError)
				return  "noStructureError";
		// Load html data into Chisimba
		$loadData = $this->loadToChisimba($writeData, $structure);
		if(!isset($loadData))
			if($this->objError)
				return  "loadDataError";
		// Load image data into Chisimba
		$uploadToChisimba = $this->uploadToChisimba();
		if(!isset($uploadToChisimba))
			if($this->objError)
				return  "uploadError";
		// Rebuild html images and url links
		$rebuildHtml = $this->rebuildHtml($loadData,$fileNames);
		if(!isset($rebuildHtml))
			if($this->objError)
				return  "rebuildHtmlError";
		// Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);

		return TRUE;
	}

	/**
	 * Sets the global variables and directory paths
	 * 
	 * @param string $contextcode 
	 * @param string $courseTitle
	 * 
	 * @return array $locations - Array of all locations used
	 * 
	*/
	function initLocations($contextcode, $courseTitle)
	{
		// Static Chisimba file locations
		// opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$this->contentBasePath = $this->objConf->getcontentBasePath();
		$this->courseContentBasePath = $this->contentBasePath."content/";
		$this->contextCode = trim(strtolower(str_replace(' ','_',$contextcode)));
		$this->courseTitle = trim(str_replace(' ','_',$courseTitle));
		$this->courseContentPath = $this->courseContentBasePath.$this->contextCode;
		$this->imagesLocation = $this->courseContentPath."/images";
		$this->docsLocation = $this->courseContentPath."/staticcontent";
		$this->filesLocation = $this->courseContentPath."/documents";
		$locations = array('contentBasePath' => $this->contentBasePath,
					'courseContentBasePath' => $this->courseContentBasePath,
					'contextCode' => $this->contextCode,
					'courseContentPath' => $this->courseContentPath,
					'imagesLocation' => $this->imagesLocation,
					'docsLocation' => $this->docsLocation
					);
		if($this->objDebug)
		{
			echo $this->contentBasePath."<br />";
			echo $this->courseContentBasePath."<br />";
			echo $this->contextCode."<br />";
			echo $this->courseContentPath."<br />";
			echo $this->imagesLocation."<br />";
			echo $this->docsLocation."<br />";
		}

		return $locations;
	}

	/**
	 * Function to unzip an uploaded zip file
	 * 
	 * @param $_FILES global - Location of uploaded zip-file
	 * @param boolean $forceUpload - whether or not to ignore upload errors
	 *
	 * @return string $folder - Temp extraction folder location
	 * 
	*/
	function unzipIMSFile($FILES, $forceUpload = '')
	{
		if(!is_uploaded_file($FILES['upload']['tmp_name']) && $forceUpload == FALSE)
			return "unzipError";
		else if ($FILES['upload']['error'] != UPLOAD_ERR_OK)
			return "unzipError";
		else
		{
			$type = $FILES['upload']['type'];
			$name = $FILES['upload']['name'];
			$name = preg_replace('/^(.*)\.php$/i', '\\1.phps', $name);
			for ($i=0;$i<strlen($name);$i++) 
			{
				if ($name{$i} == ' ') 
					$name{$i} = '_';
			}
			$extension = "";
			$len = strlen($name);
			$i = $len-1;
			while($i >= 0 && $name[$i]!='.')
			{
				$extension = $name[$i].$extension;
				$i--;
			}
			$j = 0;
			$newname = "";
			while($len > $j && $name[$j]!='.')
			{
				$newname .= $name[$j];
				$j++;
			}
			$name = $newname;
			if ($extension == 'zip')
			{
				// !!!Need to find a way to test if directory is created
				$tempfile=$FILES['upload']['tmp_name'];
				$tempdir=substr($tempfile,0,strrpos($tempfile,'/'));
				$tempdir .= '/'.$name.'_'.$this->objIEUtils->generateUniqueId();
				$this->folder=$tempdir;
				$objWZip=&$this->getObject('wzip','utilities');
				$objWZip->unzip($tempfile,$this->folder);
			}
		}

	return $this->folder;
	}

	/**
	 * Function to retrieve all file Locations within a specified folder
	 *
	 * @param $folder - Location of folder to scan
	 *
	 * @return array $fileLocations - Locations of all files within folder
	 * 
	*/
	function locateAllFiles($folder)
	{
		$this->fileLocations = $this->objIEUtils->list_dir_files($folder,1);
	
		return $this->fileLocations;
	}

	/**
	 * Scans a specified array of strings for a specified string
	 *
	 * @param array $files - list of file locations
	 * @param string $regex - filename to scan in regular expression form
	 *
	 * @return string $imsmanifestLocation - path to ims file in package
	 *
	*/
	function locateIMSFile($files, $regex)
	{
		foreach($files as $aFile)
			if(preg_match($regex, $aFile))
				$this->imsmanifestLocation = $aFile;

		return $this->imsmanifestLocation;
	}

	/**
	 * Creates a simpleXml object
	 *
	 * @param string $imsFileLocation - Path to imsmanifest.xml file
	 *
	 * @return simpleXml $simpleXmlObj - simplexml access to imsmanifest
	 *
	*/
	function loadSimpleXml($imsFileLocation)
	{
		// Load imsmanifest.xml file
		if(file_exists($imsFileLocation)) 
    			$this->simpleXmlObj = simplexml_load_file($imsFileLocation);
		else 
    			return  "simpleXmlError";
		
		return $this->simpleXmlObj;
	}

	/**
	 * Creates a DOMDocument object
	 *
	 * @param string $imsFileLocation - Path to imsmanifest.xml file
	 *
	 * @return DOMDocument $domDocumentObj
	 *
	*/
	function loadDOMDocument($imsFileLocation)
	{
		$this->domDocumentObj = new DOMDocument();
		// Create domdocument object to access xml file
		if($this->domDocumentObj->load($imsFileLocation))
		{
		}
		else 
    			return  "domReadError";

		return $this->domDocumentObj;
	}

	/**
	 * Creates a DOMXPath object
	 *
	 * @param DOMDocument $domDocumentObj
	 *
	 * @return DOMXPath $domDocumentObj
	 *
	*/
	function loadXPath($domDocumentObj)
	{
		// Create xpath object to access xml file
		$this->xpathObj = new DOMXPath($domDocumentObj);

		return $this->xpathObj;
	}

	/**
	 * Extract the course information from imsmanifest.xml
	 *
	 * @param simpleXml $xml - simplexml access to imsmanifest
	 * @param DOMDocument $doc - DOMDocument access to imsmanifest
	 * @param DOMXPath $xpath - DOMXPath access to imsmanifest
	 * @param $packageType - type of package being imported
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function extractCourseData($xml, $doc, $xpath, $packageType = '')
	{
		// Set eduCommons namespaces
		$xpath->registerNamespace("educommons", "http://albatross.ed.usu.edu/xsd/educommons_v1");
		// Set imsmd namespaces
		$xpath->registerNamespace("imsmd", "http://www.imsglobal.org/xsd/imsmd_v1p2");
		$xpath->registerNamespace('adlcp','http://www.adlnet.org/xsd/adlcp_rootv1p2');
		$xpath->registerNamespace('cwspace','http://www.dspace.org/xmlns/cwspace_imscp'); 
		$xpath->registerNamespace('ocw','http://ocw.mit.edu/xmlns/ocw_imscp');
		// Create course
		// Establish which resource is a course
		if($packageType == 'mit')
		{
			$query = '//lom:identifier/lom:entry';// course id (contextcode)
			$results = $xpath->evaluate($query);
			$courseId = trim((string)$results->item($i)->nodeValue);
			$courseId .= '_'.$this->objIEUtils->generateUniqueId('8');
			$this->newCourse['contextcode'] = $courseId;
			$query = '//lom:title';// course title (title)
			$results = $xpath->evaluate($query);
			$courseTitle = trim((string)$results->item(0)->nodeValue);
			$this->newCourse['title'] = $courseTitle;
			$this->newCourse['menutext'] = $courseTitle;// course title (menu text)
			$this->newCourse['userid'] = $this->objUser->userId();// course title (userId)
			$this->newCourse['courseIdentifier'] = '';// course identifier (not in use)
			$this->newCourse['about'] = '';// course description (about)
			$courseStatus = "Public";// course status (status)
			$this->newCourse['isactive'] = $courseStatus;
			$courseAccess = "UnPublished";// course access (access)
			$this->newCourse['isclosed'] = $courseAccess;
		}
		else if($packageType == 'exe')
		{
			$courseId = trim($xml->organizations->organization->title);
			$courseId .= '_'.$this->objIEUtils->generateUniqueId('8');
			$this->newCourse['contextcode'] = $courseId;
			$courseTitle = $courseId;//'None';
			$this->newCourse['title'] = $courseTitle;
			$menutext = $courseId;//'None';
			$this->newCourse['menutext'] = $menutext;// course title (menu text)
			$this->newCourse['userid'] = $this->objUser->userId();// course title (userId)
			$this->newCourse['courseIdentifier'] = '';// course identifier (not in use)
			$this->newCourse['about'] = '';// course description (about)
			$courseStatus = "Public";// course status (status)
			$this->newCourse['isactive'] = $courseStatus;
			$courseAccess = "UnPublished";// course access (access)
			$this->newCourse['isclosed'] = $courseAccess;
		}
		else
		{
			foreach($xml->resources->resource as $resource)
			{
				// Retrieve file type
				$objectType = $resource->metadata->eduCommons->objectType;
				// Cast to string
				$objectType = (string)$objectType;
				// Remove whitespaces for comparison
				$objectType = trim($objectType);
				// Check file type
				// Course
				if(strcmp($objectType,"Course")==0)
				{
					$courseId = $resource->metadata->eduCommons->courseId;
					$courseId = (string)$courseId;
					$courseId = trim($courseId);
					if(!(strlen($courseId) > 1))
						return 'courseReadError';
					$this->newCourse['contextcode'] = $courseId.'_'.$this->objIEUtils->generateUniqueId('8');
					$courseTitle = $resource->metadata->lom->general->title->langstring;
					$courseTitle = (string)$courseTitle;
					$courseTitle = trim($courseTitle);
					$this->newCourse['title'] = $courseTitle;
					if(!(strlen($courseTitle) > 1))
						return 'courseReadError';
					$this->newCourse['menutext'] = $courseTitle;
					$this->newCourse['userid'] = $this->objUser->userId();
					$courseIdentifier = $resource['identifier'];
					$courseIdentifier = (string)$courseIdentifier;
					$courseIdentifier = trim($courseIdentifier);
					$this->newCourse['courseIdentifier'] = $courseIdentifier;
					if(!(strlen($courseIdentifier) > 1))
						return 'courseReadError';
					$courseDescription = $resource->metadata->lom->general->description->langstring;
					$courseDescription = (string)$courseDescription;
					$courseDescription = trim($courseDescription);
					$this->newCourse['about'] = $courseDescription;
					if(!(strlen($courseDescription) > 1))
						$this->newCourse['about'] = "Description Not Available";
					$courseStatus = "Public";
					$this->newCourse['isactive'] = $courseStatus;
					$courseAccess = "UnPublished";
					$this->newCourse['isclosed'] = $courseAccess;
				}
			}
		}

		return $this->newCourse;
	}

	/**
	 * Move the resources to appropriate locations on the file system
	 * and to the proper databases
	 *
	 * @param simpleXml $xml - simplexml access to imsmanifest
	 * @param string $folder - content folder in usrfiles directory
	 * @param array $newCourse - all created course data
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function writeResources($xml, $folder, $newCourse)
	{
		// Pre-initialize variables.
		$resourceFileLocations = array();
		// First add course to Chisimba database.
		foreach($xml->resources->resource as $resource)
		{
			// Retrieve file type.
			$objectType = $resource->metadata->eduCommons->objectType;
			// Cast to string.
			$objectType = (string)$objectType;
			// Remove whitespaces for comparison.
			$objectType = trim($objectType);
			// Check file type.
			// Course.
			if(strcmp($objectType,"Course")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				// Retrieve relative file location.
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				// Chech if file exists on local system.
				if(!file_exists($fileLocation))
				{
					$folders = $this->objIEUtils->list_dir($folder, '0', '0');
					foreach($folders as $aFolder)
					{
						$testerLocation = $folder."/".$aFolder."/".$file;
						if(file_exists($testerLocation))
							$fileLocation = $testerLocation;
					}
				}
				// Retrieve contents of file.
				$fileContents = file_get_contents($fileLocation);
				// Save all data.
				$this->allData['0'] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
			}
		}
		// Add all other resources to Chisimba database.
		foreach($xml->resources->resource as $resource)
		{
			static $i = 1;
			// Retrieve file type.
			$objectType = $resource->metadata->eduCommons->objectType;
			// Cast to string.
			$objectType = (string)$objectType;
			// Remove whitespaces for comparison.
			$objectType = trim($objectType);
			// Check file type.
			// Document.
			if(strcmp($objectType,"Document")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				// Retrieve relative file location.
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				// Chech if file exists on local system.
				if(!file_exists($fileLocation))
				{
					$folders = $this->objIEUtils->list_dir($folder, '0', '0');
					foreach($folders as $aFolder)
					{
						$testerLocation = $folder."/".$aFolder."/".$file;
						if(file_exists($testerLocation))
							$fileLocation = $testerLocation;
					}
				}
				// Write file contents to documents folder.
				// Retrieve contents of file.
				$fileContents = file_get_contents($fileLocation);
				// Check filename.
				if(!preg_match("/.html|.htm/",$filename))
				{
					// Correct filename.
					$filename = $filename.".html";
					$this->fileMod = TRUE;
					$this->objIEUtils->fileModOn();
				}
				// New location for Documents.
				$newLocation = $this->docsLocation."/".$filename;
				// Store resource locations.
				$resourceFileLocations[$i] = $newLocation;
				// Store filesname.
				$this->resourceFileNames[$i] = $filename;
				// Open images directory.
				$fp = fopen($newLocation,'w');
				// Write the file to images directory.
				if((fwrite($fp, $fileContents) === FALSE))
					return  "writeResourcesError";
				// Close the directory.
				fclose($fp);
				// Save all data.
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents, 
							'contextCode' => $this->contextCode, 
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
			}
			// File.
			if(strcmp($objectType,"File")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				// Retrieve relative file location.
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				// Chech if file exists on local system.
				if(!file_exists($fileLocation))
				{
					$folders = $this->objIEUtils->list_dir($folder, '0', '0');
					foreach($folders as $aFolder)
					{
						$testerLocation = $folder."/".$aFolder."/".$file;

						if(file_exists($testerLocation))
						{
							$fileLocation = $testerLocation;
						}
					}
				}
				// Write file contents to documents folder.
				// Retrieve contents of file.
				$fileContents = file_get_contents($fileLocation);
				// New location for Files.
				$newLocation = $this->docsLocation."/".$filename;
				// Store resource locations.
				$resourceFileLocations[$i] = $newLocation;
				// Store filesname.
				$this->resourceFileNames[$i] = $filename;
				// Open images directory.
				$fp = fopen($newLocation,'w');
				// Write the file to images directory.
				if((fwrite($fp, $fileContents) === FALSE))
					return  "writeResourcesError";
				// Close the directory.
				fclose($fp);
				// Save all data.
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
			}
			// Image.
			if(strcmp($objectType,"Image")==0)
			{
				// Retrieve filename.
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				// Retrieve relative file location.
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				// Retrieve absolute file location.
				$fileLocation = $folder."/".$file;
				// Chech if file exists on local system.
				if(!file_exists($fileLocation))
				{
					$folders = $this->objIEUtils->list_dir($folder, '0', '0');
					foreach($folders as $aFolder)
					{
						$testerLocation = $folder."/".$aFolder."/".$file;
						if(file_exists($testerLocation))
							$fileLocation = $testerLocation;
					}
				}
				// Write file contents to images folder.
				// Retrieve contents of file.
				$fileContents = file_get_contents($fileLocation);
				// Check filename
				if(!preg_match("/.jpg|.gif|.png/",$filename))
				{
					// Correct filename
					$filename = $filename.".jpg";
					$this->fileMod = TRUE;
					$this->objIEUtils->fileModOn();
				}
				// New location for Images
				$newLocation = $this->imagesLocation."/".$filename;
				// Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				// Store filesname
				$this->resourceFileNames[$i] = $filename;
				// Open images directory
				$fp = fopen($newLocation,'w');
				// Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
					return  "writeResourcesError";
				// Close the directory
				fclose($fp);
				// Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
			}
		$i++;
		}

		return $this->allData;
	}

	/**
	 * Writes all images used by course to new database (Chisimba) by default
	 * Can also write a specified directory of unknown files into database
	 * 
	 * Makes query to tbl_files
	 * 
	 * @param string $folder - selected course
	 * @param array $fileNames - all file names
	 * 
	 * @return array $indexFolder - list of id fields belonging to images
	 * 
	*/
	function uploadToChisimba($folder = '', $fileNames = '')
	{
		$noFilesWritten = 0;
		// Initialize Inner variables
		parent::init('tbl_files');
		// Add Images to database
		if(!(strlen($folder) > 1))
			$indexFolder = $this->objIndex->indexFolder($this->imagesLocation, $this->objUser->userId());
		else
			$indexFolder = $this->objIndex->indexFolder($folder, $this->objUser->userId());
		// Match image Id's to image names
		foreach($indexFolder as $pageId)
		{
			$filter = "WHERE id = '$pageId'";
			$result = $this->getAll($filter);
			$aFile = $result['0']['filename'];
			if(!(strlen($folder) > 1))
				$this->imageIds[$aFile] = $pageId;
			else
				$this->fileIds[$aFile] = $pageId;
			$noFilesWritten = 1;
		}
		if(!(strlen($folder) > 1))
		{
			if($noFilesWritten == 1)
				return $this->imageIds;
			else
				return TRUE;
		}
		else
		{
			if($noFilesWritten == 1)
				return $this->fileIds;
			else
				return TRUE;
		}
	}

	/**
	 * Function to return the order in which pages should be added
	 * 
	 * @param simpleXML $xml - simplexml access to imsmanifest
	 *
	 * @return array $titles 
	 * 
	*/
	function getStructure($xml)
	{
		$titles = array();
		static $i = 0;
		foreach($xml->organizations->organization->item as $item)
		{
			$aTitle = (string)$item->title;
			$aTitle = trim($aTitle);
			$titles[$i] = $aTitle;
			$i++;
			foreach($item as $sublevel)
			{
				$aTitle = $this->subLevelItem($sublevel);
				if($aTitle)
				{
					$titles[$i] = $aTitle;
					$i++;
				}
			}
		}

		return $titles;
	}

	/**
	 * Control loading resources into Chisimba
	 * and file manipulation functions
	 *
	 * @param array $writeData - all data needed
	 * @param array $structure - the order in which pages should be added
	 * @param array $filePaths - relative paths of all files
	 *
	 * @return array $menutitles - all menutitles of pages
	 *
	*/
	function loadToChisimba($writeData, $structure, $filePaths = '')
	{
		if($filePaths == 'Y')
			return $this->loadToChisimbaFromPaths($writeData, $structure);
		else
		{
			// Pre-initialize variables
			static $i = 0;
			static $j = 0;
			static $k = 0;
			$menutitles = array();
			$orderedData = array();
			$numItems = count($structure);
			$numVisibleItems = 0;
			// Change Structure of data
			// Add all pages
			foreach($writeData as $resource)
			{
				// Unpack data
				$xmlResource = $resource['resource'];
				$objectType = $resource['objectType'];
				$objectType = (string)$objectType;
				$objectType = trim($objectType);
				$resourceId = (string)$xmlResource['identifier'];
				$resourceId = trim($resourceId);
				// Check file type
				if(strcmp($objectType,"Image")!=0)
				{
					// Retrieve title
					$title = $xmlResource->metadata->lom->general->title->langstring;
					$title = (string)$title;
					$title = trim($title);
					$index = array_search($title, $structure);
					if($index === FALSE)
					{
						$orderedData[$j+$numItems] = $resource;
						$j++;
					}	
					else
					{
						$orderedData[$index] = $resource;
						$this->chapterIds[$k] = $resourceId;
						$k++;
						$numVisibleItems++;
						if(strcmp($objectType,"Course")==0)
							$indexOfCourse = $index;
					}
				}
			}
			// Fix indexing
			for($i=0;$i<count($orderedData);$i++)
				$indexedData[$i] = $orderedData[$i];
			$start = 0;
			$before = $indexOfCourse;
			$after = count($orderedData);
			$fromStart = array_slice($indexedData, $start, $before);
			$toEnd = array_slice($indexedData, $before+1, $after);
			$course = array_slice($indexedData, $before, $before);
			$orderedData = array_merge($course, $fromStart, $toEnd);
			// Retrieve resource Id's
			foreach($orderedData as $resource)
			{
				// Unpack data
				$xmlResource = $resource['resource'];
				$fileContents = $resource['fileContents'];
				$resourceId = (string)$xmlResource['identifier'];
				$resourceId = trim($resourceId);
				foreach($this->resourceFileNames as $aFile)
				{
					if($this->fileMod)
						$aFile = 	preg_replace("/.html|.htm|.jpg|.gif|.png/","",$aFile);
					$regex = '/(href=".*'.$aFile.'.*?")/i';
					preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
					if($matches)
					{
						$index = array_search($resourceId, $this->chapterIds);
						if($index === FALSE){}
							else{}
					}
				}
				$this->resourceIds[$i] = $resourceId;
				$i++;
			}
			$this->chapterId = $this->objIEUtils->addChapters($this->contextCode, $this->courseTitle, $this->newCourse['about']);
			// Add ordered data
			for($i=0;$i<count($orderedData);$i++)
			{
				// Unpack data
				$xmlResource = $orderedData[$i]['resource'];
				$fileContents = $orderedData[$i]['fileContents'];
				$contextCode = $orderedData[$i]['contextCode'];
				$file = $orderedData[$i]['file'];
				$objectType = $orderedData[$i]['objectType'];
				// Cast to string
				$objectType = (string)$objectType;
				// Remove whitespaces for comparison
				$objectType = trim($objectType);
				// Write Course to Chisimba database
				if($i > $numVisibleItems)
					$menutitle = $this->passPage($xmlResource, $fileContents, $contextCode,$i,'N');
				else
					$menutitle = $this->passPage($xmlResource, $fileContents, $contextCode,$i,'Y');
				$menutitle = (string)$menutitle;
				$menutitles[$i] = $menutitle;
			}
		}

		return $menutitles;
	}

	/**
	 * Retrieve page details and pass data
	 * database insertion functions
	 *
	 * @param simpleXml $resource - simple xml object
	 * @param string $fileContents - html page
	 * @param string $contextCode - course contextcode
	 * @param string $bookmark - order of bookmarks
	 * @param string $isBookmark - whether or not the page is a bookmark
	 *
	 * @return string $menutitle - menu title of page
	 *
	*/
	function passPage($resource, $fileContents, $contextCode, $bookmark='', $isBookmark='')
	{
		// Check if menutitle exists
		$resId = (string)$resource['identifier'];
		$resId = trim($resId);
		foreach($this->simpleXmlObj->organizations->organization->item as $item)
		{
			$orgId = (string)$item['identifierref'];
			$orgId = trim($orgId);
			if(strcmp($resId, $orgId)==0)
			{
				$aTitle = (string)$item->title;
				$aTitle = trim($aTitle);
				$menutitle = $aTitle;
			}
		}
		// Retrieve page data
		$titleid = $resource->metadata->lom->general->title->langstring;
		if(!(strlen($menutitle) > 0))
			$menutitle = $resource->metadata->lom->general->description->langstring;
		$content = $fileContents;
		$language = $resource->metadata->lom->general->language;
		$headerscript = "";
		$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
		// Load to $values
		if(!strlen($menutitle) > 0)
			$menutitle = $titleid;
		$values = array('titleid' => (string)$titleid,
				'menutitle' => (string)$menutitle,
				'content' => (string)$content,
				'language' => (string)$language,
				'headerscript' => (string)$headerscript,
				'filename' => trim((string)$filename),
				'bookmark' => $bookmark,
				'isbookmark' => $isBookmark);
		// Insert into database
		$writePage = $this->writePage($values, $contextCode);

		return $menutitle;
	}

	/**
	 * Write content to Chisimba database
	 *
	 * @param array $values - page details
	 * @param string $contextCode - course contextcode
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function writePage($values, $contextCode)
	{
		$tree = $this->objContentOrder->getTree($contextCode, 'dropdown', $parent);
		// Add page
		$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'],
								$values['content'],
								$values['language'],
								$values['headerscript']);
		$this->pageIds[$values['filename']] = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterId, $values['bookmark'], $values['isbookmark']);

		return TRUE;
	}

	/**
	 * Control function to manipulate html pages and resources
	 * and re-insert into database
	 * 
	 * @param array $menutitles - each menu title of html page
	 * @param array $fileNames - the file name of resource
	 *
	 * @return TRUE - on successful execution
	 *
	*/
	function rebuildHtml($menutitles, $fileNames)
	{
		// switch tables
		parent::init('tbl_contextcontent_pages');
		// Retrieve resources
		// Manipulate images
		static $i = 0;
		static $j = 0;
		foreach($this->pageIds as $pageOrderId)
		{
			parent::init('tbl_contextcontent_order');
			$filter = "WHERE id = '$pageOrderId'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				$pageId = $result['0']['titleid'];
				parent::init('tbl_contextcontent_pages');
				$filter = "WHERE titleid = '$pageId'";
				$result = $this->getAll($filter);
				if(count($result) > 0)
				{
					// Retrieve page contents
					$fileContents = $result['0']['pagecontent'];
					$id = $result['0']['id'];
					// Rewrite images source in html
					$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $this->resourceFileNames, $this->imageIds);
					// Rewrite links source in html
					$page = $this->objIEUtils->changeLinkUrl($page, $this->contextCode, $this->resourceFileNames, $this->pageIds);
					// Reinsert into database with updated images
					if(strlen($page) > 1 )
					{
						$update = $this->update('id', $id, array('pagecontent' => $page));
						if($i==0)
						{
							// Modify about in tbl_context
							parent::init('tbl_context');
							$this->update('id', $this->courseId, array('about' => $page));
							// switch tables
							parent::init('tbl_contextcontent_pages');
							$i++;
						}
					}
				}
			}
		}

		return TRUE;
	}

	/**
	 * Locates all secondary xml files holding information,
	 * besides imsmanifest.xml
	 * @param $xpath - simplexml access to imsmanifest
	 * 
	 * @return array $xmlFilesLocation
	 * 
	*/
	function locateAllXmlFiles($xpath)
	{
		$query = '//adlcp:location';
		$results = $xpath->evaluate($query);
		static $j = 0;
		for($i=0;$i<$results->length;$i++)
		{
			$location = trim((string)$results->item($i)->nodeValue);
			if(preg_match('/CourseHome/', $location))
				$xmlFilesLocation['CourseHome'] = $location;
			else if(preg_match('/Syllabus/', $location))
				$xmlFilesLocation['Syllabus'] = $location;
			else if(preg_match('/Calendar/', $location))
				$xmlFilesLocation['Calendar'] = $location;
			else if(preg_match('/Readings/', $location))
				$xmlFilesLocation['Readings'] = $location;
			else if(preg_match('/Labs/', $location))
				$xmlFilesLocation['Labs'] = $location;
			else if(preg_match('/LectureNotes/', $location))
				$xmlFilesLocation['LectureNotes'] = $location;
			else if(preg_match('/Assignments/', $location))
				$xmlFilesLocation['Assignments'] = $location;
			else if(preg_match('/Exams/', $location))
				$xmlFilesLocation['Exams'] = $location;
			else if(preg_match('/Projects/', $location))
				$xmlFilesLocation['Projects'] = $location;
			else if(preg_match('/Tools/', $location))
				$xmlFilesLocation['Tools'] = $location;
			else if(preg_match('/RelatedResources/', $location))
				$xmlFilesLocation['RelatedResources'] = $location;
			else if(preg_match('/DiscussionGroup/', $location))
				$xmlFilesLocation['DiscussionGroup'] = $location;
			else if(preg_match('/DownloadthisCourse/', $location))
				$xmlFilesLocation['DownloadthisCourse'] = $location;
			$j++;
		}

		return $xmlFilesLocation;
	}

	/**
	 * Finds the relative location of all files relevant in MIT package
	 * 
	 * @param array $xpath - simplexml access to imsmanifest
	 * 
	 * @return array $allMITFilePaths
	 * 
	*/
	function locateAllMITFiles($xpath)
	{
		static $i = 0;
		foreach($xpath->resources->resource as $resource)
		{
			$location = trim((string)$resource->file['href']);
			$allMITFilePaths[$i] = $location;
			$i++;
		}

		return $allMITFilePaths;
	}

	/**
	 * Loads all .xml files in package
	 * 
	 * @param string $folder - Location of unzip temporary folder
	 * @param array $xmlFilesLocation - Location of xml files
	 *
	 * @return array $allXmlPackageData
	 *  
	*/
	function loadAllXmlFiles($folder, $xmlFilesLocation)
	{
		if(strlen($xmlFilesLocation['CourseHome'])>1)
		{
			// Read Course Home
			// Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['CourseHome'];
			$courseSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$courseDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$courseXpath = $this->loadXPath($courseDomDocument);
			$allXmlPackageData['CourseHome']['simple'] = $courseSimpleXml;
			$allXmlPackageData['CourseHome']['dom'] = $courseDomDocument;
			$allXmlPackageData['CourseHome']['xpath'] = $courseXpath;
		}
		if(strlen($xmlFilesLocation['Syllabus'])>1)
		{
			// Read Syllabus
			$location = $folder.'/'.$xmlFilesLocation['Syllabus'];
			$syllabusSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$syllabusDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$syllabusXpath = $this->loadXPath($syllabusDomDocument);
			$allXmlPackageData['Syllabus']['simple'] = $syllabusSimpleXml;
			$allXmlPackageData['Syllabus']['dom'] = $syllabusDomDocument;
			$allXmlPackageData['Syllabus']['xpath'] = $syllabusXpath;
		}
		if(strlen($xmlFilesLocation['Calendar'])>1)
		{
			// Read Calendar
			$location = $folder.'/'.$xmlFilesLocation['Calendar'];
			$calendarSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$calendarDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$calendarXpath = $this->loadXPath($calendarDomDocument);
			$allXmlPackageData['Calendar']['simple'] = $calendarSimpleXml;
			$allXmlPackageData['Calendar']['dom'] = $calendarDomDocument;
			$allXmlPackageData['Calendar']['xpath'] = $calendarXpath;
		}
		if(strlen($xmlFilesLocation['Readings'])>1)
		{
			// Read Readings
			$location = $folder.'/'.$xmlFilesLocation['Readings'];
			$readingsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$readingsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml files
			$readingsXpath = $this->loadXPath($readingsDomDocument);
			$allXmlPackageData['Readings']['simple'] = $readingsSimpleXml;
			$allXmlPackageData['Readings']['dom'] = $readingsDomDocument;
			$allXmlPackageData['Readings']['xpath'] = $readingsXpath;
		}
		if(strlen($xmlFilesLocation['LectureNotes'])>1)
		{
			// Read Lecture Notes
			// Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['LectureNotes'];
			$lectureSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$lectureDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$lectureXpath = $this->loadXPath($lectureDomDocument);
			$allXmlPackageData['LectureNotes']['simple'] = $lectureSimpleXml;
			$allXmlPackageData['LectureNotes']['dom'] = $lectureDomDocument;
			$allXmlPackageData['LectureNotes']['xpath'] = $lectureXpath;
		}
		if(strlen($xmlFilesLocation['Labs'])>1)
		{
			// Read Labs
			$location = $folder.'/'.$xmlFilesLocation['Labs'];
			$labsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$labsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$labsXpath = $this->loadXPath($labsDomDocument);
			$allXmlPackageData['Labs']['simple'] = $labsSimpleXml;
			$allXmlPackageData['Labs']['dom'] = $labsDomDocument;
			$allXmlPackageData['Labs']['xpath'] = $labsXpath;
		}
		if(strlen($xmlFilesLocation['Assignments'])>1)
		{
			// Read Assignments
			$location = $folder.'/'.$xmlFilesLocation['Assignments'];
			$assignmentsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$assignmentsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$assignmentsXpath = $this->loadXPath($assignmentsDomDocument);
			$allXmlPackageData['Assignments']['simple'] = $assignmentsSimpleXml;
			$allXmlPackageData['Assignments']['dom'] = $assignmentsDomDocument;
			$allXmlPackageData['Assignments']['xpath'] = $assignmentsXpath;
		}
		if(strlen($xmlFilesLocation['Exams'])>1)
		{
			// Read Exams
			// Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['Exams'];
			$examsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$examsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$examsXpath = $this->loadXPath($examsDomDocument);
			$allXmlPackageData['Exams']['simple'] = $examsSimpleXml;
			$allXmlPackageData['Exams']['dom'] = $examsDomDocument;
			$allXmlPackageData['Exams']['xpath'] = $examsXpath;
		}
		if(strlen($xmlFilesLocation['Projects'])>1)
		{
			// Read Projects
			$location = $folder.'/'.$xmlFilesLocation['Projects'];
			$projectsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$projectsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$projectsXpath = $this->loadXPath($projectsDomDocument);
			$allXmlPackageData['Projects']['simple'] = $projectsSimpleXml;
			$allXmlPackageData['Projects']['dom'] = $projectsDomDocument;
			$allXmlPackageData['Projects']['xpath'] = $projectsXpath;
		}
		if(strlen($xmlFilesLocation['Tools'])>1)
		{
			// Read Tools
			// Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['Tools'];
			$toolsSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$toolsDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$toolsXpath = $this->loadXPath($toolsDomDocument);
			$allXmlPackageData['Tools']['simple'] = $toolsSimpleXml;
			$allXmlPackageData['Tools']['dom'] = $toolsDomDocument;
			$allXmlPackageData['Tools']['xpath'] = $toolsXpath;
		}
		if(strlen($xmlFilesLocation['RelatedResources'])>1)
		{
			// Read Related Resources
			$location = $folder.'/'.$xmlFilesLocation['RelatedResources'];
			$relatedSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$relatedDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$relatedXpath = $this->loadXPath($relatedDomDocument);
			$allXmlPackageData['RelatedResources']['simple'] = $relatedSimpleXml;
			$allXmlPackageData['RelatedResources']['dom'] = $relatedDomDocument;
			$allXmlPackageData['RelatedResources']['xpath'] = $relatedXpath;
		}
		if(strlen($xmlFilesLocation['DiscussionGroup'])>1)
		{
			// Read Discussion Group
			$location = $folder.'/'.$xmlFilesLocation['DiscussionGroup'];
			$discussionSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$discussionDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$discussionXpath = $this->loadXPath($discussionDomDocument);
			$allXmlPackageData['DiscussionGroup']['simple'] = $discussionSimpleXml;
			$allXmlPackageData['DiscussionGroup']['dom'] = $discussionDomDocument;
			$allXmlPackageData['DiscussionGroup']['xpath'] = $discussionXpath;
		}
		if(strlen($xmlFilesLocation['DownloadthisCourse'])>1)
		{
			// Read Download this Course
			$location = $folder.'/'.$xmlFilesLocation['DownloadthisCourse'];
			$downloadSimpleXml = $this->loadSimpleXML($location);
			// Create domdocument object to access xml file	
			$downloadDomDocument = $this->loadDOMDocument($location);
			// Create xpath object to access xml file
			$downloadXpath = $this->loadXPath($downloadDomDocument);
			$allXmlPackageData['DownloadthisCourse']['simple'] = $downloadSimpleXml;
			$allXmlPackageData['DownloadthisCourse']['dom'] = $downloadDomDocument;
			$allXmlPackageData['DownloadthisCourse']['xpath'] = $downloadXpath;
		}

		return $allXmlPackageData;
	}

	/**
	 * Writes html files to staticcontent folder in usrfiles, and stores
	 * location of file to be uploaded into system later
	 * 
	 * @param string $newFolder - Location of unzip temporary folder
	 * @param array $allXmlPackageData - all the data collected on all xml files
	 *
	 * @return array $mitHtmlsPath
	 *  
	*/
	function getMITHtmls($newFolder, $allXmlPackageData)
	{
		// Read Course Home
		$courseXpath = $allXmlPackageData['CourseHome']['xpath'];
		if(isset($courseXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $courseXpath->evaluate($query);
			$coursePath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['CourseHome'] = $newFolder.$coursePath;
			// Retrieve contents of file
			$fileContents = file_get_contents($mitHtmlsPath['CourseHome']);
			// New location for Files
			$newLocation = $this->docsLocation."/".'CourseHome.html';
			// Open html directory
			$fp = fopen($newLocation,'w');
			// Write the file to static directory
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			// Close the directory
			fclose($fp);
		}
		// Read Syllabus
		$syllabusXpath = $allXmlPackageData['Syllabus']['xpath'];
		if(isset($syllabusXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $syllabusXpath->evaluate($query);
			$syllabusPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Syllabus'] = $newFolder.$syllabusPath;
			$fileContents = file_get_contents($mitHtmlsPath['Syllabus']);
			$newLocation = $this->docsLocation."/".'Syllabus.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Calendar
		$calendarXpath = $allXmlPackageData['Calendar']['xpath'];
		if(isset($calendarXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $calendarXpath->evaluate($query);
			$calendarPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Calendar'] = $newFolder.$calendarPath;
			$fileContents = file_get_contents($mitHtmlsPath['Calendar']);
			$newLocation = $this->docsLocation."/".'Calendar.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Readings
		$readingsXpath = $allXmlPackageData['Readings']['xpath'];
		if(isset($readingsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $readingsXpath->evaluate($query);
			$readingsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Readings'] = $newFolder.$readingsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Readings']);
			$newLocation = $this->docsLocation."/".'Readings.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Lecture Notes
		$lectureXpath = $allXmlPackageData['LectureNotes']['xpath'];
		if(isset($lectureXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $lectureXpath->evaluate($query);
			$lecturePath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['LectureNotes'] = $newFolder.$lecturePath;
			$fileContents = file_get_contents($mitHtmlsPath['LectureNotes']);
			$newLocation = $this->docsLocation."/".'LectureNotes.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Labs
		$labsXpath = $allXmlPackageData['Labs']['xpath'];
		if(isset($labsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $labsXpath->evaluate($query);
			$labsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Labs'] = $newFolder.$labsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Labs']);
			$newLocation = $this->docsLocation."/".'Labs.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Assignments
		$assignmentsXpath = $allXmlPackageData['Assignments']['xpath'];
		if(isset($assignmentsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $assignmentsXpath->evaluate($query);
			$assignmentsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Assignments'] = $newFolder.$assignmentsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Assignments']);
			$newLocation = $this->docsLocation."/".'Assignments.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		// Read Exams
		$examsXpath = $allXmlPackageData['Exams']['xpath'];
		if(isset($examsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $examsXpath->evaluate($query);
			$examsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Exams'] = $newFolder.$examsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Exams']);
			$newLocation = $this->docsLocation."/".'Exams.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Projects
		$projectsXpath = $allXmlPackageData['Projects']['xpath'];
		if(isset($projectsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $projectsXpath->evaluate($query);
			$projectsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Projects'] = $newFolder.$projectsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Projects']);
			$newLocation = $this->docsLocation."/".'Projects.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Tools
		$toolsXpath = $allXmlPackageData['Tools']['xpath'];
		if(isset($toolsXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $examsXpath->evaluate($query);
			$toolsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Tools'] = $newFolder.$toolsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Tools']);
			$newLocation = $this->docsLocation."/".'Tools.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Resources
		$resourcesXpath = $allXmlPackageData['RelatedResources']['xpath'];
		if(isset($resourcesXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $resourcesXpath->evaluate($query);
			$resourcesPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['RelatedResources'] = $newFolder.$resourcesPath;
			$fileContents = file_get_contents($mitHtmlsPath['RelatedResources']);
			$newLocation = $this->docsLocation."/".'RelatedResources.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Discussion Group
		$discussionXpath = $allXmlPackageData['DiscussionGroup']['xpath'];
		if(isset($discussionXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $discussionXpath->evaluate($query);
			$discussionPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['DiscussionGroup'] = $newFolder.$discussionPath;
			$fileContents = file_get_contents($mitHtmlsPath['DiscussionGroup']);
			$newLocation = $this->docsLocation."/".'DiscussionGroup.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}
		// Read Download this Course
		$downloadXpath = $allXmlPackageData['DownloadthisCourse']['xpath'];
		if(isset($downloadXpath))
		{
			$query = '//lom:technical/lom:location';
			$results = $downloadXpath->evaluate($query);
			$downloadPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['DownloadthisCourse'] = $newFolder.$downloadPath;
			$fileContents = file_get_contents($mitHtmlsPath['DownloadthisCourse']);
			$newLocation = $this->docsLocation."/".'DownloadthisCourse.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
				return  "writeResourcesError";
			fclose($fp);
		}

		return $mitHtmlsPath;
	}

	/**
	 * Load all html pages to chisimba
	 *
	 * @param array $mitHtmlsPath - location of html page in temp folder
	 * @param array $allXmlPackageData - all the data collected on all xml files
	 *
	 * @return array $menutitles
	 *
	*/
	function loadToChisimbaFromPaths($mitHtmlsPath, $allXmlPackageData)
	{
		$menutitles = array();
		$this->chapterId = $this->objIEUtils->addChapters($this->contextCode, $this->courseTitle, $this->newCourse['about']);
		static $i = 0;
		foreach($mitHtmlsPath as $htmlPath)
		{
			$fileContents = file_get_contents($htmlPath);
			// Strip navigational tags
			$fileContents = $this->removeMITLayouts($fileContents);
			if(preg_match('/CourseHome/', $htmlPath))
			{
				$menutitle = 'Course Home';
				$filename = 'CourseHome.html';
			}
			else if(preg_match('/Syllabus/', $htmlPath))
			{
				$menutitle = 'Syllabus';
				$filename = 'Syllabus.html';
			}
			else if(preg_match('/Calendar/', $htmlPath))
			{
				$menutitle = 'Calendar';
				$filename = 'Calendar.html';
			}
			else if(preg_match('/Readings/', $htmlPath))
			{
				$menutitle = 'Readings';
				$filename = 'Readings.html';
			}
			else if(preg_match('/Lecture/', $htmlPath))
			{
				$menutitle = 'Lecture Notes';
				$filename = 'LectureNotes.html';
			}
			else if(preg_match('/Labs/', $htmlPath))
			{
				$menutitle = 'Labs';
				$filename = 'Labs.html';
			}
			else if(preg_match('/Assignments/', $htmlPath))
			{
				$menutitle = 'Assignments';
				$filename = 'Assignments.html';
			}
			else if(preg_match('/Exams/', $htmlPath))
			{
				$menutitle = 'Exams';
				$filename = 'Exams.html';
			}
			else if(preg_match('/Projects/', $htmlPath))
			{
				$menutitle = 'Projects';
				$filename = 'Projects.html';
			}
			else if(preg_match('/Resources/', $htmlPath))
			{
				$menutitle = 'Related Resources';
				$filename = 'RelatedResources.html';
			}
			else if(preg_match('/DownloadthisCourse/', $htmlPath))
			{
				$menutitle = 'Download this Course';
				$filename = 'DownloadthisCourse.html';
			}
			else
			{
				$menutitle = 'None';
				$filename = 'None.html';
			}
			$tree = $this->objContentOrder->getTree($this->contextCode, 'dropdown', $parent);
        		$titleId = $this->objContentTitles->addTitle('',
								$menutitle,
								$fileContents,
								'en',
								'');
        		$this->pageIds[$filename] = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $this->chapterId, $i, 'Y');
			$menutitles[$i] = $menutitle;
			$i++;
		}

		return $menutitles;
	}

	/**
	 * Writes all images to the image directory of usrfiles, to be 
	 * indexed later into the filemanager
	 * 
	 * @param string $newFolder - Location of unzip temporary folder
	 * @param array $allFilesLocation - Location of all files in package
	 * @param array $fileNames - image filenames
	 *
	 * @return array $imagePaths
	 *
	*/
	function writeMITImages($newFolder='', $allFilesLocation, $fileNames='')
	{
		static $i = 0;
		foreach($allFilesLocation as $fileLocation)
		{
			foreach($fileNames as $fileName)
			{
				if(preg_match("/$fileName/",$fileLocation))
				{
					if(preg_match("/.jpg|.gif|.png/",$fileLocation) && !(preg_match("/.xml/",$fileLocation)))
					{
						$filename = $fileName;
						$imagePaths[$fileLocation] = $filename;
						if((strlen($newFolder) > 1))
							$fileLocation = $newFolder.'/'.$fileLocation;
						$fileContents = file_get_contents($fileLocation);
						$newLocation = $this->imagesLocation."/".$filename;
						$fp = fopen($newLocation,'w');
						if((fwrite($fp, $fileContents) === FALSE))
							return  "writeResourcesError";
						fclose($fp);
						$i++;
					}
				}
			}
		}

		return $imagePaths;
	}

	/**
	 * Writes files other than images and htmls (pdf's) to the documents directory,
	 * to be indexed later into the filemanager
	 * 
	 * @param string $newFolder - Location of unzip temporary folder
	 * @param array $allFilesLocation - 
	 * @param array $fileNames
	 *
	 * @return array $filePaths
	 * 
	*/
	function writeMITFiles($newFolder='', $allFilesLocation, $fileNames='')
	{
		static $i = 0;
		$noFilesWritten = 0;
		foreach($allFilesLocation as $fileLocation)
		{
			foreach($fileNames as $fileName)
			{
				if(preg_match("/$fileName/",$fileLocation))
				{
					if(!(preg_match("/.txt|.htm|.html|.xml|.css|.js|.jpg|.gif|.png|.xsd/",$fileLocation)))
					{
						$filename = $fileName;
						$filePaths[$fileLocation] = $filename;
						if((strlen($newFolder) > 1))
							$fileLocation = $newFolder.'/'.$fileLocation;
						$fileContents = file_get_contents($fileLocation);
						$newLocation = $this->filesLocation."/".$filename;
						$fp = fopen($newLocation,'w');
						if((fwrite($fp, $fileContents) === FALSE))
							return  "writeResourcesError";
						fclose($fp);
						$i++;
						$noFilesWritten = 1;
					}
				}
			}
		}

		if($noFilesWritten == 1)
			return $filePaths;
		else
			return TRUE;
	}

	/**
	 * Removes all navigational tags from MIT htmls
	 * 
	 * @param string $fileContents - html page
	 *
	 * @return string $fileContents
	*/
	function removeMITLayouts($fileContents)
	{
		$fileContents = preg_replace('/skip to content/','',$fileContents);
		$fileContents = preg_replace('%<div class="header-redline(.|\\n)+?<!-- end header-redline -->%','',$fileContents);
		$fileContents = preg_replace('%<div class="sitetools(.|\\n)+?</div>%','',$fileContents);
		$fileContents = preg_replace('/<!-- LeftNav(.|\\n)+?<!-- End LeftNav -->/','',$fileContents);
		$fileContents = preg_replace('/<!-- begin search(.|\\n)+?<!-- end search area -->/','',$fileContents);
		$fileContents = preg_replace('%<div class="bread-crumb(.|\\n)+?</div>%','',$fileContents);

		return $fileContents;
	}

	/**
	 *
	 * Control function to manipulate html pages and resources
	 * and re-insert into database
	 * 
	 * @param array $menutitles - each menu title of html page
	 * @param array $fileNames - the file name of resource
	 * @param $allFilesLocation - 
	 *
	 * @return TRUE - on successful execution
	 *
	*/
	function rebuildMITHtml($menutitles, $fileNames, $allFilesLocation)
	{
		// switch tables
		parent::init('tbl_contextcontent_pages');
		// Retrieve resources
		// Manipulate images
		static $i = 0;
		static $j = 0;
		foreach($this->pageIds as $pageOrderId)
		{
			parent::init('tbl_contextcontent_order');
			$filter = "WHERE id = '$pageOrderId'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				$pageId = $result['0']['titleid'];
				parent::init('tbl_contextcontent_pages');
				$filter = "WHERE titleid = '$pageId'";
				$result = $this->getAll($filter);
				if(count($result) > 0)
				{
					// Retrieve page contents
					$fileContents = $result['0']['pagecontent'];
					$id = $result['0']['id'];
					// Rewrite images source in html
					$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $fileNames, $this->imageIds);
					// Rewrite links source in html
					$page = $this->objIEUtils->changeMITLinkUrl($page, $this->contextCode, $fileNames, $this->fileIds);
					// Rewrite internal links source in html
					$page = $this->objIEUtils->changeLinkUrl($page, $this->contextCode, $fileNames, $this->pageIds, 'mit', $allFilesLocation);
					// Rewrite data links source in html
					$page = $this->objIEUtils->changeDataLink($page, $this->contextCode, $fileNames, $this->fileIds, 'mit', $allFilesLocation);
					// Reinsert into database with updated images
					if(strlen($page) > 1 )
					{
						$update = $this->update('id', $id, array('pagecontent' => $page));
						if($i==0)
						{
							// Modify about in tbl_context
							parent::init('tbl_context');
							$this->update('id', $this->courseId, array('about' => $page));
							// switch tables
							parent::init('tbl_contextcontent_pages');
							$i++;
						}
					}
				}
			}
		}

		return TRUE;
	}

	function loadToChisimbaFromContent($htmlPages, $filenames, $structure, $menuTitles)
	{
		$menutitles = array();
		$this->chapterId = $this->objIEUtils->addChapters($this->contextCode, $this->courseTitle, $this->newCourse['about']);
		static $i = 0;
		$numItems = count($structure);
		foreach($htmlPages as $htmlPage)
		{
			$tree = $this->objContentOrder->getTree($this->contextCode, 'dropdown', $parent);
        		$titleId = $this->objContentTitles->addTitle('',
								$menuTitles[$i],
								$htmlPage,
								'en',
								'');
			if(array_search($menuTitles[$i], $structure))
			{
			$this->pageIds[$filenames[$i]] = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $this->chapterId, $i, 'Y');
			}
			else
			{
			$this->pageIds[$filenames[$i]] = $this->objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $this->chapterId, $i, 'N');
			}
			$i++;
		}

		return $this->pageIds;
	}

	function subLevelItem($sublevel)
	{
		$aTitle = (string)$sublevel->title;
		
		return $aTitle;
	}

	function getMenuTitles($htmlPages)
	{
		foreach($htmlPages as $htmlPage)
		{
			static $i = 0;
			$regex = '%<p id="nodeTitle">.*?</p>%s';
			preg_match_all($regex, $htmlPage, $result, PREG_PATTERN_ORDER);
			if($result)
			{
				$menutitle = $result[0][0];
				$menutitle = preg_replace('/<p id="nodeTitle">/s', '', $result[0][0]);
				$menutitle = preg_replace('%</p>%s', '', $menutitle);
				$menutitle = trim($menutitle);
				$menuTitles[$i] = $menutitle;
				$i++;
			}
		}

		return $menuTitles;
	}

	/**
	 * Sets debugging on
	 *
    	 * @param NULL
	 *
	*/
	function debugOn()
	{
		$this->objDebug = TRUE;
	}

	/**
	 * Sets debugging off
	 *
    	 * @param NULL
	 *
	*/
	function debugOff()
	{
		$this->objDebug = FALSE;
	}
}
?>