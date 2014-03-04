<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the treenodes table for menu management module.
*
* @package publicportal
* @category sems
* @copyright AVOIR
* @license GNU GPL
* @author Prince Mbekwa
*/

class dbgroups extends dbTable
{
    /**
    * The user object
    *
    * @access public
    * @var object
    */
    public $objUser;

   /**
    * Class Constructor
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try {
            parent::init('tbl_groupadmin_group');
            $this->objUser = & $this->newObject('user', 'security');

       } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage();
            exit();
       }
    }

      /**
	 * Method to return the child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return array The array of child nodes
     * @access public
	 */
    public function getChildNodes($parentId)
    {
		try {
            $sql = "SELECT * FROM tbl_groupadmin_group";
            if (!is_null($parentId)) {
                $sql .= " WHERE parent_id = '".$parentId."'";
            } else {
                $sql .= " WHERE ISNULL(parent_id)";
            }
			return $this->query($sql);
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
	 * Method to return the number of child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return integer The number of records
     * @access public
	 */
    public function getChildNodeCount($parentId)
    {
		try {
            $sql = "SELECT COUNT(*) AS cnt FROM tbl_groupadmin_group";
            if (!is_null($parentId)) {
                $sql .= " WHERE parent_id = '".$parentId."'";
            } else {
                $sql .= " WHERE ISNULL(parent_id)";
            }
			$nodeCount = $this->query($sql);
            if (count($nodeCount)){
                return $nodeCount[0]['cnt'];
            }else{
                return 0;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
	 * Method to return a particular node
     *
	 * @param string $id The id of the node to return
     * @return array The node record from the db
     * @access public
	 */
  	public function getNode($id, $noPermissions = TRUE)
	{
		try {
            $sql = "SELECT * FROM tbl_groupadmin_group"
                   ." WHERE id = '".$id."'";
			return $this->query($sql);
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
	}
}

?>
