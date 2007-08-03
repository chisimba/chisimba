<?php
/**
 * The class exportIMSPackage that manages the export of IMS specification content
 * 
 * @category  Chisimba
 * @package   contextadmin
 * @author    Jarrett Jordaan
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   1.0
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
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
        	$this->objUser =& $this->getObject('user', 'security');
		$this->objConf = & $this->newObject('altconfig','config');
		$this->objDir = & $this->newObject('dircreate','utilities');
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
		// Start of DOM.
		$dom = new DOMDocument('1.0', 'utf-8');
		$manifest = $dom->appendChild($dom->createElement('manifest'));
		$manId = 'MAN'.$this->objIEUtils->generateUniqueId();
		$manifest->setAttribute('identifier', $manId);
		$manifest->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$manifest->setAttribute('xmlns:eduCommons', 'http://cosl.usu.edu/xsd/eduCommonsv1.1');
		$manifest->setAttribute('xmlns:imsmd', 'http://www.imsglobal.org/xsd/imsmd_v1p2');
		$manifest->setAttribute('xmlns:version', date('Y-m-j G:i:s'));
		$manifest->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$manifest->setAttribute('xsi:schemaLocation', 'http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd');
		$metadata = $manifest->appendChild($dom->createElement('metadata'));
		$metadata = $this->getMetadata($dom, $metadata);
		$organizations = $manifest->appendChild($dom->createElement('organizations'));
		$orgId = 'ORG'.$this->objIEUtils->generateUniqueId();
		$organizations->setAttribute('default', $orgId);
		$resources = $manifest->appendChild($dom->createElement('resources'));
		// Retrieve data within context.
		$courseData = $this->objIEUtils->getCourse($contextcode, 'new');
		// Course id.
		$courseId = $courseData['id'];
		// Course title.
		$courseTitle = $courseData['title'];
		// Course Context code.
		$contextcode = $courseData['contextcode'];
		// Create a temporary folder.
		$temp = '/tmp';
		$this->objDir->makeFolder($contextcode, $temp);
		// Store temporary folder.
		$tempDirectory = $temp.'/'.$contextcode;
		// Write Schema files.
		$writeSchemaFiles = $this->objIEUtils->writeSchemaFiles($tempDirectory);
		// Create resources folder.
		$this->objDir->makeFolder($contextcode, $tempDirectory);
		// Store resources folder.
		$resourceFolder = $tempDirectory."/".$contextcode;
		// Retrieve Course Html.
		$courseHtml = $this->objIEUtils->getCourseHtml($contextcode);
		// Retrieve Course Html images.
		$courseImageNames = $this->objIEUtils->getImageNames($courseHtml);
		// Retrieve Course Html file links.
		$courseResourceIds = $this->objIEUtils->getResourceIds($courseHtml);
		// Re-write Course Html.
		$courseHtml = $this->rebuildHtml($contextcode, $courseHtml, $courseImageNames, $courseResourceIds);
		// Write Course Html to specified directory (resources folder).
		$courseFilenames = $this->objIEUtils->writeFiles($courseHtml, $tempDirectory, $courseTitle,'html');
		// File location in package.
		$fileLocation = $tempDirectory.'/'.$courseFilenames[0].'.html';
		// Add Course Organization (Chapter).
		$organization = $organizations->appendChild($this->createOrganization($dom, $orgId));
		// Can only create one course resource
		$courseItemId = 'ITM'.$this->objIEUtils->generateUniqueId();
		$courseResId = 'RES'.$this->objIEUtils->generateUniqueId();
		// Add Items to Organization.
		$organization->appendChild($this->createItem($dom, $courseTitle, 'true', $courseItemId, $courseResId));
		// Add Course Resource.
		$resources->appendChild($this->createResource($dom, $courseTitle, $contextcode, $fileLocation, 'Course', $courseResId));
		// Write Images to specified directory (resources folder).
		if(count($courseImageNames) > 0)
		{
			$imageNames = $this->objIEUtils->writeImages($courseImageNames, $resourceFolder);
			foreach($courseImageNames as $courseImageName)
			{
				$resId = 'RES'.$this->objIEUtils->generateUniqueId();
				// File location in package.
				$fileLocation = $resourceFolder.'/'.$courseImageName;
				// Add Course Resource.
				$resources->appendChild($this->createResource($dom, $courseImageName, $contextcode, $fileLocation, 'Image', $resId));
			}
		}
		//.Write Resources to specified directory (resources folder).
		if(count($courseResourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($courseResourceIds, $resourceFolder);
		// Retrieve Html pages.
		$htmlPages = $this->objIEUtils->getHtmlPages($contextcode, '', '', '', 'pagecontent');
		// Remove course page.
		array_shift($htmlPages);
		// Retrieve Html page menutitles.
		$menutitles = $this->objIEUtils->getHtmlPages($contextcode, '', '', '', 'menutitle');
		// Retrieve Html images.
		$imageNames = $this->objIEUtils->getImageNames($htmlPages);
		// Retrieve Html file links.
		$resourceIds = $this->objIEUtils->getResourceIds($htmlPages);
		// Re-write Htmls.
		$htmlPages = $this->rebuildHtml($contextcode, $htmlPages, $imageNames, $resourceIds);
		// Write Htmls to specified directory (resources folder).
		$htmlFilenames = $this->objIEUtils->writeFiles($htmlPages, $resourceFolder,'','html');
		// Write Images to specified directory (resources folder).
		if(count($imageNames) > 0)
		{
			$imageIds = $this->objIEUtils->writeImages($imageNames, $resourceFolder);
			foreach($imageNames as $imageName)
			{
				$resId = 'RES'.$this->objIEUtils->generateUniqueId();
				// File location in package.
				$fileLocation = $resourceFolder.'/'.$imageName;
				// Add Course Resource.
				$resources->appendChild($this->createResource($dom, $imageName, $contextcode, $fileLocation, 'Image', $resId));
			}
		}
		// Write Resources to specified directory (resources folder).
		if(count($resourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($resourceIds, $resourceFolder);
		$chapterOrder = $this->objIEUtils->chapterOrder($contextcode);
		$i = 1;
		foreach($chapterOrder as $chapter)
		{
			$pageOrder = $this->objIEUtils->pageOrder($contextcode, $chapter['chapterid']);
			array_shift($pageOrder);
			foreach($pageOrder as $page)
			{
				if($page["isbookmarked"] == 'Y')
				{
					$itemId = 'ITM'.$this->objIEUtils->generateUniqueId();
					$resId = 'RES'.$this->objIEUtils->generateUniqueId();
					$pageDetails = $this->objIEUtils->pageContent($page['titleid']);
					$courseTitle = $pageDetails['menutitle'];
					// Add Items to Organization.
					$organization->appendChild($this->createItem($dom, $courseTitle, 'true', $itemId, $resId));
					// File location in package.
					$fileLocation = $resourceFolder.'/'.$htmlFilenames[$i];
					// Add Course Resource.
					$resources->appendChild($this->createResource($dom, $courseTitle, $contextcode, $fileLocation, 'Document', $resId));
					$i++;
				}
				else
				{
					// File location in package.
					$fileLocation = $resourceFolder.'/'.$htmlFilenames[$i];
					// Add Course Resource.
					$resources->appendChild($this->createResource($dom, $courseTitle, $contextcode, $fileLocation, 'Document', $resId));
					$i++;
				}
			}
		}
		$ims = $this->objIEUtils->writeFiles($dom->saveXML(), $tempDirectory, 'imsmanifest', 'xml');
		//$this->zipAndDownload($contextcode, $tempDirectory);

		return TRUE;
	}

	function createResource($dom, $name, $contextcode, $fileLocation, $type, $resId)
	{
		// Retrieve all data
		$fileName = preg_replace('/\..*/','',$name);
		$fileSize = filesize($fileLocation);
		$location = $this->objConf->getsiteRoot().$this->objConf->getcontentPath().'content/'.$contextcode.'/'.$name;
		$username = $this->objUser->userName($this->objUser->userId());
		$userDetails = $this->objUser->lookupData($username);
		$userEmail = $userDetails['emailaddress'];
		$copyright = 'Copyright '.date('Y');
		$copyrightCleared = 'false';
		$lang = 'en';
		$pageDescription = 'Course Home Page';
		$courseKeywords = 'HomePage';
		$dateCreated = date('Y-m-j G:i:s');
		$courseTerm = 'Not Specified';
		$displayEmail = 'false';
		$licenseName = 'Not Specified';
		$licenseUrl = 'Not Specified';
		$licenseIconUrl = 'Not Specified';
		// Create resource
		$resource = $dom->createElement('resource');
		$resource->setAttribute('identifier', $resId);
		$resource->setAttribute('type', 'webcontent');
		$resource->setAttribute('href', $location);
		$metadata = $resource->appendChild($dom->createElement('metadata'));
		$lom = $metadata->appendChild($dom->createElement('lom'));
		$lom->setAttribute('xmlns', 'http://www.imsglobal.org/xsd/imsmd_v1p2');
		$general = $lom->appendChild($dom->createElement('general'));
		$identifier = $general->appendChild($dom->createElement('identifier', $name));
		$title = $general->appendChild($dom->createElement('title'));
		$titleLangstring = $title->appendChild($dom->createElement('langstring', $fileName));
		$language = $general->appendChild($dom->createElement('language', $lang));
		if($type == 'Course')
		{
			$description = $general->appendChild($dom->createElement('description', $pageDescription));
			$keyword = $general->appendChild($dom->createElement('keyword', $courseKeywords));
			$keyword->setAttribute('xml:lang', 'en');
		}
		$lifecycle = $lom->appendChild($dom->createElement('lifecycle'));
		$contribute = $lifecycle->appendChild($dom->createElement('contribute'));
		$role = $contribute->appendChild($dom->createElement('role'));
		$source = $role->appendChild($dom->createElement('source'));
		$sourceLangstring = $source->appendChild($dom->createElement('langstring', 'LOMv1.0'));
		$sourceLangstring->setAttribute('xml:lang', 'x-none');
		$value = $role->appendChild($dom->createElement('value'));
		$valueLangstring = $value->appendChild($dom->createElement('langstring', 'author'));
		$valueLangstring->setAttribute('xml:lang', 'x-none');
		if($type == 'Course')
		{
		$centity = $contribute->appendChild($dom->createElement('centity'));
		$vcard = $centity->appendChild($dom->createElement('vcard', 'BEGIN:VCARD FN:'.$username.' EMAIL;INTERNET:'.$userEmail.' END:VCARD'));
		$date = $contribute->appendChild($dom->createElement('date', $dateCreated));
		$metametadata = $lom->appendChild($dom->createElement('metametadata'));
		$catalogentry = $metametadata->appendChild($dom->createElement('catalogentry'));
		$catalog = $catalogentry->appendChild($dom->createElement('catalog', $userEmail));
		$entry = $catalogentry->appendChild($dom->createElement('entry'));
		$entryLangstring = $entry->appendChild($dom->createElement('langstring', $name));
		}
		else
		{
		$centity = $contribute->appendChild($dom->createElement('centity'));
		$vcard = $centity->appendChild($dom->createElement('vcard', 'BEGIN:VCARD FN:(site default) END:VCARD'));
		$date = $contribute->appendChild($dom->createElement('date', $dateCreated));
		$metametadata = $lom->appendChild($dom->createElement('metametadata'));
		$catalogentry = $metametadata->appendChild($dom->createElement('catalogentry'));
		$catalog = $catalogentry->appendChild($dom->createElement('catalog', $userEmail));
		$entry = $catalogentry->appendChild($dom->createElement('entry'));
		$entryLangstring = $entry->appendChild($dom->createElement('langstring', $name));
		}
		if($type == 'Course')
		{
			$contribute = $metametadata->appendChild($dom->createElement('contribute'));
			$role = $contribute->appendChild($dom->createElement('role'));
			$source = $role->appendChild($dom->createElement('source'));
			$sourceLangstring = $source->appendChild($dom->createElement('langstring', 'LOMv1.0'));
			$value = $role->appendChild($dom->createElement('value'));
			$valueLangstring = $value->appendChild($dom->createElement('langstring', 'creator'));
			$centity = $contribute->appendChild($dom->createElement('centity'));
			$vcard = $centity->appendChild($dom->createElement('vcard', 'BEGIN:VCARD FN:'.$username.' EMAIL;INTERNET:'.$userEmail.' END:VCARD'));
			$date = $contribute->appendChild($dom->createElement('date', $dateCreated));
		}
		$metadatascheme = $metametadata->appendChild($dom->createElement('metadatascheme', 'LOMv1.0'));
		$language = $metametadata->appendChild($dom->createElement('language', $lang));
		$technical = $lom->appendChild($dom->createElement('technical'));
		$format = $technical->appendChild($dom->createElement('format','text/html'));
		$size = $technical->appendChild($dom->createElement('size', $fileSize));
		$location = $technical->appendChild($dom->createElement('location', $location));
		$rights = $lom->appendChild($dom->createElement('rights'));
		$copyrightandotherrestrictions = $rights->appendChild($dom->createElement('copyrightandotherrestrictions'));
		$source = $copyrightandotherrestrictions->appendChild($dom->createElement('source'));
		$sourceLangstring = $source->appendChild($dom->createElement('langstring', 'LOMv1.0'));
		$sourceLangstring->setAttribute('xml:lang', 'x-none');
		$value = $copyrightandotherrestrictions->appendChild($dom->createElement('value'));
		$valueLangstring = $value->appendChild($dom->createElement('langstring', 'yes'));
		$valueLangstring->setAttribute('xml:lang', 'x-none');
		$description = $rights->appendChild($dom->createElement('description'));
		$descriptionLangstring = $description->appendChild($dom->createElement('langstring', $copyright));
		$eduCommons = $metadata->appendChild($dom->createElement('eduCommons'));
		$eduCommons->setAttribute('xmlns', 'http://cosl.usu.edu/xsd/eduCommonsv1.1');
		$objectType = $eduCommons->appendChild($dom->createElement('objectType', $type));
		if($type == 'Course')
			$copyright = $eduCommons->appendChild($dom->createElement('copyright', $copyright));
		$license = $eduCommons->appendChild($dom->createElement('license'));
		$licenseName = $license->appendChild($dom->createElement('licenseName', $licenseName));
		$licenseUrl = $license->appendChild($dom->createElement('licenseUrl', $licenseUrl));
		$licenseIconUrl = $license->appendChild($dom->createElement('licenseIconUrl', $licenseIconUrl));
		$clearedCopyright = $eduCommons->appendChild($dom->createElement('clearedCopyright', $copyrightCleared));
		if($type == 'Course')
		{
			$courseId = $eduCommons->appendChild($dom->createElement('courseId', $contextcode));
			$term = $eduCommons->appendChild($dom->createElement('term', $courseTerm));
			$displayInstructorEmail = $eduCommons->appendChild($dom->createElement('displayInstructorEmail', $displayEmail));
		}
		$file = $resource->appendChild($dom->createElement('file'));
		$file->setAttribute('href', $name);

		return $resource;
	}

	function getMetadata($dom, $metadata)
	{
		$schema = $metadata->appendChild($dom->createElement('schema', 'IMS CONTENT'));
		$schemaversion = $metadata->appendChild($dom->createElement('schemaversion', '1.2'));

		return $metadata;
	}

	function createOrganization($dom, $orgId)
	{
		$organization = $dom->createElement('organization');
		$organization->setAttribute('identifier', $orgId);

		return $organization;
	}

	function createItem($dom, $menutitle, $bookmark, $itemId, $resId)
	{
		$item = $dom->createElement('item');
		$item->setAttribute('isvisible', $bookmark);
		$item->setAttribute('identifier', $itemId);
		$item->setAttribute('identifierref', $resId);
		$title = $item->appendChild($dom->createElement('title', $menutitle));

		return $item;
	}

	function rebuildHtml($contextcode, $htmlPages, $courseImageNames, $courseResourceIds)
	{
		$i = 0;
		// Check if its Course Home Page
		if(count($htmlPages) == 1)
		{
			if(count($courseImageNames) > 0)
				$htmlPages = $this->objIEUtils->changeImageSRC($htmlPages, $contextcode, $courseImageNames,'','2');
			if(count($courseResourceIds) > 0)
				$htmlPages = $this->changeLinkUrl($contextcode, $htmlPages, $courseResourceIds);
			$htmlsModified = $htmlPages;
		}
		else
		{
			foreach($htmlPages as $htmlPage)
			{
				if(count($courseImageNames) > 0)
					$htmlPage = $this->objIEUtils->changeImageSRC($htmlPage, $contextcode, $courseImageNames,'',TRUE);
				if(count($courseResourceIds) > 0)
					$htmlPage = $this->changeLinkUrl($htmlPage, $courseResourceIds);
				$htmlsModified[$i] = $htmlPage;
				$i++;
			}
		}

		return $htmlsModified;
	}

	function changeLinkUrl($htmlPages, $courseResourceIds)
	{
		$newLink = '"../'.$contextcode;
		foreach($courseResourceIds as $courseResourceId)
		{
			
		}

		return $htmlPages;
	}

	/**
	 * Retrieve imsmanifest.xml contents
	 *
	 * @param array $courseData
	 * @param array $filelist
	 * @return string $imsmanifest
	 */
	function createIMSManifestContent($courseData, $filelist, $tempDirectory, $menutitles)
	{
		//$imsmanifest = $this->imsSkeleton($filelist, $dirlist)->saveXML();
		//$imsmanifest = $this->objIMSTools->moodle($courseData, $filelist, $tempDirectory);
		//$imsmanifest = $this->ECIETool($filelist, $dirlist, $fileLoc);
		$imsmanifest = $this->objIMSTools->getManifest();
		$imsmanifest .= $this->objIMSTools->getMetadata();
		$imsmanifest .= $this->objIMSTools->getOrganizations($menutitles);
		//$imsmanifest .= $this->objIMSTools->getResources($courseData['0']['contextcode']);
		$imsmanifest .= "</manifest>";

		//$imsmanifest = $this->objIMSTools->getIMS($courseData['0']['contextcode']);

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