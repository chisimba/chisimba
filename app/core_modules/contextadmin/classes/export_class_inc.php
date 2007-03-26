<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class export that manages 
 * the export of static content 
 * @package export
 * @category context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie 
 * The process for export static content is:
 * Get the content for the context that your are in
 * 
 */

class export extends object 
{
    /**
	* @var object $objDBContentNodes
	*/
    var $objDBContentNodes;
    
    /**
	* @var object $objDBContext
	*/
	var $objDBContext;
    
    /**
    * @var object $objConfig
    */
    var $objConfig;
   
    
    /**
    * @var string $staticFolder
    */
    var $staticFolder;
    
	public $objBlogImport;
	public $imsmanifest;
	public $contextCode;
	
    function init()
    {
        $this->objDBContentNodes = & $this->newObject('dbcontentnodes','context');
        $this->objDBContext = &$this->newObject('dbcontext','context');
        $this->objConfig = & $this->newObject('altconfig','config');
	     $this->objWZip = &$this->newObject('wzip','utilities');
    }
    
    /**
    * Method to export static content
    * @return null
    * @access public
    */
    function exportStaticContent()
    {
        $contextid = $this->getParam('contextid');        
        //$contextCode = $this->getParam('contextcode');      
        $contextCode = $this->objDBContext->getContextCode();
        
        $objConfig = & $this->newObject('config','config');
        $dircreate = & $this->newObject('dircreate','utilities');
         //check if the course folder exist
         if(!is_dir($objConfig->siteRootPath().'usrfiles')){
             $dircreate->makeFolder('usrfiles',$objConfig->siteRootPath());
         }
         if(!is_dir($objConfig->siteRootPath().'usrfiles/content')){
             $dircreate->makeFolder('content',$objConfig->siteRootPath().'usrfiles/');
         }
         if(!is_dir($objConfig->siteRootPath().'usrfiles/content/'.$contextCode)){
             $dircreate->makeFolder($contextCode,$objConfig->siteRootPath().'usrfiles/content/');
         }
         
        //create 'staticcontent' folder
        $dircreate->makeFolder('staticcontent',$objConfig->siteRootPath().'usrfiles/content/'.$contextCode.'/');
        
        //create 'staticcontent/courseCode' folder      
        $staticFolder = $objConfig->siteRootPath().'usrfiles/content/'.$contextCode.'/staticcontent/';
        
        $this->staticFolder = $staticFolder;
        $this->archive = $objConfig->siteRootPath().'usrfiles/content/'.$contextCode.'/'.$contextCode.'.zip';
        
        //create 'images' folder
        $dircreate->makeFolder('assets',$staticFolder);
        $imagesFolder = $staticFolder.'/assets';        
        
        
        $rootnodeid=$this->objDBContext->getRootNodeId($contextid);        
        $this->objDBContentNodes->resetTable();
        $nodesArr=$this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id='$rootnodeid'");
       
       //create nodes
        $this->createNodes($nodesArr,$rootnodeid);
        //copy images to images folder
        $this->copyImages($imagesFolder,$rootnodeid);
        
        //copy the stylesheet
        $objSkin = & $this->newObject('skin','skin');
        copy($objSkin->getSkinLocation().'/kewl_css.php',$this->staticFolder.'/kewl.css');       

        //copy banners
        copy($objSkin->getSkinLocation().'banners/smallbanner.jpg',$this->staticFolder.'/smallbanner.jpg');

        //copy nav buttons
        copy($objSkin->getSkinLocation().'icons/prev.gif',$this->staticFolder.'/prev.gif');
        copy($objSkin->getSkinLocation().'icons/next.gif',$this->staticFolder.'/next.gif');
        
        //create 'treeimages' folder
        $dircreate->makeFolder('treeimages',$staticFolder);
        $this->copyTreeImages($staticFolder,$objSkin->getSkinLocation());
        
        //copy the TreeMenu.js File
         copy($objConfig->siteRootPath().'core_modules/tree/resources/TreeMenu.js', $staticFolder.'/TreeMenu.js');

         //zip the static folder
         $this->zipFolder();
         
    }
    
     /**
    * Method to create the html file with its content
    * @param string $rootNodeId The Id of the parentNode
    * @param array $nodesArr The array of nodes
    */   
    function createNodes($nodesArr,$rootNodeId)
    {
        $objDublinCore = & $this->newObject('dublincore','dublincoremetadata');
        
        foreach ( $nodesArr as $list)
        {

            $str = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html>
                <head>
                 <link rel="stylesheet" type="text/css" href="kewl.css">
                 <title>'.$this->objDBContentNodes->getMenuText($list['id']).'</title>
                 '. $objDublinCore->getMetadataTags($list['id']).'
                 </head>
                 <body>
                    <div id="container">      
                       	<div id="top">
                    		<img src="smallbanner.jpg" alt="banner">
                         </div>
                         
                         <div id="leftnav" >        
                             <script type="text/javascript" src="TreeMenu.js"></script>
                             <script type="text/javascript" src="treedata.js"></script>                  
                    	     
                    	 </div>    
                    	 
                    	 <div id="content">
                    	 '.$this->replaceImages($this->objDBContentNodes->getBody($list['id'])).'
                    	 <p><p><p>
                    	 '.$this->_getNavButtons($list['prev_Node'], $list['next_Node']).'
                    	 </div>      
                        <div id="footer">
                    	</div>
                    </div>
        	       ';
            $str.='</body></html>';            
            
            //create html file
            $this->_createFile($list['id'],$str);
        }
        
        $this->createIndexFile($nodesArr);
        $this->createTreeFile($nodesArr);
        //$this->createFile('header.html', '<image src="smallbanner.jpg" borger="0">');
        $this->createFile('content.html', 'hello');
    }    
    
    /**
    * Method to create the tree file
    * @param array $nodesArr The array of nodes that is needed to create links
    * @return boolean
    */
    function createTreeFile($nodesArr)
    {       
        $objTree = & $this->newObject('contenttree','tree');        
        
        $fp=@fopen($this->staticFolder.'/treedata.js',"wb");
        if ($fp==FALSE)
        {
            return FALSE; // if we can't write to the specified location, we don't try.
        }
        fwrite($fp,$this->_removeScriptTags($objTree->getStaticTree($nodesArr)));           
        fclose($fp);
        return TRUE; 
    }
    
    
    /**
    * Method to create the index file
    * @param array $nodesArr The array of nodes that is needed to create links
    * @return boolean
    */
    function createIndexFile($nodesArr)
    {
        $objTree = & $this->newObject('contenttree','tree');
        
        $str = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html>
                <head>
                 <link rel="stylesheet" type="text/css" href="kewl.css">
                 <title>'. $this->objDBContext->getTitle($this->getParam('contextCode')).'</title>
                 
                 </head>
                 <body>
                    <div id="container">      
                       	<div id="top">
                    		<img src="smallbanner.jpg" alt="banner">
                         </div>
                         
                         <div id="leftnav" style="height:80%">        
                             <script type="text/javascript" src="TreeMenu.js"></script>
                             <script type="text/javascript" src="treedata.js"></script>                  
                    	     
                    	 </div>    
                    	 
                    	 <div id="content" style="height:80%">
                    	 '.$this->objDBContext->getField('about').'
                    	 </div>      
                        <div id="footer">
                    	</div>
                    </div>
        	       ';
         $str.='</body></html>';
         $this->createFile('index.html', $str);        
    }
    
    /**
    * Method to create a textfile 
    * @param string $fileName The name of the file
    * @param string $content The content of the text file
    * @return boolean
    */    
    function createFile($fileName, $content=NULL){
        $str = $content;
        $fp=@fopen($this->staticFolder.'/'.$fileName,"wb");
        if ($fp==FALSE)
        {
            return FALSE; // if we can't write to the specified location, we don't try.
        }
        fwrite($fp,$str);
        fclose($fp);
        return TRUE; 
    }
    
     /**
    * Method to create the file on the file system 
    * @param string $name The name of the file
    * @param string $folder The path to the files
    */   
    function _createFile($name,$content)
    {
        $fp=@fopen($this->staticFolder.'/'.$name.'.html',"wb");
        if ($fp==FALSE)
        {
            return FALSE; // if we can't write to the specified location, we don't try.
        }
        fwrite($fp,$content);
        fclose($fp);
        return TRUE; 
    }
    
    /**
    * Method to copy all the images to the images folder
    * @param string $rootNodeId The Id of the parentNode
    * @param string $folder The path to the files
    */   
    function copyImages($folder,$rootNodeId)
    {
        $this->objDBContentNodes->changeTable('tbl_context_file');
        $filelist = $this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id = '$rootNodeId'");
        $this->objDBContentNodes->resetTable();
        foreach ($filelist  as $files)
        {
            $this->writeFile($files,$folder);
        }
    }
    
