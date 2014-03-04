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
 * @package   hospital
 * @author    Administrative User <admin@localhost.local>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php,v 1.32 2007-10-24 09:33:23 arithon Exp $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
/**
 * Description for $GLOBALS
 * @global integer $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 *
 * Model controller for the table tbl_phonebook
 * @authors:Onyach, Gatheru
 * @copyright 2007 University of the Western Cape
 */
class hospital extends controller
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
    public $objDbHospital;

    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objUser;
public function init()
{
 //$this->objUser = $this->getObject('user', 'security');

//Instantiate the language object
$this->objLanguage = $this->getObject('language', 'language');
$this->objDbHospital = $this->getObject('dbhospital', 'hospital');
$this->objConfig = $this->getObject('altconfig', 'config');
}
public function dispatch($action = NULL)
{
switch ($action) {
            default:
            case 'default':
	    	//$userId = $this->objUser->userId();
                $records = $this->objDbHospital->listAll();
                $this->setVarByRef('records', $records);
                return 'view_tpl.php';
                break;
            // Case to add an entry
            case 'addentry';
	    
	    $patientid = htmlentities($this->getParam('patientid') , ENT_QUOTES);
            $firstname = htmlentities($this->getParam('firstname') , ENT_QUOTES);
	    $othernames = htmlentities($this->getParam('othernames') , ENT_QUOTES);
	    $age = htmlentities($this->getParam('age') , ENT_QUOTES);
	    $sex = htmlentities($this->getParam('sex') , ENT_QUOTES);
	    $nationalid = htmlentities($this->getParam('nationalid') , ENT_QUOTES);
	    $phoneno = htmlentities($this->getParam('phoneno') , ENT_QUOTES);
	    $nokname = htmlentities($this->getParam('nokname') , ENT_QUOTES);
	    $nokaddress = htmlentities($this->getParam('nokaddress') , ENT_QUOTES);
	    $nokphoneno = htmlentities($this->getParam('nokphoneno') , ENT_QUOTES);
            $patientdesc = htmlentities($this->getParam('patientdesc') , ENT_QUOTES);
	    if(empty($patientid) && empty($patientname) && empty($patientdesc)) {
            return "addentry_tpl.php";
		} else {
		$this->objDbHospital->insertRecord($patientid, $firstname,$othernames, $age, $sex, $nationalid, $phoneno, $nokname, $nokaddress, $nokphoneno, $patientdesc);
		$this->nextAction('');
		}
	
            break;
        // Link to the template
        case 'link':
            return 'addentry_tpl.php';
            break;
        // Case to get the information from the form
        case 'editentry':
            $patientid = html_entity_decode($this->getParam('patientid'));
            $oldrec = $this->objDbHospital->listSingle($patientid);
            $this->setVarByRef('oldrec', $oldrec);
            return 'editentry_tpl.php';
        // Case to edit/update an entry
        case 'updateentry':
            $patientid = htmlentities($this->getParam('patientid') , ENT_QUOTES);
            $firstname = htmlentities($this->getParam('firstname') , ENT_QUOTES);
	    $othernames = htmlentities($this->getParam('othernames') , ENT_QUOTES);
	    $age = htmlentities($this->getParam('age') , ENT_QUOTES);
	    $sex = htmlentities($this->getParam('sex') , ENT_QUOTES);
	    $nationalid = htmlentities($this->getParam('nationalid') , ENT_QUOTES);
	    $phoneno = htmlentities($this->getParam('phoneno') , ENT_QUOTES);
	    $nokname = htmlentities($this->getParam('nokname') , ENT_QUOTES);
	    $nokaddress = htmlentities($this->getParam('nokaddress') , ENT_QUOTES);
	    $nokphoneno = htmlentities($this->getParam('nokphoneno') , ENT_QUOTES);
            $patientdesc = htmlentities($this->getParam('patientdesc') , ENT_QUOTES);
           
            $this->objDbHospital->updateRec($patientid, $firstname,$othernames, $age, $sex, $nationalid, $phoneno, $nokname, $nokaddress, $nokphoneno, $patientdesc);
            return $this->nextAction('');
            break;
        // Case to delete an entry
        case 'deleteentry':
            $this->objDbHospital->deleteRec($this->getParam('patientid'));
            return $this->nextAction('view_tpl.php');
            break;
    } 
return "editadd_tpl.php";
}
}
?>