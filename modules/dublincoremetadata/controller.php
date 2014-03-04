<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class dublincoremetadata that manages 
 * the Dublin Core Metadata
 * @package contextadmin
 * @category context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie 
 */
class dublincoremetadata extends controller
{
    
    /**
    * @var  object $objLanguage
    */
    var $objLanguage;
   
   /**
   * Constructor
   */   
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSkin = $this->getObject('skin', 'skin');
    }
    
    /**
    *The dispatch method that kick starts the module
    */
    function dispatch($action)
    {        
        switch ($action) 
        {
            case null:
            default:
                //$this->setPageTemplate('note_page_tpl.php');
                return 'add_tpl.php';
        }
    }
}

?>