    /**
    * Method to copy the tree images
    * @param string $staticFolder The working folder that the static content is exported to
    * @return null
    */
    function copyTreeImages($staticFolder,$skinFolder)
    {
        //print $skinFolder;
        //die;
        $path = $skinFolder.'treeimages/imagesAlt2';
      //  $path = $this->objConfig->
        $hndl=opendir($path);
		while($SrcPathFile=readdir($hndl))
		{	
			$fileArr=pathinfo($SrcPathFile);
			if (!strcmp(".", $SrcPathFile))continue; // this may not the most efficient way to detect the . and .. entries
			if (!strcmp("..", $SrcPathFile))continue;			
            if (!strcmp("cvs", strtolower($SrcPathFile)))continue;			
			//echo 'Images  ---- '.$path.'/'.$SrcPathFile.'<br>';
			if(is_file($path.'/'.$SrcPathFile)){
                if(! copy($path.'/'.$SrcPathFile, $staticFolder.'/treeimages/'.$SrcPathFile))
                {
                    print $staticFolder.'/treeimages/<br>';
                }
            }				
		}
    }
    
    /**
    * Method to copy the file from the blob to the file system
    * @param array $arr The file information
    * @param string $location The path to the files
    */
    function writeFile($arr, $location)
    {
        $name=$arr['name'];      
        $this->objDBContentNodes->changeTable('tbl_context_filedata');
        $data = $this->objDBContentNodes->getAll("WHERE tbl_context_file_id  = '".$arr['id']."' ORDER BY segment");
        $fp=@fopen($location.'/'.$name,"wb");
        if ($fp==FALSE)
        {
            return FALSE; // if we can't write to the specified location, we don't try.
        }
        
        foreach ($data as $line)
        {           
            fwrite($fp,$line['filedata']);
        }
        
        fclose($fp);
        return TRUE; 
        $this->objDBContentNodes->resetTable();
    
    }
    
    /**
    * Method to write the CSS file
    */
    function writeCSS()
    {
      $location = 'http://localhost/'.$this->objConfig->siteRoot().'skins/'.$this->objConfig->defaultSkin().'/kewl_css.php';  
      $contents = implode('', file($location));
      $fp = fopen($this->staticFolder.'kewl.css','wb');
      fwrite($fp,$contents);
      fclose($fp);      
      
      //print '<pre>';
      //print_r($_SERVER); 
     // print '</pre>';
     /*$url = 'http://'.$_SERVER['SERVER_NAME'].$this->objConfig->siteRoot().'skins/'.$this->objConfig->defaultSkin().'/kewl_css.php';
     // initialize curl handle
     $ch = curl_init();
     // set url to post to
     curl_setopt($ch, CURLOPT_URL,$url);
     // return into a variable
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     // times out after 4s
     curl_setopt($ch, CURLOPT_TIMEOUT, 4);
     //set username and password
     curl_setopt($ch, CURLOPT_PROXYUSERPWD ,'wnitsckie:wheel' );
     // add POST fields
     //curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
     // run the whole process
     $result = curl_exec($ch);
     $result = utf8_encode($result);
     
     $fp = fopen($this->staticFolder.'kewl.css','wb');
     print $result;
     fwrite($fp,$result);
     fclose($fp);      
      */
    }
    
    /**
    * Method to remove to the script tags
    * @param string $str
    * @return string
    */
    function _removeScriptTags($str)
    {
        $str = str_replace('<script language="javascript" type="text/javascript">', "", $str);
        $str = str_replace('</script>', "", $str);
        
        return $str;   
    }
    
    /**
    * Method to add the navigation buttons
    * @param string $prevId The previous Id
    * @param string $nextId The Next Id
    * @return string 
    */
    function _getNavButtons($prevId = NULL, $nextId = NULL)
    {
        $objStr = & $this->newObject('contenttree', 'tree');
        $str = '';
        
        if(!$prevId == NULL)   
        {
            $title = $this->objDBContentNodes->getField('title',$prevId);
            $str = '<a href="'.$prevId.'.html"><img alt="Previous Page:'.$title.'" src="prev.gif" border="0">'.
                $objStr->shortenString($title).'</a>';
        }
     
        if(!$nextId == NULL)   
        {
            $title = $this->objDBContentNodes->getField('title',$nextId);
            $str .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$nextId.'.html">'.
                $objStr->shortenString($title).
                    '<img alt="Next Page:'.$title.'" src="next.gif" border="0"></a>';
        }
        
        return $str;
    }
    
    /**
    * Method to zip the static folder
    */
    function zipFolder()
    {
        $objWZip = $this->newObject('wzip', 'utilities');
        $objWZip->addArchive($this->staticFolder, $this->archive, $this->staticFolder);
    }
    
     /**
    * Method to replace the image tags with 
    * ones pointing to static content
    * @param string $body
    * @return string 
    */
    function replaceImages($body = NULL)
    {
        //print '<PRE>';
        //return if no body
         if($body == NULL)
         {
            return $body;
         } 
         else 
         {
             //find '/contextdownload&id=' in the body
            $str = preg_match_all('/contextdownload&id=\w*/',$body, $matches);
            //check for matches
            if(array_key_exists(0, $matches))
            {
                if(array_key_exists(0, $matches[0]))
                {
                    //loop through matches
                    foreach($matches[0] as $m)
                    {
                        //get the id from the match
                        $id = str_replace('contextdownload&id=','',$m);
                        
                        //get the filename from the matched id
                        $objFile = & $this->newObject('dbfile', 'context');
                        $line = $objFile->getRow('id',$id);
                        $imgName = $line['name'];
                        
                        //create the replacement string
                        $replace = 'assets/'.$imgName;
                        //create the search pattern
                        $pattern = $this->objConfig->siteRoot().'index.php?module=contextview&action=contextdownload&id='.$id;
                        //replace all the found strings 
                        $body = str_replace($pattern, $replace, $body); 
                        //print $pattern.'<br>';
                    }
                }
            }
            return $body;
         }
       // print '</PRE>';
       
    }

	function doXMLExport($context, $filelist, $dirlist, $imsfilepath)
	{
 		//check if you are in a context
		if($this->objDBContext->isInContext())
		{
			//$this->contextCode = $this->objDBContext->getContextCode();
			//Path to imsmanifest.xml file in context
         $filePath = $this->objConfig->getsiteRootPath().'usrfiles/content/'.$context.'/imsmanifest.xml';
			//check if the xml file exist
			if (file_exists($filePath)) 
			{
				//delete the xml file
				unlink($filePath);
			}
         //create the xml file
			$fp = fopen($filePath,'w');
			//add the xml data
			$contents = $this->getXMLData($filelist, $dirlist, $imsfilepath);
			fwrite($fp,$contents);
			//close the file
			fclose($fp);
			//Old path to imsmanifest.xml file in nextgen
			$oldPath = '/opt/lampp/htdocs/nextgen/usrfiles/content/'.$context.'/imsmanifest.xml';
			//check if the old xml file exist
			if (file_exists($oldPath)) 
			{
				//delete the old xml file
				unlink($oldPath);
			}
			$fp = fopen($oldPath,'w');
			//write to file
			fwrite($fp,$contents);
			//close the file
			fclose($fp);
			//create zip file
			//hard coded old system directory paths
			$folderToZip = "/opt/lampp/htdocs/nextgen/usrfiles/content/".$context;
			$folderPath = "/opt/lampp/htdocs/nextgen/usrfiles/exportedimscontext/";
			$newZipFileName = "/opt/lampp/htdocs/nextgen/usrfiles/exportedimscontext/".$context."-ims.zip";
			//zip the context folder
			$this->objWZip->addArchive($folderToZip, $newZipFileName,$folderToZip);
			
			return TRUE;
		} 
		else 
		{
			return FALSE;
		}
	}


	public $manifest = "";
	//Initialize tbl_context variables
	public $id="";
	public $contextcode="";
	public $title="";
	public $menutext="";
	public $about="";
	public $userid="";
	public $datecreated="";
	public $status="";
	public $access="";
	public $lastupdatedby="";
	public $updated="";
	public $startdate="";
	public $finishdate="";
	
