<?php
# security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
# end security check
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
	 * @var object $objLanguage
	*/
	public $objLanguage;
	/**
	 * @var object $objDBContext
	*/
	public $objDBContext;
	/**
	 * @var object $objContextContent
	*/
	public $objContextContent;
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
	/**
	 * The constructor
	*/
	function init()
	{
		#Load Filemanager class
		$this->objIndex =& $this->getObject('indexfileprocessor', 'filemanager');
		$this->objUpload =& $this->getObject('upload', 'filemanager');
		#Load System classes
		$this->objConfig = & $this->newObject('altconfig','config');
        	$this->objLanguage = & $this->newObject('language', 'language');
        	$this->objDBContext = & $this->newObject('dbcontext', 'context');
        	$this->objUser =& $this->getObject('user', 'security');
		#Load Inner classes
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
		#Load Chapter Classes
		$this->objChapters =& $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters =& $this->getObject('db_contextcontent_contextchapter','contextcontent');
		#Load context classes
        	$this->objContentPages =& $this->getObject('db_contextcontent_pages','contextcontent');
	        $this->objContentOrder =& $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles =& $this->getObject('db_contextcontent_titles','contextcontent');
	        $this->objContentInvolvement =& $this->getObject('db_contextcontent_involvement','contextcontent');
		$this->pageIds = array();
		#Initialize Flags
		$this->fileMod = FALSE;
		$this->objDebug = FALSE;
		#Un-comment to view debug information
		#$this->objDebug = TRUE;
		//$this->objError = FALSE;
		#Un-comment to catch errors
		$this->objError = TRUE;
	}
	
	/**
	 * 
	 * Controls the process for import IMS specification content,
	 * either aa eduCommons or MIT packages
	 * 
	 * @param $_FILES global - uploaded file
	 * @param string $package
	*/
	function importIMScontent($FILES, $package)
	{
		$this->fileMod = FALSE;
		$this->objIEUtils->fileModOff();
		$this->courseId = '';$package = 'mit';
		var_dump($package);
		if($package == 'default')
		{
			return $this->defaultPackage($FILES);
		}
		else if($package == 'mit')
		{
			return $this->mitPackage($FILES);
		}
		else
			return FALSE;
	}

	/**
	 * 
	 * Controls the process for import IMS specification content,
	 * either aa eduCommons or MIT packages
	 * 
	 * @param $_FILES global - uploaded file
	 * 
	*/
	function mitPackage($FILES)
	{
		#Check archive type
		if(!isset($FILES))// || $FILES['upload']['type'] != 'application/zip')
		{
			if($this->objError)
				return  "zipFileError";
		}
		echo 's1';
		#Retrieve temp folder
		$folder = $this->unzipIMSFile($FILES);
		if(!isset($folder) || $folder == 'unzipError')
		{
			if($this->objError)
				return  "unzipError";
		}
		echo 's2';
		#Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
//var_dump($fileNames);
		if(!isset($fileNames))
		{
			if($this->objError)
				return  "fileReadError";
		}
		echo 's3';
		#Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
		{
			if($this->objError)
				return  "fileReadError";
		}
		echo 's4';
		#Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
		{
			if($this->objError)
				return  "imsReadError";
		}
		echo 's5';
		#Read imsmanifest.xml file
		#Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj) || $simpleXmlObj == 'simpleXmlError')
		{
			if($this->objError)
				return  "simpleXmlError";
		}
		echo 's6';
		#Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj) || $domDocumentObj == 'domReadError')
		{
			if($this->objError)
				return  "domReadError";
		}
		echo 's7';
		#Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
		{
			if($this->objError)
				return  "xpathSetError";
		}
		echo 's8';
		#Retrieve all resource files in package
		$allFilesLocation = $this->locateAllMITFiles($simpleXmlObj);
		#Retrieve all .xml files
		$xmlFilesLocation = $this->locateAllXmlFiles($xpathObj);
		#Retrieve all files
		$allLocations = $this->objIEUtils->list_dir_files($folder, 1);
		#Load all .xml files
		$newFolder = $folder.'/'.preg_replace("/.zip/","",$FILES['upload']['name']);
		$allXmlPackageData = $this->loadAllXmlFiles($newFolder, $xmlFilesLocation);
		#Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj,'mit');
		if(!isset($courseData) || $courseData == 'courseReadError')
		{
			if($this->objError)
				return  "courseReadError";
		}
		echo 's9';
		#Initialize all locations
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		if(!isset($init))
		{
			if($this->objError)
				return  "initializeError";
		}
		echo 's10';
		#Create course
		$courseCreated = $this->objIEUtils->createCourseInChisimba($courseData);
		$this->courseId = $courseCreated;
		if(!isset($courseCreated) || $courseCreated == 'courseWriteError')
		{
			if($this->objError)
				return  "courseWriteError";
		}
		echo 's11';
		#Write Resources
		#Retrieve Html locations and move to usrfiles
		$mitHtmlsPath = $this->getMITHtmls($newFolder, $allXmlPackageData);
		//echo 's12';
		//Write Images to Chisimba usrfiles directory
		$writeImages = $this->writeMITImages('', $allLocations, $fileNames);
		//Write Files to Chisimba usrfiles directory
		$writeFiles = $this->writeMITFiles('', $allLocations, $fileNames);
		//echo 's13';
		#Load html data into Chisimba
		$loadData = $this->loadToChisimba($mitHtmlsPath, $allXmlPackageData, 'Y');
		if(!isset($loadData))
		{
			if($this->objError)
				return  "loadDataError";
		}
		echo 's14';
		#Load image data into Chisimba
		$uploadImagesToChisimba = $this->uploadImagesToChisimba();
		#Load image data into Chisimba
		$uploadFilesToChisimba = $this->uploadImagesToChisimba($this->filesLocation);
		if(!isset($uploadImagesToChisimba))
		{
			if($this->objError)
				return  "uploadError";
		}
		echo 's15';
		#Rebuild html images and url links
		$allFileNames = $this->objIEUtils->list_dir_files($folder,0);
		$rebuildHtml = $this->rebuildMITHtml($loadData, $allFileNames);
		if(!isset($rebuildHtml))
		{
			if($this->objError)
				return  "rebuildHtmlError";
		}

		return TRUE;
	}

	public $courseId;
	/**
	 *
	 * Controls the process for import IMS specification content
	 * Calls all necessary functions an does error checking
	 * 
	 * @param $_FILES global - uploaded file
	 *
	 * @return TRUE - Successful execution
	 * 

	*/
	function defaultPackage($FILES)
	{
		#Check archive type
		if(!isset($FILES) || $FILES['upload']['type'] != 'application/zip')
		{
			if($this->objError)
				return  "zipFileError";
		}
		//echo 's1';
		#Retrieve temp folder
		$folder = $this->unzipIMSFile($FILES);
		if(!isset($folder) || $folder == 'unzipError')
		{
			if($this->objError)
				return  "unzipError";
		}
		//echo 's2';
		#Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
//var_dump($fileNames);
		if(!isset($fileNames))
		{
			if($this->objError)
				return  "fileReadError";
		}
		//echo 's3';
		#Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
		{
			if($this->objError)
				return  "fileReadError";
		}
		//echo 's4';
		#Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
		{
			if($this->objError)
				return  "imsReadError";
		}
		//echo 's5';
		#Read imsmanifest.xml file
		#Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj) || $simpleXmlObj == 'simpleXmlError')
		{
			if($this->objError)
				return  "simpleXmlError";
		}
		//echo 's6';
		#Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj) || $domDocumentObj == 'domReadError')
		{
			if($this->objError)
				return  "domReadError";
		}
		//echo 's7';
		#Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
		{
			if($this->objError)
				return  "xpathSetError";
		}
		//echo 's8';
		#Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj);
		if(!isset($courseData) || $courseData == 'courseReadError')
		{
			if($this->objError)
				return  "courseReadError";
		}
		//echo 's9';
		#Initialize all locations
		$init = $this->initLocations($courseData['contextcode'], $courseData['title']);
		if(!isset($init))
		{
			if($this->objError)
				return  "initializeError";
		}
		//echo 's10';
		#Create course
		$courseCreated = $this->objIEUtils->createCourseInChisimba($courseData);
		$this->courseId = $courseCreated;
		if(!isset($courseCreated) || $courseCreated == 'courseWriteError')
		{
			if($this->objError)
				return  "courseWriteError";
		}
		//echo 's11';
		#Write Resources
		$writeData = $this->writeResources($simpleXmlObj, $folder, $courseData);
		if(!isset($writeData))
		{
			if($this->objError)
				return  "writeResourcesError";
		}
		//echo 's12';
		#Get organizations
		$structure = $this->getStructure($simpleXmlObj);
		if(!isset($structure))
		{
			if($this->objError)
				return  "noStructureError";
		}
		//echo 's13';
		#Load html data into Chisimba
		$loadData = $this->loadToChisimba($writeData, $structure);
		if(!isset($loadData))
		{
			if($this->objError)
				return  "loadDataError";
		}
		//echo 's14';
		#Load image data into Chisimba
		$uploadImagesToChisimba = $this->uploadImagesToChisimba();
		if(!isset($uploadImagesToChisimba))
		{
			if($this->objError)
				return  "uploadError";
		}
		//echo 's15';
		#Rebuild html images and url links
		$rebuildHtml = $this->rebuildHtml($loadData,$fileNames);
		if(!isset($rebuildHtml))
		{
			if($this->objError)
				return  "rebuildHtmlError";
		}
		#!!!Under going testing
		//$this->objIEUtils->eduCommonsData($imsFileLocation);

		return TRUE;
	}

	public $contentBasePath;
	public $courseContentBasePath;
	public $contextCode;
	public $courseTitle;
	public $courseContentPath;
	public $imagesLocation;
	public $docsLocation;
	public $filesLocation;
	/**
	 * 
	 * Sets the global variables and directory paths
	 * 
	 * @param string $contextcode 
	 * @param string $courseTitle
	 * 
	 * @return 
	 * 
	*/
	function initLocations($contextcode, $courseTitle)
	{
		#Static Chisimba file locations
		#opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$this->contentBasePath = $this->objConfig->getcontentBasePath();
		$this->courseContentBasePath = $this->contentBasePath."content/";
		$this->contextCode = strtolower(str_replace(' ','_',$contextcode));
		$this->courseTitle = strtolower(str_replace(' ','_',$courseTitle));
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
		#Enter context
		$enterContext = $this->objDBContext->joinContext($this->contextCode);
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
	 * @var object $folder - relative path of temporary folder
	*/
	public $folder;
	/**
	 * 
	 * Function to unzip an uploaded zip file
	 * 
	 * @param $_FILES global - Location of uploaded zip-file
	 *
	 * @return string $folder - Temp extraction folder location
	 * 
	*/
	function unzipIMSFile($FILES)
	{
		if(!is_uploaded_file($FILES['upload']['tmp_name']))
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
				#!!!Need to find a way to test if directory is created
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
	 * @var object $fileLocations - relative paths to all files
	*/
	public $fileLocations;
	/**
	 * 
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
	 * @var object $imsmanifestLocation - relative paths to IMS manifest file
	*/
	public $imsmanifestLocation;
	/**
	 * 
	 * Scans a specified array of strings for a specified string
	 *
	 * @param array $files - list of file locations
	 * @param string $regex - filename to scan in regular expression form
	 *
	 * @return $imsmanifestLocation
	 *
	*/
	public function locateIMSFile($files, $regex)
	{
		foreach($files as $aFile)
		{
			if(preg_match($regex, $aFile))
			{
				$this->imsmanifestLocation = $aFile;
			}
		}

		return $this->imsmanifestLocation;
	}

	/**
	 * @var object $simpleXmlObj - simplexml object to access xml file
	*/
	public $simpleXmlObj;
	/**
	 * 
	 * Takes the imsmanifest.xml file as input
	 *
	 * @param string $imsFileLocation
	 *
	 * @return simpleXml $simpleXmlObj
	 *
	*/
	function loadSimpleXml($imsFileLocation)
	{
		#Load imsmanifest.xml file
		if(file_exists($imsFileLocation)) 
		{
    			$this->simpleXmlObj = simplexml_load_file($imsFileLocation);
		}
		else 
		{
    			return  "simpleXmlError";
		}
		
		return $this->simpleXmlObj;
	}

	/**
	 * @var object $domDocumentObj - dom document object to access xml file
	*/
	public $domDocumentObj;
	/**
	 * 
	 * Takes the imsmanifest.xml file as input
	 *
	 * @param string $imsFileLocation
	 *
	 * @return DOMDocument $domDocumentObj
	 *
	*/
	function loadDOMDocument($imsFileLocation)
	{
		$this->domDocumentObj = new DOMDocument();
		#Create domdocument object to access xml file
		if($this->domDocumentObj->load($imsFileLocation))
		{
		}
		else 
		{
    			return  "domReadError";
		}

		return $this->domDocumentObj;
	}

	/**
	 * @var object $xpathObj - xpath object to access xml file
	*/
	public $xpathObj;
	/**
	 * 
	 * Takes a DOM document as input
	 *
	 * @param DOMDocument $domDocumentObj
	 *
	 * @return DOMXPath $domDocumentObj
	 *
	*/
	function loadXPath($domDocumentObj)
	{
		#Create xpath object to access xml file
		$this->xpathObj = new DOMXPath($domDocumentObj);

		return $this->xpathObj;
	}

	/**
	 * @var object $newCourse - all course data needed to create course
	*/
	public $newCourse;
	/**
	 * 
	 * Extract the course information from imsmanifest.xml
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function extractCourseData($xml, $doc, $xpath, $packageType = '')
	{
		#Set eduCommons namespaces
		$xpath->registerNamespace("educommons", "http://albatross.ed.usu.edu/xsd/educommons_v1");
		#Set imsmd namespaces
		$xpath->registerNamespace("imsmd", "http://www.imsglobal.org/xsd/imsmd_v1p2");
		$xpath->registerNamespace('adlcp','http://www.adlnet.org/xsd/adlcp_rootv1p2');
		$xpath->registerNamespace('cwspace','http://www.dspace.org/xmlns/cwspace_imscp'); 
		$xpath->registerNamespace('ocw','http://ocw.mit.edu/xmlns/ocw_imscp');
		#Create course
		#Establish which resource is a course
		if($packageType == 'mit')
		{
#course id (contextcode)
			$query = '//lom:identifier/lom:entry';
			$results = $xpath->evaluate($query);
			$courseId = trim((string)$results->item($i)->nodeValue);
			$courseId .= '_'.$this->objIEUtils->generateUniqueId();
			$this->newCourse['contextcode'] = $courseId;
#course title (title)
			$query = '//lom:title';
			$results = $xpath->evaluate($query);
			$courseTitle = trim((string)$results->item(0)->nodeValue);
			$this->newCourse['title'] = $courseTitle;
#course title (menu text)
			$this->newCourse['menutext'] = $courseTitle;
#course title (userId)
			$this->newCourse['userid'] = "1";
#course identifier (not in use)
			$this->newCourse['courseIdentifier'] = '';
#course description (about)
			$this->newCourse['about'] = '';
#course status (status)
			$courseStatus = "Public";
			$this->newCourse['isactive'] = $courseStatus;
#course access (access)
			$courseAccess = "UnPublished";
			$this->newCourse['isclosed'] = $courseAccess;
		}
		else
		{
			foreach($xml->resources->resource as $resource)
			{
				#Retrieve file type
				$objectType = $resource->metadata->eduCommons->objectType;
				#Cast to string
				$objectType = (string)$objectType;
				#Remove whitespaces for comparison
				$objectType = trim($objectType);
				#Check file type
				#Course
				if(strcmp($objectType,"Course")==0)
				{
					$courseId = $resource->metadata->eduCommons->courseId;
					$courseId = (string)$courseId;
					$courseId = trim($courseId);
					if(!(strlen($courseId) > 1))
						return 'courseReadError';
					$this->newCourse['contextcode'] = $courseId.'_'.$this->objIEUtils->generateUniqueId();
					$courseTitle = $resource->metadata->lom->general->title->langstring;
					$courseTitle = (string)$courseTitle;
					$courseTitle = trim($courseTitle);
					$this->newCourse['title'] = $courseTitle;
					if(!(strlen($courseTitle) > 1))
						return 'courseReadError';
					$this->newCourse['menutext'] = $courseTitle;
					$this->newCourse['userid'] = "1";
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
	 * @var object $resourceFileNames - the filenames of resources, including type
	*/
	public $resourceFileNames;
	/**
	 * @var object $alldata - all data needed to add file to database
	*/
	public $alldata;
	/**
	 * 
	 * Move the resources to appropriate locations on the file system
	 * and to the proper databases
	 *
	 * @param $xml
	 * @param $folder
	 * @param $newCourse
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function writeResources($xml, $folder, $newCourse)
	{
		#Pre-initialize variables
		$resourceFileLocations = array();
		#First add course to Chisimba database
		foreach($xml->resources->resource as $resource)
		{
			#Retrieve file type
			$objectType = $resource->metadata->eduCommons->objectType;
			#Cast to string
			$objectType = (string)$objectType;
			#Remove whitespaces for comparison
			$objectType = trim($objectType);
			#Check file type
			#Course
			if(strcmp($objectType,"Course")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				#Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				#Chech if file exists on local system
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
				#Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				#Save all data
				$this->allData['0'] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				#Check debug flag
				if($this->objDebug)
				{
					echo "Course"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
		}
		#Add all other resources to Chisimba database
		foreach($xml->resources->resource as $resource)
		{
			static $i = 1;
			#Retrieve file type
			$objectType = $resource->metadata->eduCommons->objectType;
			#Cast to string
			$objectType = (string)$objectType;
			#Remove whitespaces for comparison
			$objectType = trim($objectType);
			#Check file type
			#Document
			if(strcmp($objectType,"Document")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				#Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				#Chech if file exists on local system
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
				#Write file contents to documents folder
				#Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				#Check filename
				if(!preg_match("/.html|.htm/",$filename))
				{
					#Correct filename
					$filename = $filename.".html";
					$this->fileMod = TRUE;
					$this->objIEUtils->fileModOn();
				}
				#New location for Documents
				$newLocation = $this->docsLocation."/".$filename;
				#Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				#Store filesname
				$this->resourceFileNames[$i] = $filename;
				#Open images directory
				$fp = fopen($newLocation,'w');
				#Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				#Close the directory
				fclose($fp);
				#Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents, 
							'contextCode' => $this->contextCode, 
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				#Check debug flag
				if($this->objDebug)
				{
					echo "Document"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
			#File
			if(strcmp($objectType,"File")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				#Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				#Chech if file exists on local system
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
				#Write file contents to documents folder
				#Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				#New location for Files
				$newLocation = $this->docsLocation."/".$filename;
				#Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				#Store filesname
				$this->resourceFileNames[$i] = $filename;
				#Open images directory
				$fp = fopen($newLocation,'w');
				#Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				#Close the directory
				fclose($fp);
				#Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				#Check debug flag
				if($this->objDebug)
				{
					echo "Course"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
			#Image
			if(strcmp($objectType,"Image")==0)
			{
				#Retrieve filename
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				#Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				#Retrieve absolute file location
				$fileLocation = $folder."/".$file;
				#Chech if file exists on local system
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
				#Write file contents to images folder
				#Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				#Check filename
				if(!preg_match("/.jpg|.gif|.png/",$filename))
				{
					#Correct filename
					$filename = $filename.".jpg";
					$this->fileMod = TRUE;
					$this->objIEUtils->fileModOn();
				}
				#New location for Images
				$newLocation = $this->imagesLocation."/".$filename;
				#Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				#Store filesname
				$this->resourceFileNames[$i] = $filename;
				#Open images directory
				$fp = fopen($newLocation,'w');
				#Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				#Close the directory
				fclose($fp);
				#Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $this->contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				#Check debug flag
				if($this->objDebug)
				{
					echo "Image"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
		$i++;
		}
		if($this->objDebug)
		{
			var_dump($resourceFileLocations);
			var_dump($this->resourceFileNames);
		}

		return $this->allData;
	}

	public $imageIds;
	public $fileIds;
	/**
	 * 
	 * Writes all images used by course to new database (Chisimba) by default
	 * Can also write a specified directory of unknown files into database
	 * 
	 * Makes query to tbl_files
	 * 
	 * @param string $folder - selected course
	 * @return array $indexFolder - list of id fields belonging to images
	*/
	function uploadImagesToChisimba($folder = '', $fileNames = '')
	{
		#Initialize Inner variables
		parent::init('tbl_files');
		#Add Images to database
		if(!(strlen($folder) > 1))
			$indexFolder = $this->objIndex->indexFolder($this->imagesLocation, $this->objUser->userId());
		else
			$indexFolder = $this->objIndex->indexFolder($folder, $this->objUser->userId());
		#Match image Id's to image names
		foreach($indexFolder as $pageId)
		{
			$filter = "WHERE id = '$pageId'";
			$result = $this->getAll($filter);
			$aFile = $result['0']['filename'];
			if(!(strlen($folder) > 1))
				$this->imageIds[$aFile] = $pageId;
			else
				$this->fileIds[$aFile] = $pageId;
		}
		if(!(strlen($folder) > 1))
			return $this->imageIds;
		else
			return $this->fileIds;
	}

	/**
	 * 
	 * Function to return the order in which pages should be added
	 * 
	 * @param simpleXML object 
	 *
	 * @return array $titles 
	 * 
	*/
	function getStructure($xml)
	{
		$titles = array();
		$i = 0;
		foreach($xml->organizations->organization->item as $item)
		{
			$aTitle = (string)$item->title;
			$aTitle = trim($aTitle);
			$titles[$i] = $aTitle;
			$i++;
		}

		return $titles;
	}

	public $resourceIds;
	public $chapterIds;
	/**
	 * 
	 * Control loading resources into Chisimba
	 * and file manipulation functions
	 *
	 * @param array $writeData - all data needed
	 * @param array $structure - the order in which pages should be added
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
		#Pre-initialize variables
		static $i = 0;
		static $j = 0;
		static $k = 0;
		$menutitles = array();
		$orderedData = array();
		$numItems = count($structure);
		$numVisibleItems = 0;
		#Change Structure of data
		#Add all pages
		foreach($writeData as $resource)
		{
			#Unpack data
			$xmlResource = $resource['resource'];
			$objectType = $resource['objectType'];
			$objectType = (string)$objectType;
			$objectType = trim($objectType);
			$resourceId = (string)$xmlResource['identifier'];
			$resourceId = trim($resourceId);
			#Check file type
			if(strcmp($objectType,"Image")!=0)
			{
				#Retrieve title
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
					{
						$indexOfCourse = $index;
					}
				}
			}
		}
		#Fix indexing
		for($i=0;$i<count($orderedData);$i++)
		{
			$indexedData[$i] = $orderedData[$i];
		}
		$start = 0;
		$before = $indexOfCourse;
		$after = count($orderedData);
		$fromStart = array_slice($indexedData, $start, $before);
		$toEnd = array_slice($indexedData, $before+1, $after);
		$course = array_slice($indexedData, $before, $before);
		$orderedData = array_merge($course, $fromStart, $toEnd);
		#Retrieve resource Id's
		foreach($orderedData as $resource)
		{
			#Unpack data
			$xmlResource = $resource['resource'];
			$fileContents = $resource['fileContents'];
			$resourceId = (string)$xmlResource['identifier'];
			$resourceId = trim($resourceId);
			foreach($this->resourceFileNames as $aFile)
			{
				if($this->fileMod)
					$aFile = preg_replace("/.html|.htm|.jpg|.gif|.png/","",$aFile);
				$regex = '/(href=".*'.$aFile.'.*?")/i';
				preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
				if($matches)
				{
					$index = array_search($resourceId, $this->chapterIds);
					if($index === FALSE)
					{
					}
					else
					{
					}
				}
			}

			$this->resourceIds[$i] = $resourceId;
			$i++;
		}
		$this->addChapters();
		#Add ordered data
		for($i=0;$i<count($orderedData);$i++)
		{
			#Unpack data
			$xmlResource = $orderedData[$i]['resource'];
			$fileContents = $orderedData[$i]['fileContents'];
			$contextCode = $orderedData[$i]['contextCode'];
			$file = $orderedData[$i]['file'];
			$objectType = $orderedData[$i]['objectType'];
			#Cast to string
			$objectType = (string)$objectType;
			#Remove whitespaces for comparison
			$objectType = trim($objectType);
			#Write Course to Chisimba database
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

	public $chapterId;
	function addChapters()
	{
		#Add Chapters
		#Course
		$title = $this->contextCode;
		$intro = $this->newCourse['about'];
		$visibility = 'Y';
		$this->chapterId = $this->objChapters->addChapter('', $title, $intro);
		$result = $this->objContextChapters->addChapterToContext($this->chapterId, $title, $visibility);
		#Add additional Chapters
		
	}

	/**
	 * 
	 * Retrieve page details and pass data
	 * database insertion functions
	 *
	 * @param simpleXml $resource - simple xml object
	 * @param string $fileContents - html page
	 * @param string $contextCode - course contextcode
	 *
	 * @return boolean 
	 *
	*/
	function passPage($resource, $fileContents, $contextCode, $bookmark='', $isBookmark='')
	{
		#Check if menutitle exists
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
		#Retrieve page data
		$titleid = $resource->metadata->lom->general->title->langstring;
		if(!(strlen($menutitle) > 0))
			$menutitle = $resource->metadata->lom->general->description->langstring;
		$content = $fileContents;
		$language = $resource->metadata->lom->general->language;
		$headerscript = "";
		$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
		#Load to $values
		if(!strlen($menutitle) > 0)
		{
			$menutitle = $titleid;
		}
		$values = array('titleid' => (string)$titleid,
				'menutitle' => (string)$menutitle,
				'content' => (string)$content,
				'language' => (string)$language,
				'headerscript' => (string)$headerscript,
				'filename' => trim((string)$filename),
				'bookmark' => $bookmark,
				'isbookmark' => $isBookmark);
		#Insert into database
		$writePage = $this->writePage($values, $contextCode);

		return $menutitle;
	}

	/**
	 * Write content to Chisimba database
	 *
	 * @param array $values - page details
	 * @param string $contextCode - course contextcode
	 *
	 * @return boolean 
	 *
	*/
	function writePage($values, $contextCode)
	{
		#duplication error needs to be fixed by Tohir
		#parent::init('tbl_contextcontent_pages');
		#$menutitle = $values['menutitle'];
		#$filter = "WHERE menutitle = '$menutitle'";
		#$result = $this->getAll($filter);
		#un-comment to force no duplication
		#if(!count($result) > 1)
		#{
			#No idea!!!
			$tree = $this->objContentOrder->getTree($contextCode, 'dropdown', $parent);
			#Add page
        		$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'],
								$values['content'],
								$values['language'],
								$values['headerscript']);
        		$this->pageIds[$values['filename']] = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterId, $values['bookmark'], $values['isbookmark']);
		#}

		return TRUE;
	}

	/**
	 * 
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
		#switch tables
		parent::init('tbl_contextcontent_pages');
		#Retrieve resources
		#Manipulate images
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
					#Retrieve page contents
					$fileContents = $result['0']['pagecontent'];
					$id = $result['0']['id'];
					#Rewrite images source in html
					$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $this->resourceFileNames, $this->imageIds);
					#Rewrite links source in html
					$page = $this->objIEUtils->changeLinkUrl($page, $this->contextCode, $this->resourceFileNames, $this->pageIds);
					#Reinsert into database with updated images
					if(strlen($page) > 1 )
					{
						$update = $this->update('id', $id, array('pagecontent' => $page));
						if($i==0)
						{
							#Modify about in tbl_context
							parent::init('tbl_context');
							$this->update('id', $this->courseId, array('about' => $page));
							#switch tables
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
	 * 
	 * Locates all secondary xml files holding information,
	 * besides imsmanifest.xml
	 * @param $xpath - 
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
			{
//			echo $location.'<br />';
				$xmlFilesLocation['CourseHome'] = $location;
			}
			else if(preg_match('/Syllabus/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Syllabus'] = $location;
			}
			else if(preg_match('/Calendar/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Calendar'] = $location;
			}
			else if(preg_match('/Readings/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Readings'] = $location;
			}
			else if(preg_match('/Readings/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Readings'] = $location;
			}
			else if(preg_match('/LectureNotes/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['LectureNotes'] = $location;
			}
			else if(preg_match('/Assignments/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Assignments'] = $location;
			}
			else if(preg_match('/Exams/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Exams'] = $location;
			}
			else if(preg_match('/Projects/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Projects'] = $location;
			}
			else if(preg_match('/Tools/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['Tools'] = $location;
			}
			else if(preg_match('/RelatedResources/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['RelatedResources'] = $location;
			}
			else if(preg_match('/DiscussionGroup/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['DiscussionGroup'] = $location;
			}
			else if(preg_match('/DownloadthisCourse/', $location))
			{
//			echo $location.'<br />';
				$xmlFilesLocation['DownloadthisCourse'] = $location;
			}
			$j++;
		}

		return $xmlFilesLocation;
	}

	/**
	 * Finds the relative location of all files relevant in MIT package
	 * 
	 * @param array $xpath
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
	 * @param string $folder
	 * @param array $xmlFilesLocation
	 *
	 * @return 
	 *  
	*/
	function loadAllXmlFiles($folder, $xmlFilesLocation)
	{
		if(strlen($xmlFilesLocation['CourseHome'])>1)
		{
			#Read Course Home
			#Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['CourseHome'];
			$courseSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$courseDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$courseXpath = $this->loadXPath($courseDomDocument);
			$allXmlPackageData['CourseHome']['simple'] = $courseSimpleXml;
			$allXmlPackageData['CourseHome']['dom'] = $courseDomDocument;
			$allXmlPackageData['CourseHome']['xpath'] = $courseXpath;
		}
		if(strlen($xmlFilesLocation['Syllabus'])>1)
		{
			#Read Syllabus
			$location = $folder.'/'.$xmlFilesLocation['Syllabus'];
			$syllabusSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$syllabusDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$syllabusXpath = $this->loadXPath($syllabusDomDocument);
			$allXmlPackageData['Syllabus']['simple'] = $syllabusSimpleXml;
			$allXmlPackageData['Syllabus']['dom'] = $syllabusDomDocument;
			$allXmlPackageData['Syllabus']['xpath'] = $syllabusXpath;
		}
		if(strlen($xmlFilesLocation['Syllabus'])>1)
		{
			#Read Calendar
			$location = $folder.'/'.$xmlFilesLocation['Calendar'];
			$calendarSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$calendarDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$calendarXpath = $this->loadXPath($calendarDomDocument);
			$allXmlPackageData['Calendar']['simple'] = $calendarSimpleXml;
			$allXmlPackageData['Calendar']['dom'] = $calendarDomDocument;
			$allXmlPackageData['Calendar']['xpath'] = $calendarXpath;
		}
		if(strlen($xmlFilesLocation['Readings'])>1)
		{
			#Read Readings
			$location = $folder.'/'.$xmlFilesLocation['Readings'];
			$readingsSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$readingsDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml files
			$readingsXpath = $this->loadXPath($readingsDomDocument);
			$allXmlPackageData['Readings']['simple'] = $readingsSimpleXml;
			$allXmlPackageData['Readings']['dom'] = $readingsDomDocument;
			$allXmlPackageData['Readings']['xpath'] = $readingsXpath;
		}
		if(strlen($xmlFilesLocation['LectureNotes'])>1)
		{
			#Read Course Home
			#Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['LectureNotes'];
			$lectureSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$courseDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$lectureXpath = $this->loadXPath($lectureDomDocument);
			$allXmlPackageData['LectureNotes']['simple'] = $lectureSimpleXml;
			$allXmlPackageData['LectureNotes']['dom'] = $lectureDomDocument;
			$allXmlPackageData['LectureNotes']['xpath'] = $lectureXpath;
		}
		if(strlen($xmlFilesLocation['Labs'])>1)
		{
			#Read Labs
			$location = $folder.'/'.$xmlFilesLocation['Labs'];
			$labsSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$labsDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$labsXpath = $this->loadXPath($labsDomDocument);
			$allXmlPackageData['Labs']['simple'] = $labsSimpleXml;
			$allXmlPackageData['Labs']['dom'] = $labsDomDocument;
			$allXmlPackageData['Labs']['xpath'] = $labsXpath;
		}
		if(strlen($xmlFilesLocation['Assignments'])>1)
		{
			#Read Assignments
			$location = $folder.'/'.$xmlFilesLocation['Assignments'];
			$assignmentsSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$assignmentsDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$assignmentsXpath = $this->loadXPath($assignmentsDomDocument);
			$allXmlPackageData['Assignments']['simple'] = $assignmentsSimpleXml;
			$allXmlPackageData['Assignments']['dom'] = $assignmentsDomDocument;
			$allXmlPackageData['Assignments']['xpath'] = $assignmentsXpath;
		}
		if(strlen($xmlFilesLocation['Exams'])>1)
		{
			#Read Course Home
			#Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['Exams'];
			$lectureSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$courseDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$lectureXpath = $this->loadXPath($lectureDomDocument);
			$allXmlPackageData['Exams']['simple'] = $lectureSimpleXml;
			$allXmlPackageData['Exams']['dom'] = $lectureDomDocument;
			$allXmlPackageData['Exams']['xpath'] = $lectureXpath;
		}
		if(strlen($xmlFilesLocation['Projects'])>1)
		{
			#Read Projects
			$location = $folder.'/'.$xmlFilesLocation['Projects'];
			$projectsSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$projectsDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$projectsXpath = $this->loadXPath($projectsDomDocument);
			$allXmlPackageData['Projects']['simple'] = $projectsSimpleXml;
			$allXmlPackageData['Projects']['dom'] = $projectsDomDocument;
			$allXmlPackageData['Projects']['xpath'] = $projectsXpath;
		}
		if(strlen($xmlFilesLocation['Tools'])>1)
		{
			#Read Course Home
			#Create simplexml object to access xml file
			$location = $folder.'/'.$xmlFilesLocation['Exams'];
			$lectureSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$courseDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$lectureXpath = $this->loadXPath($lectureDomDocument);
			$allXmlPackageData['Tools']['simple'] = $lectureSimpleXml;
			$allXmlPackageData['Tools']['dom'] = $lectureDomDocument;
			$allXmlPackageData['Tools']['xpath'] = $lectureXpath;
		}
		if(strlen($xmlFilesLocation['RelatedResources'])>1)
		{
			#Read Related
			$location = $folder.'/'.$xmlFilesLocation['RelatedResources'];
			$relatedSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$relatedDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$relatedXpath = $this->loadXPath($relatedDomDocument);
			$allXmlPackageData['RelatedResources']['simple'] = $relatedSimpleXml;
			$allXmlPackageData['RelatedResources']['dom'] = $relatedDomDocument;
			$allXmlPackageData['RelatedResources']['xpath'] = $relatedXpath;
		}
		if(strlen($xmlFilesLocation['DiscussionGroup'])>1)
		{
			#Read Discussion Group
			$location = $folder.'/'.$xmlFilesLocation['DiscussionGroup'];
			$discussionSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$discussionDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
			$discussionXpath = $this->loadXPath($discussionDomDocument);
			$allXmlPackageData['DiscussionGroup']['simple'] = $discussionSimpleXml;
			$allXmlPackageData['DiscussionGroup']['dom'] = $discussionDomDocument;
			$allXmlPackageData['DiscussionGroup']['xpath'] = $discussionXpath;
		}
		if(strlen($xmlFilesLocation['DownloadthisCourse'])>1)
		{
			#Read Download this Course
			$location = $folder.'/'.$xmlFilesLocation['DownloadthisCourse'];
			$downloadSimpleXml = $this->loadSimpleXML($location);
			#Create domdocument object to access xml file	
			$downloadDomDocument = $this->loadDOMDocument($location);
			#Create xpath object to access xml file
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
	 * @param string $newFolder
	 * @param array $allXmlPackageData
	 *
	 * @return 
	 *  
	*/
	function getMITHtmls($newFolder, $allXmlPackageData)
	{
		#Read Course Home
		$courseXpath = $allXmlPackageData['CourseHome']['xpath'];
		if(strlen($courseXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $courseXpath->evaluate($query);
			$coursePath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['CourseHome'] = $newFolder.$coursePath;
			#Retrieve contents of file
			$fileContents = file_get_contents($mitHtmlsPath['CourseHome']);
			#New location for Files
			$newLocation = $this->docsLocation."/".'CourseHome.html';
			#Open html directory
			$fp = fopen($newLocation,'w');
			#Write the file to static directory
			if((fwrite($fp, $newContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			#Close the directory
			fclose($fp);
		}
		#Read Syllabus
		$syllabusXpath = $allXmlPackageData['Syllabus']['xpath'];
		if(strlen($syllabusXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $syllabusXpath->evaluate($query);
			$syllabusPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Syllabus'] = $newFolder.$syllabusPath;
			$fileContents = file_get_contents($mitHtmlsPath['Syllabus']);
			$newLocation = $this->docsLocation."/".'Syllabus.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Calendar
		$calendarXpath = $allXmlPackageData['Calendar']['xpath'];
		if(strlen($calendarXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $calendarXpath->evaluate($query);
			$calendarPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Calendar'] = $newFolder.$calendarPath;
			$fileContents = file_get_contents($mitHtmlsPath['Calendar']);
			$newLocation = $this->docsLocation."/".'Calendar.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Readings
		$readingsXpath = $allXmlPackageData['Readings']['xpath'];
		if(strlen($readingsXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $readingsXpath->evaluate($query);
			$readingsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Readings'] = $newFolder.$readingsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Readings']);
			$newLocation = $this->docsLocation."/".'Readings.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Lecture Notes
		$lectureXpath = $allXmlPackageData['LectureNotes']['xpath'];
		if(strlen($lectureXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $lectureXpath->evaluate($query);
			$lecturePath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['LectureNotes'] = $newFolder.$lecturePath;
			$fileContents = file_get_contents($mitHtmlsPath['LectureNotes']);
			$newLocation = $this->docsLocation."/".'LectureNotes.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Labs
		$labsXpath = $allXmlPackageData['Labs']['xpath'];
		if(strlen($labsXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $labsXpath->evaluate($query);
			$labsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Labs'] = $newFolder.$labsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Labs']);
			$newLocation = $this->docsLocation."/".'Labs.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Assignments
		$assignmentsXpath = $allXmlPackageData['Assignments']['xpath'];
		if(strlen($assignmentsXpath) > 1)
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
		#Read Exams
		$examsXpath = $allXmlPackageData['Exams']['xpath'];
		if(strlen($examsXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $examsXpath->evaluate($query);
			$examsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Exams'] = $newFolder.$examsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Exams']);
			$newLocation = $this->docsLocation."/".'Exams.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Projects
		$projectsXpath = $allXmlPackageData['Projects']['xpath'];
		if(strlen($projectsXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $projectsXpath->evaluate($query);
			$projectsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Projects'] = $newFolder.$projectsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Projects']);
			$newLocation = $this->docsLocation."/".'Projects.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Tools
		$toolsXpath = $allXmlPackageData['Tools']['xpath'];
		if(strlen($toolsXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $examsXpath->evaluate($query);
			$toolsPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['Tools'] = $newFolder.$toolsPath;
			$fileContents = file_get_contents($mitHtmlsPath['Tools']);
			$newLocation = $this->docsLocation."/".'Tools.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Resources
		$resourcesXpath = $allXmlPackageData['RelatedResources']['xpath'];
		if(strlen($resourcesXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $resourcesXpath->evaluate($query);
			$resourcesPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['RelatedResources'] = $newFolder.$resourcesPath;
			$fileContents = file_get_contents($mitHtmlsPath['RelatedResources']);
			$newLocation = $this->docsLocation."/".'RelatedResources.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Discussion Group
		$discussionXpath = $allXmlPackageData['DiscussionGroup']['xpath'];
		if(strlen($discussionXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $discussionXpath->evaluate($query);
			$discussionPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['DiscussionGroup'] = $newFolder.$discussionPath;
			$fileContents = file_get_contents($mitHtmlsPath['DiscussionGroup']);
			$newLocation = $this->docsLocation."/".'DiscussionGroup.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}
		#Read Download this Course
		$downloadXpath = $allXmlPackageData['DownloadthisCourse']['xpath'];
		if(strlen($downloadXpath) > 1)
		{
			$query = '//lom:technical/lom:location';
			$results = $downloadXpath->evaluate($query);
			$downloadPath = trim((string)$results->item(0)->nodeValue);
			$mitHtmlsPath['DownloadthisCourse'] = $newFolder.$downloadPath;
			$fileContents = file_get_contents($mitHtmlsPath['DownloadthisCourse']);
			$newLocation = $this->docsLocation."/".'DownloadthisCourse.html';
			$fp = fopen($newLocation,'w');
			if((fwrite($fp, $fileContents) === FALSE))
			{
				return  "writeResourcesError";
			}
			fclose($fp);
		}

		return $mitHtmlsPath;
	}

	/**
	 * Load all html pages to chisimba
	 *
	 * @param array $mitHtmlsPath 
	 * @param array $allXmlPackageData
	 *
	 * @return 
	 *
	*/
	function loadToChisimbaFromPaths($mitHtmlsPath, $allXmlPackageData)
	{
		$menutitles = array();
		$this->addChapters();
		static $i = 0;
		foreach($mitHtmlsPath as $htmlPath)
		{
			$fileContents = file_get_contents($htmlPath);
			#Strip navigational tags
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
			else if(preg_match('/Projects/', $htmlPath))
			{
				$menutitle = 'Projects';
				$filename = 'Projects.html';
			}
			else if(preg_match('/Resources/', $htmlPath))
			{
				$menutitle = 'Resources';
				$filename = 'Resources.html';
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
			#No idea!!!
			$tree = $this->objContentOrder->getTree($this->contextCode, 'dropdown', $parent);
			#Add page
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
	 * @param string $newFolder
	 * @param array $allFilesLocation
	 * @param array $fileNames
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
	 * @param string $newFolder
	 * @param array $allFilesLocation
	 * @param array $fileNames
	 * 
	*/
	function writeMITFiles($newFolder='', $allFilesLocation, $fileNames='')
	{
		static $i = 0;
		foreach($allFilesLocation as $fileLocation)
		{
			foreach($fileNames as $fileName)
			{
				if(preg_match("/$fileName/",$fileLocation))
				{
					if(!(preg_match("/.txt|.htm|.html|.xml|.css|.js|.jpg|.gif|.png/",$fileLocation)))
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
					}
				}
			}
		}

		return $filePaths;
	}

	/**
	 * Removes all navigational tags from MIT htmls
	 * 
	 * @param string $fileContents
	 *
	 * @return string $fileContents
	*/
	function removeMITLayouts($fileContents)
	{

		//Strip skip to content div (working)
		$fileContents = preg_replace('/skip to content/','',$fileContents);
		//Strip Header div
		$fileContents = preg_replace('%<div class="header-redline(.|\\n)+?<!-- end header-redline -->%','',$fileContents);
		//Strip site tools div (working)
		$fileContents = preg_replace('%<div class="sitetools(.|\\n)+?</div>%','',$fileContents);
		//Strip left navigation div (working)
		$fileContents = preg_replace('/<!-- LeftNav(.|\\n)+?<!-- End LeftNav -->/','',$fileContents);
		//Strip search-form div (working)
		$fileContents = preg_replace('/<!-- begin search(.|\\n)+?<!-- end search area -->/','',$fileContents);
		//Strip main content div (not working)
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
	 *
	 * @return TRUE - on successful execution
	 *
	*/
	function rebuildMITHtml($menutitles, $fileNames)
	{
		#switch tables
		parent::init('tbl_contextcontent_pages');
		#Retrieve resources
		#Manipulate images
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
					#Retrieve page contents
					$fileContents = $result['0']['pagecontent'];
					$id = $result['0']['id'];
					#Rewrite images source in html
					$page = $this->objIEUtils->changeImageSRC($fileContents, $this->contextCode, $fileNames, $this->imageIds);
					#Rewrite links source in html
					$page = $this->objIEUtils->changeMITLinkUrl($page, $this->contextCode, $fileNames, $this->fileIds);
					$page = $this->objIEUtils->changeLinkUrl($page, $this->contextCode, $fileNames, $this->pageIds);
					#Reinsert into database with updated images
					if(strlen($page) > 1 )
					{
						$update = $this->update('id', $id, array('pagecontent' => $page));
						if($i==0)
						{
							#For some strange reason we have to enter context here
							$enterContext = $this->objDBContext->joinContext($this->contextCode);
							#Modify about in tbl_context
							parent::init('tbl_context');
							$this->update('id', $this->courseId, array('about' => $page));
							#switch tables
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