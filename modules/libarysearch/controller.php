<?php

/**
 * libarysearch Controller
 * 
 * controller class for libarysearch package
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   bittorrent
 * @author    Yasser Jimmy
 * @copyright 2009  
 * @license   gpl
 * 
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check



class libarysearch extends controller
{

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objLanguage;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objConfig;
	
	
	
	/**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }

    }
    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null)
    {

     $this->setLayoutTemplate('tpl_libarysearch_layout.php');

        switch ($action) {
            default:
            	

            	return "dump_tpl.php";
            	break;
            	

 			case "save": 
            	
echo "THE AJAX MAGIC DONE HERE CHARL? + Any one who would wanna Help out?";

            	//return "dump_tpl.php";
            	break;
          
        }
    }
}
?>