	function getXMLData($filelist, $dirlist, $imsfilepath)
	{

		//Global
		global $manifest;
		//Remote host
		$dsn = "localhost";
		//Table to query
		$table = "tbl_context";
		//Query to execute
		$filter = "SELECT * FROM tbl_context";
		//Blog Module
		$this->objBlogImport = &$this->getObject('blogimporter',blog);
		//Set up to connect to the server
		$dsn = $this->objBlogImport->setup($dsn);
		//Connect to the remote db
		$dbobj = $this->objBlogImport->_dbObject();
		//Execute Query
		$data = $this->objBlogImport->queryTable($table,$filter);
		//Retrieve context code
		$contextCode = $this->objDBContext->getContextCode();
		//??Doesnt make sense why proper context code is returned
		//echo $contextCode;
		//Load data from database into variables
		foreach($data as $datas)
		{
			if($datas['contextcode'] == $contextCode)
			{	
				$this->id = $datas['id'];
				$this->contextcode = $datas['contextcode'];
				$this->title = $datas['title'];
				$this->menutext = $datas['menutext'];
				$this->about = $datas['about'];
				$this->userid = $datas['userid'];
				$this->datecreated = $datas['datecreated'];
				$this->status = $datas['status'];
				$this->access = $datas['access'];
				$this->lastupdatedby = $datas['lastupdatedby'];
				$this->updated = $datas['updated'];
				$this->startdate = $datas['startdate'];
				$this->finishdate = $datas['finishdate'];
			}
		}
		//$imsmanifest = $this->imsSkeleton($filelist, $dirlist)->saveXML();
		$imsmanifest = $this->eduIMS($filelist, $dirlist);
		return $imsmanifest;
	}
//===========================================================================	
//eduCommons IMS Skeleton
function eduIMS($filelist, $dirlist)
{
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
$imsmanifest .= "identifier =\"".$this->id."\"";
$imsmanifest .= "version =\"".$this->contextcode."\"";
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
$res .= "<metadata>";
$res .= "<lom>";
$res .= "<general>";
$res .= "<identifier>";
$res .= $this->title;
$res .= "</identifier>\n";
$res .= "<title>";
$res .= "<langstring>";
$res .= "</langstring>\n";
$res .= "</title>\n"; 
$res .= "<language>";
$res .= "</language>\n";
$res .= "<description>";
$res .= "<langstring>";
$res .= "</langstring>\n";
$res .= "</description>\n";
$res .= "<keyword>";
$res .= "</keyword>\n";
$res .= "</general>\n";
$res .= "<lifecycle>";
$res .= "<contribute>";
$res .= "</contribute>\n";
$res .= "</lifecycle>\n";
$res .= "<metametadata>";
$res .= "<catalogentry>";
$res .= "<catalog>";
$res .= "</catalog>";
$res .= "<entry>";
$res .= "</entry>";
$res .= "</catalogentry>\n";
$res .= "<metadataschema>";
$res .= "</metadataschema>\n";
$res .= "<language>";
$res .= "</language>\n";
$res .= "</metametadata>\n";
$res .= "<technical>";
$res .= "<format>";
$res .= "</format>\n";
$res .= "<size>";
$res .= "</size>\n";
$res .= "<location>";
$res .= "</location>\n";
$res .= "</technical>\n";
$res .= "<rights>";
$res .= "<copyrightandotherrestrictions>";
$res .= "<source>";
$res .= "</source>\n";
$res .= "<value>";
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
//===========================================================================
function genORG()
{
$orgCode = "ORG";
$random = mt_rand();
$orgCode = $orgCode.$random;
 
return $orgCode;
}	
//===========================================================================
function genITM()
{
$itmCode = "ITM";
$random = mt_rand();
$itmCode = $itmCode.$random;
 
return $itmCode;
}	
//===========================================================================
function genRES()
{
$resCode = "RES";
$random = mt_rand();
$resCode = $resCode.$random;
 
return $resCode;
}	
//===========================================================================
//IMS Skeleton
function imsSkeleton($filelist, $dirlist)
{
	//Initialiize Global Variables
	$this->initializeGlobals();
	//Create XML document
	$imsDoc = new DomDocument('1.0');
	$imsDoc->formatOutput = true;
	//Manifest
	$manifest = $imsDoc->createElement('manifest');
	$manifest = $imsDoc->appendChild($manifest);
	//Add Schema's Locations
	$xmlns = $manifest->setAttributeNS('','xmlns',$this->xmlns);
	$xmlnsims = $manifest->setAttribute('xmlns:imsmd',$this->xmlnsims);
	$xmlxsi = $manifest->setAttribute('xmlns:xsi',$this->xmlxsi);
	$schemaLocation = $manifest->setAttribute('xsi:schemaLocation',$this->schemaLocation);
	//Add identifier and version
	$identifier = $manifest->setAttributeNS('','identifier',$this->id);
	$version = $manifest->setAttributeNS('','version',$this->contextcode);
	//Metadata
	$metadata = $this->buildMetadataTree($imsDoc, $manifest);
	//Organizations
	$organizations = $imsDoc->createElement('organizations');
	$organizations = $manifest->appendChild($organizations);
	$organizations->setAttributeNS('','default',$this->orgGenCode());
	//Run through all files
	foreach($filelist as $file)
	{
		//Check if file is html
		if(preg_match("/.html/",$file) > 0)
		{
			$imsDoc = $this->buildOrganizationTree($imsDoc, $organizations, "");
		}
	}
	//Resources
	$resources = $imsDoc->createElement('resources');
	$resources = $manifest->appendChild($resources);
	//Run through all files
	foreach($filelist as $file)
	{
		$imsDoc = $this->buildResourceTree($imsDoc, $resources, $file);
	}
	//Run through all directories
	foreach($dirlist as $dir)
	{
		$imsDoc = $this->buildResourceTree($imsDoc, $resources, $dir);
	}
	$this->buildAdditional($imsDoc, $manifest);
	return $imsDoc;
}

//========================================================================
//Global variables
	public $xmlns = "";
	public $xmlnsims = "";
	public $xmlxsi = "";
	public $schemaLocation = "";
	public $schemaVal = "";
	public $schemaversionVal = "";
	
	public $elements = "";
	public $structure = "";
	public $values = "";
	
//Initialize Globals
function initializeGlobals()
{
	//Schema Locations
	$this->xmlns = "http://www.imsglobal.org/xsd/imscp_v1p1";
	$this->xmlnsims= "http://www.imsglobal.org/xsd/imsmd_v1p2";
	$this->xmlxsi= "http://www.w3.org/2001/XMLSchema-instance";
	$this->schemaLocation = "http://www.imsglobal.org/xsd/imscp_v1p1 http://www.imsglobal.org/xsd/imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2.xsd";
	$this->schemaVal = "IMS CONTENT";
	$this->schemaversionVal = "1.2";
}
//========================================================================

//========================================================================
//Build Metadata tree
function buildMetadataTree($imsDoc, $manifest)
{
	//Metadata
	$metadata = $manifest->appendChild(new DOMElement('metadata'));
	$schema = $metadata->appendChild(new DOMElement('schema'));
	$schemaText = $imsDoc->createTextNode($this->schemaVal);
	$schemaText = $schema->appendChild($schemaText);
	$schemaversion = $metadata->appendChild(new DOMElement('schemaversion'));
	$schemaVtext = $imsDoc->createTextNode($this->schemaversionVal);
	$schemaVtext = $schemaversion->appendChild($schemaVtext);
}
//========================================================================

//========================================================================
//Build Organization tree
function buildOrganizationTree($imsDoc, $manifest, $file)
{
	$manifest = $this->organizationSkeleton($imsDoc, $manifest, $file);
	return $imsDoc;
}

//Build Organization Skeleton
function organizationSkeleton($imsDoc, $manifest, $file)
{
	//Get elements as strings
	$elements = $this->organizationElements();
	//Get the structure
	$structure = $this->organizationStructure();
	//Get elements as strings
	$attributes = $this->organizationAttributes();
	//Get the structure
	$values = $this->organizationAttrValues("identifier");
	//Create DOM elements with attributes
	$arrayElements = $this->createElementNodes($imsDoc, $elements, $attributes, $values);
	//Run through Nodes appending children according to structure
	$manifest = $this->buildOrganization($manifest, $arrayElements, $structure);
	return $manifest;
}

//Build Single Organization 
function buildOrganization($manifest, $arrayElements, $structure)
{
	for($i=0;$i<count($arrayElements);$i++)
	{
		if($i==0)
		{
			//Create organization
			$manifest = $manifest->appendChild($arrayElements[$i]);
			//Set attributes
			$arrayElements[$i]->setAttributeNS("","xmlns",$this->xmlns);
		}
		else
		{
			//Retrieve child
			$tempChild = $arrayElements[$i];
			//Retrieve parent
			$tempParent = $arrayElements[$structure[$i]-1]; 
			//Append child to parent
			$tempParent->appendChild($tempChild);
			//Set attributes
			$arrayElements[$i]->setAttributeNS("","xmlns",$this->xmlns);
		}
	}
	return $manifest;
}

//Create DOM Element Nodes
function createElementNodes($imsDoc, $elements, $attributes, $values)
{
	//Run through list of elements as String and create new nodes
	for($i=0;$i<count($elements);$i++)
	{
		$arrayElements[$i] = $imsDoc->createElement($elements[$i]);
		//Run through all nodes and add attribute/value pairs
		$count = count($attributes);
		//Check if there are any attributes and if its a new node
		if($count>0 && $i==0)
		{
			for($j=0;$j<count($attributes);$j++)
			{
				$arrayElements[$i]->setAttributeNS("",$attributes[$j],$values[$j]);
			}
		}
	}
	return $arrayElements;
}

//Add Textnodes to Elements
function createTextNodes($imsDoc, $arrayElements, $text)
{
	for($i=0;$i<count($arrayElements);$i++)
	{
		$arrayTextElements = $imsDoc->createTextNode($text[$i]);
		$arrayElements[$i]->appendChild($arrayTextElements);
	}
	return $arrayElements;
}

//Get Organization Elements 
function organizationElements()
{
	$elements[0] = "organization";
	$elements[1] = "item";
	$elements[2] = "title";
	
	return $elements;
}

//Get Organization Structure
function organizationStructure()
{
	$structure[0] = 1;
	$structure[1] = 1;
	$structure[2] = 2;
	
	return $structure;
}

function organizationAttributes()
{
	$attributes[0] = "identifier";
	
	return $attributes;
}

function organizationAttrValues($identifier)
{
	$values[0] = $identifier;
	
	return $values;
}

//Generates a Random Code for organization default
function orgGenCode()
{
$organizationCode = "ORG";

return $organizationCode;
}
//========================================================================

//========================================================================
//Build Resource tree
function buildResourceTree($imsDoc, $manifest, $file)
{
	$manifest = $this->resourceSkeleton($imsDoc, $manifest, $file);
	
	return $imsDoc;
}

//Build Resource Skeleton
function resourceSkeleton($imsDoc, $manifest, $file)
{
	//Get elements as strings
	$this->elements = $this->resourceElements();
	//Get the structure as strings
	$this->structure = $this->resourceStructure();
	//Get attributes as strings
	$attributes = $this->resourceAttributes();
	//Get values as strings
	$this->values = $this->resourceValues();
	//Get additional attributes
	$attrValues = $this->resourceAttrValues($this->resGenCode(), $this->resServLoc(), $this->resType());
	//Get General Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->generalElements(), $this->generalStructure(), $this->generalValues());
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("12"), $this->langstringValues($this->title));
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("14"), $this->langstringValues(""));
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("15"), $this->langstringValues(""));
	//Get lifecycle Elements and Structure
	//??multiple duplicate elemets.Not sure how to generate
	$this->addToResource($this->elements, $this->structure, $this->values, $this->lifecycleElements(), $this->lifecycleStructure(), $this->lifecycleValues());
	//Get metametadata Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->metametadataElements(), $this->metametadataStructure(), $this->metametadataValues("en"));
	//Get catalogentry Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->catalogentryElements(), $this->catalogentryStructure(), $this->catalogentryValues(""));
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure(count($this->elements)), $this->langstringValues($file));
	//Get contribute Elements and Structure
	//??multiple duplicate elemets.Not sure how to generate
	$this->addToResource($this->elements, $this->structure, $this->values, $this->contributeElements(), $this->contributeStructure(), $this->contributeValues());
	//Get role Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->roleElements(), $this->roleStructure("27"), $this->roleValues());
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("31"), $this->langstringValues(""));
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("30"), $this->langstringValues(""));
	//Get centity Elements and Structure
	//??multiple duplicate elemets.Not sure how to generate.And why
	$this->addToResource($this->elements, $this->structure, $this->values, $this->centityElements(), $this->centityStructure(), $this->centityValues());
	//Get datetime Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->datetimeElements(), $this->datetimeStructure(), $this->datetimeValues());	
	//Get technical Elements and Structure   
	$this->addToResource($this->elements, $this->structure, $this->values, $this->technicalElements(), $this->technicalStructure(), $this->technicalValues());
	//Get rights Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->rightsElements(), $this->rightsStructure(), $this->rightsValues());
	//Get langstring Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->langstringElements(), $this->langstringStructure("40"), $this->langstringValues(""));
	//Get role Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->roleElements(), $this->roleStructure("39"), $this->roleValues());	
	//Get eduCommons Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->eduCommonsElements(), $this->eduCommonsStructure(), $this->eduCommonsValues());
	//Get license Elements and Structure
	$this->addToResource($this->elements, $this->structure, $this->values, $this->licenseElements(), $this->licenseStructure(), $this->licenseValues());
	//Create DOM elements with attributes 
	$arrayElements = $this->createElementNodes($imsDoc, $this->elements, $attributes, $attrValues);
	//Create DOM text elements
	$this->createTextNodes($imsDoc, $arrayElements, $this->values);
	//Run through Nodes appending children according to structure
	$manifest = $this->buildResource($manifest, $arrayElements, $this->structure);

	return $manifest;
}

