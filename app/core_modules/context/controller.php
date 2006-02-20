<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The context controller manages
 * the context module
 * @author Wesley Nitsckie
 * @version $Id$
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
 **/

class context extends controller
{    
    /**
    * @var object $objSkin 
    */
    var $objSkin;

    /**
    * @var  object $objLanguage 
    */
    var $objLanguage;
    
    /**
    * @var object $objContext 
    */
    var $objContext;
    
    /**
    * @var object $objDBContext 
    */
    var $objDBContext;
    
    /**
    * @var object $objDBContent 
    */
    var $objDBContent;
    
    /**
    * @var object $objDBContentNodes    
    */
    var $objDBContentNodes;
    
    /**
    * @var object $objIcon 
    */
    var $objIcon;
    
    /**
    * @var string  $contextId 
    */
    var $contextId;
    
    /**
    * @var string $treeicon 
    */
    var $treeicon='paper.gif';
    /**
    * @var string $treeiconexpanded 
    */
    var $treeiconexpanded='folder-expanded.gif';
    
    /**
    * @var string $contentId
    *
    */
    var $contentId;
    
    /**
    * @var string contextCode
    */ 
    var $contextCode;
    
    /**
    * @var string nodeId
    */ 
    var $nodeId;
    
    /**
    * @var object $objNodeAdmin
    */
    var $objNodeAdmin;
    
    /**
    * @var object $objUser
    */ 
    var $objUser;
    
    /**
    * @var object $objPermission;
    */ 
    var $objPermissions;
    
    /**
    * @var object $objLink: Use to create links
    */
    var $objLink;
    
    /**
    * @var object $objDBContextModules
    */
    var $objDBContextModules;
    
    /**
    *@var object $objLoggendId;
    */
    var $objLoggedIn;
    
    /**
    *@var bool $isRootNode
    */
    var $isRootNode;
    
   /**
    *@var object $objPopup
    */
    var $objPopup;
    
    /**
    *@var object objNote
    */
    var $objNote;
    
    /**
    *@var object $objWebcal
    */
    var $objWebcal;
    
    /**
    *@var object $objModule
    */    
    var $objModule;
    
    /**
    *@var object $objDublinCore
    */    
    var $objDublinCore;
    
    
    
    /**
    * Method to initialise the controller
    */
    function init(){
        
        $this->objSkin = & $this->getObject('skin','skin');
        $this->objLanguage=& $this->getObject('language', 'language');        
        $this->objDBContext=& $this->getObject('dbcontext');
        $this->objDBContentNodes=& $this->getObject('dbcontentnodes');        
        $this->objIcon=& $this->getObject('geticon','htmlelements');
        $this->objUser= & $this->getObject('user','security');        
        $this->objPop=& $this->getObject('windowpop','htmlelements');
        $this->objLink=&$this->newObject('link','htmlelements');
        $this->objDBContextModules=&$this->newObject('dbcontextmodules','context');
        $this->objNote=&$this->newObject('dbnotes','context');
        $this->objModule=&$this->newObject('modulesadmin','modulelist');
        $this->objDublinCore=&$this->newObject('dublincore','dublincoremetadata');
       
        $this->objCal->callingModule = "context";        
        $this->objLoggedIn=&$this->newObject('loggedin','communications');
        $this->objWebcal = $this->getObject('contextcalender','contextcalendar');
        
        //the tree classes
        $this->loadClass('treemenu','tree');
        $this->loadClass('treenode','tree');        
        $this->loadClass('dhtml','tree');        
        
        //get the current contextcode and contextId
        $this->contextCode=$this->objDBContext->getContextCode();
        $this->contextId=$this->objDBContext->getContextId();        
        
        //get the nodeId
        $this->nodeId=$this->getParam('nodeid');
        if($this->nodeId == ''){
            $this->nodeId = $this->objDBContentNodes->_getFirstContentNodeId();            
        }
        
         //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();


    }

    /** This is a method to determine if the user has to be logged in or not
    */
      function requiresLogin() // overides that in parent class
     {
        return FALSE;
        
     }
     
