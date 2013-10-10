<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access (db model) Class for the qrreview module
 *
 * This is a database model class for the qrreview module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author Paul Scott
 * @filesource
 * @copyright AVOIR
 * @package qrreview
 * @category chisimba
 * @access public
 */

class dbqrreview extends dbTable
{

	/**
	 * Standard init function - Class Constructor
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objLanguage = $this->getObject("language", "language");
		parent::init('tbl_qrreview_prods');
	}
	
    /**
    * Method to add a record
    * @param array $insarr Array with record fields
    * @return string Last Insert Id
    */
	public function insertRecord($insarr)
	{
	    $insarr['creationdate'] = $this->now();
		return $this->insert($insarr, 'tbl_qrreview_prods');
	}
	
	/**
	 * Method to retrieve a specific record by ID from the table
	 * 
	 * @param string id
	 * @access public
	 * @return array
	 */
	 public function getRecord($id) {
	     return $this->getAll("WHERE id = '$id'");
	 }
	 
	 public function getLastProds($num) {
	     return $this->getALL("ORDER BY puid DESC LIMIT 0,$num"); 
	 }
	 
	 public function updateQR($recid, $fileurl) {
	     $this->update('id', $recid, $fileurl);
	 }
	 
	 public function changeTable($table) {
	     parent::init($table);
	 }
	 
	 public function addComment($commarr){
	     $this->changeTable('tbl_qrreview_reviews');
	     $commarr['creationdate'] = $this->now();
	     $id = $this->insert($commarr, 'tbl_qrreview_reviews');
	     $this->changeTable('tbl_qrreview_prods');
	     
	     return $id;
	 }
	 
	 public function getLastReviews($num) {
	     $this->changeTable('tbl_qrreview_reviews');
	     $data = $this->getAll("ORDER BY puid DESC LIMIT 0, $num");
	     $this->changeTable('tbl_qrreview_prods');
	     return $data;
	     
	 }
	 
	 public function getTopScore($num) {
	     $data = $this->getAll("ORDER BY aggregate DESC LIMIT 0, $num");
	     return $data;
	 }
	 
	 public function updateScores($id, $score) {
	     // first get the record
	     $rec = $this->getRecord($id);
	     $rec = $rec[0];
	     $tscore = $score + intval($rec['score']);
	     $numrev = $rec['numrev'] + 1;
	     $aggregate = ($tscore / ($numrev*10))*100;
	     $uparr = array('score' => $score, 'numrev' => $numrev, 'aggregate' => $aggregate);
	     return $this->update('id', $rec['id'], $uparr);
	 }
}
?>
