<?php
/* ------ controller class for contentblocks module extends controller -----*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
 *
 * Controller class for ContentBlocks
 *
 * Controller class for module ContentBlocks.
 * This handles all the contentblocks actions.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package   contentblocks
 * @author    Paul Mungai paulwando@gmail.com
 * @copyright 2012 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
*/
class contentblocks extends controller
{

    /**
    * @var string $action The action parameter from the querystring 
    */
    public $action;

    /**
    * Standard constructor method 
    */
    public function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the database class for this module
        $this->objDb = $this->getObject("dbcontentblocks", "contentblocks");

        //Create an instance of the language object
        $this->objLanguage = $this->getObject("language", "language");
        // Load the module helper Javascript
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('contentblock.js',
          'contentblocks'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * Standard dispatch method 
    */
    public function dispatch()
    {
        switch ($this->action) {
            case null:
            case 'content_text':
            case 'view':
                // Set the layout template to compatible one
                $this->setLayoutTemplate('layout_tpl.php');
                return 'narrowblockview_tpl.php';
                break;
            case 'content_widetext':
                // Set the layout template to compatible one
                $this->setLayoutTemplate('layout_tpl.php');
                return 'wideblockview_tpl.php';
                break;
            case 'deleteajax':
                $this->objDb->deleteRecord($this->getParam('id', Null));
                die("RECORD_DELETED");
                break;
            case 'ajaxedit':
                // Set the layout template to compatible one
                $this->setLayoutTemplate('layout_tpl.php');
                return "editajax_tpl.php";
                break;
            case 'save':
                $this->objDb->saveRecord($this->getParam('mode', Null));
                $blockType = $this->getParam('blocktype', 'narrowblock');
                return $this->nextAction($blockType);
                break;
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown",NULL,"Unknown action").": ".$this->action);
                return 'dump_tpl.php';
        }
    }
    
    /**
    * Override the default requirement for login
    */
    public function requiresLogin()
    {
        return TRUE;
    }
}
?>