    /**
    * Method the engine uses to kickstart the module
    */
    function dispatch(){
    
        $this->checkAgreement();
        
        $action = $this->getParam('action');
        switch($action){
            case NULL:
                if(!$this->objUser->isLoggedIn())
                {
                     return $this->nextAction('content',null);
                }
            case 'contenthome':
                return 'list_tpl.php';
                
                // Print page content to pdf
            case 'print':
                $page = $this->showPage();
                $this->loadClass('html2fpdf','pdf');

                $pdf = new HTML2FPDF();
                $pdf->AddPage();
                $pdf->WriteHTML($page);
                $pdf->Output($this->contextCode.'_'.$this->nodeId.'.pdf','D');
                return $this->nextAction('',array(''),'pdf');
                
            case 'content':
                 if($this->objUser->isLoggedIn()){
                    $this->contextArea();
                    return 'contentarea_tpl.php';
                 } else {
                     return $this->nextAction('content', array('nodeid' => $this->getParam('nodeid')),'resourcekit');
                 }
            case 'showcontent':
                $this->setVar('content',$this->showContent());
                //$this->setVar('pageSuppressContainer',TRUE);
                $this->setPageTemplate('note_page_tpl.php');
                return 'content_tpl.php';
            case 'addnode':
                $this->setVar('addType','sibling');
                return 'add_tpl.php';                
            case 'addchildnode':
                $this->setVar('addType','child');
                return 'add_tpl.php';                 
            case 'editnode':
                return 'add_tpl.php';              
            case 'save_add':
                $newNodeId = $this->createNode();
                return $this->nextAction('content',array('nodeid' => $newNodeId));                      
            case 'delete':                
                $this->deleteNode();
                return $this->nextAction('content',null);             
            case 'save_edit':
                $newNodeId = $this->objDBContentNodes->saveNode('edit');
                return $this->nextAction('content',array('nodeid' => $newNodeId));                 
            case 'joincontext';
                if($this->objDBContext->joinContext())
                    return $this->nextAction(null,null);
                else
                    die('UNEXPECTED ERROR');               
            case 'leavecontext';             
                $this->objDBContext->leaveContext();                       
                break;
            case 'listsharedcontexts':
                return 'sharedcontexts_tpl.php';              
            case 'shownote';
                $this->getNoteDetails();
                $this->setPageTemplate('note_page_tpl.php');
                return 'notes_tpl.php';              
            case 'savenote':
                $this->saveNote(addslashes(TRIM($_POST['mode'])));
                $bodyParams = "onload=\"window.close();\"";
                $this->setVarByRef('bodyParams', $bodyParams);
                $this->setPageTemplate('note_page_tpl.php');
                return 'blank_tpl.php';  
            case 'images' :
                $this->setPageTemplate('imagedownload_page_tpl.php');
                return 'filedownload_tpl.php';
            case 'removechars';
                $this->removeIllChars();
                //return $this->nextAction('content');
        } 
    
    }
    
    /**********************************************************
    *****************CONTEXT HOME*************************
    **********************************************************/

    /**********************************************************
    * *************** CONTENT AREA **************************
    * *********************************************************/
    
    /**
    * Method to show content
    */    
    function contextArea()
    {
        if(!$this->objDBContentNodes->hasNodes())
        {          
            $this->objDBContentNodes->createParentNode();
        }                
      
        $this->setVar('content',$this->showContentArea());        
    }
    
    
    /**
    * Method that shows the content area
    */
    function showContentArea(){
        //$objTree = &$this->newObject('contenttree','tree');
       /// $this->setVar('tree',$objTree->show());       
        $this->setVar('page',$this->showPage());    
        $this->setVar('footerStr',$this->showFooter())    ;
    }
        

    /*** PAGE CONTENT****/
    /**
    * Method to show the contents
    * of a page or a node
    * @return string :the content 
    * @access public
    */
    function showPage(){        
   //DEBUG 
   //   still deciding whether to use iframe for the content
    /* 
        $iframe=&$this->newObject('iframe','htmlelements');
        $iframe->src=$this->uri(array('action'=>'showcontent','nodeid'=>$this->getParam('nodeid')));
        $iframe->frameborder=0;
        return $iframe->show();
        */
        return $this->showContent();
    }
    
    /**
    *Method to show the content
    * @return string The cotent
    * @access public
    */
    function showContent(){
        $nodeid=$this->nodeId;
        //$nodeid= $this->getParam('nodeid');
        $heading=&$this->newObject('htmlheading','htmlelements');
        $heading->type=5;
        
        //if nodeis is null then get the root node id        
        if($this->getParam('nodeid') == NULL)
        {
            return '<P><P><P>'.stripslashes($this->objDBContext->getField("about"));
        }
        
        if($nodeid){
			$licence = '';
            $this->objDBContentNodes->resetTable();
            $line=$this->objDBContentNodes->getRow('id',$nodeid);
			if ($this->objModule->checkIfRegistered('', 'creativecommons'))
			{
				$objCC = & $this->newObject('dbcreativecommons', 'creativecommons');
				$licence = '<center>'.$objCC->checkLicence($nodeid).'</center>';
			}
          //  if(in_array('title',$line))
                $heading->str=stripslashes($line['title']);
            //parse the content through the glossary if the module is registered            
            if ($this->objModule->checkIfRegistered('', 'glossary')){
                $glossary=&$this->newObject('dbglossary','glossary');                
                return $heading->show().
					stripslashes(
						$glossary->parse(
							$this->objDBContentNodes->getBody($nodeid),$this->contextCode)).
								'<P>'.$this->_showMiniNavBar().
									$license;
            }else{
                return $heading->show().
					stripslashes(
						$this->objDBContentNodes->getBody($nodeid)).
							'<P>'.$this->_showMiniNavBar().
							   $license;
				
            }
        }
        
    }
    
