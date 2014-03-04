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
 * @author    Administrative User <admin@localhost.local>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbcontacts_class_inc.php 11940 2008-12-29 21:21:54Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts

/**
 * Description for $GLOBALS
 * @global integer $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!$GLOBALS['kewl_entry_point_run']) 
{
    die("You cannot view this page directly");
}


/**
 * Model class for the table tbl_mxit_words
 * @author:Godwin Qhamani Fenama
 * @copyright 2007 University of the Western Cape
 */


/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   mxitdictionary
 * @author    Qhamani Fenama
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbcontacts_class_inc.php 11940 2008-12-29 21:21:54Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class dbContacts extends dbTable
{
    /**
     * Constructor method to define the table
     */
    public function init() 
    {
		parent::init('tbl_mxit_words');
    }

 	public function getRange($start, $num){
		$range = $this->getAll ( "ORDER BY word ASC LIMIT {$start}, {$num}" );
		return $range;
	}

	public function getWordsRecordCount(){
		return $this->getRecordCount();
	}

	public function listAll($alph, $start, $num){

		$sql = "SELECT * FROM tbl_mxit_words ";
		$sql .= "WHERE tbl_mxit_words.word LIKE '$alph%' ";
		$sql .= "ORDER BY word ASC LIMIT {$start}, {$num} ";
		$userrec = $this->getArray($sql);
		return $userrec;
		
	}

	/**
     * Return a single record in the tbl_mxit_words.
     *
     * @param $word is the word taken from the tbl_mxit_words
     */
	public function getDefinition($word){
		$onerec = $this->getRow('word', $word);
		return $onerec;
    }
   
    /**
     * Return a single record in the tbl_mxit_words.
     *
     * @param $id is the id taken from the tbl_mxit_words
     */
    public function listSingle($id){
		$onerec = $this->getRow('id', $id);
        return $onerec;
    }

    /**
     * Insert a record in the tbl_mxit_words.
     *
     * @param $wprd           is the id taken from the form
     * @param $definition     is the name taken from the form
     *
     *                           Also checks if text inputs are empty and returns the add a record template
     */ 
    public function insertRecord($word, $definition){
        $arrayOfRecords = array(
            'word' => $word,
            'definition' => $definition
        );

        if (empty($word) && empty($definition)) {
            return "addentry_tpl.php";
        } else {
	
            return $this->insert($arrayOfRecords, 'tbl_mxit_words');
        }
    }

    /**
     * Deletes a record from the tbl_mxit_words
     *
     * @param $id is the generated id for a single record
     */

    public function deleteRec($id){
        return $this->delete('id', $id, 'tbl_mxit_words');
    }

    /**
     * Updates a record to the tbl_mxit_words                      
     */

    public function updateRec($id, $arrayOfRecords){
        return $this->update('id', $id, $arrayOfRecords, 'tbl_mxit_words');
    }
}
?>
