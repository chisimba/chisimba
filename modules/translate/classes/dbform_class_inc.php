<?php
/**
 * @package translate
 * @filesource
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class for providing access to the table tbl_langs_avai in the database
 * @author Edwin C
 *
 * @copyright (c) 2009 UDSM
 * @package translate
 * @version 1.2
 */
class dbform extends dbtable
{
		/**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
public function init()
{
	parent::init('tbl_langs_avail');
	$this->table = 'tbl_langs_avail';
	$this->objUser = $this->getObject('user', 'security');
	}
	/*
	*Method to insert information to the database 
	*/
	public function addfields($fields, $id = NULL)
    	{
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;

   	 }
}
?>
