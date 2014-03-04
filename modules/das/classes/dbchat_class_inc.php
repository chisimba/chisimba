<?php
/**
 *
 * Viewer class for rendering an array of messages to the browser
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
 * @category  Chisimba
 * @package   das
 * @author    Wesley Nitsckie wesleynitsckie@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * Class to manage the Alias' for a user. This is to hide the user's details and gives the
 * advisors a way to identify conversations 
 *
 * @author Wesley Nitsckie
 * @package DAS
 *
 */
class dbchat extends dbTable {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     *
     * Constructor

     * @access public
     *
     */
    public function init() {
		parent::init('tbl_das_chat');
	}
	
	
	/**
	* Method to add or update an alias
	* @parent string $personId
	* @return boolean
	*/
	public function addMessage($message, $userId)
	{
		
		//add
		if($message != "")
		{
			return $this->insert(array('userid' => $userId, 'body' => $message, 'datesent' => $this->now()), 'tbl_das_chat');
		} else {
			return FALSE;
		}
			
		
		
	}
	
	
	/**
     * Method to search all messages
     * @param string $keyword
     * @return array
     */
    public function searchMessages($keyword)
    {
        $ret = $this->getAll("WHERE msgbody LIKE '%%$keyword%%' OR msgfrom LIKE '%%$keyword%%' ORDER BY datesent DESC LIMIT 50");
	    return $ret;
    }
    
	/**
	* Method to get the person's alias
	* @param string $personId
	* @return string
	*/
	public function getMessages($fromTime = null)
	{
		
		$recs = $this->getAll("ORDER BY datesent DESC limit 20");
		return $recs;
	}
}
