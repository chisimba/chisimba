<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
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
		//Load Filemanager class
		$this->objIndex =& $this->getObject('indexfileprocessor', 'filemanager');
		$this->objUpload =& $this->getObject('upload', 'filemanager');
		//Load System classes
		$this->objConfig = & $this->newObject('altconfig','config');
        	$this->objLanguage = & $this->newObject('language', 'language');
        	$this->objDBContext = & $this->newObject('dbcontext', 'context');
        	$this->objUser =& $this->getObject('user', 'security');
		//Load Inner classes
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
		//Load Chapter Classes
		$this->objChapters =& $this->getObject('db_contextcontent_chapters','contextcontent');
		$this->objContextChapters =& $this->getObject('db_contextcontent_contextchapter','contextcontent');
		//Load context classes
        	$this->objContentPages =& $this->getObject('db_contextcontent_pages','contextcontent');
	        $this->objContentOrder =& $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles =& $this->getObject('db_contextcontent_titles','contextcontent');
	        $this->objContentInvolvement =& $this->getObject('db_contextcontent_involvement','contextcontent');
		$this->pageIds = array();
		//Initialize Flags
		$this->fileMod = FALSE;
		$this->objDebug = FALSE;
//		$this->objDebug = TRUE;
	}
	
	public $courseId;
	/**
	 * Controls the process for import IMS specification content
	 * Calls all necessary functions an does error checking
	 * 
	 * @param $_FILES global - uploaded file
	 *
	 * @return TRUE - Successful execution
	 * 
	*/
	function importIMScontent($FILES)
	{
		$this->fileMod = FALSE;
		$this->courseId = '';
		if(!isset($FILES))
		{
			return  "fileError";
		}
		//Retrieve temp folder
		$folder = $this->unzipIMSFile($FILES);
		if(!isset($folder))
		{
			return  "folderError";
		}
		//Retrieve file names
		$fileNames = $this->objIEUtils->list_dir_files($folder,0);
		if(!isset($fileNames))
		{
			return  "fileError";
		}
		//Retrieve file locations
		$filesLocation = $this->locateAllFiles($folder);
		if(!isset($filesLocation))
		{
			return  "fileError";
		}
		//Locate imsmanifest.xml file
		$imsFileLocation = $this->locateIMSfile($filesLocation, "/imsmanifest/");
		if(!isset($imsFileLocation))
		{
			return  "imsReadError";
		}
		//Read imsmanifest.xml file
		//Create simplexml object to access xml file
		$simpleXmlObj = $this->loadSimpleXML($imsFileLocation);
		if(!isset($simpleXmlObj))
		{
			return  "simpleXmlError";
		}
		//Create domdocument object to access xml file	
		$domDocumentObj = $this->loadDOMDocument($imsFileLocation);
		if(!isset($domDocumentObj))
		{
			return  "domError";
		}
		//Create xpath object to access xml file
		$xpathObj = $this->loadXPath($domDocumentObj);
		if(!isset($xpathObj))
		{
			return  "xpathError";
		}
		//Extract course data
		$courseData = $this->extractCourseData($simpleXmlObj, $domDocumentObj, $xpathObj);
		if(!isset($courseData))
		{
			return  "courseReadError";
		}
		//Initialize all locations
		$init = $this->initLocations($courseData['contextcode']);
		if(!isset($init))
		{
			return  "initializeError";
		}
		//Create course
		$courseCreated= $this->objIEUtils->createCourseInChisimba($courseData);
		$this->courseId = $courseCreated;
		if(!isset($courseCreated))
		{
			return  "courseCreateError";
		}
		//Write Resources
		$writeData = $this->writeResources($simpleXmlObj, $folder, $courseData);
		if(!isset($writeData))
		{
			return  "writeResourcesError";
		}
		//Get organizations
		$structure = $this->getStructure($simpleXmlObj);
		if(!isset($structure))
		{
			return  "noStructureError";
		}
		//Load html data into Chisimba
		$loadData = $this->loadToChisimba($writeData, $structure);
		if(!isset($loadData))
		{
			return  "loadDataError";
		}
		//Load image data into Chisimba
		$uploadImagesToChisimba = $this->uploadImagesToChisimba($folder);
		if(!isset($uploadImagesToChisimba))
		{
			return  "uploadError";
		}
		//Rebuild html images and url links
		$rebuildHtml = $this->rebuildHtml($loadData,$fileNames);
		if(!isset($rebuildHtml))
		{
			return  "rebuildHtmlError";
		}

		return TRUE;
	}

	public $contentBasePath;
	public $courseContentBasePath;
	public $contextCode;
	public $courseContentPath;
	public $imagesLocation;
	public $docsLocation;
	/**
	 * 
	 * 
	 * 
	*/
	function initLocations($contextcode)
	{
		//Pre-check parameters
		if(!isset($contextcode))
		{
			return  "precheckError";
		}
		//Pre-Initialize global variables
		$this->contentBasePath = '';
		$this->courseContentBasePath = '';
		$this->contextCode = '';
		$this->courseContentPath = '';
		$this->imagesLocation = '';
		$this->docsLocation = '';
		//Static Chisimba file locations
		//opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$this->contentBasePath = $this->objConfig->getcontentBasePath();
		$this->courseContentBasePath = $this->contentBasePath."content/";
		$this->contextCode = strtolower(str_replace(' ','_',$contextcode));
		$this->courseContentPath = $this->courseContentBasePath.$this->contextCode;
		$this->imagesLocation = $this->courseContentPath."/images";
		$this->docsLocation = $this->courseContentPath."/documents";
		$locations = array('contentBasePath' => $this->contentBasePath,
					'courseContentBasePath' => $this->courseContentBasePath,
					'contextCode' => $this->contextCode,
					'courseContentPath' => $this->courseContentPath,
					'imagesLocation' => $this->imagesLocation,
					'docsLocation' => $this->docsLocation
					);
		if($this->objDebug)
		{
			echo $contentBasePath."<br />";
			echo $courseContentBasePath."<br />";
			echo $contextCode."<br />";
			echo $courseContentPath."<br />";
			echo $imagesLocation."<br />";
			echo $docsLocation."<br />";
		}

		return $locations;
	}

	/**
	 * @var object $folder - relative path of temporary folder
	*/
	public $folder;
	/**
	 * Function to unzip an uploaded zip file
	 * 
	 * @param $_FILES global - Location of uploaded zip-file
	 *
	 * @return string $folder - Temp extraction folder location
	 * 
	*/
	function unzipIMSFile($FILES)
	{
		$this->folder = '';
		if (isset($FILES['upload']))
		{
			if(!is_uploaded_file($FILES['upload']['tmp_name']))
			{
				return "error";
			}
			else if ($FILES['upload']['error'] != UPLOAD_ERR_OK)
			{
				return "error";
			}
			else
			{
				$type = $FILES['upload']['type'];
				$name = $FILES['upload']['name'];
				$name = preg_replace('/^(.*)\.php$/i', '\\1.phps', $name);
                    		for ($i=0;$i<strlen($name);$i++) 
				{
                        		if ($name{$i} == ' ') 
					{
                            			$name{$i} = '_';
                        		}
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
					$tempfile=$FILES['upload']['tmp_name'];
					$tempdir=substr($tempfile,0,strrpos($tempfile,'/'));
					$objDir=&$this->getObject('dircreate','utilities');
					$objDir->makeFolder("$name.unzip",$tempdir);
					$this->folder="$tempdir/$name";
					$objWZip=&$this->getObject('wzip','utilities');
					$objWZip->unzip($tempfile,$this->folder);
				}

			}
		}

	return $this->folder;
	}

	/**
	 * @var object $fileLocations - relative paths to all files
	*/
	public $fileLocations;
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
		$this->fileLocations = array();
		$this->fileLocations = $this->objIEUtils->list_dir_files($folder,1);
	
		return $this->fileLocations;
	}

	/**
	 * @var object $imsmanifestLocation - relative paths to IMS manifest file
	*/
	public $imsmanifestLocation;
	/**
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
		$this->imsmanifestLocation = "";
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
	 * Takes the imsmanifest.xml file as input
	 *
	 * @param string $imsFileLocation
	 *
	 * @return simpleXml $simpleXmlObj
	 *
	*/
	function loadSimpleXml($imsFileLocation)
	{
		$this->simpleXmlObj = array();
		//Load imsmanifest.xml file
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
		//Create domdocument object to access xml file
		if($this->domDocumentObj->load($imsFileLocation))
		{
		}
		else 
		{
    			return  "domError";
		}

		return $this->domDocumentObj;
	}

	/**
	 * @var object $xpathObj - xpath object to access xml file
	*/
	public $xpathObj;
	/**
	 * Takes a DOM document as input
	 *
	 * @param DOMDocument $domDocumentObj
	 *
	 * @return DOMXPath $domDocumentObj
	 *
	*/
	function loadXPath($domDocumentObj)
	{
		//Create xpath object to access xml file
		$this->xpathObj = new DOMXPath($domDocumentObj);

		return $this->xpathObj;
	}

	/**
	 * @var object $newCourse - all course data needed to create course
	*/
	public $newCourse;
	/**
	 * Extract the course information from imsmanifest.xml
	 *
	 * @return TRUE - Successful execution
	 *
	*/
	function extractCourseData($xml, $doc, $xpath)
	{
		$this->newCourse = array();
		//Set eduCommons namespaces
		$xpath->registerNamespace("educommons", "http://albatross.ed.usu.edu/xsd/educommons_v1");
		//Set imsmd namespaces
		$xpath->registerNamespace("imsmd", "http://www.imsglobal.org/xsd/imsmd_v1p2");
		//Create course
		//Establish which resource is a course
		foreach($xml->resources->resource as $resource)
		{
			//Retrieve file type
			$objectType = $resource->metadata->eduCommons->objectType;
			//Cast to string
			$objectType = (string)$objectType;
			//Remove whitespaces for comparison
			$objectType = trim($objectType);
			//Check file type
			//Course
			if(strcmp($objectType,"Course")==0)
			{
#course id (contextcode)
				$courseId = $resource->metadata->eduCommons->courseId;
				$courseId = (string)$courseId;
				$courseId = trim($courseId);
				$this->newCourse['contextcode'] = $courseId;
#course title (title)
				$courseTitle = $resource->metadata->lom->general->title->langstring;
				$courseTitle = (string)$courseTitle;
				$courseTitle = trim($courseTitle);
				$this->newCourse['title'] = $courseTitle;
#course title (menu text)
				$this->newCourse['menutext'] = $courseTitle;
#course title (userId)
				$this->newCourse['userid'] = "1";
#course identifier (not in use)
				$courseIdentifier = $resource['identifier'];
				$courseIdentifier = (string)$courseIdentifier;
				$courseIdentifier = trim($courseIdentifier);
				$this->newCourse['courseIdentifier'] = $courseIdentifier;
#course description (about)
				$courseDescription = $resource->metadata->lom->general->description->langstring;
				$courseDescription = (string)$courseDescription;
				$courseDescription = trim($courseDescription);
				$this->newCourse['about'] = $courseDescription;
				if(!(strlen($courseDescription) > 1))
					$this->newCourse['about'] = "Description Not Available";
#course status (status)
				$courseStatus = "Public";
				$this->newCourse['isactive'] = $courseStatus;
#course access (access)
				$courseAccess = "UnPublished";
				$this->newCourse['isclosed'] = $courseAccess;
			//echo "Course";
			}
			//Document
			if(strcmp($objectType,"Document")==0)
			{
				//echo "Document";
			}
			//File
			if(strcmp($objectType,"File")==0)
			{
				//echo "File";
			}
			//Image
			if(strcmp($objectType,"Image")==0)
			{
				//echo "Image";
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
		//Pre-initialize globals
		$this->resourceFileNames = array();
		$this->allData = array();
		//Pre-initialize variables
		$resourceFileLocations = array();
		//Pre-check parameters
		if(!isset($xml))
		{
			return  "precheckError";
		}
		if(!isset($folder))
		{
			return  "precheckError";
		}
		if(!isset($newCourse))
		{
			return  "precheckError";
		}
		//Static Chisimba file locations
		//opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$contentBasePath = $this->objConfig->getcontentBasePath();
		$userBasePath = $this->objConfig->getsiteRootPath();
		$courseContentBasePath = $contentBasePath."content/";
		$contextCode = strtolower(str_replace(' ','_',$newCourse['contextcode']));
		//Store context code globally
		$this->contextCode = $contextCode;
		$courseContentPath = $courseContentBasePath.$contextCode;
		$imagesLocation = $courseContentPath."/images";
		$docsLocation = $courseContentPath."/documents";
		$userFilePath = $userBasePath.$this->objUser->userId();
		//Enter context
		$enterContext = $this->objDBContext->joinContext($contextCode);
		if($this->objDebug)
		{
			echo $contentBasePath."<br />";
			echo $courseContentBasePath."<br />";
			echo $contextCode."<br />";
			echo $courseContentPath."<br />";
			echo $imagesLocation."<br />";
			echo $docsLocation."<br />";
		}
		//First add course to Chisimba database
		foreach($xml->resources->resource as $resource)
		{
			//Retrieve file type
			$objectType = $resource->metadata->eduCommons->objectType;
			//Cast to string
			$objectType = (string)$objectType;
			//Remove whitespaces for comparison
			$objectType = trim($objectType);
			//Check file type
			//Course
			if(strcmp($objectType,"Course")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				//Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				//Chech if file exists on local system
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
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//Save all data
				$this->allData['0'] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				//Check debug flag
				if($this->objDebug)
				{
					echo "Course"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
		}
		//Add all other resources to Chisimba database
		foreach($xml->resources->resource as $resource)
		{
			static $i = 1;
			//Retrieve file type
			$objectType = $resource->metadata->eduCommons->objectType;
			//Cast to string
			$objectType = (string)$objectType;
			//Remove whitespaces for comparison
			$objectType = trim($objectType);
			//Check file type
			//Document
			if(strcmp($objectType,"Document")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				//Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				//Chech if file exists on local system
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
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//Check filename
				if(!preg_match("/.html|.htm/",$filename))
				{
					//Correct filename
					$filename = $filename.".html";
					$this->fileMod = TRUE;
				}
				//New location for Documents
				$newLocation = $docsLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$this->resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents, 
							'contextCode' => $contextCode, 
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				//Check debug flag
				if($this->objDebug)
				{
					echo "Document"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
			//File
			if(strcmp($objectType,"File")==0)
			{
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				//Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				$fileLocation = $folder."/".$file;
				//Chech if file exists on local system
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
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Files
				$newLocation = $docsLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$this->resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				//Check debug flag
				if($this->objDebug)
				{
					echo "Course"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
			//Image
			if(strcmp($objectType,"Image")==0)
			{
				//Retrieve filename
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				$filename = (string)$filename;
				$filename = trim($filename);
				//Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				//Retrieve absolute file location
				$fileLocation = $folder."/".$file;
				//Chech if file exists on local system
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
				//Write file contents to images folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//Check filename
				if(!preg_match("/.jpg|.gif|.png/",$filename))
				{
					//Correct filename
					$filename = $filename.".jpg";
					$this->fileMod = TRUE;
				}
				//New location for Images
				$newLocation = $imagesLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$this->resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Save all data
				$this->allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $contextCode,
							'file' => $file,
							'objectType' => $objectType,
							'filename' => $filename);
				//Check debug flag
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
	/**
	 * Writes all images used by course to new database (Chisimba)
	 * Makes query to tbl_files
	 * 
	 * @param string $folder - selected course
	 * @return array $indexFolder - list of id fields belonging to images
	*/
	function uploadImagesToChisimba($folder = '')
	{
		//Pre-Initialize global variables
		$this->imageIds = array();
		//Initialize Inner variables
		parent::init('tbl_files');
		//Add Images to database
		$indexFolder = $this->objIndex->indexFolder($this->imagesLocation, $this->objUser->userId());
		//Match image Id's to image names
		foreach($indexFolder as $pageId)
		{
			$filter = "WHERE id = '$pageId'";
			$result = $this->getAll($filter);
			$aFile = $result['0']['filename'];
			$this->imageIds[$aFile] = $pageId;
		}

		return TRUE;
	}

	/**
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
	 * Control loading resources into Chisimba
	 * and file manipulation functions
	 *
	 * @param array $writeData - all data needed
	 * @param array $structure - the order in which pages should be added
	 *
	 * @return array $menutitles - all menutitles of pages
	 *
	*/
	function loadToChisimba($writeData, $structure)
	{
		//Pre-Initialize global variables
		$this->resourceIds = array();
		$this->chapterIds = array();
		//Pre-initialize variables
		static $i = 0;
		static $j = 0;
		static $k = 0;
		$menutitles = array();
		$orderedData = array();
		$numItems = count($structure);
		//Change Structure of data
		//Add all pages
		foreach($writeData as $resource)
		{
			//Unpack data
			$xmlResource = $resource['resource'];
			$objectType = $resource['objectType'];
			$objectType = (string)$objectType;
			$objectType = trim($objectType);
			$resourceId = (string)$xmlResource['identifier'];
			$resourceId = trim($resourceId);
			//Check file type
			if(strcmp($objectType,"Image")!=0)
			{
				//Retrieve title
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
					if(strcmp($objectType,"Course")==0)
					{
						$indexOfCourse = $index;
					}
				}
			}
		}
		//Move course info to the start
		$start = 0;
		$before = $indexOfCourse;
		$after = count($orderedData);
		$fromStart = array_slice($orderedData, $start, $before);
		$toEnd = array_slice($orderedData, $before+1, $after);
		$course = array_slice($orderedData, $before, $before);
		$orderedData = array_merge($course, $fromStart, $toEnd);
		//Retrieve resource Id's
		foreach($orderedData as $resource)
		{
			//Unpack data
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
		//Add ordered data
		for($i=0;$i<count($orderedData);$i++)
		{
			//Unpack data
			$xmlResource = $orderedData[$i]['resource'];
			$fileContents = $orderedData[$i]['fileContents'];
			$contextCode = $orderedData[$i]['contextCode'];
			$file = $orderedData[$i]['file'];
			$objectType = $orderedData[$i]['objectType'];
			//Cast to string
			$objectType = (string)$objectType;
			//Remove whitespaces for comparison
			$objectType = trim($objectType);
			//Write Course to Chisimba database
			$menutitle = $this->passPage($xmlResource, $fileContents, $contextCode);
			$menutitle = (string)$menutitle;
			$menutitles[$i] = $menutitle;
//var_dump($orderedData[0]);
//echo $objectType."<br />";die;
		}

		return $menutitles;
	}

	function addChapters()
	{
		//Add Chapters
		//Course
		$title = $this->contextCode;
		$intro = $this->newCourse['about'];
		$visibility = 'Y';
		$this->chapterIds = $this->objChapters->addChapter('', $title, $intro);
		$result = $this->objContextChapters->addChapterToContext($this->chapterIds, $title, $visibility);
		//Add additional Chapters
		
	}

	/**
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
	function passPage($resource, $fileContents, $contextCode)
	{
		//Check if menutitle exists
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
		//Retrieve page data
		$titleid = $resource->metadata->lom->general->title->langstring;
		if(!(strlen($menutitle) > 0))
			$menutitle = $resource->metadata->lom->general->description->langstring;
		$content = $fileContents;
		$language = $resource->metadata->lom->general->language;
		$headerscript = "";
		$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
		//Load to $values
		if(!strlen($menutitle) > 0)
		{
			$menutitle = $titleid;
		}
		$values = array('titleid' => (string)$titleid,
				'menutitle' => (string)$menutitle,
				'content' => (string)$content,
				'language' => (string)$language,
				'headerscript' => (string)$headerscript,
				'filename' => trim((string)$filename));
		//Insert into database
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
		//duplication error needs to be fixed by Tohir
		parent::init('tbl_contextcontent_pages');
		$menutitle = $values['menutitle'];
		$filter = "WHERE menutitle = '$menutitle'";
		$result = $this->getAll($filter);
		//force a write
		//$result = '';
		if(!count($result) > 0)
		{
			//No idea!!!
			$tree = $this->objContentOrder->getTree($contextCode, 'dropdown', $parent);
			//Add page
        		$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'],
								$values['content'],
								$values['language'],
								$values['headerscript']);
        		$this->pageIds[$values['filename']] = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode, $this->chapterIds);
		}

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
		//switch tables
		parent::init('tbl_contextcontent_pages');
		//Retrieve resources
		//Manipulate images
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				//Retrieve page contents
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				//Rewrite images source in html
				$page = $this->changeImageSRC($fileContents, $this->contextCode, $fileNames);
				//Reinsert into database with updated images
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
				}
			}
		}
		//Manipulate links 
		foreach($menutitles as $menutitle)
		{
			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				//Retrieve page contents
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				//Rewrite links source in html
				$page = $this->changeLinkUrl($fileContents, $this->contextCode, $fileNames);
				//Reinsert into database with updated links
				if(strlen($page) > 1 )
				{
					$update = $this->update('id', $id, array('pagecontent' => $page));
				}
			}
		}

		return TRUE;
	}

  	/**
    	 * Method to replace image source links with links to the blob system
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param string $fileNames - names of all files in package
    	 * 
    	 * @return string $page - the finished modified text page
    	 * @return TRUE - if page is un-modified
	 *
	*/
    	function changeImageSRC($fileContents, $contextCode, $fileNames, $static='')
    	{
		//Image location on disc
		$imageLocation =  'src="'.'http://localhost/chisimba_framework/app/usrfiles/content/';
		$imageLocation = $imageLocation.$contextCode.'/images/';
		//Image location on localhost
		$action = 'src="'.'http://localhost/chisimba_framework/app/index.php?module=filemanager&amp;action=file&amp;id=';
		//Only run through html's contained in package
		foreach($this->resourceFileNames as $aFile)
		{
			//Check if its an Image
			if(preg_match("/.jpg|.gif|.png/",$aFile))
			{
				//Create new file source location
				//Check if its a static package
				if(!($static == ''))
					$newLink = $imageLocation.$aFile.'"';
				else
				{
					$newLink = $action.$this->imageIds[$aFile].'&amp;filename='.$aFile.'&amp;type=.jpg"';
				}
				//Convert filename into regular expression
				$regex = '/'.$aFile.'/';
				//Find filename in html page if it exists
				preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
				if($matches)
				{
					$page = preg_replace('/(src=".*?")/i', $newLink, $fileContents);

					return $page;
				}
				//If the image was renamed
				else
				{
					$aFile = preg_replace("/.jpg|.gif|.png/","",$aFile);
					$regex = '/'.$aFile.'/';
					preg_match_all($regex, $fileContents, $matches, PREG_SET_ORDER);
					if($matches)
					{
						$page = preg_replace('/(src=".*?")/i', $newLink, $fileContents);

						return $page;
					}
				}
			}
		}

		return TRUE;
    	}

	/**
    	 * Method to replace image source links with links to the blob system
	 *
    	 * @author Kevin Cyster
	 * @Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course context code
    	 * @param string $fileNames - names of all files in package
    	 * 
    	 * @return string $page - the finished modified text page
	 *
	*/
    	function changeLinkUrl($fileContents, $contextCode, $fileNames, $static='')
    	{
		$action ='href="'.'http://localhost/chisimba_framework/app/index.php?module=contextcontent&amp;action=viewpage&amp;id=';
		//Run through each resource
		$page = $fileContents;
		foreach($this->resourceFileNames as $aFile)
		{
			if($this->fileMod)
			{
				$aFile = preg_replace("/.html|.htm|.jpg|.gif|.png/","",$aFile);;
			}
			$regReplace = '/(href=".*'.$aFile.'.*?")/i';
			$modAction = $action.$this->pageIds[$aFile].'"';
			$page = preg_replace($regReplace, $modAction, $page);
		}

		return $page;
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