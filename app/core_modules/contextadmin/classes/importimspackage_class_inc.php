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

class importIMSPackage extends object 
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
	 * The constructor
	*/
	function init()
	{
		$this->objConfig = & $this->newObject('altconfig','config');
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
        	$this->objLanguage = & $this->newObject('language', 'language');
        	$this->objDBContext = & $this->newObject('dbcontext', 'context');
        	$this->objUser =& $this->getObject('user', 'security');
	}

	/**
	 * Controls the process for import IMS specification content
	 * Calls all necessary functions an does error checking
	 * 
	 * @param $_FILES global - uploaded file
	 * @return TRUE - Successful execution
	*/
	function importIMScontent($FILES)
	{
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
		//Create course
		$courseCreated= $this->objIEUtils->createCourseInChisimba($courseData);
		if(!isset($courseCreated))
		{
			return  "courseCreateError";
		}
		//Write Resources 
		$writeData = $this->writeResources($simpleXmlObj, $folder);
		if(!isset($writeData))
		{
			return  "writeResourcesError";
		}

		return TRUE;
	}

	/**
	 * Function to unzip an uploaded zip file
	 * 
	 * @param $_FILES global - Location of uploaded zip-file
	 * @return string $folder - Temp extraction folder location
	*/
	function unzipIMSFile($FILES)
	{
		if (isset($FILES['upload']))
		{
			if(!is_uploaded_file($FILES['upload']['tmp_name']))
			{
			echo "error uploading file";
			}
			else if ($FILES['upload']['error'] != UPLOAD_ERR_OK)
			{
				echo "upload error ok";
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
				$this->contextcode = $name;
				if ($extension == 'zip')
				{
					$tempfile=$FILES['upload']['tmp_name'];
					$tempdir=substr($tempfile,0,strrpos($tempfile,'/'));
					$objDir=&$this->getObject('dircreate','utilities');
					$objDir->makeFolder("$name.unzip",$tempdir);
					$folder="$tempdir/$name";
					$objWZip=&$this->getObject('wzip','utilities');
					$objWZip->unzip($tempfile,$folder);
				}

			}
		}
	return $folder;
	}

	/**
	 * Funcion to retrieve all file Locations within a specified folder
	 *
	 * @param $folder - Location of folder to scan
	 * @return array $fileLocations - Locations of all files within folder
	*/
	function locateAllFiles($folder)
	{
		$fileLocations = $this->objIEUtils->list_dir_files($folder,1);
	
		return $fileLocations;
	}

	/**
	 * Scans a specified array of strings for a specified string
	 *
	 * @param array $files - list of filenames
	 * @param string $regex - filename to scan in regular expression form
	 * @return $imsmanifestLocation
	*/
	public function locateIMSFile($files, $regex)
	{
		$imsmanifestLocation = "";
		foreach($files as $aFile)
		{
			if(preg_match($regex, $aFile))
			{
				$imsmanifestLocation = $aFile;
			}
		}
		return $imsmanifestLocation;
	}

	/**
	 * Takes the imsmanifest.xml file as input
	 *
	 * @param string $imsFileLocation
	 * @return simpleXml $simpleXmlObj
	*/
	function loadSimpleXml($imsFileLocation)
	{
		$simpleXmlObj = "";
		//Load imsmanifest.xml file
		if(file_exists($imsFileLocation)) 
		{
			//Create simplexml object to access xml file
    			$simpleXmlObj = simplexml_load_file($imsFileLocation);
		}
		else 
		{
    			return  "simpleXmlError";
		}
		
		return $simpleXmlObj;
	}

	/**
	 * Takes the imsmanifest.xml file as input
	 *
	 * @param string $imsFileLocation
	 * @return DOMDocument $domDocumentObj
	*/
	function loadDOMDocument($imsFileLocation)
	{
		$domDocumentObj = new DOMDocument();
		//Create domdocument object to access xml file
		if($domDocumentObj->load($imsFileLocation))
		{
		}
		else 
		{
    			return  "domError";
		}

		return $domDocumentObj;
	}

	/**
	 * Takes a DOM document as input
	 *
	 * @param DOMDocument $domDocumentObj
	 * @return DOMXPath $domDocumentObj
	*/
	function loadXPath($domDocumentObj)
	{
		//Create xpath object to access xml file
		$xpathObj = new DOMXPath($domDocumentObj);

		return $xpathObj;
	}

	/**
	 * Extract the course information from imsmanifest.xml
	 *
	 * @return TRUE - Successful execution
	*/
	function extractCourseData($xml, $doc, $xpath)
	{
		$newCourse = array();
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
				$newCourse['contextcode'] = $courseId;
#course title (title)
				$courseTitle = $resource->metadata->lom->general->title->langstring;
				$courseTitle = (string)$courseTitle;
				$courseTitle = trim($courseTitle);
				$newCourse['title'] = $courseTitle;
#course title (menu text)
				$newCourse['menutext'] = $courseTitle;
#course title (userId)
				$newCourse['userid'] = "1";
#course identifier (not in use)
				$courseIdentifier = $resource['identifier'];
				$courseIdentifier = (string)$courseIdentifier;
				$courseIdentifier = trim($courseIdentifier);
				$newCourse['courseIdentifier'] = $courseIdentifier;
#course description (about)
				$courseDescription = $resource->metadata->lom->general->description->langstring;
				$courseDescription = (string)$courseDescription;
				$courseDescription = trim($courseDescription);
				$newCourse['about'] = $courseDescription;
#course status (status)
				$courseStatus = "Public";
				$newCourse['isactive'] = $courseStatus;
#course access (access)
				$courseAccess = "UnPublished";
				$newCourse['isclosed'] = $courseAccess;

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


		return $newCourse;
	}

	/**
	 * Move the resources to appropriate locations on the file system
	 * and to the proper databases
	 *
	 * @param array $resources
	 * @return TRUE - Successful execution
	*/
	function writeResources($xml, $folder)
	{
		//Static Chisimba file locations
		//opt/lampp/htdocs/chisimba_framework/app/usrfiles/
		$contentBasePath = $this->objConfig->getcontentBasePath();
		$courseContentBasePath = $contentBasePath."content/";
		$contextCode = strtolower(str_replace(' ','_',$newCourse['courseId']));
		$courseContentPath = $courseContentBasePath.$contextCode;
		$imagesLocation = $courseContentPath."/images";
		$docsLocation = $courseContentPath."/documents";
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
				//echo "Course"."<br />";
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				//echo $filename."<br />";
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				//echo $file."<br />";
				$fileLocation = $folder."/".$file;
				//echo $fileLocation."<br />";
				//Write file contents to "about" in course
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//echo $fileContents;
				$newCourse['courseDescription'] = $fileContents;
				$this->objDBContext->saveAboutEdit($newCourse);
				//Write course contents to documents folder
				//New location for Courses
				$newLocation = $docsLocation."/".$filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
			}
			//Document
			if(strcmp($objectType,"Document")==0)
			{
				//echo "Document"."<br />";
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				//echo $filename."<br />";
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				//echo $file."<br />";
				$fileLocation = $folder."/".$file;
				//echo $fileLocation."<br />";
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Documents
				$newLocation = $docsLocation."/".$filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
			}
			//File
			if(strcmp($objectType,"File")==0)
			{
				//echo "File"."<br />";
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				//echo $filename."<br />";
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				//echo $file."<br />";
				$fileLocation = $folder."/".$file;
				//echo $fileLocation."<br />";
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Files
				$newLocation = $docsLocation."/".$filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
			}
			//Image
			if(strcmp($objectType,"Image")==0)
			{
				//echo "Image"."<br />";
				//Retrieve filename
				$filename = $resource->metadata->lom->metametadata->catalogentry->entry->langstring;
				//echo $filename."<br />";
				//Retrieve relative file location
				$file = $resource->file['href'];
				$file = (string)$file;
				$file = trim($file);
				//echo $file."<br />";
				//Retrieve absolute file location
				$fileLocation = $folder."/".$file;
				//echo $fileLocation."<br />";
				//Write file contents to images folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Images
				$newLocation = $imagesLocation."/".$filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//$filesInFolder = $this->objIndexing->scanDirectory($imagesLocation);
				//var_dump($filesInFolder);
				$files = $this->objUpload->indexFolder($imagesLocation, $this->objUser->userId());
				var_dump($files);
			}

		}

		return TRUE;
	}

	/**
	 * Layout of html pages and resources
	 *
	 * @param array $organizations
	 * @return TRUE - Successful execution
	*/
	function applyOrganizations($metadata)
	{

		return TRUE;
	}


}
?>