<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
 * Class to import the UNESCO static
 * content from the file system and dump it
 * in to the database
 * 
 * @author Wesley Nitsckie
 * @package contextadmin
 */
 
class import extends dbTable
{		
	/**
	*@var $objUser : the user Object
	*/
	var $objUser;
	var $currentNodeId=null;
	var $rootId=null;
	/**
	*Initialize method
	*/
	function init()
	{
		//set initial table
		parent::init('tbl_context');
		//get the user object
		$this->objUser= & $this->getObject('user','security');	
		$this->userId=$this->objUser->userId();
	}
	
	
	/**
	*Method to create a course and add all the nodes
	*@param string $contextCode : The contextCode
	*@param string $title : the Course Title
	*@param string $path : The path to the static content
	*/
	function doAll($contextCode,$title,$path)
	{
	//insert course
		$this->resetTable();
		$contextId=$this->insert(array(
                    'contextCode' => $contextCode,
                    'title' => $title,
                    'menutext' => $title,
					'isActive' => '1',
					'isClosed' => '0',
                    'userid' => '1',
                    'dateCreated' => $this->getDate()));
        $this->contextID=$this->getLastInsertId();
		
		//insert the first Node
		$this->insertRootNode($contextId,$title,$contextCode,$path);
		//$this->getParentFolders($path,$contextId);
	}
	
	/**
	*MEthod to insert the Root Node
	*@param string $contextId : The context Id
	*@param string $title : The title of the course
	*/
	function insertRootNode($contextId,$title,$contextCode,$path){
		//insert the root node
		$this->changeTable('tbl_context_parentnodes');
		$rootId=$this->insert(array(
			'title' => $title,
			'datemodified' => $this->getDate(),
			'dateCreated' => $this->getDate(),
			'userId' =>$this->objUser->userId(),
			'menu_text' => $title));
		$this->rootId=$rootId;
		
		//create a bridge entry
		$this->changeTable('tbl_context_parentnodes_has_tbl_context');
		$this->insert(array(
			'tbl_context_contextCode' => $contextCode,
			'tbl_context_parentnodes_id' => $rootId,
			'tbl_context_id '=>$contextId));
		//recurse the folder and add to nodes table
		//print $rootId.$path;
		$this->recurseChildren($path,$rootId,$parentNodeId=null);
	}
	
	/**
	*Method to loop through a folder and 
	*insert the files and folders as nodes
	*@param string $path : The path to the current working folder
	*@param string $rootId: The id of the root entry in the tbl_context_parentnodes
	*@param string parentNodeId : The link to a node by which to biuld a tree
	*/
	function recurseChildren($path,$rootId,$parentNodeId=null)
	{
		//print $path;
		$baseDir=$path;
		$hndl=opendir($baseDir);
	   while($file=readdir($hndl)) 
	   {
		      $completepath=addslashes("$baseDir/$file");
			//if ($file=='.' || $file=='..' || $file=='CVS') continue;
			if (!strcmp(".", $file))continue; // this may not the most efficient way to detect the . and .. entries
			if (!strcmp("..", $file))continue; // but it is the easiest to understand
			if (!strcmp("CVS", $file))continue; // ignore CVS folders
			if (!strcmp("config", $file))continue; // do not display config folder
			if (!strcmp("_vti_cnf", $file))continue;//ignore frontpage crap
			if (strcmp("images", $file)) 
			{
				//add the images seperately				
				$this->addImages(addslashes($baseDir."/".$file),$rootId);
				continue; 
			}		
		  
		   if (is_dir($completepath)) 
		   {			
			   //get the title from the foldername
				$titleArr = pathinfo($completepath);				
				//insert the node
				$pid=$this->insertNode($rootId,$parentNodeId,$titleArr['basename']);
				//loop through the folders and pages
				$this->recurseChildren($completepath,$rootId,$pid);
		   } 
		   else 
		   {
			   if(is_file($completepath))
			   {
					// its a file.
					//get the file details
					$ftype=pathinfo($completepath);
					
					//only add content for a html file 
					switch (strtolower($ftype['extension']))
					{
						case 'html':
						case 'htm':						
							//get the contents of the html file
							$html=$this->getFileToString($completepath);
							//get the title from the title tags
							$title = $this->getTitle($html);						
							//get the body 
							$body=$this->getBody($html);
							//insert the node and get the nodeid
							$pid=$this->insertNode($rootId,$parentNodeId,$title);
							//insert the contents and get the contentid
							$contentId=$this->insertContent($body,$title);
							//then insert a bridge entry 
							$this->insertContentNodeBridge($contentId,$pid,$rootId);
							break;
						default:
							//the rest is treated as documents							
							$this->addFile($completepath,$rootId);
							break;
					}	
				}
		   }
	   }
	}
	
	/**
	*Method to insert the contents
	*@param string $body : The body of the html file
	*@param string $title :the title of the page
	*/
	function insertContent($body,$title){
		$this->changeTable('tbl_context_page_content');
		$contentId=$this->insert(array(
							'body' =>$body,
							'ownerId' =>$this->objUser->userId(),
							'menu_text' => $title));
		return $contentId;
	}
	
	/**
	*Method to insert a bridge entry 
	*between tbl_context_page_contents and tbl_context_nodes_has_tbl_context_page_contents
	*@param string $contentId : the content Id	
	*@param string $parentId : The id of the parent
	*@param string $rootId : The id of the root
	*/
	function insertContentNodeBridge($contentId,$parentId,$rootId)
	{
	$this->changeTable('tbl_context_nodes_has_tbl_context_page_content');
		$this->insert(array(
			'tbl_context_nodes_tbl_context_parentnodes_id' =>$rootId,
			'tbl_context_nodes_id' => $parentId,
			'tbl_context_page_content_id'  => $contentId
		));
	}
	
