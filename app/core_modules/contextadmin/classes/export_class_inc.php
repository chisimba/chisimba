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

	function doXMLExport()
	{
 //check if you are in a context
            if($this->objDBContext->isInContext())
            {
                //check if the xml file exist
					 $this->contextCode = $this->objDBContext->getContextCode();
                $filePath = $this->objConfig->getsiteRootPath().'usrfiles/content/'.$contextCode.'/imsmanifest.xml';
                // print $filePath;
                if (file_exists($filePath)) {
                //delete the xml file
                    unlink($filePath);
                }
                //create the xml file
                $fp = fopen($filePath,'w');
                //add the xml data
                $contents = $this->getXMLData();
                //write to file
                fwrite($fp,$contents);
                //close the file
                fclose($fp);
		//create zip file
                $folderToZip = $this->objConfig->getsiteRootPath().'usrfiles/content/'.$contextCode.'/';
                $folderPath =  $this->objConfig->getsiteRootPath().'usrfiles/exportedimscontext/';
                $newZipFileName =  $this->objConfig->getsiteRootPath().'usrfiles/exportedimscontext/'.$contextCode.'-ims.zip';
                //zip the context folder
                $this->objWZip->addArchive($folderToZip, $newZipFileName,$folderToZip);
                die;
                return TRUE;
            } else {
                return FALSE;
            }
	}

	function doXMLWrite()
	{
	$xml = simplexml_load_file("/opt/lampp/htdocs/chisimba_framework/app/usrfiles/content/101CC/imsmanifest.xml");
	var_dump($xml);
	//echo $contextCode;

	
	}


	
	function getXMLData()
	{
		$dsn = "localhost";
		$table = "tbl_context";
		$filter = "SELECT * FROM tbl_context";

		$this->objBlogImport = &$this->getObject('blogimporter',blog);

		//set up to connect to the server
		$dsn = $this->objBlogImport->setup($dsn);
		//connect to the remote db
		$dbobj = $this->objBlogImport->_dbObject();
		$data = $this->objBlogImport->queryTable($table,$filter);
	
		$contextCode = $this->objDBContext->getContextCode();
		echo $contextCode;

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
		
		//Example IMSmanifest

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

		//Column Names
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
		$imsmdLom = new DOMElement('imsmd:lom', '', 'http://xyz');
		$metadata->appendChild($imsmdLom);
		//lom elements
		//general
		$imsmdGen = new DOMElement('imsmd:general', '', 'http://xyz');
		$imsmdLom->appendChild($imsmdGen);
		//lifecycle
		$imsmdLif = new DOMElement('imsmd:lifecycle', '', 'http://xyz');
		$imsmdLom->appendChild($imsmdLif);
		//technical
		$imsmdTec = new DOMElement('imsmd:technical', '', 'http://xyz');
		$imsmdLom->appendChild($imsmdTec);
		//general elements
		//title
		$imsmdTit = new DOMElement('imsmd:title', '', 'http://xyz');
		$imsmdGen->appendChild($imsmdTit);
		//title elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', $title, 'http://xyz');
		$imsmdTit->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		//language
		$imsmdLan = new DOMElement('imsmd:language', 'en-US', 'http://xyz');
		$imsmdGen->appendChild($imsmdLan);
		//description
		$imsmdDes = new DOMElement('imsmd:discription', '', 'http://xyz');
		$imsmdGen->appendChild($imsmdDes);
		//language elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', $about, 'http://xyz');
		$imsmdDes->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
	
		//lifecycle elements
		//version
		$imsmdVer = new DOMElement('imsmd:version', '', 'http://xyz');
		$imsmdLif->appendChild($imsmdVer);
		//version elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', '1.0', 'http://xyz');
		$imsmdVer->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//status
		$imsmdSta = new DOMElement('imsmd:status', '', 'http://xyz');
		$imsmdLif->appendChild($imsmdSta);
		//status elements
		//source
		$imsmdSou = new DOMElement('imsmd:source', '', 'http://xyz');
		$imsmdSta->appendChild($imsmdSou);
		///source elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://xyz');
		$imsmdSou->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//value
		$imsmdVal = new DOMElement('imsmd:value', '', 'http://xyz');
		$imsmdSta->appendChild($imsmdVal);
		//value elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Final', 'http://xyz');
		$imsmdVal->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//contribute
		$imsmdCon = new DOMElement('imsmd:contribute', '', 'http://xyz');
		$imsmdLif->appendChild($imsmdCon);
		//contribute elements
		//role
		$imsmdRol = new DOMElement('imsmd:role', '', 'http://xyz');
		$imsmdCon->appendChild($imsmdRol);
		//role elements
		//source
		$imsmdSou = new DOMElement('imsmd:source', '', 'http://xyz');
		$imsmdRol->appendChild($imsmdSou);
		//source elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://xyz');
		$imsmdSou->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//value
		$imsmdVal = new DOMElement('imsmd:value', '', 'http://xyz');
		$imsmdRol->appendChild($imsmdVal);
		//value elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Author', 'http://xyz');
		$imsmdVal->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//centity
		$imsmdCen = new DOMElement('imsmd:centity', '', 'http://xyz');
		$imsmdCon->appendChild($imsmdCen);
		//centity elements
		$imsmdVca = new DOMElement('imsmd:vcard', 'BEGIN:vCard FN:Chris Moffatt N:Moffatt END:vCard', 'http://xyz');
		$imsmdCen->appendChild($imsmdVca);
		//date
		$imsmdDat = new DOMElement('imsmd:date', '', 'http://xyz');
		$imsmdCon->appendChild($imsmdDat);
		//date elements
		$imsmdDtt = new DOMElement('imsmd:datetime', $datecreated, 'http://xyz');
		$imsmdDat->appendChild($imsmdDtt);
		//technical elements
		//format
		$imsmdFor = new DOMElement('imsmd:format', 'XMLL 1.0', 'http://xyz');
		$imsmdTec->appendChild($imsmdFor);
		//size
		$imsmdSiz = new DOMElement('imsmd:size', '70306', 'http://xyz');
		$imsmdTec->appendChild($imsmdSiz);
		//location
		$imsmdLoc = new DOMElement('imsmd:location', 'http://www.imsglobal.org/content', 'http://xyz');
		$imsmdTec->appendChild($imsmdLoc);
		$imsmdLoc->setAttributeNode(new DOMAttr('type','URI'));
		//requirement
		$imsmdReq = new DOMElement('imsmd:requirement', '', 'http://xyz');
		$imsmdTec->appendChild($imsmdReq);
		//requirements elements
			//type
			$imsmdTyp = new DOMElement('imsmd:type', '', 'http://xyz');
			$imsmdReq->appendChild($imsmdTyp);
			//name
			$imsmdNam = new DOMElement('imsmd:name', '', 'http://xyz');
			$imsmdReq->appendChild($imsmdNam);
			//status elements
			//source
			$imsmdSou = new DOMElement('imsmd:source', '', 'http://xyz');
			$imsmdNam->appendChild($imsmdSou);
				///source elements
				//langstring
				$imsmdLas = new DOMElement('imsmd:langstring', 'LOMv1.0', 'http://xyz');
				$imsmdSou->appendChild($imsmdLas);
				$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
				//value
				$imsmdVal = new DOMElement('imsmd:value', '', 'http://xyz');
				$imsmdNam->appendChild($imsmdVal);
					//value elements
					//langstring
					$imsmdLas = new DOMElement('imsmd:langstring', 'XML', 'http://xyz');
					$imsmdVal->appendChild($imsmdLas);
					$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'x-none'));
		//minimumversion
		$imsmdMin = new DOMElement('imsmd:minimumversion', '1.0', 'http://xyz');
		$imsmdReq->appendChild($imsmdMin);
		//maximumversion
		$imsmdMax = new DOMElement('imsmd:maximumversion', '5.2', 'http://xyz');
		$imsmdReq->appendChild($imsmdMax);
		//installationremarks
		$imsmdIns = new DOMElement('imsmd:installationremarks', '', 'http://xyz');
		$imsmdTec->appendChild($imsmdIns);
			//installationremarks elements
			//langstring
			$imsmdLas = new DOMElement('imsmd:langstring', 'Download', 'http://xyz');
			$imsmdIns->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//otherplatformrequirements
		$imsmdOth = new DOMElement('imsmd:otherplatformrequirements', '', 'http://xyz');
		$imsmdTec->appendChild($imsmdOth);
		//otherplatformrequirements elements
		//langstring
		$imsmdLas = new DOMElement('imsmd:langstring', 'Requires web browser for rendering', 'http://xyz');
		$imsmdOth->appendChild($imsmdLas);
		$imsmdLas->setAttributeNode(new DOMAttr('xml:lang', 'en'));
		//duration
		$imsmdDur = new DOMElement('imsmd:duration', '', 'http://xyz');
		$imsmdTec->appendChild($imsmdDur);

		//$imsmd = $metadata->appendChild(new DOMElement('imsmd'));
		//Organizations
		$organizations = $manifest->appendChild(new DOMElement('organizations'));

		//Resources
		$resources = $manifest->appendChild(new DOMElement('resources'));

//		echo $imsDoc->saveXML();
		$imsmanifest = $imsDoc->saveXML();

		$contents = $imsDoc->saveXML();
//		if($imsDoc->validate())
			return $imsmanifest;
//		else
//			return "error";


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

	}
/*
	function getXMLData()
	{
	//$content = $this->collect_object_infos("title", "description");
	$content = $this->collect_object_tree("id", "firstfile", "structure");
	echo $content;
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

}
?>