//Build Single Resource 
function buildResource($manifest, $arrayElements, $structure)
{
	for($i=0;$i<count($arrayElements);$i++)
	{
		if($i==0)
		{
			//Create organization
			$manifest = $manifest->appendChild($arrayElements[$i]);
		}
		else
		{
			//Retrieve child
			$tempChild = $arrayElements[$i];
			//Retrieve parent
			$tempParent = $arrayElements[$structure[$i]-1];
			//Append child to parent
			$tempParent->appendChild($tempChild);
		}
	}
	
	return $manifest;
}

//Build Additional Attributes
function buildAdditional($imsDoc, $manifest)
{
	//Get elements as strings
	$elements = $this->generalElements();
	//Get the structure
	$structure = $this->generalStructure();
	//Create DOM elements
	$arrayElements = $this->createElementNodes($imsDoc, $elements,NULL,NULL);
	//Run through Nodes appending children according to structure
	$general = $imsDoc->getElementsByTagName('general');
	
	foreach($general as $gen)
	{
		foreach($arrayElements as $ele)
		{
			$gen->appendChild($ele);
		}
	}
	
}

//Generates a Random Code for resource identifier
function resGenCode()
{
	$resourceCode = "RES";

	return $resourceCode;
}

//Returns the resource type
function resType()
{
	$resourceType = "html";

	return $resourceType;
}

//Returns the resource location on server
function resServLoc()
{
	$serverLoc = $this->objConfig->getsiteRoot();

	return $serverLoc;
}

//Get Resource Elements
function resourceElements()
{
	$elements[0] = "resource";
	$elements[1] = "metadata";
	$elements[2] = "lom";
	$elements[3] = "general";
	$elements[4] = "lifecycle";
	$elements[5] = "metametadata";
	$elements[6] = "technical";
	$elements[7] = "rights";
	$elements[8] = "file";
	$elements[9] = "eduCommons";
	
	return $elements;
}

//Get Resource Structure
function resourceStructure()
{
	$structure[0] = 0;
	$structure[1] = 1;
	$structure[2] = 2;
	$structure[3] = 3;
	$structure[4] = 3;
	$structure[5] = 3;
	$structure[6] = 3;
	$structure[7] = 3;
	$structure[8] = 1;
	$structure[9] = 2;
	
	return $structure;
}

//Get general Values
function resourceValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	$values[3] = "";
	$values[4] = "";
	$values[5] = "";
	$values[6] = "";
	$values[7] = "";
	$values[8] = "";
	$values[9] = "";
	
	return $values;
}

//Additional Resource Attributes
function resourceAttributes()
{
	$attributes[0] = "identifier";
	$attributes[1] = "type";
	$attributes[2] = "href";
	
	return $attributes;
}

//Additional Resource Attributes Values
function resourceAttrValues($identifier, $type, $href)
{
	$values[0] = $identifier;
	$values[1] = $type;
	$values[2] = $href;
	
	return $values;
}

//Additional langstring Attributes
function langstringAttributes()
{
	$attributes[0] = "lang";
	
	return $attributes;
}

//Additional langstring Attributes Values
function langstringAttrValues($lang)
{
	$values[0] = $lang;
	
	return $values;
}

//Get langstring Values
function langstringValues($value)
{
	$values[0] = $value;
	
	return $values;
}

//General Elements
function generalElements()
{
	$elements[0] = "identifier";
	$elements[1] = "title";
	$elements[2] = "language";
	$elements[3] = "description";
	$elements[4] = "keyword";

	return $elements;

}
//General Structure
function generalStructure()
{
	$structure[0] = 4;
	$structure[1] = 4;
	$structure[2] = 4;
	$structure[3] = 4;
	$structure[4] = 4;

	return $structure;
}

//Get general Values
function generalValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	$values[3] = "";
	$values[4] = "";
	
	return $values;
}

//Lifecycle Elements
function lifecycleElements()
{
	$elements[0] = "contribute";

	return $elements;
}

//Lifecycle Structure
function lifecycleStructure()
{
	$structure[0] = 5;

	return $structure;
}

//Get general Values
function lifecycleValues()
{
	$values[0] = "";
	
	return $values;
}

//Metametadata Elements
function metametadataElements()
{
	$elements[0] = "catalogentry";
	$elements[1] = "contribute";
	$elements[2] = "metadatascheme";
	$elements[3] = "language";
	
	return $elements;
}

