<?php
/**
 *
 *
 * PHP version 5.1.0+
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
 * @package   MXit Dictionary
 * @author    Qhamani Fenama
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
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

class viewer extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $objConfig;
    public $objSysConfig;
    public $objWashout;
    public $objUser;

    /**
     * Constructor
     *
     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->objUser = $this->getObject('user', 'security');
    }

        
    public function renderOutputForBrowser($records) {
		
        $ret = NULL;
	
		$objTableClass = $this->newObject('htmltable', 'htmlelements');
		//language item for no records
		$norecords = $this->objLanguage->languageText('mod_mxit_nodata', 'mxitdictionary');
			//A statement not to display the records if it is empty.
			if (empty($records)) {
				$objTableClass->addCell($norecords, NULL, NULL, 'center', 'noRecordsMessage', 'colspan="7"');

			}
			 else {
				//Create an array for each value in the table.
				foreach($records as $record) {
				$rowcount++;
				// Set odd even colour scheme
				$class = ($rowcount%2 == 0) ? 'odd' : 'even';
				$objTableClass->startRow();
		
				//add word 
				$word = $record['word'];
				$objTableClass->addCell($word, '', 'center', 'left', $class);
				
				//add definition
				$definition = '&nbsp;' .$record['definition'];
				$objTableClass->addCell($definition, '', 'center', 'left', $class);
		
				//check if the user is admin
				if($this->objUser->isAdmin())
				{
					//get id for deleting and editing
					$id = $record['id'];

					//Create delete icon and delete action
					$objDelIcon = $this->newObject('geticon', 'htmlelements');
					$delLink = array(
						'action' => 'deleteentry',
						'id' => $id,
						'module' => 'mxitdictionary',
						'confirm' => 'yes',
					);

					$deletephrase = $this->objLanguage->languageText('mod_mxit_deleteicon', 'mxitdictionary');
					$conf = $objDelIcon->getDeleteIconWithConfirm('', $delLink, 'mxitdictionary', $deletephrase);
					$update = $conf;
					$objTableClass->addCell($update, '', 'center', 'center', $class);

					// Create edit icon and action
					$this->loadClass('link', 'htmlelements');
					$objIcon = $this->newObject('geticon', 'htmlelements');
					$link = new link($this->uri(array(
						'action' => 'editentry',
						'id' => $id
					) , 'mxitdictionary'));
					$objIcon->setIcon('edit');
					$link->link = $objIcon->show();
					$update = $link->show();
					$objTableClass->addCell($update, '', 'center', 'center', $class);
					}
				$objTableClass->endRow();
				} //end of loop   
			}

			//shows the array in a table
			$ret = $objTableClass->show();
		    header ( "Content-Type: text/html;charset=utf-8" );
        return $ret;
    }

    public function renderTopBoxen($userid = NULL) {
    }
    
    public function renderLeftBoxen($userid = NULL) {
    }

    public function renderRightBoxen($userid = NULL) {
    }

}
?>
