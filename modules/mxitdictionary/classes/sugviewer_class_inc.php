<?php
/*
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


/**
 *
 *
 * PHP version 5.1.0+
 *
 * @author Qhamani Fenama
 * @package MXit Dictionary
 *
 */
class sugviewer extends object {

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
		$norecords = $this->objLanguage->languageText('mod_mxit_nosugg', 'mxitdictionary');
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
				$word = $record['word'] . '&nbsp;' . '&nbsp;' . '&nbsp;' . '&nbsp;' ;
				$objTableClass->addCell($word, '', 'center', 'left', $class);

				//add definition
				$definition = $record['definition'];
				$objTableClass->addCell($definition, '', 'center', 'left', $class);
		
				        //check if the user is admin
				if($this->objUser->isAdmin())
				{
					//get id for deleting and editing
					$id = $record['id'];


					// Create delete icon and delete action
					$this->loadClass('link', 'htmlelements');
					$objIcon = $this->newObject('geticon', 'htmlelements');
					$link = new link($this->uri(array(
						'action' => 'approve',
						'id' => $id
					) , 'mxitdictionary'));
					$objIcon->setIcon('greentick');
					$link->link = $objIcon->show();
					$update = $link->show();
					$objTableClass->addCell($update, '', 'center', 'center', $class);

					// Create edit icon and action
					$this->loadClass('link', 'htmlelements');
					$objIcon = $this->newObject('geticon', 'htmlelements');
					$link = new link($this->uri(array(
						'action' => 'reject',
						'id' => $id
					) , 'mxitdictionary'));
					$objIcon->setIcon('failed');
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