//Metametadata Structure
function metametadataStructure()
{
	$structure[0] = 6;
	$structure[1] = 6;
	$structure[2] = 6;
	$structure[3] = 6;
	
	return $structure;
}

//Get metametadata Values
function metametadataValues($language)
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "LOMv1.0";
	$values[3] = $language;
	
	return $values;
}

//Technical Elements
function technicalElements()
{
	$elements[0] = "format";
	$elements[1] = "size";
	$elements[2] = "location";
	
	return $elements;
}

//Technical Structure
function technicalStructure()
{
	$structure[0] = 7;
	$structure[1] = 7;
	$structure[2] = 7;
	
	return $structure;
}

//Get technical Values
function technicalValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	
	return $values;
}

//Rights Elements
function rightsElements()
{
	$elements[0] = "copyrightandotherrestrictions";
	$elements[1] = "description";
	
	return $elements;
}

//Rights Structure
function rightsStructure()
{
	$structure[0] = 8;
	$structure[1] = 8;
	
	return $structure;
}

//Get rights Values
function rightsValues()
{
	$values[0] = "";
	$values[1] = "";
	
	return $values;
}

//Get eduCommons Elements
function eduCommonsElements()
{
	$elements[0] = "objectType";
	$elements[1] = "copyright";
	$elements[2] = "license";
	$elements[3] = "clearedCopyright";
	$elements[4] = "courseId";
	$elements[5] = "term";
	$elements[6] = "displayInstructorEmail";
	
	return $elements;
}

//Get eduCommons Structure
function eduCommonsStructure()
{
	$structure[0] = 10;
	$structure[1] = 10;
	$structure[2] = 10;
	$structure[3] = 10;
	$structure[4] = 10;
	$structure[5] = 10;
	$structure[6] = 10;
	
	return $structure;
}

//Get eduCommons Values
function eduCommonsValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	$values[3] = "";
	$values[4] = $this->contextcode;
	$values[5] = "";
	$values[6] = "";
	
	return $values;
}

//langstring Elements
function langstringElements()
{
	$elements[0] = "langstring";
	
	return $elements;
}

//langstring Structure
//arguments : int : the position of node in tree
function langstringStructure($node)
{
	$structure[0] = $node;
	
	return $structure;
}

//catalogentry Elements
function catalogentryElements()
{
	$elements[0] = "catalog";
	$elements[1] = "entry";
	
	return $elements;
}

//catalogentry Structure
function catalogentryStructure()
{
	$structure[0] = 20;
	$structure[1] = 20;
		
	return $structure;
}

//Get eduCommons Values
function catalogentryValues($file)
{
	$values[0] = $this->objConfig->getsiteRoot();
	$values[1] = $file;
	
	return $values;
}

//contribute Elements
function contributeElements()
{
	$elements[0] = "role";
	$elements[1] = "centity";
	$elements[2] = "date";
	
	return $elements;
}

//contribute Structure
function contributeStructure()
{
	$structure[0] = 21;
	$structure[1] = 21;
	$structure[2] = 21;
		
	return $structure;
}

//Get contribute Values
function contributeValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	
	return $values;
}

//role Elements
function roleElements()
{
	$elements[0] = "source";
	$elements[1] = "value";
	
	return $elements;
}

//role Structure
function roleStructure($node)
{
	$structure[0] = $node;
	$structure[1] = $node;
		
	return $structure;
}

//Get role Values
function roleValues()
{
	$values[0] = "";
	$values[1] = "";
	
	return $values;
}

//centity Elements
function centityElements()
{
	$elements[0] = "vcard";
	
	return $elements;
}

//centity Structure
function centityStructure()
{
	$structure[0] = 28;
		
	return $structure;
}

//Get centity Values
function centityValues()
{
	$values[0] = "";
	
	return $values;
}

//datetime Elements
function datetimeElements()
{
	$elements[0] = "datetime";
	
	return $elements;
}

//datetime Structure
function datetimeStructure()
{
	$structure[0] = 29;
		
	return $structure;
}

//Get datetime Values
function datetimeValues()
{
	$values[0] = "";
	
	return $values;
}

//license Elements
function licenseElements()
{
	$elements[0] = "licenceName";
	$elements[1] = "licenceUrl";
	$elements[2] = "licenceIconUrl";
	
	return $elements;
}

//license Structure
function licenseStructure()
{
	$structure[0] = 46;
	$structure[1] = 46;
	$structure[2] = 46;
			
	return $structure;
}

//Get license Values
function licenseValues()
{
	$values[0] = "";
	$values[1] = "";
	$values[2] = "";
	
	return $values;
}

//========================================================================
//Additional Functions
//========================================================================
//Merges 2 arrays, namely the Nodes and Structure arrays
//arguments : array() :	existing elements
//				: array() :	existing structure
//				: array() :	existing values
//				: array() :	new elements
//				: array() : new structure
//				: array() : new values
function addToResource($elements, $structure, $values, $subElements, $subStructure, $subValues)
{
	//Add Elements
	$this->elements = array_merge($elements, $subElements); 
	//Add Structure
	$this->structure = array_merge($structure, $subStructure);
	//Add Values
	$this->values = array_merge($values, $subValues);
}

}

/*
function resourceAttributes($root)
{

}

$nodes[0] = "resources";
$nodes[1] = "resource";
$nodes[2] = "metadata";
$nodes[3] = "lom";
$nodes[4] = "general";
$nodes[5] = "lifecycle";
$nodes[6] = "metametadata";
$nodes[7] = "technical";
$nodes[8] = "rights";

$parent[0] = 0;
$parent[1] = 1;
$parent[2] = 1;
$parent[3] = 1;
$parent[4] = 2;
$parent[5] = 2;
$parent[6] = 2;
$parent[7] = 2;
$parent[8] = 2;
*/

/*
function attachChild($root, $child, $parent)
{
$newElement = new DOMElement($child);
	if(strlen($parent)>0)
	{
		$pElement = new DOMElement($parent);
		return $root->appendChild($pElement);
	}
	else
	{
		return $root->appendChild($newElement);
	}
}
*/
/*
function attachChild($root, $child, $parent)
{
	$childElement = new DOMElement($child);
	//$parentElement = new DOMElement($parent);
	//echo $parent."p";
	//echo $parentElement;
	echo $childElement."c";
	if(strlen($parent)>0)
	{
	return $parent->appendChild($childElement);
	}
	else
	{
	return $root->appendChild($childElement);
	}
}
*/

/*
function attachChild($root, $child)
{
	$childElement = new DOMElement($child);
	return $root->appendChild($childElement);
}

function buildTree($manifest, $nodes, $parent, $index)
{
	for($i=0;$i <= count($nodes);$i++)
	{
		$childElement = new DOMElement($nodes[i]);
		if($parent[$i] == 1)
		{
			
			//$manifest = $this->attachChild($manifest, $nodes[i]);
		}
		else
		{
		
		}		
	}
}
*/

/*
function buildTree($manifest, $nodes, $parent, $index)
{
for($i=0;$i <= count($nodes);$i++)
{
	if($childElement = new DOMElement($nodes[$i]))
	{
		echo "kewl";
	}
	else
	{
		echo "unkewl";	
	}
	//$preChildElement = new DOMElement($nodes[$i-1]);

try{
$childElement = new DOMElement($nodes[$i]);
}catch(DOMExceptoin $e){
print $e."error";
}
	
	if($parent[$i] == 1)
	{
		//$childElement = new DOMElement($nodes[$i]);
		//$manifest = $this->attachChild($manifest, $childElement);
		echo "1";
	}
	else
	{
//		$childElement = new DOMElement($nodes[$i]);
		//$manifest = $this->attachChild($prevNode, $childElement);
		echo "0";
	}
}

for($i=0;$i<count($nodes);$i++)
{
	if($parent[$i] == 0)
		{
		$manifest = $this->attachChild($manifest, $nodes[$i]);
		}
		else
		{
		$manifest = $this->attachChild($nodes[$i-1], $nodes[$i]);
		}
	}
return $manifest; 
}
*/

/*
function buildTree($manifest, $nodes, $parent, $index)
{
//Create new element
$newElement = new DOMElement($nodes[$index]);
//Check if its a root node
echo $index."\n";
		if($parent[$index] == 0 && $index < count($nodes))
		{
			$manifest->appendChild($newElement);
			//$this->buildTree($manifest, $nodes, $parent, $index++);
		}
}
*/

/*
function buildTree($manifest, $nodes, $parent, $index)
{
$newElement = new DOMElement($nodes[$index]);
echo $index."\n";
		if($parent[$index] == 0 && $index < count($nodes))
		{
			$manifest->appendChild($newElement);
			$this->buildTree($manifest, $nodes, $parent, $index++);
		}
		else if($parent[$index] == 1 && $index < count($nodes))
		{
			$this->buildTree($nodes[$index], $nodes, $parent, $index++);
		}
		else if($index > count($nodes)){
		return $manifest; 		
		}
		//return $this->buildTree($manifest, $nodes, $parent, $index++);		
}
*/

