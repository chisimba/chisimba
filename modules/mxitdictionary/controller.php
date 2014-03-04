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
 * @package   Mxit Dictionary
 * @author    Qhamani Fenama
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11940 2008-12-29 21:21:54Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die("You cannot view this page directly");
}

// end security check

/**
 *
 * Model controller for the table tbl_phonebook
 * @authors:Godwin Du Plessis, Ewan Burns, Helio Rangeiro, Jacques Cilliers, Luyanda Mgwexa and Qoane Seitlheko.
 * @copyright 2007 University of the Western Cape
 */
class mxitdictionary extends controller
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
	public $objDbSuggested;

    

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
			$this->objDbSuggested = $this->getObject('dbSuggested', 'mxitdictionary');
            $this->objUser = $this->getObject('user', 'security');
            $this->objDbContacts = $this->getObject('dbContacts', 'mxitdictionary');
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
    public function dispatch($action) 
    {
		switch ($action) {
			default:
			case 'default':
			//$this->requiresLogin(TRUE);
			    $count = $this->objDbContacts->getWordsRecordCount ();
                $pages = ceil ( $count / 10 );
                $this->setVarByRef ( 'pages', $pages );
				header("Content-Type: text/html;charset=utf-8");

                return 'view_tpl.php';
                break;
			case 'viewallajax' :
			//$this->requiresLogin(FALSE);
				$page = intval ( $this->getParam ( 'page', 0 ) );
                if ($page < 0) {
                    $page = 0;
                }
                $start = $page * 10;
                $records = $this->objDbContacts->getRange($start, 10);

                $this->setVarByRef ( 'records', $records );

                header("Content-Type: text/html;charset=utf-8");
                return 'view_ajax_tpl.php';
                break;

			// Link to the template
			case 'link2':
			//$this->requiresLogin(FALSE);
				return 'addsugg_tpl.php';
            	break;
			
			case 'link3':
			//$this->requiresLogin(TRUE);
			    $count = $this->objDbSuggested->getWordsRecordCount ();
                $pages = ceil ( $count / 10 );
                $this->setVarByRef ( 'pages', $pages );
				header("Content-Type: text/html;charset=utf-8");

                return 'viewsugg_tpl.php';
                break;
			case 'sugviewallajax':
				//$this->requiresLogin(FALSE);
				$page = intval ( $this->getParam ( 'page', 0 ) );
                if ($page < 0) {
                    $page = 0;
                }
                $start = $page * 10;
                $records = $this->objDbSuggested->getRange($start, 10);

                $this->setVarByRef ( 'records', $records );

                header("Content-Type: text/html;charset=utf-8");
                return 'viewsugg_ajax_tpl.php';
                break;

			//case approve a word
			case 'approve':
				$id = html_entity_decode($this->getParam('id'));
 				$rec = $this->objDbSuggested->listSingle($id);
				
				$word = $rec['word'];
				$definition = $rec['definition'];
	            $this->objDbContacts->insertRecord($word, $definition);
				$this->objDbSuggested->deleteRec($id);
                $records = $this->objDbSuggested->listAll();
                $this->setVarByRef('records', $records);
                return 'viewsugg_tpl.php';
                break;
			//case reject a word
			case 'reject':
				$this->objDbSuggested->deleteRec($this->getParam('id'));
				$records = $this->objDbSuggested->listAll();
                $this->setVarByRef('records', $records);
                return 'viewsugg_tpl.php';
                break;
	    
			//case to suggest a word
		    case 'suggest';

				return $this->save();
				break;
            // Case to add a word
            case 'addentry';
		    	$word = htmlentities($this->getParam('word') , ENT_QUOTES);
	    		$definition = htmlentities($this->getParam('definition') , ENT_QUOTES);
            	$this->objDbContacts->insertRecord($word, $definition);
            	$this->nextAction('view_tpl.php');

            	break;
        		// Link to the template
        	case 'link':
				$this->requiresLogin(TRUE);
				return 'addentry_tpl.php';
            	break;
        	// Case to get the information from the form
        	case 'editentry':
	    		$id = html_entity_decode($this->getParam('id'));
            	$oldrec = $this->objDbContacts->listSingle($id);
            	$this->setVarByRef('oldrec', $oldrec);
            	return 'editentry_tpl.php';
				break;
        	//Case to edit/update an entry
        	case 'updateentry':
            	$id = $this->getParam('id');
	            $word = htmlentities($this->getParam('word'));
	            $definition = htmlentities($this->getParam('definition'));

	            $arrayOfRecords = array(
	                'word' => $word,
	                'definition' => $definition
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

//end of dispatch function
//
		public function save()
			{
			$captcha = htmlentities($this->getParam('request_captcha') , ENT_QUOTES);
			$word = htmlentities($this->getParam('word') , ENT_QUOTES);
	    	$definition = htmlentities($this->getParam('definition') , ENT_QUOTES);
			
			if(!isset($word) && !isset($defination))
			{
				$msg = 'nodata';
				$this->setVarByRef('msg', $msg);
				$this->setVarByRef('insarr', $insarr);
				//return 'form_tpl.php';					
				return 'addsugg_tpl.php';
				}
			elseif (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha))
				{
				$msg = 'badcaptcha';
				$this->setVarByRef('msg', $msg);
				$this->setVarByRef('insarr', $insarr);
				//return 'form_tpl.php';
				return 'addsugg_tpl.php';
				}
			else 
				{
				$this->objDbSuggested->insertRecord($word, $definition);
				//return a thanks template
				$msg = 'thanks';
				$this->setVarByRef('msg', $msg);
				//returns next action
				return $this->display_post();
				}	

    
		}

  		public function display_post()
		{
	    	$this->nextAction('view_tpl.php');
    	}
 		public function requiresLogin()
    	{
        	return FALSE;
		}
    	
//
}
?>
