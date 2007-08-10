<?php
/**
 * The class exportIMSPackage that manages the export of IMS specification content
 * 
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
	 * @var object $objWZip
	*/
	public $objWZip;
	/**
	 * The constructor
	*/
	function init()
	{
		// Load System classes.
        	$this->objUser = $this->getObject('user', 'security');
		$this->objConf =  $this->newObject('altconfig','config');
		// Load Inner classes.
		$this->objIEUtils = $this->newObject('importexportutils');
		$this->objIMSTools =  $this->newObject('imstools');
		// Load utility classes.
		$this->objWZip = $this->getObject('wzip','utilities');
		$this->objDir =  $this->newObject('dircreate','utilities');
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
		$metadata = $this->objIMSTools->getMetadata($dom, $metadata);
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
		$courseFilename = $courseFilenames[0].'.html';
		$fileLocation = $tempDirectory.'/'.$courseFilename;
		//$packageLocation = "..".$temp.'/'.$contextcode.'/';
		$packageLocation = $contextcode.'/';
		$relativeLocation = $packageLocation.$courseFilename;
		// Add Course Organization (Chapter).
		$organization = $organizations->appendChild($this->objIMSTools->createOrganization($dom, $orgId));
		// Can only create one course resource
		$courseItemId = 'ITM'.$this->objIEUtils->generateUniqueId();
		$courseResId = 'RES'.$this->objIEUtils->generateUniqueId();
		// Add Items to Organization.
		$organization->appendChild($this->objIMSTools->createItem($dom, $courseTitle, 'true', $courseItemId, $courseResId));
		// lom values for course
		$username = $this->objUser->userName($this->objUser->userId());
		$userDetails = $this->objUser->lookupData($username);
		$userEmail = $userDetails['emailaddress'];
		$fileSize = filesize($fileLocation);
		$lomValues = array('resourceId' => $courseResId,
			'identifier' => $contextcode,
			'title' => $courseTitle,
			'language' => 'en',
			'description' => 'Course Home Page',
			'keyword' => 'Home Page',
			'role' => 'Author',
			'name' => $username,
			'email' => $userEmail,
			'datecreated' => $courseData['datecreated'],
			'dateDescription' => 'Course Creation Date',
			'metaRole' => 'Creator',
			'courseFilename' => $courseFilename,
			'format' => 'text/html',
			'size' => $fileSize,
			'location' => $relativeLocation,
			'packageLocation' => $packageLocation,
			'copyrightDate' => 'Copyright '.date('Y'));
		// eduCommons values for course
		$objectType = 'Course';
		$copyright = '';
		$licenseName = '';
		$licenseUrl = '';
		$licenseIconUrl = '';
		$clearedCopyright = 'false';
		$courseId = $contextcode;
		$term = '';
		$displayInstructorEmail = 'false';
		$name = $fileLocation;
		$eduCommonsValues = array('objectType' => $objectType,
				'copyright' => $copyright,
				'category' => 'Creative Commons License',
				'licenseName' => $licenseName,
				'licenseUrl' => $licenseUrl,
				'licenseIconUrl' => $licenseIconUrl,
				'clearedCopyright' => $clearedCopyright,
				'courseId' => $courseId,
				'term' => $term,
				'displayInstructorEmail' => $displayInstructorEmail,
				'name' => $name);
		// Add Course Resource.
		$resources->appendChild($this->objIMSTools->createResource($dom, $lomValues, $eduCommonsValues));
		// Write Images to specified directory (resources folder).
		if(count($courseImageNames) > 0)
		{
			$imageNames = $this->objIEUtils->writeImages($courseImageNames, $resourceFolder);
			foreach($courseImageNames as $courseImageName)
			{
				$resId = 'RES'.$this->objIEUtils->generateUniqueId();
				// File location in package.
				$fileLocation = $resourceFolder.'/'.$courseImageName;
				$fileSize = filesize($fileLocation);
				$imageName = preg_replace('/\..*/','',$courseImageName);
				$relativeLocation = $contextcode.'/'.$courseImageName;
				// lom values for course images
				$lomValues = array('resourceId' => $resId,
					'identifier' => $courseImageName,
					'title' => $imageName,
					'language' => 'en',
					'role' => 'rights holder',
					'name' => $username,
					'email' => $userEmail,
					'datecreated' => $courseData['datecreated'],
					'dateDescription' => 'Image Creation Date',
					'metaRole' => 'Creator',
					'courseFilename' => $courseImageName,
					'format' => 'image/jpeg',
					'size' => $fileSize,
					'location' => $relativeLocation,
					'packageLocation' => $packageLocation);
				// eduCommons values for course images
				$eduCommonsValues = array('objectType' => 'Image',
					'category' => 'Site Default',
					'clearedCopyright' => 'true',
					'name' => $fileLocation);
				// Add Resource.
				$resources->appendChild($this->objIMSTools->createResource($dom, $lomValues, $eduCommonsValues));
			}
		}
		//.Write Resources to specified directory (resources folder).
		if(count($courseResourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($courseResourceIds, $resourceFolder);
		// Retrieve Html pages.
		$htmlPages = $this->objIEUtils->getHtmlPages($contextcode, '', '', '', 'pagecontent');
		// Remove course page.
		array_shift($htmlPages);
		// Retrieve Html page html Id's.
		$htmlIds = $this->objIEUtils->getHtmlPages($contextcode, '', '', '', 'titleid');
		array_shift($htmlIds);
		// Retrieve Html images.
		$imageNames = $this->objIEUtils->getImageNames($htmlPages);
		// Re-write Htmls.
		$htmlPages = $this->rebuildHtml($contextcode, $htmlPages, $imageNames, $htmlIds);
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
				$fileSize = filesize($fileLocation);
				$imageNameMod = preg_replace('/\..*/','',$imageName);
				$relativeLocation = $contextcode.'/'.$imageName;
				// lom values
				$lomValues = array('resourceId' => $resId,
					'identifier' => $imageName,
					'title' => $imageNameMod,
					'language' => 'en',
					'role' => 'rights holder',
					'name' => $username,
					'email' => $userEmail,
					'datecreated' => $courseData['datecreated'],
					'dateDescription' => 'Image Creation Date',
					'metaRole' => 'Creator',
					'courseFilename' => $imageName,
					'format' => 'image/jpeg',
					'size' => $fileSize,
					'location' => $relativeLocation,
					'packageLocation' => $packageLocation);
				// eduCommons values
				$eduCommonsValues = array('objectType' => 'Image',
					'category' => 'Site Default',
					'clearedCopyright' => 'true',
					'name' => $fileLocation);
				// Add Resource.
				$resources->appendChild($this->objIMSTools->createResource($dom, $lomValues, $eduCommonsValues));
			}
		}
		// Write Resources to specified directory (resources folder).
		if(count($resourceIds) > 0)
			$resourceNames = $this->objIEUtils->writeResources($resourceIds, $resourceFolder);
		$chapterOrder = $this->objIEUtils->chapterOrder($contextcode);
		$i = 0;
		foreach($chapterOrder as $chapter)
		{
			$pageOrder = $this->objIEUtils->pageOrder($contextcode, $chapter['chapterid']);
			array_shift($pageOrder);
			foreach($pageOrder as $page)
			{
				$itemId = 'ITM'.$this->objIEUtils->generateUniqueId();
				$resId = 'RES'.$this->objIEUtils->generateUniqueId();
				$pageDetails = $this->objIEUtils->pageContent($page['titleid']);
				$menuTitle = $pageDetails['menutitle'];
				// Add Items to Organization.
				if($page["isbookmarked"] == 'Y')
					$organization->appendChild($this->objIMSTools->createItem($dom, $menuTitle, 'true', $itemId, $resId));
				// File location in package.
				$fileLocation = $resourceFolder.'/'.$htmlFilenames[$i];
				$fileName = preg_replace('/\..*/','',$htmlFilenames[$i]);
				$fileSize = filesize($fileLocation);
				$relativeLocation = $contextcode.'/'.$htmlFilenames[$i];
				// Add Course Resource.
				// lom values
				$lomValues = array('resourceId' => $resId,
					'identifier' => $fileName,
					'title' => $menuTitle,
					'language' => 'en',
					'keyword' => 'Assignments',
					'role' => 'rights holder',
					'name' => $username,
					'email' => $userEmail,
					'datecreated' => $courseData['datecreated'],
					'dateDescription' => 'Document Creation Date',
					'metaRole' => 'Creator',
					'courseFilename' => $relativeLocation,
					'format' => 'text/html',
					'size' => $fileSize,
					'location' => $relativeLocation,
					'packageLocation' => $packageLocation,
					'copyrightDate' => 'Copyright '.date('Y'));
				// eduCommons values
				$eduCommonsValues = array('objectType' => 'Document',
				'category' => 'Site Default',
					'clearedCopyright' => 'true',
					'name' => $fileLocation);
					// Add Resource.
				$resources->appendChild($this->objIMSTools->createResource($dom, $lomValues, $eduCommonsValues));
				$i++;
			}
		}
		$ims = $this->objIEUtils->writeFiles($dom->saveXML(), $tempDirectory, 'imsmanifest', 'xml');

		$this->zipAndDownload($contextcode, $tempDirectory);

		return TRUE;
	}

	function rebuildHtml($contextcode, $htmlPages, $courseImageNames, $courseResourceIds)
	{
		$i = 0;
		// Check if its Course Home Page
		if(count($htmlPages) == 1)
		{
			// Rewrite images source in html.
			if(count($courseImageNames) > 0)
				$htmlPages = $this->objIEUtils->changeImageSRC($htmlPages, $contextcode, $courseImageNames,'','2');
			// Rewrite links source in html.
			if(count($courseResourceIds) > 0)
				$htmlPages = $this->changeLinkUrl($contextcode, $htmlPages, $courseResourceIds);
			// Rewrite internal links source in html.

			// Rewrite data links source in html.

			$htmlsModified = $htmlPages;
		}
		else
		{
			foreach($htmlPages as $htmlPage)
			{
				// Rewrite images source in html.
				if(count($courseImageNames) > 0)
					$htmlPage = $this->objIEUtils->changeImageSRC($htmlPage, $contextcode, $courseImageNames,'',TRUE);
				// Rewrite links source in html.
				if(count($courseResourceIds) > 0)
					$htmlPage = $this->changeLinkUrl($htmlPage, $courseResourceIds);
				$htmlsModified[$i] = $htmlPage;
				// Rewrite internal links source in html.

				// Rewrite data links source in html.

				$i++;
			}
		}

		return $htmlsModified;
	}

	function changeLinkUrl($htmlPage, $resourceIds)
	{
		for($i = 0; $i < count($resourceIds); $i++)
		{
			$id = $this->objIEUtils->getHtmlPageId($resourceIds[$i]);
			$regReplace = '/(href=".*'.$id.'.*?")/i';
			$replacement = 'href="resource'.$i.'.html"';
			$htmlPage = preg_replace($regReplace, $replacement, $htmlPage);
		}

		return $htmlPage;
	}

	/**
	 * Zip IMS package
	 *
	 * @param string $contextcode
	 * @param string $tempDirectory
	 */
	function zipAndDownload($contextcode, $tempDirectory)
	{
		$filelist = $this->objIEUtils->list_dir_files($tempDirectory, '1');
		$objZip = new PclZip('/tmp/archive.zip');
		$results = $objZip->create($filelist,PCLZIP_OPT_REMOVE_PATH, $tempDirectory);

		return TRUE;
	}

}
?>