//Schema
//$schema = appendChild($metadata,"schema","IMS CONTENT",$imsDoc);
//Schema Version
//imsmd:lom Elements
//$schemaversion = appendChild($metadata,"schemaversion","1.2",$imsDoc);
//lom
//$imsmdLom= appendChild($metadata,"imsmd:lom","",$imsDoc);
//lom elements
//$imsmdGen= appendChild($imsmdLom,"imsmd:general","",$imsDoc);
//$imsmdLif= appendChild($imsmdGen,"imsmd:lifecycle","",$imsDoc);
//$imsmdTec= appendChild($imsmdLif,"imsmd:technical","",$imsDoc);
//general elements
//$imsmdTit= appendChild($imsmdGen,"imsmd:title","",$imsDoc);


//$metadata = $imsDoc->createElement('metadata');
//$organizations = $imsDoc->createElement('organizations');
//$resources = $imsDoc->createElement('resources');
//$metadata = $imsDoc->appendChild($metadata);
//$organizations = $imsDoc->appendChild($metadata);
//$resources = $imsDoc->appendChild($metadata);
//$metadat  = $metadata->appendChild(new DOMElement('toot'));


/*
	function getXMLData1()
	{
	//$content = $this->collect_object_infos("title", "description");
	$content = $this->collect_object_tree("id", "firstfile", "structure");
	return $content;
	}

	function getXMLData2()
	{
	//$content = $this->collect_object_infos("title", "description");
	$content = $this->collect_object_infos("title", "description");
	return $content;
	}
		
	function parsestring($str, $separator){
		$tok = strtok($str, $separator);
		$iter = 0;
		$string_array = array();
		while ($tok) {
			$string_array[$iter] = trim( strtolower($tok) );
			$tok = strtok($separator);
			$iter++;
		}
		return $string_array;
	}

	function seems_utf8($Str) {
		for ($i=0; $i<strlen($Str); $i++) {
			if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
			else 
				return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	function utf8_ensure($str) {
		return $this->seems_utf8($str)? $str: $this->utf8_encode($str);
	}

	function createXML($content, $name){
        if( ($fd = fopen($name, "wt")) == false )
			return false; //can't create file
		
		if( !fwrite($fd, $content) ){
			fclose($fd);
			return false; // can't add content to the file
		}
		else {
			fclose($fd);
			return true;
		}
	}
	
	//METADATA
	function collect_object_infos($title, $description){		
		$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content.="<lom xmlns=\"http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd\">\n";
		$content.="<general>\n";
		
		//TITLE
		$content.="<title>\n<langstring xml:lang=\"en\">".$this->utf8_ensure($title)."</langstring>\n</title>\n";
		
		//Catalog entry (save MOODLE SERVER - COURSE NUMBER - OBJECT NUMBER)
		$content.="<catalogentry>\n";
		$content.="<catalog></catalog>\n";
		$content.="<entry><langstring></langstring></entry>\n";
		$content.="</catalogentry>\n";
		
		$content.="<language></language>\n";
		
		//DESCRIPTION
		$content.="<description>\n";
		$content.="<langstring xml:lang=\"en\">".$this->utf8_ensure($description)."</langstring>\n";
		$content.="</description>\n";
		
		//EMPTY PART start
		$content.="<aggregationlevel>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n";				
		$content.="<value><langstring xml:lang=\"x-none\">2</langstring></value>\n";
		$content.="</aggregationlevel>\n";
		
		//Keywords (EMPTY)
		$content.="<keyword>\n";
		$content.="<langstring xml:lang=\"en\"></langstring>\n";
		$content.="</keyword>\n";
		
		$content.="</general>\n";
		$content.="\n";
		$content.="<lifecycle>\n";
		$content.="<status>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOM v1.0</langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\">Final</langstring></value>\n";
		$content.="</status>\n";
		$content.="<contribute>\n";
		$content.="<role>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOM v1.0</langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\">Instructional Designer</langstring></value>\n";
		$content.="</role>\n";

		$content.="<centity>\n";
		$content.="<vcard></vcard>\n";
		$content.="</centity>\n";
		$content.="<date>\n";
		$content.="<datetime></datetime>\n";
		$content.="</date>\n";
		$content.="</contribute>\n";
		$content.="</lifecycle>\n";
		$content.="\n";
		$content.="<technical>\n";
		$content.="<format></format>\n";
		$content.="<size></size>\n";
		$content.="<location type=\"URI\"></location>\n";
		$content.="<requirement>\n";
		$content.="<type>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOM v1.0</langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\"></langstring></value>\n";
		$content.="</type>\n";
		$content.="<name>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOM v1.0</langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\"></langstring></value>\n";
		$content.="</name>\n";
		$content.="<minimumversion></minimumversion>\n";
		$content.="<maximumversion></maximumversion>\n";
		$content.="</requirement>\n";
		$content.="</technical>\n";
		$content.="\n";
		$content.="<educational>\n";
		//Type of resource (EMPTY)
		$content.="<learningresourcetype>\n";
		$content.="<source><langstring xml:lang=\"x-none\"></langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\"></langstring></value>\n";
		$content.="</learningresourcetype>\n";
		$content.="<intendedenduserrole>\n";
		$content.="<source><langstring xml:lang=\"x-none\"></langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\"></langstring></value>\n";
		$content.="</intendedenduserrole>\n";
		$content.="<context>\n";
		$content.="	<source><langstring xml:lang=\"x-none\"></langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\"></langstring></value>\n";
		$content.="</context>\n";
		//Time required (0)
		$content.="<typicallearningtime><datetime>00:00:00</datetime></typicallearningtime>\n";
		$content.="<language></language>\n";		
		$content.="</educational>\n";
		$content.="\n";
		$content.="<rights>\n";
		$content.="<copyrightandotherrestrictions>\n";
		$content.="<source><langstring xml:lang=\"x-none\">LOM v1.0</langstring></source>\n";
		$content.="<value><langstring xml:lang=\"x-none\">yes</langstring></value>\n";
		$content.="</copyrightandotherrestrictions>\n";
		$content.="<description><langstring xml:lang=\"en\"></langstring></description>\n";
		$content.="</rights>\n";
		//EMPTY PART end
		$content.="</lom>\n";
		
		return $content;
	}

	//MANIFEST
	function collect_object_tree($id, $firstfile, $structure){		
		//Initialize the 3 parts of the imsmanifest content
		$precontent="";
		$content="";
		$organization="";
		//Generate the imsmanifest content
		$precontent.="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$precontent.="<manifest xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd\">\n";
		$precontent.="<metadata>\n";
		$precontent.="<schema>IMS Content</schema>\n";
		$precontent.="<schemaversion>1.1</schemaversion>\n";
		$precontent.="<location>metadata_".$id.".xml</location>\n";
		$precontent.="</metadata>\n";
		$precontent.="<organizations>\n";
		$content.="</organizations>\n";
		$content.="<resources>\n";
		$res=0;
		$org=0;
		$item=0;
		
		if($structure == "metaims"){
            //Insert files
            foreach($firstfile as $file){
                $content.="<resource identifier=\"RESOURCE_".$res."\" type=\"webcontent\" href=\"".$file."\">\n";
                $content.="<file href=\"".$file."\"/>\n";
                $content.="</resource>\n";
                $res = $res+1;
            }
        }else{
            //Insert firstfile or url
		    $content.="<resource identifier=\"RESOURCE_".$res."\" type=\"webcontent\" href=\"".$firstfile."\">\n";
		    if($structure != "")
			    $content.="<file href=\"".$firstfile."\"/>\n";
		    $content.="</resource>\n";
		    //Organization
		    $organization.="<organization identifier=\"ORGANIZATION_".$org."\">\n";
		    $organization.="<title>Default Organization</title>\n";
		    $organization.="<item identifier=\"ITEM_".$item."\" identifierref=\"RESOURCE_".$res."\"></item>\n";
		    $organization.="</organization>\n";
		}
        
		$content.="</resources>\n";
		$content.="</manifest>\n";
		return $precontent.$organization.$content;
	}
	*/