    /**
    * Method to show the mini 
    * Navigation bar
    * @access: public
    * @param string the Footer with navigation bar
    */
    function showFooter()
    {
        if($this->objUser->isLoggedIn())
        {
            $this->objIcon->setIcon("home");
            $this->objIcon->alt=$this->objLanguage->languageText("word_course").' '.$this->objLanguage->languageText("word_home");
            $this->objIcon->align = "absmiddle";
            
            $str='&nbsp;<a href="'.$this->URI(null,'context').'">';
            $str.=$this->objIcon->show();
            $str.='</a>&nbsp;';
            
            //$str.=$this->_showMiniNavBar();
            $str.=$this->_showContentManager();
            $str.=$this->_showOtherBar();
            return $str;
        }
        else
        {
            return $this->_showMiniNavBar();
        }
    }
    
    /**
    *Method that shows the
    *notes and other features on the bar
    * @return string Icons on the bar
    * @access public
    */
    function _showOtherBar()
    {
        if(!$this->nodeId=='')
        {
            $this->objIcon->setIcon("student_notes");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_notes");
            $this->objIcon->align = "absmiddle";            
            
            $location=$this->uri(array(
              'action'=>'shownote',
              'nodeid'=>$this->nodeId, 
              'module'=>'context'));
            $this->objPop->set('location',$location);
            $this->objPop->set('linktext', $this->objIcon->show());
            $this->objPop->set('width','300');
            $this->objPop->set('height','150');
            $this->objPop->set('left','300');
            $this->objPop->set('top','300');
            //$this->objPop->putJs();
            
            //$this->getNoteDetails();
           // return $this->objPop->show();  
        }        
    }
    
    
    //***************USERS PAGE NOTES*******
    /**
    * Method get the details of of a note
    * @access private 
    * @return null
    */
    function getNoteDetails(){    
        $arr=$this->objNote->getNote($this->nodeId,$this->objUser->userId());
        
        if(sizeof($arr)>0){
            if(is_array($arr)){
                $note=$arr[0]['note'];
                $id=$arr[0]['id'];
            }    
        }else{
            $note='';
            $id='';
        }        
        $this->setVarByRef('noteValue',$note);
        $this->setVarByRef('noteId',$id);    
        $this->setVar('nodeId',$this->nodeId);    
    }
    
    /**
    * Method that saves a node
    * @param string $mode: Either edit or add mode
    * @access private
    * @return null
    */
    function saveNote($mode=null){
        $this->objNote->saveRecord( $this->objUser->userId(),$mode);
    }
    //*************END OF NOTES********
    
