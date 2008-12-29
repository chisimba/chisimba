<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
* Class to access the Context Tables 
* @package dbfile
* @category context
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 
* @author Wesley  Nitsckie
* @example :
*/

 class organisortree extends object{
     
     /**
     * @var array $arrTree;
     */
     var $arrTree = array();
     
     /**
     * @var counter;
     */
     var $counter;
       /**
     * @var form;
     */
     var $form;
       /**
     * @var link;
     */
     var $link;
       /**
     * @var input;
     */
     var $input;
    
       /**
     * @var objIcon;
     */
 
     var $objIcon;
     
       /**
     * @var string  module;
     */
     var $module;
      /**
     * @var object $objDBContentNode : The context nodes
     */
    var $objDBContentNodes;
    
         
     /**
     * Constructor
     */
     function init(){
        $this->objIcon = $this->newObject('geticon', 'htmlelements');        
        $this->form = $this->newObject('form', 'htmlelements');        
        $this->input = $this->newObject('textinput', 'htmlelements');        
        $this->link = $this->newObject('link', 'htmlelements');        
        $this->button = $this->newObject('button', 'htmlelements');       
        $this->objDBContext =  $this->newObject('dbcontext', 'context');
        $this->objDBContentNodes =  $this->newObject('dbcontentnodes', 'context');        
     }
     
     /**
     * Method generate the tree
     * @param array $arrNodes The list of nodes
     * @return string 
     */
     function getNodesTree($arrNodes, $module = 'contextcontent'){
         $this->arrTree = $arrNodes;        
         $this->module = $module;
         $str = $this->getJavascript();        
         foreach($arrNodes as $node){            
            if($node['parent_Node']==null){
                $str .= $this->_makeNode($node['id'], $node['title']);              
            }
        }
         return $str;
     }
     
      /**
     * Method biuld the tree
     * @param string $parentId The id that you are currently working  with
     * @param string $menuText The text that will be displayed in the tree 
     * @param string $pageNo The page number
     * @return string 
     */
     function _makeNode($parentId, $menuText){
        $this->objIcon = $this->newObject('geticon', 'htmlelements');        
        $this->form = $this->newObject('form', 'htmlelements');        
        $this->input = $this->newObject('textinput', 'htmlelements');        
        $this->link = $this->newObject('link', 'htmlelements');        
        $this->button = $this->newObject('button', 'htmlelements');    
        $this->radio = $this->newObject('radio', 'htmlelements');    
        $node = $this->objDBContentNodes->getRow('id', $parentId);
        if($menuText == '')
        {
            $menuText = '[TEXT NEEDED]';
        }
                
        //set the icon
         $this->objIcon->setIcon('plus');
         $str = '
                     <table border=0 cellpadding="1" cellspacing="2">
                         <tr>
                             <td width="16" ><a id="x'.$parentId.'" href="javascript:Toggle(\''.$parentId.'\');">'.$this->objIcon->show().'</a></td>
                             <td width="300"><a href="'. $this->uri(array('action' =>'content' , 'nodeid' => $parentId), 'context').'" >'.$menuText.'</a></td>';
         
                   
        //set move up icon      
         if(!$this->objDBContentNodes->isFirstSibling($parentId))
         {
             $this->objIcon->setIcon('mvup');
             $str .='        
                             <td><a href="'. $this->uri(array('action' =>'moveup' , 'nodeid' => $parentId), $this->module).'" >'.$this->objIcon->show().'</a></td>';
          } else {
              $str .='        
                           <td>&nbsp;&nbsp;&nbsp;</td>';
          }
          
          //set move down icon
          if(!$this->objDBContentNodes->isLastSibling($parentId))
          {
                  $this->objIcon->setIcon('mvdown');
                  $str .='    
                             <td><a href="'. $this->uri(array('action' =>'movedown' , 'nodeid' => $parentId), $this->module).'" >'.$this->objIcon->show().'</a></td>
                 ';
          }   
          
         //create a small form for moving straight to a node
        
        $this->radio->name = 'nodeid';
        $this->radio->addOption($parentId,'');
        $str .='<td>'.$this->radio->show().'</td>';
      
        $str .=' </tr></table>';
       
         
         $str .= ' 
         <div id="'.$parentId.'" style="display: none; margin-left: 2em;">';
         $cnt = 0;
         $tmpstr = '';
         
         //check for more children recursively
         $myArr = $this->objDBContentNodes->getSortedNodes($parentId);
         foreach ($myArr as $line) 
        {                                       
                $str .= $this->_makeNode($line['id'], $line['title']);           
        }
        
        $str .= ' </div>';
        
         return $str;
     }
     
      /**
     * Method get the javascript     
     * @return string 
     */
     function getJavascript(){
        $this->objIcon->setIcon('plus');
        $this->objIcon->height = '16';
        $this->objIcon->width = '16';
        $this->objIcon->extra = "hspace='0' vspace='0' border='0' ";
        $folderIcon = $this->objIcon->show();
        
        $this->objIcon->setIcon('plus');
        $textIcon = $this->objIcon->show();
        
        $this->objIcon->setIcon('minus');
        $textFolderIcon = $this->objIcon->show();
        
        $str = '  <script language="Javascript">
                            function Toggle(item) {
                            obj=document.getElementById(item);
                               visible=(obj.style.display!="none")
                               key=document.getElementById("x" + item);
                               if (visible) {
                                 obj.style.display="none";
                                 key.innerHTML="'.addslashes($folderIcon).'";
                               } else {
                                  obj.style.display="block";
                                  key.innerHTML="'.addslashes($textFolderIcon).'";
                               }
                            }
                            
                            function Expand() {
                               divs=document.getElementsByTagName("DIV");
                               for (i=0;i<divs.length;i++) {
                                 divs[i].style.display="block";
                                 key=document.getElementById("x" + divs[i].id);
                                 key.innerHTML="'.addslashes($textFolderIcon).'";
                               }
                            }
                            
                            function Collapse() {
                               divs=document.getElementsByTagName("DIV");
                               for (i=0;i<divs.length;i++) {
                                 divs[i].style.display="none";
                                 key=document.getElementById("x" + divs[i].id);
                                 key.innerHTML="'.addslashes($folderIcon).'";
                               }             
                            } 
                            
                         //   Expand();
                            
                            </script>';
        return $str;

     }
     
     
     /**
     *  Method to get a sorted list of nodes with indented spaces
     * @return string
     */
     function getListBox(){
         //create dropdown box
         $dropdown =  $this->newObject('dropdown', 'htmlelements');
         $dropdown->name = 'sourceId';
         //get the root id
         $contextCode = $this->objDBContext->getContextCode();
         if($contextCode){
            $id = $this->objDBContext->getField('id', $contextCode);
        } else {
            $id = Null;
        }
        $rootnodeid=$this->objDBContext->getRootNodeId($id);
         
         //get the list of nodes for the context        
        $nodesArr=$this->objDBContentNodes->getAll("WHERE tbl_context_parentnodes_id='$rootnodeid' ORDER BY sortindex");
       
       //create a root node
        $dropdown->addOption(NULL,'[ROOT]');
        
        //add the nodes to the dropdown recursively
        foreach($nodesArr as $node){            
            //start with the nodes that has no parent 
            if($node['parent_Node']==null){       
                
                $text = $this->objDBContentNodes->getMenuText($node['id']);
                if($text==''){
                    $text = $node['title'];
                }                
                $dropdown->addOption($node['id'],$text);
                $this->addNode($nodesArr, $node['id'], $dropdown,  '&nbsp;&nbsp;');
            }
        }        
        return $dropdown->show();      
     }
     
     /**
     * Recurcive method 
     */
     function addNode($nodeArr, $parentId, &$dropdown,$sp=NULL)
     {
         $spaces = '&nbsp;&nbsp;'.$sp;
         foreach ($nodeArr as $line) 
        {   
            if($line['parent_Node']==$parentId)
            {
                $text = $this->objDBContentNodes->getMenuText($line['id']);
                if($text=='')
                {
                    $text = $line['title'];
                }
                 $dropdown->addOption($line['id'],$spaces.$text);
                 $this->addNode($nodeArr, $line['id'], $dropdown,$spaces.'&nbsp;&nbsp;');
            }
        }
     
     } 
 }
 ?>