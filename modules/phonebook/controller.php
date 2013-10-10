<?php
/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version unknow
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
 * @category  Chisimba
 * @package   phonebook
 * @author    Administrative User <admin@localhost.local>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 16179 2010-01-08 13:41:38Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}
// end security check

/**
 *
 * Model controller for the table tbl_phonebook
 * @authors:Godwin Du Plessis, Ewan Burns, Helio Rangeiro, Jacques Cilliers, Luyanda Mgwexa and Qoane Seitlheko.
 * @copyright 2007 University of the Western Cape
 */
class phonebook extends controller
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
     * Description for public
     * @var    object
     * @access public
     */
    public $objDbContacts;

    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objUser;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init() 
    {
        try {
            $this->objUser = $this->getObject('user', 'security');
            $this->objDbContacts = $this->getObject('dbContacts', 'phonebook');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    } //end of init function
    
    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null) 
    {
        switch ($action) {
            default:
            case 'default':
                $userId = $this->objUser->userId();
                $records = $this->objDbContacts->listAll($userId);
                $this->setVarByRef('records', $records);
                return 'view_tpl.php';
                break;
            // Case to add an entry
            case 'addentry';
            $userId = $this->objUser->userId();
            $firstname = htmlentities($this->getParam('firstname') , ENT_QUOTES);
            $lastname = htmlentities($this->getParam('lastname') , ENT_QUOTES);
            $emailaddress = htmlentities($this->getParam('emailaddress') , ENT_QUOTES);
            $cellnumber = htmlentities($this->getParam('cellnumber') , ENT_QUOTES);
            $landlinenumber = htmlentities($this->getParam('landlinenumber') , ENT_QUOTES);
            $address = htmlentities($this->getParam('address') , ENT_QUOTES);
            $this->objDbContacts->insertRecord($userId, $firstname, $lastname, $emailaddress, $cellnumber, $landlinenumber, $address);
            $this->nextAction('');
            break;
        // Link to the template
        case 'link':
            return 'addentry_tpl.php';
            break;
        // Case to get the information from the form
        case 'editentry':
            $id = html_entity_decode($this->getParam('id'));
            $oldrec = $this->objDbContacts->listSingle($id);
            $this->setVarByRef('oldrec', $oldrec);
            return 'editentry_tpl.php';
        // Case to edit/update an entry
        case 'updateentry':
            $id = $this->getParam('id');
            $firstname = htmlentities($this->getParam('firstname'));
            $lastname = htmlentities($this->getParam('lastname'));
            $emailaddress = htmlentities($this->getParam('emailaddress'));
            $cellnumber = htmlentities($this->getParam('cellnumber'));
            $landlinenumber = htmlentities($this->getParam('landlinenumber'));
            $address = htmlentities($this->getParam('address'));
            $this->objUser = $this->getObject('user', 'security');
            $arrayOfRecords = array(
                'userid' => $this->objUser->userId() ,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'emailaddress' => $emailaddress,
                'cellnumber' => $cellnumber,
                'landlinenumber' => $landlinenumber,
                'address' => $address,
                'modified' => $this->objDbContacts->now()
            );
            $this->objDbContacts->updateRec($id, $arrayOfRecords);
            return $this->nextAction('view_tpl.php');
            break;
        // Case to delete an entry
        case 'deleteentry':
            $this->objDbContacts->deleteRec($this->getParam('id'));
            return $this->nextAction('view_tpl.php');
            break;
    } //end of switch
  }  
} //end of dispatch function
?>