    /**
    * Method that biulds the edit , add and delete buttons
    * @access private
    * @return string $str : The buttons for the navbar
    */ 
    function _showContentManager()
    {
        $str='';      
        if($this->isValid('editnode'))
        {  
            if(!$this->nodeId=='')
            {            
                //edit a node
                $this->objIcon->setIcon("edit");
                $this->objIcon->alt=$this->objLanguage->languageText("word_edit");
                   $this->objIcon->align = "absmiddle";
                
                $str.='&nbsp;<a href="'.$this->URI(array('action' => 'editnode','nodeid' => $this->nodeId)).'">';
                $str.=$this->objIcon->show();
                $str.='</a>';
            }
        }
        
        if($this->isValid('delete'))
        {  
            if(!$this->nodeId==''){
                //delete a node
                $this->objIcon->setIcon("delete");
                $this->objIcon->alt=$this->objLanguage->languageText("word_delete");
                $this->objIcon->align = "absmiddle";
                
                $uri=$this->URI(array('action' => 'delete','nodeid' => $this->nodeId));
                $objConfirm = &$this->newObject('confirm','utilities');
                $objConfirm->setConfirm($this->objIcon->show(),$uri,$this->objLanguage->languageText("mod_context_delmessnode"));
                $str .= $objConfirm->show();
                /*
                $str.='&nbsp;<a href=" '.$this->URI(array('action' => 'delete','nodeid' => $this->getParam('nodeid'))).'">';
                $str.=$this->objIcon->show();
                $str.='</a>';
                */
            
            }
        }
        
        // Print to pdf
        if(!$this->nodeId==''){
            $this->objIcon->setModuleIcon('pdf');
            $this->objIcon->alt=$this->objLanguage->languageText('mod_context_convertpdf', 'Convert to PDF');
            $this->objIcon->align = 'absmiddle';

            $this->objLink->link('javascript:void(0)');
            $this->objLink->link = $this->objIcon->show();
            $this->objLink->extra = "onclick=\"window.open('".$this->uri(array('action'=>'print', 'nodeid'=>$this->nodeId))."', 'print', 'width=2, height=2')\"";

            $str .= $this->objLink->show();
        }
        
        if($this->isValid('addnode'))
        {  
            if(!$this->nodeId==''){
                //add a sibling node
                $this->objIcon->setIcon("addsibling");
                $this->objIcon->alt=$this->objLanguage->languageText("mod_context_addsibling");
                $this->objIcon->align = "absmiddle";
                    
                $str.='&nbsp;<a href="'.$this->URI(array('action' => 'addnode','nodeid' => $this->nodeId)).'">';
                $str.=$this->objIcon->show();
                $str.='</a>';    
            }        
        }

        if($this->isValid('addchildnode'))
        {  
            //add a child node
            $this->objIcon->setIcon("addchild");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_addchild");
            $this->objIcon->align = "absmiddle";
        
        
            $str.='&nbsp;<a href="'.$this->URI(array('action' => 'addchildnode','nodeid' => $this->nodeId)).'">';
            $str.=$this->objIcon->show();
            $str.='</a>';            
        }
        
        return $str;
    }
    
    /**
    * Method that shows the Mini Nav Bar
    * like go next to node,previous node, first node
    * @return null
    * @access private
    */
    function _showMiniNavBar()
    {
        $lastbutton=false;
        //get the current node
        if(!$this->nodeId=='')
        {    
            $this->objDBContentNodes->resetTable();
            $line=$this->objDBContentNodes->getRow('id',$this->nodeId);
            $objStr = & $this->newObject('contenttree', 'tree');
            
            // CREATE THE NAVIGATION BUTTONS
            
            //--- BEGINNING NAV BUTTONS ---//
            $this->objIcon->setIcon("first_grey");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_first");
            $this->objIcon->align = "absmiddle";            
            $firstOFF=$this->objIcon->show();
            
            $this->objIcon->setIcon("first");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_first");
            $this->objIcon->align = "absmiddle";            
            $firstON=$this->objIcon->show();
            
            //---NEXT NAV BUTTONS---//
            $this->objIcon->setIcon("next");
            $tmp = $this->objLanguage->languageText("mod_context_nav_next").' : '.$objStr->shortenString($this->objDBContentNodes->getField('title',$line['next_Node']));
            $this->objIcon->alt= $tmp;
            $this->objIcon->align = "absmiddle";            
            $nextON=$tmp.$this->objIcon->show();
            
            $this->objIcon->setIcon("next_grey");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_next");
            $this->objIcon->align = "absmiddle";            
            $nextOFF=$this->objIcon->show();
            
            //---PREVIOUS NAV BUTTONS---//
            $this->objIcon->setIcon("prev");
            $tmp = $this->objLanguage->languageText("mod_context_nav_prev").' : '.$objStr->shortenString($this->objDBContentNodes->getField('title',$line['prev_Node']));
            $this->objIcon->alt=$tmp;
            $this->objIcon->align = "absmiddle";            
            $prevON=$this->objIcon->show().$tmp;
            
            $this->objIcon->setIcon("prev_grey");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_prev");
            $this->objIcon->align = "absmiddle";            
            $prevOFF=$this->objIcon->show();
            
            //---LAST NAV BUTTONS---//
            $this->objIcon->setIcon("last");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_last");
               $this->objIcon->align = "absmiddle";            
            $lastON=$this->objIcon->show();
            
            $this->objIcon->setIcon("last_grey");
            $this->objIcon->alt=$this->objLanguage->languageText("mod_context_nav_last");
               $this->objIcon->align = "absmiddle";            
            $lastOFF=$this->objIcon->show();
            
            
            
            $str='';
            
            
            //first button
            /*
            if($this->objDBContentNodes->isFirstNode($this->getParam('nodeid')))
            {
                $str.=$firstOFF;
            }else{
            
                $str.=$firstON;
            }
            */
            
            //Previous button                
            if (!$line['prev_Node']==null)            
            {         
                $str.='<a href="'.$this->URI(array('action' => 'content','nodeid' => $line['prev_Node'])).'">';
                $str.=$prevON;
                $str.='</a>&nbsp;&nbsp;&nbsp;';            
            }
            else
                $str.='';//$prevOFF;        
            
            //next button
            if (!$line['next_Node']==null)
            {                
                $str.='<a href="'.$this->URI(array('action' => 'content','nodeid' => $line['next_Node'])).'">';
                $str.=$nextON;
                $str.='</a>';
            }else
                $str.='';//$nextOFF;
                
            //last button
            /*
            if($lastbutton)
            {
                $str.='<a href="'.$this->URI(array('action' => 'content','nodeid' => $this->objDBContentNodes->getField('nextNodeId',$this->nodeId))).'">';
                $str.=$lastON;
                $str.='</a>';
            }
            else
                $str.=$lastOFF;
                */
            return $str;
        }
        
    }
    /**********************************************************
    * *************** END OF CONTENT AREA *********************
    * *********************************************************/
    
    
    
    
    
    
    /**********************************************************
    * *************** ADD CONTENT AREA *********************
    * *********************************************************/

