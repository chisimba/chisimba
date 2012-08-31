<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The content tree
 * Used when you want a list of the content for the current course that you are in
 * @author Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package tree
 **/
class contenttree extends object{

    /**
    * @var object $objDBContext
    */
    var $objDBContext;
    /**
    * @var object $objSkin
    */
    var $objSkin ;
    /**
    * @var object $objDBContentNodes
    */
    var $objDBContentNodes;
    
    /**
    * @var string $module
    */
    var $module;

    /**
    * @var boolean $publicTree
    */
    var $publicTree;
    
    /**
    * Initialize method
    */
    function init(){
        $this->objDBContentNodes = $this->newObject('dbcontentnodes','context');
        $this->objDBContext = $this->newObject('dbcontext','context');
        $this->objSkin =  $this->getObject('skin','skin');
         //the tree classes
        $this->loadClass('treemenu','tree');
        $this->loadClass('treenode','tree');        
        $this->loadClass('dhtml','tree');      
         $this->loadClass('listbox','tree');
        
        $this->module='context';
    }
    
    /**
    * Method to show tree
    * @access public
    * @return string 
    */
    function show($module='context', $contextCode = NULL){
        $this->module=$module;
        return $this->_biuldTree($contextCode) ;
    }
    
    /**
    * Method to show the biuld
    * @access private
    * @return string 
    */
     function _biuldTree($contextCode = NULL){
        $icon = 'folder_up.gif';
        $expandedIcon = 'folder-expanded.gif';
        $link='';
        if($contextCode){
            $id = $this->objDBContext->getField('id', $contextCode);
        } else {
            $id = Null;
        }
        $rootnodeid=$this->objDBContext->getRootNodeId($id);        
        $rootlabel='';//$this->objDBContentNodes->getRootTitle($rootnodeid);
    
        //Create a new menu        
        $menu  = new treemenu();
        $contentlink=$this->URI(array('action','contenthome'),$this->module);
        
        //create base node
        $basenode = new treenode(array('text' => $rootlabel, 'link' => $contentlink, 'icon' => 'base.gif', 'expandedIcon' => 'base.gif'));
        
        //$basenode = new treenode();    
        
        //get all the nodes for the course
        $this->objDBContentNodes->resetTable();
        $nodesArr=$this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id='$rootnodeid' ORDER BY sortindex");
        
        //get all the shared nodes
        $this->objDBContentNodes->changeTable('tbl_context_sharednodes');
        $sharedNodesArr=$this->objDBContentNodes->getAll("WHERE root_nodeid='$rootnodeid'");
      
        $this->objDBContentNodes->changeTable('tbl_context_nodes_has_tbl_context_page_content');
        
        foreach($nodesArr as $node){            
            if($node['parent_Node']==null){            
                $text = $this->objDBContentNodes->getMenuText($node['id']);
                if($text==''){
                    $text = $node['title'];
                }
                $basenode->addItem($this->getChildNodes($nodesArr, $node['id'], stripslashes($this->shortenString($text)), $sharedNodesArr ));
            }
        }
        $this->objDBContentNodes->resetTable();
        
        $menu->addItem($basenode);
        //$menu->addItem($this->recurTree($rootnodeid,$rootlabel));

        // Create the presentation class
        $treeMenu = &new dhtml($menu, array('images' => $this->objSkin->getSkinURL().'treeimages/imagesAlt2', 'defaultClass' => 'treeMenuDefault'));

        
        //Added by ghinde
        //checks for publicTree var and removes
        //context heading if TRUE
        //else reverst to normal operation
        if($this->publicTree==TRUE)
        {
            return $treeMenu->getMenu();
        }
        else
        {
            return '<h5>'.$this->objDBContext->getTitle($contextCode).'</h5>'.$treeMenu->getMenu();
        }
    }
    
    /**
    * Method to create a child node for the tree recursively
    * @access Private
    * @param array $nodesArr : The list of nodes for a course
    * @param string $parentId : The Id of the parent Node
    * @param string $title : The Title of the node
    * @param string $sharedNodesArr : The array of shared nodes
    * @return string $basenode : A tree node 
    */
    function getChildNodes($nodeArr,$parentId,$title,$sharedNodesArr=NULL){
        //setup the link
        $link=$this->URI(array('nodeid' => $parentId,'action'=>'content'),$this->module);
        
        //create an indicator to see where you are on the tree
        if($this->getParam('nodeid') == $parentId){
            $icon = 'arrow.gif';
         } else {
             $icon = NULL;
         }
         
        //create a new tree node
        if($title=='')
        {
            $title='-[missing title]-';
        }
        $basenode = new treenode(array('text' => stripslashes($title), 'link' => $link, 'icon' => $icon, 'expandedIcon' => NULL));        
        
        //add the shared nodes to the tree
        if(is_array($sharedNodesArr)){
            foreach ($sharedNodesArr as $line) 
            {   
                if($parentId==$line['nodeid'])
                {
                    //print $line['nodeid'];
                    $this->objDBContentNodes->resetTable();
                    $sharedRootNodeId = $this->objDBContentNodes->getField('tbl_context_parentnodes_id', $line['shared_nodeid']);
                    $tmpList = $this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id='".$sharedRootNodeId."' ");
  
                   $basenode->addItem(
                        $this->getChildNodes(
                            $tmpList, 
                            $line['shared_nodeid'], 
                            $this->shortenString($this->objDBContentNodes->getField('title', $line['shared_nodeid']))
                        )
                    );
                }              
            }        
         }
         
         
        //search for more children
        foreach ($nodeArr as $line) 
        {   
            if($line['parent_Node']==$parentId){
                $text = $this->objDBContentNodes->getMenuText($line['id']);
                if($text==''){
                    $text = $line['title'];
                }
                $basenode->addItem(
                    $this->getChildNodes($nodeArr, $line['id'], 
                        $this->shortenString($text),
                        $sharedNodesArr));
            }
        }
        return $basenode;
    }    
    
