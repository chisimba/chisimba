<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class imstools that manages 
 * the creation of the imsmanifest.xml file
 * @package imstools
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Jarrett Jordaan
 * The process for import KNG content is:
 * 
 * 
 */

class imstools extends object 
{

	/**
	 * The constructor
	*/
	function init()
	{
        	$this->objUser =& $this->getObject('user', 'security');
		//Load Import Export Utilities class
		$this->objIEUtils = & $this->newObject('importexportutils','contextadmin');
	}

	function getManifest()
	{
		//Start of xml file
		$imsmanifest = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		//manifest
		$imsmanifest .= "<manifest ";
		$imsmanifest .= "identifier=\"MAN".$this->objIEUtils->generateUniqueId()."\" ";
		$imsmanifest .= "xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\" xmlns:eduCommons=\"http://cosl.usu.edu/xsd/eduCommonsv1.1\" xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\" xmlns:version=\"".date('Y-m-j G:i:s')."\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd\">\n";	

		return $imsmanifest;
	}

	function getMetadata()
	{
		//metadata
		$metadata = "<metadata>\n";
		$metadata .= "<schema>\n";
		$metadata .= "IMS CONTENT";
		$metadata .= "</schema>\n";
		$metadata .= "<schemaversion>\n";
		$metadata .= "1.2\n";
		$metadata .= "</schemaversion>\n";
		$metadata .= "</metadata>\n";

		return $metadata;
	}
// Structure
	public $orgsId;
	public $itemIds;
	public $resIds;

	function getIMS($contextcode)
	{
		$imsmanifest = $this->getManifest();
		$imsmanifest .= $this->getMetadata();
		$this->orgsId = 'ORG'.$this->objIEUtils->generateUniqueId();
		$imsmanifest .= "<organizations ";
		$imsmanifest .= "default= \"".$this->orgsId."\">\n";
		$chapterOrders = $this->objIEUtils->chapterOrder($contextcode);
		foreach($chapterOrders as $chapter)
		{
//var_dump($chapterOrders);die;
			$imsmanifest .= "<organization ";
			$imsmanifest .= "default= \"".$this->orgsId."\">\n";
			$chapterContent = $this->objIEUtils->chapterContent($chapter['chapterid']);
			$pageOrders = $this->objIEUtils->pageOrder($contextcode);
//var_dump($pageOrders);die;
			foreach($pageOrders as $pageOrder)
			{
				$pageContents = $this->objIEUtils->pageContent($pageOrder['titleid']);
				$i = 0;
//var_dump($pageContents);die;
				foreach($pageContents as $pageContent)
				{	
//var_dump($pageContent);die;
					$imsmanifest .= $this->getItem($pageContent['menutitle'],$i);
					$i++;
				}
			}

			foreach($chapterContent as $content)
			{
				
			}
			$imsmanifest .= "</organization>\n";
		}
		$imsmanifest .= "</organizations>\n";

//var_dump($chapterOrder);die;
		//$chapterContent = $this->objIEUtils->chapterContent();
		//$pageOrder = $this->objIEUtils->pageOrder($contextcode);
//var_dump($pageOrder);die;
		//$pageContent = $this->objIEUtils->pageContent();
		$imsmanifest .= "</manifest>";

		return $imsmanifest;
	}

	function getOrganizations($menutitles)
	{
		//organizations
		$this->orgsId = 'ORG'.$this->objIEUtils->generateUniqueId();
		//organization
		$organizations .= "<organizations ";
		$organizations .= "default= \"".$this->orgsId."\">\n";
		$organizations .= $this->getOrganization($menutitles);
		$organizations .= "</organizations>\n";

		return $organizations;
	}
// Chapters
	function getOrganization($menutitles)
	{
		//organization
		$organization .= "<organization ";
		$organization .= "default= \"".$this->orgsId."\">\n";
		$i = 0;
		foreach($menutitles as $menutitle)
		{
			$organization .= $this->getItem($menutitle, $i);
			$i++;
		}
		$organization .= "</organization>\n";

		return $organization;
	}
// Pages
	function getItem($menutitle, $i)
	{
		$this->itemIds[$i] = 'ITM'.$this->objIEUtils->generateUniqueId();
		$this->resIds[$i] = 'RES'.$this->objIEUtils->generateUniqueId();
		$this->menutitles[$i] = $menutitle;
		$item = "<item ";
		$item .= "identifier=\"".$itemId."\"";
		$item .= "identifierref=\"".$resId."\"";
		$item .= "isVisible=\"".'true'."\">\n";
		$item .= "<title>";
		$item .= $menutitle."\n";
		$item .= "</title>";
		$item .= "</item>\n";

		return $item;
	}
//Resources
	function getResources($contextcode)
	{
		$resources = "<resources>\n";
		foreach($this->menutitles as $menutitle)
		{
			$resources .= $this->getResource($contextcode);
		}
		$resources .= "</resources>\n";

		return $resources;
	}

