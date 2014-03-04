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
 
class importu extends dbTable
{        
    /**
    *@var $objUser : the user Object
    */
    var $objUser;
    var $currentNodeId=null;
    var $rootId;    
    /**
    * @var array $arrLinks;
    */
    var $arrLinks = array();
    
    /**
    * @var array docLinks
    */
     var $docLinks = array();
     
    /**
    * @var object objMetadata;
    */
    var $objMetadata;
    
    /**
    *Initialize method
    */
    function init()
    {
        //set initial table
        parent::init('tbl_context');
        //get the user object
        $this->objUser=  $this->getObject('user','security');    
        $this->objMetadata=  $this->getObject('dublincore','dublincoremetadata');    
        $this->objDBContentNodes=  $this->getObject('dbcontentnodes','context');    
        $this->objDBContext=  $this->getObject('dbcontext','context');    
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
        $this->changeTable('tbl_context_parentnodes_has_tbl_context');
        if(!$this->valueExists('tbl_context_contextCode',$contextCode)){
            $this->changeTable('tbl_context_parentnodes');
            
            //create a bridge entry
            $this->changeTable('tbl_context_parentnodes_has_tbl_context');
            $this->insert(array(
                'tbl_context_contextCode' => $contextCode,                
                'tbl_context_id '=>$contextId));
            
            $this->changeTable('tbl_context_parentnodes');
            $rootId=$this->insert(array(
                'tbl_context_parentnodes_has_tbl_context_tbl_context_id' => $contextId,
                'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode' => $contextCode,
                'title' => $title,
                'datemodified' => $this->getDate(),
                'dateCreated' => $this->getDate(),
                'userId' =>$this->objUser->userId(),
                'menu_text' => $title));
            $this->contextTitle = $title;
            $this->rootId=$rootId;
            //recurse the folder and add to nodes table
            //print $rootId.$path;
            $this->recurseChildren($path,$rootId,null);
               print '<pre>';
            //print_r($this->docLinks);
            print '</pre>';
            $this->changeLinkUrl();
        }
     
    }
    
    /**
    * Method to import content into an existing node
    * @param sting $nodeId The Node Id under which you want to import
    * @param string $path The path to the unzipped folder
    * @return null
    */
    function insertToNode($nodeId, $path){
        $rootId = $this->objDBContentNodes->getField('tbl_context_parentnodes_id',$nodeId);
        $this->rootId=$rootId;
        
        $this->contextTitle = $this->objDBContext->getTitle();
        $this->recurseChildren($path,$rootId,$nodeId);
        $this->changeLinkUrl();
    }
    
   
    
