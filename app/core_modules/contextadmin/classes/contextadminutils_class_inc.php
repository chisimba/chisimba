<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class contextadminutils is a set of utilities that the contextadmin modules needs
 * @package contextadminutils
 * @category context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie 
 * 
 
 */

class contextadminutils extends object {
    
    /**
    * @var  object $objLanguage
    */
    var $objLanguage;
   
   /**
    * @var object $objDBNodes
    */ 
    var $objDbNodes;    
   
   /**
    * @var object $objConfig
    */
    var $objConfig;
   
   /**
    * @var object $objUser
    */
    var $objUser;
   
   /**
    * @var object $objButtons
    */
    var $objButtons;
    
    /**
    * @var object $Table
    */
    var $Table;    
   
   /**
    * @var object objModule
    */
    var $objModule;
    
    /**
    * @var object objDBContext
    */
    var $objDBContext;
   
   /**
    * @var object objDBContextModules
    */
    var $objDBContextModules; 
  
    /**
    *@var object $objectConfirm
    */
    var $objConfirm;
    
    /*
    * @var object objExportContent
    */
    var $objExportContent;
    
    /*
    * @var object objIcon
    */
    var $objIcon;
    
    /*
    * @var object objLink
    */
    var $objLink;
    
    /*
    * @var string contextCode
    */
    var $contextCode;
    
    /*
    * Method that initializes the objects
    */
    function init()
    {
        $this->objDBContextModules=&$this->getObject('dbcontextmodules','context');
        $this->objButtons = &$this->getObject('navbuttons', 'navigation');
        $this->objDBContext = &$this->getObject('dbcontext', 'context');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objDBContentNodes = &$this->getObject('dbcontentnodes', 'context');
        $this->objConfig = &$this->getObject('config', 'config');
        $this->Table = &$this->getObject('htmltable', 'htmlelements');        
        $this->objModule=& $this->getObject('modules','modulecatalogue');  
        $this->loadClass('checkbox','htmlelements');
        $this->objConfirm=&$this->newObject('confirm','utilities');
        $this->objExportContent = & $this->newObject('export','contextadmin');
        $this->objIcon = & $this->newObject('geticon','htmlelements');
        $this->objLink = & $this->newObject('link','htmlelements');
        
        $this->contextCode = $this->objDBContext->getContextCode();
    } 
    
    
    /**
     * This method produces a list of context
     * @param string $orderBy The list order
     * @param array $arrList The list 
     * @param array $arrAdminLinks The edit , delete, course admin links
     * return $string
     */
     function displayData($arrList, $arrHeadings=array(), $arrAdminLinks=TRUE, $orderBy = 'id'){
        $H3 = & $this->newObject('htmlheading', 'htmlelements');
        $H3->str = $this->objLanguage->languageText("mod_contextadmin_name",'contextadmin');
        $addIcon = $this->objIcon->getAddIcon($this->uri( array('action' => 'add'), "contextadmin"));
     //   print_r($arrList);
        //start the heading
        $this->Table->init();
        $this->Table->startRow();
        $this->Table->addCell($H3->show(), '50%','middle', 'right',NULL);
        $this->Table->addCell( $addIcon , '50%','middle', 'left',NULL);
        $this->Table->endRow();        
        $str = $this->Table->show();
        
        
        //start the list of courses
        $this->Table->init();
        $this->Table->addHeader($arrHeadings);
        
        $rowcount = 0;
        foreach($arrList as $list){
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $this->Table->startRow();
            $this->Table->addCell($list['contextCode'], NULL, NULL, NULL, $oddOrEven);           
            $this->Table->addCell($list['title'], NULL, NULL, NULL, $oddOrEven);
            $this->Table->addCell($list['menutext'], NULL, NULL, NULL, $oddOrEven);
            $this->Table->addCell($this->_getIcon($list['isActive']), NULL, NULL, NULL, $oddOrEven);
            $this->Table->addCell($this->_getIcon($list['isClosed']), NULL, NULL, NULL, $oddOrEven);
                    
            
            $this->Table->addCell($this->_getAdminLinks($list['contextCode']), NULL, NULL, 'right', $oddOrEven);
            $this->Table->endRow();
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
       
        $str .= $this->Table->show();
         
         return $str;
     }
     
     /**
     * This method generates admins links 
     * for the list of context
     * @param string $contextCode The Context Code
     * @return string 
     */
     function _getAdminLinks($contextCode){
         $str = '';
         //course admin
         $this->objIcon->setModuleIcon('contextadmin');
        $this->objIcon->alt = $this->objLanguage->languageText("mod_contextadmin_configcontext",'contextadmin');
        $this->objLink->href = $this->uri(array('action' => 'courseadmin', 'contextCode' => $contextCode), "contextadmin");
        $this->objLink->link = $this->objIcon->show();               
        $str .= $this->objLink->show().'&nbsp;';
         
            //edit
        $editArray = array('action' => 'edit',
            'fromwhere' => 'contextadmin',
            'contextCode' => $contextCode);
        $this->objIcon->setIcon('edit');
        $this->objIcon->alt = $this->objLanguage->languageText("mod_contextadmin_editcontext",'contextadmin');
        $this->objLink->href = $this->uri($editArray, "contextadmin");
        $this->objLink->link = $this->objIcon->show();               
        $str .= $this->objLink->show().'&nbsp;';
         
             //delete
         $deleteArray = array('action' => 'delete',
            'contextCode' => $contextCode);     
        $this->objIcon->setIcon('delete');
        $this->objIcon->alt=$this->objLanguage->languageText("mod_contextadmin_deletecontext",'contextadmin');
        $this->objConfirm->setConfirm($this->objIcon->show(),$this->uri($deleteArray, "contextadmin"),$this->objLanguage->languageText("mod_contextadmin_deletequest",'contextadmin'));              
        $str.=$this->objConfirm->show().'&nbsp;';
           
        return $str;
     }
     
     /**
     * Method to show the proper icon 
     *@param boolean $isOn 
     *@return string
     */
     function _getIcon($isOn){
         $icon = ($isOn == 0) ? "offline" : "online";
         $alt = ($isOn == 0) ? "False" : "True";
         $this->objIcon->alt=$alt;
         $this->objIcon->setIcon($icon);
         return $this->objIcon->show();
     }
    
    
       /**
    * Method to get the proper list of course that 
    * is available to a user
    * @param string $userId the user ID
    * @return array
    */
    function getUserContext(){
        $where = 'WHERE (isClosed <> 1 OR isNull(isClosed)) AND (isNull(isActive) OR isActive=1)';
        if($this->objModule->checkIfRegistered('contextgroups', 'contextgroups'))
        {
            $group = & $this->newObject('managegroups', 'contextgroups');
            $codes = $group->userContextCodes($this->objUser->userId());
            $memberOf = "'".implode("', '", $codes )."'";
            $where .= " OR contextCode IN ($memberOf)";
        }
        $rs = $this->objDBContext->getAll( $where.' ORDER BY UPPER(menuText)');
        return $rs;
    }
    
    
    
}
?>