	/**
	*Method to insert a node
	*@param string rootId: The root Id to the node
	*@param string $parentNodeId : The parent node Id
	*@param string $title : The title of the node
	*/
	function insertNode($rootId,$parentNodeId,$title=null)
	{
		$this->changeTable('tbl_context_nodes');
		$nodeId=$this->insert(array(
			'tbl_context_parentnodes_id' => $rootId,
			'title' => $title,
			'prev_Node' => $this->currentNodeId,			
			'parent_Node' =>$parentNodeId));
		
		$this->update('id',$this->currentNodeId,array('next_Node' => $nodeId));
		$this->currentNodeId=$nodeId;
		return $nodeId;
	}
	
	/**
	*Method to change the working table
	*@param string $tName : The name of the table
	*/
	function changeTable($tName)
	{
		parent::init($tName);
	}
	
	/**
	*Method to reset the working table to 'tbl_context'
	*/
	function resetTable()
	{
		parent::init('tbl_context');
	}
	
	/**
	*Method to return a formatted date string
	*/
	function getDate(){
		return date("Y-m-d H:i:s");
	}
	
	/**
	*Method to retrieve the contents
	* of the html body tags
	*@param string $str : the HMTL
	*/
	function getBody($str){	
		if (preg_match("/<body>(.*)<\/body>/is",$str, $body)) { 
				$title = $body[0];
				$title = substr($title,strpos(strtolower($title),">")+1);
				$tmp = strrev($title);
				$tmp = substr($tmp,strpos(strtolower($tmp),"<")+1);
				return $this->changeImageSRC(strrev($tmp));
		}		
	}
	
	/**
	*Method to retrieve the
	*title from the html title tags
	*@param string $str : Th html string
	*/
	function getTitle($str){
		if (preg_match("/<title>(.*)<\/title>/i",$str, $contents))  {
				$title = $contents[1];
				$title = strip_tags($title);
				return $title;
		}
	}
	
	/**
	*Method to get the contents of a text File
	*@param string $file; The file name
	*@return string $contents : The contents of the file
	*/
	function getFileToString($file)
    {
        if (file_exists($file)) {
            //read it into a string and return the string
            $fp = fopen($file, "r") 
                or die("fopen failed");   /* the file_exists should 
                                             prevent this error but trap 
                                             it anyway */
            $contents = fread($fp, filesize($file));
            fclose($fp);
            return $contents;
        } else {
            return False;
        }
	}
	
	/**
	*Method to add images as blobs to the tbl_context_file
	*@param string $path : The path to the images folder
	*/	
	function addImages($path,$rootId)
	{
            //print "<h1>$path</h1>\n";
		$hndl=opendir($path);
		while($SrcPathFile=readdir($hndl))
		{	
			$fileArr=pathinfo($SrcPathFile);
			if (!strcmp(".", $SrcPathFile))continue; // this may not the most efficient way to detect the . and .. entries
			if (!strcmp("..", $SrcPathFile))continue;			
			//echo 'Images  ---- '.$path.'/'.$SrcPathFile.'<br>';
			if(is_file($path.'/'.$SrcPathFile))
				$this->addFile($path.'/'.$SrcPathFile,$rootId);
		}
	}
	
	/**
	*Method to add a file to tbl_context_file as a blob
	*and then chop it up into 64kb chunks that is added to tbl_context_filedata	
	*@param string $completepath : The path to the images folder
	*/	
	function addFile($completepath,$rootId,$filename=NULL,$extension=NULL)
	{		
		//echo 'adding file '.filesize($completepath).'<BR>';
		$fileArr=pathinfo($completepath);		

        if ($filename == NULL) {
            $filename = $fileArr['basename'];
        }
        if ($extension == NULL) {
            $extension = $fileArr['extension'];
        }
		
		parent::init('tbl_context_file');
		$fileId=$this->insert(array(
				'tbl_context_parentnodes_id' => $rootId,
				'name' => $filename,
				'size' => filesize($completepath),
				'filedate' => date("Y-m-d H:i:s", filemtime($completepath)),
				'datatype' => $extension
			));			
		
		parent::init('tbl_context_filedata');			
		$fp = fopen(realpath($completepath), "rb");
		 $count=0;
		while (!feof($fp)) 
		{		
			  // Make the data mysql insert safe
			  
			$binarydata = fread($fp, 65535);
			$this->insert(array(
				'tbl_context_file_tbl_context_parentnodes_id' => $rootId,
				'tbl_context_file_id' => $fileId,
				'segment' => $count,
				'filedata' => $binarydata
			));			
			$count=$count+1;
		}
		fclose($fp);	
	}

    /**
    * method to replace image source links with links to the blob system.
    * @author James Scoble
    * @param strong $str - the text of the page to operate on.
    * returns string $text - the finished text
    */
    function changeImageSRC($str)
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
            $segment[1]=strstr($segment[1],"\"");
            $check=count($segment);
            $text='';
            if ($check>2){
                for($ic=1;$ic!=$check;$ic++)
                {
                    $text.=$segment($ic);
                }
            } else {
                $text=$segment[1];
            }
            $src=str_replace("\\","/",$src);
            $src=strrchr($src,"/"); 
            $src=trim(substr($src,1));
            $id='test';
            $link=$this->uri(array('action'=>'contextdownload','contextId'=>$rootId,'name'=>$src),'contextview');
            $page.= "<IMG$alt src=\"$link\" $text";
	}
        return $page;
    }
}
?>