/*
	function doXMLWrite()
	{
		$xml = simplexml_load_file("imsmanifest.xml");
		//Hard Coded Variables
		//Values
		$id = "nextgen_1";
		$contextcode = "jart101";
		$title = "tester1";
		$menutext = "menuT1";
		$about = "aboutthis page1";
		$userid = "1";
		$datecreated = "2007-01-31";
		$status = "0";
		$access = "1";
		$lastupdatedby = "1";
		$updated = "2007-01-31 14:19:30";
		$startdate = "2007-01-22 12:12:39";
		$finishdate = "NULL";
		//step 1
		//Course Code
		//Title
		//Menu Text
		//Status
		//Access
		//step 2
		//About
		//step 3
		//This not needed yet,right!?!
		//echo $contextCode;
	}
*/
/*
		foreach($data as $datas)
		{
			echo $datas['id']."  |  ";
        		echo $datas['contextcode']."  |  ";
                        echo $datas['title']."  |  ";
                        echo $datas['menutext']."  |  ";
                        echo $datas['about']."  |  ";
                        echo $datas['userid']."  |  ";
                        echo $datas['datecreated']."  |  ";
                        echo $datas['metadata_id']."  |  ";
			echo $datas['isclosed']."  |  ";
                        echo $datas['isactive']."  |  ";
                        echo $datas['isdigitallibrary']."  |  ";
                        echo $datas['updated']."  |  ";
			echo "---------------------------\n";
		}

//		var_dump($data);

		//Hard Coded Variables
		//Values
		$id = "init_6849_1170245942";
		$contextcode = "101CC";
		$title = "101T";
		$menutext = "101MT";
		$about = "101A";
		$userid = "1";
		$datecreated = "2007-01-31";
		$status = "Published";
		$access = "public";
		$lastupdatedby = "1";
		$updated = "2007-01-31 14:19:30";
		$startdate = "NULL";
		$finishdate = "NULL";
				
		//Hard Coded Column Names
		$idName = "id";
		$contextcodeName = "contextcode";
		$titleName = "title";
		$menutextName = "menutext";
		$useridName = "userid";
		$aboutName = "about";
		$datecreatedName = "datecreated";
		$statusName = "status";
		$accessName = "access";
		$lastupdatedbyName = "lastupdatedby";
		$updatedName = "updated";
		$startdateName = "startdate";
		$finishdateName = "finishdate";
*/

/*
public $listOfFiles;
function setFileList($listOfFiles)
{
	$listOfFiles = $files;
}
function getFileList()
{
	return $listOfFiles;
}

function organizationSkeleton($filelist)
{
			foreach($filelist as $files)
		{
		echo $files."\n";
			}
}
*/