    /**
    *Method to shorten a string
    * @param string $str : The string that needs to be shortened
    * @return string $str : The shortened string
    * @access Private
    */
    function shortenString($str){
        //We need a way to make the menu text a bit short but then
        // it has to b e displayed elsewhere

        
        if(strlen($str)>10){
            //$str=substr($str, 0, 13)."..";
            $i = 10;
            $len = strlen($str);
            while($i < $len){
                if ($str[$i]==' ') {
                    break;
                }
                $i++;
            } // while
            if ($i < $len) {
                $str = substr($str,0,$i).'<br/>'.substr($str,$i);
            }
        }
        
            
        return $str;
    }
    
    /**
    * Method to generate a tree that 
    * will to used in the index.php file of 
    * the static content
    */
    function getStaticTree($nodesArr)
    {
        $menu  = new treemenu();
        $basenode = new treenode(array('text' => 'Static Cotent', 'link' => '', 'icon' => 'base.gif', 'expandedIcon' => 'base.gif'));    

        foreach($nodesArr as $node)
        {            
            if($node['parent_Node']==null)
            {            
                $basenode->addItem($this->getStaticChildNodes($nodesArr,$node['id'],stripslashes($this->shortenString($node['title']))));
            }
        }
        $menu->addItem($basenode);
        //$menu->addItem($this->recurTree($rootnodeid,$rootlabel));

        // Create the presentation class
        $treeMenu = &new dhtml($menu, array('images' => 'treeimages', 'defaultClass' => 'treeMenuDefault'));
      
        return $treeMenu->getMenu();
     
    }  
    
    /*
    *Method to create a child node for the tree
    * @access Private
    * @param array $nodesArr : The list of nodes for a course
    * @param string $parentId : The Id of the parent Node
    * @param string $title : The Title of the node
    * @return string $basenode : A tree node 
    */
    function getStaticChildNodes($nodeArr,$parentId,$title){
        //setup the link      
        $link = $parentId.'.html';
        //create a new tree node
        $basenode = new treenode(array('text' => stripslashes($title), 'link' => $link, 'icon' => NULL, 'expandedIcon' => NULL ));        
        
        //search for more children
        foreach ($nodeArr as $line) 
        {   
            if($line['parent_Node']==$parentId){
                $basenode->addItem($this->getStaticChildNodes($nodeArr,$line['id'],$this->shortenString($line['title'])));
            }
        }
        return $basenode;
    }    
    
    /**
    * Method to get a tree for sharing nodes
    * @param string $contextId The Context Id
    * @param string $mode The type of tree , either DHTML or Dropdown
    * @return string 
    * @access public
    */
    function getTree($contextId,$mode = NULL, $modeParams = NULL)
    { 
    
        $icon = 'folder_up.gif';
        $expandedIcon = 'folder-expanded.gif';
        $link='';
        
        $rootnodeid=$this->objDBContext->getRootNodeId($contextId);        
        $rootlabel='';
    
        //Create a new menu        
        $menu  = new treemenu();
        $contentlink=$this->URI(array('action','contenthome'),$this->module);
        
        //create base node
        $basenode = new treenode(array('text' => $rootlabel, 'link' => $contentlink, 'icon' => 'base.gif', 'expandedIcon' => 'base.gif'));    
        
        $this->objDBContentNodes->resetTable();
        $nodesArr=$this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id='$rootnodeid'");
        
        $this->objDBContentNodes->changeTable('tbl_context_nodes_has_tbl_context_page_content');
        
        foreach($nodesArr as $node){            
            if($node['parent_Node']==null){            
                $basenode->addItem($this->getChildNodes($nodesArr,$node['id'],stripslashes($this->shortenString($node['title']))));
            }
        }
        $this->objDBContentNodes->resetTable();
        
        $menu->addItem($basenode);
        //$menu->addItem($this->recurTree($rootnodeid,$rootlabel));

        // Create the presentation class
        $treeMenu = &new dhtml($menu, array('images' => $this->objSkin->getSkinURL().'/treeimages/imagesAlt2', 'defaultClass' => 'treeMenuDefault'));
        if($mode == 'listbox')
        {
           $listBox  = &new listbox($menu, array('linkTarget' => '_self','submitText' => $modeParams['submitText'],'promoText' => $modeParams['promoText']));
           return  $listBox->getMenu();
        }
        return '<h5>'.$this->objDBContext->getTitle().'</h5>'.$treeMenu->getMenu();
    }
}
?>