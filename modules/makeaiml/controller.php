<?php
/**
 * MakeAIML Controller
 *
 * controller class for makeAIML package
 *
 * PHP version 5
 *
 * The license text...
 *
 * @category  Chisimba
 * @package   makeaiml
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   $Id: controller.php 9524 2008-06-10 06:48:28Z paulscott $
 * @link      http://avoir.uwc.ac.za
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


/**
 * Controller class for the makeaiml module
 *
 * Controller class for the makeaiml module
 *
 * @category  Chisimba
 * @package   makeaiml
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class makeaiml extends controller
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

    public $objUser;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
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
        switch ($action) {
            default:
                $userid = $this->objUser->userId();
                $path = $this->getResourcePath('aiml')."/".$userid."/";
                if(!file_exists($path)) {
                    mkdir($path, 0777);
                }
                echo $path;
                break;

        }
    }

    /**
    * Overide the login object in the parent class
    *
    * @param  void
    * @return bool
    * @access public
    */
    public function requiresLogin($action)
    {
       return FALSE;
    }
}
?>