    /**
    * Method to save a new node
    * @access public
    * @return null
    */    
    function createNode()
    {
        return $this->objDBContentNodes->saveNode('add');
    }
    
    /**
    * Method to delete the current
    * node together with its chilren
    * @access public
    * @return null
    */
    function deleteNode()
    {
        return $this->objDBContentNodes->deleteNodeRecursively($this->nodeId);
    }

    
    
    /**********************************************************
    * *************** END OF ADD CONTENT AREA ***********
    * *********************************************************/
    
    /**
    * Method to generate
    * a link in the list_tpl
    * @deprecated
    */
    function getModuleLink($moduleId,$url,$text) {
        if ($this->objDBContextModules->isVisible($moduleId,$this->objDBContext->getContextCode())){
            $this->objIcon->setModuleIcon($moduleId);
            $this->objLink->href=$url;
            $this->objLink->link=$text;
            return $this->objIcon->show().$this->objLink->show();    
        }else{
            return false;
        }
    }    
    
    /**
    * Method to create a link to the course home
    *@return null
    */
    function getContextLinks()
    {
        $this->objIcon->setIcon("home");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursehome");
        $this->objIcon->align = "absmiddle";
        
        $this->objLink->href=$this->URI(null,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();
        
        return $str;       
    }
    
     /**
    * Method to create links to the contents
    * and to the course
    *@return null
    */
    function getContentLinks()
    {
        $this->objIcon->setModuleIcon("content");
        $this->objIcon->alt=$this->objLanguage->languageText("mod_context_coursecontent");
        $this->objIcon->align = "absmiddle";
        
        $params = array('nodeid' => $this->getParam('nodeid'), 'action' => 'content');
        $this->objLink->href=$this->URI($params,'context');
        $this->objLink->link=$this->objIcon->show();
        $str = $this->objLink->show();
        
        return $str;       
    }
    
    /**
    * Method to check if the user accepted the user agreement
    *NETTEL Specific ... if someone can get a 
    * work around for this I will appreciated ..
    *    or get a work flow module or sometinh
    * this is the crap way of going about this
    */
    function checkAgreement()
    {
        
        //check if the nettel module is registered
        $objModules = & $this->getObject('modulesadmin', 'modulelist');        
        if($objModules->checkIfRegistered(NULL, 'nettel'))
        {             
             // check if the course is closed 
           // $line =  $this->objDBContext->getRow('contextCode', $this->objDBContext->getContextCode());
             //print $line['isClosed'];
             $objContextCondtition = & $this->getObject('contextcondition', 'contextpermissions');
             if($objContextCondtition->isContextMember())
             {
                 $objDBContextAgreement = & $this->getObject('dbcontextagreements', 'nettel');
                 if($objDBContextAgreement->hasAgreement())
                 {
                     //check if the user has agreed
                       $objDBUserAgreement = & $this->getObject('dbuseragreements', 'nettel');
                       if(!$objDBUserAgreement->hasAgreed())
                       {
                           //show the agreement
                           return $this->nextAction('viewuseragreement', array(), 'nettel');
                       }
                 }      
             }     
        } 
        /// END -> Nettel
    
    }
    /**
    * Method get rid of all the '?' in the content 
    */
    function removeIllChars()
    {
        $objContent = $this->newObject('dbpagecontent', 'context');
        $arr = $objContent->getAll();
        foreach ($arr as $line)
        {
            $body = $line['body'];   
            $body = str_replace('?','', $body);
            $objContent->update('id',$line['id'],array('body' => $body));
        }
    }
}
?>