/*
function buildTree($manifest, $nodes)
{
	foreach($nodes as $node)
	{
		$manifest->appendChild(new DOMElement($node));
	}
}
*/

		/*
		//Resources
		$resources = $manifest->appendChild(new DOMElement('resources'));
		//Resource
		$resource = $resources->appendChild(new DOMElement('resource'));
		//imsmd:lom Elements
		//lom
		$imsmdLom = new DOMElement('imsmd:lom', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$resource->appendChild($imsmdLom);
		//lom elements
		//general
		$imsmdGen = new DOMElement('imsmd:general', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdGen);
		//lifecycle
		$imsmdLif = new DOMElement('imsmd:lifecycle', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdLif);
		//technical
		$imsmdTec = new DOMElement('imsmd:technical', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdTec);
		*/
		
			//Schema Locations
	//$xmlns ="http://www.imsglobal.org/xsd/imscp_v1p1";
	//$xmlnsims="http://www.imsglobal.org/xsd/imsmd_v1p2";
	//$xmlxsi="http://www.w3.org/2001/XMLSchema-instance";
	//$schemaLocation = "http://www.imsglobal.org/xsd/imscp_v1p1 http://www.imsglobal.org/xsd/imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2.xsd";
	
	//$metadata = $imsDoc->createElement('metadata');
	//$metadata = $manifest->appendChild($metadata);
	
	/*
//	$resource = $imsDoc.getElementsByTagName('resource');
	$dom = new DomDocument;
	$dom->preserveWhiteSpace = FALSE;
	$dom->loadXML($imsDoc->saveXML());
		
	$params = $dom->getElementsByTagName('*');

foreach ($params as $param) {
	//echo $param -> getAttribute('resource').'<br>';
	echo $param->getAttribute("resources")."--";
}


function addGeneral($general)
{
	//Get elements as strings
	$elements = $this->generalElements();
	//Get the structure
	$structure = $this->generalStructure();
	$arrayElements = $this->createElementNodes($imsDoc, $elements,NULL,NULL);
	foreach($general as $gen)
	{
		foreach($arrayElements as $ele)
		{		
			$gen->appendChild($ele);
		}
	}
		//echo $gen;
}

	//$this->addToResource($elements, $structure, $this->Elements(), $this->Structure());

	//Get general elements as strings
	$genElements = $this->generalElements();
	//Get the general structure
	$genStructure = $this->generalStructure();
	//Add elements
	$elements = array_merge($elements, $genElements); 
	//Add Structure
	$structure = array_merge($structure, $genStructure);
	//Get langstring elements as strings
	$lanElements = $this->langstringElements();
	//Get the langstring structure
	$lanStructure = $this->langstringStructure("12");
	//Add elements
	$elements = array_merge($elements, $lanElements);
	//Add Structure
	$structure = array_merge($structure, $lanStructure);
	//Get langstring elements as strings
	$lanElements = $this->langstringElements();
	//Get the langstring structure
	$lanStructure = $this->langstringStructure("14");
	//Add elements
	$elements = array_merge($elements, $lanElements);
	//Add Structure
	$structure = array_merge($structure, $lanStructure);
	//Get langstring elements as strings
	$lanElements = $this->langstringElements();
	//Get the langstring structure
	$lanStructure = $this->langstringStructure("15");
	//Add elements
	$elements = array_merge($elements, $lanElements);
	//Add Structure
	$structure = array_merge($structure, $lanStructure);
	//Get lifecycle elements as strings
	$lifElements = $this->lifecycleElements();
	//Get the lifecycle structure
	$lifStructure = $this->lifecycleStructure();
	//Add elements
	$elements = array_merge($elements, $lifElements); 
	//Add Structure
	$structure = array_merge($structure, $lifStructure);
	//Get metametadata elements as strings
	$metElements = $this->metametadataElements();
	//Get the metametadata structure
	$metStructure = $this->metametadataStructure();
	//Add elements
	$elements = array_merge($elements, $metElements); 
	//Add Structure
	$structure = array_merge($structure, $metStructure);
	//Get technical elements as strings
	$tecElements = $this->technicalElements();
	//Get the technical structure
	$tecStructure = $this->technicalStructure();
	//Add elements
	$elements = array_merge($elements, $tecElements); 
	//Add Structure
	$structure = array_merge($structure, $tecStructure);
	//Get rights elements as strings
	$rigElements = $this->rightsElements();
	//Get the rights structure
	$rigStructure = $this->rightsStructure();
	//Add elements
	$elements = array_merge($elements, $rigElements); 
	//Add Structure
	$structure = array_merge($structure, $rigStructure);	
	//Get eduCommons elements as strings
	$eduElements = $this->eduCommonsElements();
	//Get the eduCommons structure
	$eduStructure = $this->eduCommonsStructure();
	//Add elements
	$elements = array_merge($elements, $eduElements); 
	//Add Structure
	$structure = array_merge($structure, $eduStructure);
	//Create DOM elements with attributes 
	$arrayElements = $this->createElementNodes($imsDoc, $elements, $attributes, $values);

//Get nodes with titles
function titleNodes()
{
	$title[0] = 0;
	$title[0] = 0;
	$title[0] = 1;
	return $title;
}

function organizationAttributes()
{

}

function domTree($manifest, $nodes, $parent, $filelist)
{
//Create XML document
$imsDoc = new DomDocument('1.0');
$imsDoc->formatOutput = true;

//Manifest
$manifest = $imsDoc->appendChild(new DOMElement('manifest'));
		$xmlns ="http://www.imsglobal.org/xsd/imscp_v1p1";
//Create DOM element nodes
	for($i=0;$i<count($nodes);$i++)
	{
		$arrayNodes[$i] = $imsDoc->createElement($nodes[$i]);
	}
	
	//for($j=0;$j<count($filelist);$j++)
	//{
		for($i=0;$i<count($nodes);$i++)
		{
			if($i==0)
			{
				$manifest = $manifest->appendChild($arrayNodes[$i]);
				$arrayNodes[$i]->setAttributeNS("","xmlns",$xmlns);
			}
			else
			{
				$tempChild = $arrayNodes[$i];
				$tempParent = $arrayNodes[$parent[$i]-1];
				$tempParent->appendChild($tempChild);
				$arrayNodes[$i]->setAttributeNS("","xmlns",$xmlns);
			}
		}
		$arrayNodes[8]->setAttributeNode(new DOMAttr('href', $filelist[1]));
		
	//}
		$imsmanifest = $imsDoc->saveXML();
		foreach($filelist as $files)
		{
		echo $files;
		}
		return $imsmanifest;
}

//=============================================================================
		//Schema Locations
		$xmlns ="http://www.imsglobal.org/xsd/imscp_v1p1";
		$xmlnsims="http://www.imsglobal.org/xsd/imsmd_v1p2";
		$xmlxsi="http://www.w3.org/2001/XMLSchema-instance";
		$schemaLocation = "http://www.imsglobal.org/xsd/imscp_v1p1 http://www.imsglobal.org/xsd/imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2.xsd";

		//Create XML document
		$imsDoc = new DomDocument('1.0');
		$imsDoc->formatOutput = true;

		//Manifest
		$manifest = $imsDoc->appendChild(new DOMElement('manifest'));

		//Add Schemai's Locations
		$xmlns = $manifest->setAttribute('xmlns',$xmlns);
		$xmlnsims = $manifest->setAttribute('xmlns:imsmd',$xmlnsims);
		$xmlxsi = $manifest->setAttribute('xmlns:xsi',$xmlxsi);
		$schemaLocation = $manifest->setAttribute('xsi:schemaLocation',$schemaLocation);
		
		//Add identifier and version
		$identifier = $manifest->setAttribute('identifier',$id);
		$version = $manifest->setAttribute('version',$contextcode);

		//Metadata
		$metadata = $manifest->appendChild(new DOMElement('metadata'));

		$schema = $metadata->appendChild(new DOMElement('schema'));
		$schemaVal = "IMS CONTENT";
		$schemaText = $imsDoc->createTextNode($schemaVal);
		$schemaText = $schema->appendChild($schemaText);
		$schemaversion = $metadata->appendChild(new DOMElement('schemaversion'));
		$schemaversionVal = "1.2";
		$schemaVtext = $imsDoc->createTextNode($schemaversionVal);
		$schemaVtext = $schemaversion->appendChild($schemaVtext);
		//imsmd:lom Elements
		//lom
		$imsmdLom = new DOMElement('imsmd:lom', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$metadata->appendChild($imsmdLom);
		//lom elements
		//general
		$imsmdGen = new DOMElement('imsmd:general', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdGen);
		//lifecycle
		$imsmdLif = new DOMElement('imsmd:lifecycle', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdLif);
		//technical
		$imsmdTec = new DOMElement('imsmd:technical', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLom->appendChild($imsmdTec);
		//general elements
		//title
		$imsmdTit = new DOMElement('imsmd:title', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdGen->appendChild($imsmdTit);
		//title elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', $title, 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTit->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		//language
		$imsmdLan = new DOMElement('imsmd:language', 'en-US', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdGen->appendChild($imsmdLan);
		//description
		$imsmdDes = new DOMElement('imsmd:discription', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdGen->appendChild($imsmdDes);
		//language elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', $about, 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdDes->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
	
		//lifecycle elements
		//version
		$imsmdVer = new DOMElement('imsmd:version', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLif->appendChild($imsmdVer);
		//version elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', '1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdVer->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//status
		$imsmdSta = new DOMElement('imsmd:status', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLif->appendChild($imsmdSta);
		//status elements
		//source
		$imsmdSou = new DOMElement('imsmd:source', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdSta->appendChild($imsmdSou);
		///source elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdSou->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//value
		$imsmdVal = new DOMElement('imsmd:value', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdSta->appendChild($imsmdVal);
		//value elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Final', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdVal->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//contribute
		$imsmdCon = new DOMElement('imsmd:contribute', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdLif->appendChild($imsmdCon);
		//contribute elements
		//role
		$imsmdRol = new DOMElement('imsmd:role', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdCon->appendChild($imsmdRol);
		//role elements
		//source
		$imsmdSou = new DOMElement('imsmd:source', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdRol->appendChild($imsmdSou);
		//source elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdSou->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//value
		$imsmdVal = new DOMElement('imsmd:value', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdRol->appendChild($imsmdVal);
		//value elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Author', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdVal->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//centity
		$imsmdCen = new DOMElement('imsmd:centity', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdCon->appendChild($imsmdCen);
		//centity elements
		$imsmdVca = new DOMElement('imsmd:vcard', 'BEGIN:vCard FN:Chris Moffatt N:Moffatt END:vCard', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdCen->appendChild($imsmdVca);
		//date
		$imsmdDat = new DOMElement('imsmd:date', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdCon->appendChild($imsmdDat);
		//date elements
		$imsmdDtt = new DOMElement('imsmd:datetime', $datecreated, 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdDat->appendChild($imsmdDtt);
		//technical elements
		//format
		$imsmdFor = new DOMElement('imsmd:format', 'XMLL 1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdFor);
		//size
		$imsmdSiz = new DOMElement('imsmd:size', '70306', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdSiz);
		//location
		$imsmdLoc = new DOMElement('imsmd:location', 'http://www.imsglobal.org/content', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdLoc);
		$imsmdLoc->setAttributeNode(new DOMAttr('type','URI'));
		//requirement
		$imsmdReq = new DOMElement('imsmd:requirement', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdReq);
		//requirements elements
			//type
			$imsmdTyp = new DOMElement('imsmd:type', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
			$imsmdReq->appendChild($imsmdTyp);
			//name
			$imsmdNam = new DOMElement('imsmd:name', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
			$imsmdReq->appendChild($imsmdNam);
			//status elements
			//source
			$imsmdSou = new DOMElement('imsmd:source', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
			$imsmdNam->appendChild($imsmdSou);
				///source elements
				//langstring
				$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
				$imsmdSou->appendChild($imsmdLas);
				$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
				//value
				$imsmdVal = new DOMElement('imsmd:value', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
				$imsmdNam->appendChild($imsmdVal);
					//value elements
					//langstring
					$imsmdLas = new DOMElement('imsmd:langstring', 'XML', 'http://www.imsglobal.org/xsd/imscp_v1p1');
					$imsmdVal->appendChild($imsmdLas);
					$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//minimumversion
		$imsmdMin = new DOMElement('imsmd:minimumversion', '1.0', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdReq->appendChild($imsmdMin);
		//maximumversion
		$imsmdMax = new DOMElement('imsmd:maximumversion', '5.2', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdReq->appendChild($imsmdMax);
		//installationremarks
		$imsmdIns = new DOMElement('imsmd:installationremarks', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdIns);
			//installationremarks elements
			//langstring
			$imsmdLas = new DOMElement('imsmd:langstring', 'Download', 'http://www.imsglobal.org/xsd/imscp_v1p1');
			$imsmdIns->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//otherplatformrequirements
		$imsmdOth = new DOMElement('imsmd:otherplatformrequirements', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdOth);
		//otherplatformrequirements elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Requires web browser for rendering', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdOth->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//duration
		$imsmdDur = new DOMElement('imsmd:duration', '', 'http://www.imsglobal.org/xsd/imscp_v1p1');
		$imsmdTec->appendChild($imsmdDur);

		//$imsmd = $metadata->appendChild(new DOMElement('imsmd'));
		//Organizations
		$organizations = $manifest->appendChild(new DOMElement('organizations'));
		
		//Resources
		//$resources = $manifest->appendChild(new DOMElement('resources'));
		//$this->resourceSkeleton($resources);
		//$this->resourceSkeleton($resources);
		//$this->resourceSkeleton1($resources,$filelist);
		//$manifest = $this->attachChild($manifest,"asdf");

//===============================================================================
function resourceSkeleton1($root,$filelist)
{
$nodes[0] = "resource";
$nodes[1] = "metadata";
$nodes[2] = "lom";
$nodes[3] = "general";
$nodes[4] = "lifecycle";
$nodes[5] = "metametadata";
$nodes[6] = "technical";
$nodes[7] = "rights";
$nodes[8] = "file";

$parent[0] = 0;
$parent[1] = 1;
$parent[2] = 2;
$parent[3] = 3;
$parent[4] = 3;
$parent[5] = 3;
$parent[6] = 3;
$parent[7] = 3;
$parent[8] = 1;

return $root = $this->domTree($manifest, $nodes, $parent, $filelist);
}

function resourceSkeleton($root)
{
$nodes[0] = "resource";
$nodes[1] = "metadata";
$nodes[2] = "lom";
$nodes[3] = "general";
$nodes[4] = "lifecycle";
$nodes[5] = "metametadata";
$nodes[6] = "technical";
$nodes[7] = "rights";

$parent[0] = 0;
$parent[1] = 1;
$parent[2] = 1;
$parent[3] = 2;
$parent[4] = 2;
$parent[5] = 2;
$parent[6] = 2;
$parent[7] = 2;

return $root = $this->buildTree($root, $nodes, $parent);
}

function buildTree($manifest, $nodes, $parent)
{
	for($i=0;$i<count($nodes);$i++)
	{
		$child = new DOMElement($nodes[$i]);
		if($parent[$i] == 0)
		{
			$manifest = $manifest->appendChild($child);
			$prevChild = $child;
		}
		else if($parent[$i] == 1)
		{
			$manifest = $prevChild->appendChild($child);
			$prevChild = $child;
		}
		else
		{
			$manifest = $prevChild->appendChild($child);
		}
	}

	return $manifest;
}



*/
		

?>
