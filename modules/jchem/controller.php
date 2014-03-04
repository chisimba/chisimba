<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
// end security check
/**
 * The jchem controller manages the jchem module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010, University of the Witwatersrand
 * @license GNU GPL
 * @package jchem
 */

class jchem extends controller {

    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;

    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * @var object $objDisplay: The display object
    * @access public
    */
    public $objDisplay;

    /**
    * Method to initialise the controller
    *
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objLanguage = $this->getObject( 'language', 'language' );
        $this->objUser = $this->newObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objDisplay = $this->newObject('jchemdisplay', 'jchem');
    }

    /**
    * Method the engine uses to kickstart the module
    *
    * @access public
    * @param string $action: The action to be performed
    * @return void
    */
    function dispatch( $action )
    {
        switch($action){

            default:
            case 'show':

                $html = $this->objDisplay->show();

                $this->setVarByRef('templateContent', $html);
                return 'display_tpl.php';
                break;
        }
    }
}
?>