	function getResource($contextcode)
	{
		$resource = "<resource>\n";
		$resource .= "<file ";
		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";
		$resource .= "</resource>\n";

		return $resource;
	}

	function getCourseResource($contextcode)
	{
		$resource = "<resource>\n";
		$resource .= "<metadata>\n";

		$resource .= "</metadata>\n";
		$resource .= "<file>\n";

		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";

		$resource .= "</resource>\n";

		return $resource;
	}

	function getImageResource($contextcode, $filename)
	{
		$resource = "<resource>\n";
		$resource .= "<metadata>\n";
		$resource .= "<lom>\n";
		$resource .= "<general>\n";
		$resource .= "<identifier>\n";
		$resource .= $filename;
		$resource .= "</identifier>\n";
		$resource .= "</general>\n";
		$resource .= "</lom>\n";
		$resource .= "</metadata>\n";
		$resource .= "<file\n";
		$resource .= "href=\"".$contextcode.'/'.$filename."\">\n";
		$resource .= "</resource>\n";

		return $resource;
	}

//===========================================================================
//eduCommons IMS Skeleton
function moodle($courseData, $filelist, $tempDirectory)
{
	//Retrieve all directories
	$dirlist = $this->objIEUtils->list_dir($tempDirectory,0);
	//start of xml document
	$imsmanifest = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
//manifest attributes example
/*
xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" 
xmlns:eduCommons="http://cosl.usu.edu/xsd/eduCommonsv1.1" 
xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" 
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd 
                    http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd 
                    http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd">

*/

//manifest
	$imsmanifest .= "<manifest "; 
	$imsmanifest .= "identifier =\"".$courseData['id']."\" ";
	$imsmanifest .= "version =\"".$courseData['contextcode']."\" ";
	$imsmanifest .= "xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\" xmlns:eduCommons=\"http://cosl.usu.edu/xsd/eduCommonsv1.1\" xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p2.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd http://cosl.usu.edu/xsd/eduCommonsv1.1 eduCommonsv1.1.xsd\">\n";
//===========================================================================
//metadata
	$imsmanifest .= "<metadata>\n";
	$imsmanifest .= "<schema>\n";
	$imsmanifest .= "IMS CONTENT";
	$imsmanifest .= "</schema>\n";
	$imsmanifest .= "<schemaversion>\n";
	$imsmanifest .= "1.2\n";
	$imsmanifest .= "</schemaversion>\n";
	$imsmanifest .= "</metadata>\n";
//===========================================================================
//organization
	$imsmanifest .= "<organizations ";
	$org_identifier = $this->genORG();
	$imsmanifest .= "default= \"".$org_identifier."\">\n";
//organization
	$imsmanifest .= "<organization ";
	$imsmanifest .= "identifier=\"".$org_identifier."\">\n";
	$idenrefs = array();
	foreach($filelist as $file)
	{
		static $i=0;
		$idenrefref = $this->genRES();
		$idenrefs[$i] = $idenrefref;
		$i++; 
		$imsmanifest .= $this->createOrg($org_identifier,$this->genITM(),$idenrefref,"TRUE",$file);
	}
//var_dump($idenrefs);
	$imsmanifest .= "</organization>";
	$imsmanifest .= "</organizations>\n";
//=========================================================================== 
//resources
	$imsmanifest .= "<resources>\n";
//resource
	$resValues = array();
	for($i=0; $i<count($idenrefs); $i++)
	{
//$imsmanifest .= $this->createRes($idenrefs[$i],"webcontent","1");
		$resValues["identifier"] = $idenrefs[$i];
		$resValues["type"] = "webcontent";
		$resValues["href"] = "1";
		$imsmanifest .= $this->createRes($resValues);
	}
	$imsmanifest .= "</resources>\n";
//===========================================================================
	$imsmanifest .= "</manifest>\n";

//eduCommons object
/*
<eduCommons xmlns="http://cosl.usu.edu/xsd/eduCommonsv1.1">
    <objectType>
        Course
    </objectType>
</eduCommons> 
*/

//the rights holder field
/*
<contribute>
    <role>
        <source>
            <langstring xml:lang="en">
                eduCommonsv1.1
            </langstring>
        </source>
        <value>
            <langstring xml:lang="en">
                rights holder
            </langstring>
        </value>
    </role>
    <centity>
        <vcard>
            BEGIN:VCARD
            FN: John Smith
            END:VCARD
        </vcard>
    </centity>
    <date>
        <datetime>
            2006-08-07 15:59:23
        </datetime>
    </date>
</contribute>
*/

//
/*

*/
return $imsmanifest;
}
//===========================================================================
//create organization
//organization example
/*
<organizations default="ORG1234">
    <organization identifier="ORG1234">
        <item identifier="ITM1234" identifierref="RES1234" isVisible="true">
            <title>
                Hello World
            </title>
        </item>
        ...
    </organization>
</organizations>
<resources>
    <resource identifier="RES1234">
       ...
    </resource>
    ...
</resources>
*/

function createOrg($org_identifier, $item_identifier, $item_identifierref, $item_isVisible,$file)
{
	$org .= "<item ";
	$org .= "identifier=\"".$item_identifier."\" ";
	$org .= "identifierref=\"".$item_identifierref."\" ";
	$org .= "isVisible=\"".$item_isVisible."\">\n";
	$org .= "<title>";
	$org .= $file."\n";
	$org .= "</title>";
	$org .= "</item>\n";

	return $org;
}
//===========================================================================
//function createRes($res_identifier,$res_type,$res_href)
function createRes($resValues)
{
	$res = "<resource ";
	$res .= "identifier=\"".$resValues["identifier"]."\" ";
	$res .= "type=\"".$resValues["type"]."\" ";
	$res .= "href=\"".$resValues["href"]."\">\n";
	$res .= "<metadata>\n";
	$res .= "<lom>\n";
	$res .= "<general>\n";
	$res .= "<identifier>";
	$res .= $courseData['title'];
	$res .= "</identifier>\n";
	$res .= "<title>\n";
	$res .= "<langstring>";
	$res .= $courseData['title'];
	$res .= "</langstring>\n";
	$res .= "</title>\n"; 
	$res .= "<language>";
	$res .= "</language>\n";
	$res .= "<description>\n";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</description>\n";
	$res .= "<keyword>";
	$res .= "</keyword>\n";
	$res .= "</general>\n";
	$res .= "<lifecycle>\n";
	$res .= "<contribute>\n";
	$res .= "</contribute>\n";
	$res .= "</lifecycle>\n";
	$res .= "<metametadata>\n";
	$res .= "<catalogentry>\n";
	$res .= "<catalog>\n";
	$res .= "</catalog>\n";
	$res .= "<entry>\n";
	$res .= "<langstring>";
	$res .= $courseData['title'];
	$res .= "</langstring>\n";
	$res .= "</entry>\n";
	$res .= "</catalogentry>\n";
	$res .= "<metadataschema>";
	$res .= "LOMv1.0";
	$res .= "</metadataschema>\n";
	$res .= "<language>";
	$res .= "en";
	$res .= "</language>\n";
	$res .= "</metametadata>\n";
	$res .= "<technical>\n";
	$res .= "<format>";
	$res .= "text/html";
	$res .= "</format>\n";
	$res .= "<size>";
	$res .= "</size>\n";
	$res .= "<location>";
	$res .= "http://localhost:8080/".$courseData['contextcode'];
	$res .= "</location>\n";
	$res .= "</technical>\n";
	$res .= "<rights>";
	$res .= "<copyrightandotherrestrictions>";
	$res .= "<source>";
	$res .= "<langstring>";
	$res .= "eduCommonsv1.1";
	$res .= "</langstring>\n";
	$res .= "</source>\n";
	$res .= "<value>";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</value>\n";
	$res .= "<description>";
	$res .= "<langstring>";
	$res .= "</langstring>\n";
	$res .= "</description>\n";
	$res .= "</copyrightandotherrestrictions>\n";
	$res .= "</rights>\n";
	$res .= "</lom>\n";
	$res .= "<eduCommons>\n";
	$res .= "<objectType>";
	$res .= "</objectType>\n";
	$res .= "<license>";
	$res .= "</license>\n";
	$res .= "<clearedCopyright>";
	$res .= "</clearedCopyright>\n";
	$res .= "<courseId>";
	$res .= $courseData['contextcode'];
	$res .= "</courseId>\n";
	$res .= "<term>";
	$res .= "</term>\n";
	$res .= "<displayInstructorEmail>";
	$res .= "</displayInstructorEmail>\n";
	$res .= "</eduCommons>\n";
	$res .= "</metadata>\n";
	$res .= "<file>\n";
	$res .= "</file>\n";
	$res .= "</resource>\n";
	
	return $res;
}
}
?>