    /**
    *Method to loop through a folder and 
    *insert the files and folders as nodes
    *@param string $path : The path to the current working folder
    *@param string $rootId: The id of the root entry in the tbl_context_parentnodes
    *@param string parentNodeId : The link to a node by which to biuld a tree
    */
    function recurseChildren($path,$rootId,$parentNodeId=null,$parentName=null)
    {
        
        //print $path;
        $baseDir=$path;
        $hndl=opendir($baseDir);
       while($file=readdir($hndl)) 
       {
              $completepath=$baseDir.'/'.$file;
            //if ($file=='.' || $file=='..' || $file=='CVS') continue;
            if (!strcmp(".", $file))continue; // this may not the most efficient way to detect the . and .. entries
            if (!strcmp("..", $file))continue; // but it is the easiest to understand
            if (!strcmp("CVS", $file))continue; // ignore CVS folders
            if (!strcmp("config", $file))continue; // do not display config folder
            if (!strcmp("_vti_cnf", $file))continue;//ignore frontpage crap
            if (!strcmp("images", $file)) 
            {
                //add the images seperately                
               $this->addImages($baseDir."/".$file,$rootId);
                continue; 
            }        
          
           if (is_dir($completepath)) 
           {            
               //get the title from the foldername
                $titleArr = pathinfo($completepath);                
                
                //strip the _
                $pos = strpos($titleArr['basename'], "_");            
                if ($pos == 1){
                    $basename = substr($titleArr['basename'], ($pos+1)) ;
               } else {
                   $basename = $titleArr['basename'];
                }
                
                //insert the node
                $pid=$this->insertNode($rootId,$parentNodeId,urldecode($basename));
                //loop through the folders and pages
                $this->recurseChildren($completepath,$rootId,$pid, $basename);
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
                             
                             $filename =  strtolower(str_replace( '.'.$ftype['extension'] , '', $ftype['basename']) );
                            //get the contents of the html file
                            $html=$this->getFileToString($completepath);
                            //get the title from the title tags
                            $title = $this->getTitle($html);                        
                            //get the body 
                            $body=$this->getBody($html);
                            //insert the node and get the nodeid
                           
                           
                            if(strtolower($parentName) == $filename || is_integer(strpos($filename, "__"))){
                                //add content to as an index file
                                $pid = $parentNodeId;
                             } else {
                                 //add content normally
                                $pid=$this->insertNode($rootId,$parentNodeId,$title); 
                            }    
                            //insert the contents and get the contentid
                            $contentId=$this->insertContent($pid,$body,$title);
                            
                            // insert metadata for the content nodes    
                            $metaParams = array( 'dc_title' => $title,
                                                                     'url' => $this->uri(array('action'=>'content', 'nodeid'=>$pid),'context','',TRUE),
                                                                     'dc_subject' => $this->contextTitle,
                                                                     'dc_type' => 'html'
                                                                     );
                             $this->objMetadata->insertMetaData($pid, $metaParams);
                             
                             //biuld a list of filenames so that it the links in the content can be replaced with ones pointing to internal links
                             if(is_integer(strpos($filename, "__"))){
                                 $newFilename = substr($ftype['basename'], 2);
                             } else {
                                 $newFilename = $ftype['basename'];
                              }
                             $this->arrLinks[] = array('id' => $pid, 'filename' => strtolower($newFilename));
                            break;
                        default:
                            //the rest is treated as documents                            
                            $fileId = $this->addFile($completepath,$rootId);
                            $this->docLinks[] = array('id' => $fileId , 'filename' => strtolower($ftype['basename']));
                            break;
                    }    
                }
           }
       }
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
            'sortindex' => $this->objDBContentNodes->getNextOrderIndex($parentNodeId),
            'parent_Node' =>$parentNodeId));
        
        $this->update('id',$this->currentNodeId,array('next_Node' => $nodeId));
        $this->currentNodeId=$nodeId;
        return $nodeId;
    }    
   
    
    /**
    *Method to insert the contents
    *@param string $body : The body of the html file
    *@param string $title :the title of the page
    */
    function insertContent($nodeId,$body,$title){
        $this->changeTable('tbl_context_page_content');
        $body = str_replace('�','',$body);
        $body = str_replace('�','\'',$body);
        $contentId=$this->insert(array(
                            'tbl_context_nodes_id' => $nodeId,
                            'body' => $body,
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
    *@param string $str : the HTML
    */
    function getBody($str){    
        if (preg_match("/<body(.*)>(.*)<\/body>/is",$str, $body)) { 
                $title = $body[0];
                $next = substr($title, strpos ($title, '<!--msnavigation-->')+19);
                $next2 = substr($next, strpos ($next, '<!--msnavigation-->')+19);
                $title = $next2;
                $title = substr($title,strpos(strtolower($title),">")+1);
                $tmp = strrev($title);
                $tmp = substr($tmp,strpos(strtolower($tmp),"<")+1);
                //return $this->changeImageSRC(strrev($tmp));               
                return strrev($tmp);
        }        
    }
    
    /**
    *Method to retrieve the
    *title from the html title tags
    *@param string $str : The html string
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
    *Method to add copy images from the imported context to the filesyste
    *@param string $path : The path to the images folder
    *@param string $rootId : The Id of this content entry
    */    
    function addImages($path, $rootId)
    {
        $hndl = opendir($path);
        $objFSContext=$this->getObject('fscontext','context');
        $objFSContext->createContextFolder($this->objDBContext->getContextCode());
        while( ($SrcPathFile = readdir($hndl)) )
        {    
            $fileArr = pathinfo($SrcPathFile);
            if (!strcmp(".", $SrcPathFile)) {
                continue; // this may not the most efficient way to detect the . and .. entries
            }
            if (!strcmp("..", $SrcPathFile)) {
              continue;
            }
            //echo 'Images  ---- '.$path.'/'.$SrcPathFile.'<br>';
            if(is_file($path.'/'.$SrcPathFile)) {
                $fileId = $this->addFile($path.'/'.$SrcPathFile,$rootId);               
                $ftype=pathinfo($path.'/'.$SrcPathFile);
                $this->docLinks[] = array('id' => $fileId , 'filename' => strtolower($ftype['basename']));
               
                //$objFSContext->copyImage($rootId,$path,$SrcPathFile);
            }
        }
    }
    
    /**
    *Method to add a file to tbl_context_file as a blob
    *and then chop it up into 64kb chunks that is added to tbl_context_filedata    
    *@param string $completepath : The path to the images folder
    */    
    function addFile($completepath, $rootId)
    {        
        //echo 'adding file '.filesize($completepath).'<BR>';
        $fileArr=pathinfo($completepath);        
        
        parent::init('tbl_context_file');
        $fileId=$this->insert(array(
                'tbl_context_parentnodes_id' => $rootId,
                'name' => $fileArr['basename'],
                'title' => $fileArr['basename'],
                'size' => filesize($completepath),
                'filedate' => date("Y-m-d H:i:s", filemtime($completepath)),
                'datatype' => $fileArr['extension']
            ));            
            //add metadata
            $metaParams = array( 'dc_title' => $fileArr['basename'],
                                'url' => $this->uri(array('action'=>'contextdownload', 'id'=>$fileId),'contextview','',TRUE),
                                'dc_subject' => $this->contextTitle,
                                'dc_type' =>$fileArr['extension']
                                );
        // $this->objMetadata->insertMetaData('', $metaParams);
         
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
        return $fileId;
    }
    /**
    * This function is a method to replace image source links with links to image-storage system.
    * @author James Scoble
    * @param strong $str - the text of the page to operate on.
    * returns string $text - the finished text
    */
    function changeImageSRC($str)
    {
        $rootId=$this->rootId;
        $fragment=spliti('<IMG', $str);
        unset($str); 
        $first=array_shift($fragment); 
        $page=$first;
        foreach ($fragment as $line)
        {
            $segment=spliti('src="', $line);
            $alt=$segment[0];
            $src=spliti("\"", $segment[1]);
            $src=$src[0];
            $segment[1]=strstr($segment[1], "\"");
            $check=count($segment);
            $text='';
            if ($check>2){
                for($ic = 1; $ic != $check; $ic++)
                {
                    $text.=$segment[$ic];
                }
            } else {
                $text=$segment[1];
            }
            $src=str_replace("\\", "/", $src);
            $src=strrchr($src, "/"); 
            $src=trim(substr($src, 1));
            $id='test';
            $link=$this->uri(array('action'=>'contextdownload', 'contextId'=>$rootId, 'name'=>$src), 'contextview','',TRUE);
            //$link="/kng_content/$rootId/images/$src";
            $page.= "<IMG$alt src=\"$link\" $text";
    }
        return $page;
    }
    
      /**
    * Method to change the link location 
    */
    function changeLinkUrl()
    {      
        $rootId = $this->rootId;
        $objContentNodes =  $this->newObject('dbcontentnodes', 'context');
        $objContentNodes->resetTable();
        $nodes = $objContentNodes->getAll('WHERE tbl_context_parentnodes_id="'.$rootId.'"');
       
        //loop through the record set    
        foreach ($nodes as $list)
        {          
            $body = $objContentNodes->getBody($list['id']);
            //replace all links that is in the arr
            $text = $body;                    
          
            //loop through the list of links
           foreach($this->arrLinks as $link)
           {              
                  
                $filename = str_replace('.', '\.', $link['filename']);                 
                $filename = str_replace('%20', ' ', $filename);
                
                $regstr = '<\s*[aA] \s*[hH][rR][eE][fF]=\s*"[^"]+((\.\.)?/'.trim($filename).')+"\s*>';
                $url = '<a href="'.$this->uri(array('action' => 'content' , 'nodeid' => $link['id']), 'context','',TRUE).'">';                
                $text = eregi_replace($regstr , $url, $text);
                $regstr = '<\s*[aA] \s*[hH][rR][eE][fF]=\s*"'.trim($filename).'"\s*>';
                $text = eregi_replace($regstr , $url, $text);
           }
           
           //loop through the list of document links
           
           foreach($this->docLinks as $doclink){              
                
                $filename = str_replace('.', '\.', $doclink['filename']);                 
                $filename = str_replace('%20', ' ', $filename);
                
                $regstr = '<\s*[aA] \s*[hH][rR][eE][fF]=\s*"[^"]+((\.\.)?/'.trim($filename).')+"\s*>';
                $urlLink=$this->uri(array('action'=>'contextdownload', 'id'=>$doclink['id']), 'contextview','',TRUE);
                $urlLink = '<a href="'.$urlLink.'">';                
                
                $text = eregi_replace($regstr , $urlLink, $text);
                $regstr = '<\s*[aA] \s*[hH][rR][eE][fF]=\s*"'.trim($filename).'"\s*>';
                $text = eregi_replace($regstr , $urlLink, $text);
                $regStr = '[sS][rR][cC]=\s*"[^"]+((\.\.)?/'.trim($filename).')+"';
                
              //change images src's
              $src = $this->uri(array('action'=>'contextdownload', 'id'=>$doclink['id']), 'contextview','',TRUE);
               $image = 'src="'.$src.'"';
               $text = eregi_replace($regStr , $image, $text);
           }
           
          
           
           $body = $text;
           
           //then send it back to the databse
            $objContentNodes->changeTable('tbl_context_page_content');            
            $objContentNodes->update('tbl_context_nodes_id', $list['id'], array('body' => $body) );
            $objContentNodes->resetTable();
           
         }    
         
      
    }
}

?>