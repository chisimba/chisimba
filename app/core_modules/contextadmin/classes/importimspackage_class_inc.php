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
	 * @var object $objDebug
	*/
	public $objDebug;

	/**
	 * @var object $objContextContent
	*/
	public $objContextContent;

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
		$this->objDebug = FALSE;
		//$this->objDebug = TRUE;
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
		//
		$writeData = $this->writeResources($simpleXmlObj, $folder, $courseData);
		if(!isset($writeData))
		{
			return  "writeResourcesError";
		}
		//
		$loadData = $this->loadToChisimba($writeData);
		//
		$rebuildHtml = $this->rebuildHtml($simpleXmlObj, $loadData);

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
	 * @param $xml
	 * @param $folder
	 * @param $newCourse
	 * @return TRUE - Successful execution
	*/
	function writeResources($xml, $folder, $newCourse)
	{
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
		$courseContentBasePath = $contentBasePath."content/";
		$contextCode = strtolower(str_replace(' ','_',$newCourse['contextcode']));
		$courseContentPath = $courseContentBasePath.$contextCode;
		$imagesLocation = $courseContentPath."/images";
		$docsLocation = $courseContentPath."/documents";
		$allData = array();
		$resourceFileLocations = array();
		$resourceFileNames = array();
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
			static $i = 0;
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
				//Write file contents to "about" in course
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//Save all data
				$allData[$i] = array('resource' => $resource,
							'fileContents' => $fileContents,
							'contextCode' => $contextCode,
							'file' => $file);
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
				//Write file contents to "about" in course
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//echo $fileContents;
				$newCourse['courseDescription'] = $fileContents;
				$this->objDBContext->saveAboutEdit($newCourse);
				//Write course contents to documents folder
				//New location for Courses
				$newLocation = $docsLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Check debug flag
				if($this->objDebug)
				{
					echo "Course"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
			}
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
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Documents
				$newLocation = $docsLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Check debug flag
				if($this->objDebug)
				{
					echo "Document"."<br />";
					echo $filename."<br />";
					echo $file."<br />";
					echo $fileLocation."<br />";
				}
				//Save all data
				$allData[$i] = array('resource' => $resource,'fileContents' => $fileContents, 'contextCode' => $contextCode, 'file' => $file);
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
				//Write file contents to documents folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Files
				$newLocation = $docsLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
				//Check debug flag
				if($this->objDebug)
				{
					echo "File"."<br />";
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
				//Write file contents to images folder
				//Retrieve contents of file
				$fileContents = file_get_contents($fileLocation);
				//New location for Images
				$newLocation = $imagesLocation."/".$filename;
				//Store resource locations
				$resourceFileLocations[$i] = $newLocation;
				//Store filesname
				$resourceFileNames[$i] = $filename;
				//Open images directory
				$fp = fopen($newLocation,'w');
				//Write the file to images directory
				if((fwrite($fp, $fileContents) === FALSE))
				{
					return  "writeResourcesError";
				}
				//Close the directory
				fclose($fp);
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
			var_dump($resourceFileNames);
		}

		return $allData;
	}

	/**
	 * Control loading resources into Chisimba
	 * and file manipulation functions
	 *
	 * @param array $writeData - all data needed
	 * @return 
	*/
	function loadToChisimba($writeData)
	{
		foreach($writeData as $resource)
		{
			//Unpack data
			$xml = $resource['resource'];
			$fileContents = $resource['fileContents'];
			$contextCode = $resource['contextCode'];
			$file = $resource['file'];
			//Write Course to Chisimba database
			$passPage = $this->passPage($xml, $fileContents, $contextCode);
		}
		return TRUE;
	}

	/**
	 * 
	 * 
	 * 
	*/
	function rebuildHtml($xml, $loadData)
	{
		//switch tables
		parent::init('tbl_contextcontent_pages');
		//Retrieve resources
		foreach($xml->resources->resource as $resource)
		{
			//Retrieve page data
			$titleid = $resource->metadata->lom->general->title->langstring;
			$menutitle = $resource->metadata->lom->general->description->langstring;
			if(!strlen($menutitle) > 0)
			{
				$menutitle = $titleid;
			}

			$filter = "WHERE menutitle = '$menutitle'";
			$result = $this->getAll($filter);
			if(count($result) > 0)
			{
				//Retrieve page contents
				$fileContents = $result['0']['pagecontent'];
				$id = $result['0']['id'];
				//Rewrite images source in html
				$fileContents = $this->changeImageSRC($fileContents, $contextCode, $file);
				//Rewrite links source in html
				$fileContents = $this->changeLinkUrl($fileContents, $contextCode, $file);
				//Reinsert into database
				//$this->update('id', $id, array('pagecontent' => $fileContents));
			}

		}

	}

	/**
	 * Retrieve page details and pass data 
	 * database insertion functions
	 *
	 * @param array $resource - 
	 * @param string $fileContents - 
	 * @param string $contextCode - course contextcode
	 * @return boolean 
	*/
	function passPage($resource, $fileContents, $contextCode)
	{
		//Retrieve page data
		$titleid = $resource->metadata->lom->general->title->langstring;
		$menutitle = $resource->metadata->lom->general->description->langstring;
		$content = $fileContents;
		$language = $resource->metadata->lom->general->language;
		$headerscript = "";
		//Load to $values
		if(!strlen($menutitle) > 0)
		{
			$menutitle = $titleid;
		}
		$values = array('titleid' => (string)$titleid,
				'menutitle' => (string)$menutitle,
				'content' => (string)$content,
				'language' => (string)$language,
				'headerscript' => (string)$headerscript);
		//Insert into database
		$writePage = $this->writePage($values, $contextCode);
		
		return TRUE;
	}

	/**
	 * Write content to Chisimba database
	 *
	 * @param array $values - page details
	 * @param string $contextCode - course contextcode
	 * @return boolean 
	*/
	function writePage($values, $contextCode)
	{
		//Load context classes
        	$this->objContentPages =& $this->getObject('db_contextcontent_pages','contextcontent');
	        $this->objContentOrder =& $this->getObject('db_contextcontent_order','contextcontent');
        	$this->objContentTitles =& $this->getObject('db_contextcontent_titles','contextcontent');
	        $this->objContentInvolvement =& $this->getObject('db_contextcontent_involvement','contextcontent');
		//No idea!!!
		$tree = $this->objContentOrder->getTree($contextCode, 'dropdown', $parent);
		//Add page
        	$titleId = $this->objContentTitles->addTitle('', 
								$values['menutitle'], 
								$values['content'], 
								$values['language'],
								$values['headerscript']);
        	$pageId = $this->objContentOrder->addPageToContext($titleId, $parent, $contextCode);

		return TRUE;
	}

    	/**
    	 * Method to replace html source links with links to the blob system
	 *
    	 * @author James Scoble
	 * Modified by Jarrett L Jordaan
    	 * @param string $str - the text of the page to operate on.
    	 * @param string $contextCode - course contextcode
    	 * @param string $file - 
    	 * @return string $page - the finished text
	*/
	function changeLinkUrl($str, $contextCode, $file)
	{
		//Retrieve all links in html by splitting tags(<A)
        	$fragment=spliti('<A',$str);
        	unset($str);
        	$first=array_shift($fragment); 
        	$page=$first;
        	foreach ($fragment as $line)
        	{
			$segment=spliti('href="',$line);
			$alt = $segment[0];
            		$src=spliti("\"",$segment[1]);
            		$src=$src[0];
            		$segment[1]=stristr($segment[1]," ");
			//$segment[1]=strstr($segment[1],"\"");
            		$check=count($segment);
            		$text='';
            		if ($check>2)
			{
                		for($ic=1;$ic!=$check;$ic++)
                		{
                    			$text.=$segment($ic);
                		}
            		}
			else 
			{
                		$text=$segment[1];
            		}
			$storeSrc = $src;
            		$src=str_replace("\\","/",$src);
            		$src=strrchr($src,"/"); 
            		$src=trim(substr($src,1));
            		$id='test';
			if(strlen($src) > 0)
				$link = "http://localhost/chisimba_framework/app/usrfiles/content/".$contextCode."/documents/".$src;
			else
				$link = "http://localhost/chisimba_framework/app/usrfiles/content/".$contextCode."/documents/".$storeSrc;
			$page.= "<a$alt href=\"$link\" $text";
		}
		return $page;
	}

    	/**
    	 * Method to replace image source links with links to the blob system
	 *
    	 * @author James Scoble
	 * Modified by Jarrett L Jordaan
    	 * @param strong $str - the text of the page to operate on.
    	 * @param string $contextCode - course contextcode
    	 * @param string $file - 
    	 * @return string $page - the finished text
	*/
    	function changeImageSRC($str, $contextCode, $file)
    	{
        	$rootId=$this->rootId;
        	$fragment=spliti('<IMG',$str);
        	unset($str); 
        	$first=array_shift($fragment); 
        	$page=$first;
        	foreach ($fragment as $line)
        	{
            		$segment=spliti('src="',$line);
            		$alt=$segment[0];
            		$src=spliti("\"",$segment[1]);
            		$src=$src[0];
            		$segment[1]=stristr($segment[1]," ");
			//$segment[1]=strstr($segment[1],"\"");
            		$check=count($segment);
            		$text='';
            		if ($check>2)
			{
                		for($ic=1;$ic!=$check;$ic++)
                		{
                    			$text.=$segment($ic);
                		}
            		}
			else 
			{
                		$text=$segment[1];
            		}
			$storeSrc = $src;
            		$src=str_replace("\\","/",$src);
            		$src=strrchr($src,"/"); 
            		$src=trim(substr($src,1));
            		$id='test';
			if(strlen($src) > 0)
				$link = "http://localhost/chisimba_framework/app/usrfiles/content/".$contextCode."/images/".$src;
			else
				$link = "http://localhost/chisimba_framework/app/usrfiles/content/".$contextCode."/images/".$storeSrc;
			$page.= "<IMG$alt src=\"$link\" $text";
		}
        	return $page;
    	}

	/**
	 * Sets debugging on
	 *
	*/
	function debugOn()
	{
		$this->objDebug = TRUE;
	}

	/**
	 * Sets debugging off
	 *
	*/
	function debugOff()
	{
		$this->objDebug = FALSE;
	}

}
?>