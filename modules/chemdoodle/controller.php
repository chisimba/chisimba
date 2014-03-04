<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
// end security check
/**
 * The chemdoodle controller manages the chemdoodle module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010, University of the Witwatersrand
 * @license GNU GPL
 * @package chemdoodle
 */

class chemdoodle extends controller {

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
        $this->objDisplay = $this->newObject('chemdisplay', 'chemdoodle');
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
                $file = $this->getResourcePath('molecules/larger.mol', 'chemdoodle');//'/var/www/vre/trunk/app/packages/chemdoodle/resources/molecules/larger.mol';

                $html = $this->objDisplay->show2dMolecule($file);
                $html .= '<br />';
                $html .= $this->objDisplay->show3dMolecule($file);
                $html .= '<br />';
                $html .= $this->objDisplay->showTransformerMolecule($file);
                $html .= '<br />';
                $html .= $this->objDisplay->showMolecule($file);
                $html .= '<br />';
                $html .= $this->objDisplay->getMolecule();

                $this->setVarByRef('templateContent', $html);
                return 'display_tpl.php';
                break;
        }
    }
}
?>