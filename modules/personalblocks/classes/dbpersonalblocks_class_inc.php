<?php
/**
*
* Personal blocks
*
* Allows the creation of personal blocks for display on sidebar block areas.
* Requires the blockalicious module to function. Personal blocks allow the
* addition of web widgets in locations such as a blog.
*
*/
/* ----------- data class extends dbTable for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
*
* Model class for the table tbl_dbpersonalblocks
*
* @author Derek Keats
*
*/
class dbpersonalblocks extends dbTable
{
    /**
    * @var string $objUser string object Property for holding the
    * user object
    * @access public
    *
    */
    public $objLanguage;

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_personalblocks');
        $this->objUser = & $this->getObject("user", "security");
    }

    /**
    *
    * Save method for editing a record in this table
    *
    * @param string $mode: edit if coming from edit, add if coming from add
    * @access public
    * @return TRUE
    *
    */
    public function saveRecord($userId)
    {
        $mode = $this->getParam("mode", "add");
        try
        {
            $id = $this->getParam('id', NULL);
            $location = $this->getParam('location', NULL);
            $blockname = $this->getParam('blockname', NULL);
            $blockcontent = $this->getParam('blockcontent', NULL);
            $blocktype = $this->getParam('blocktype', NULL);
            $context = $this->getParam('context', NULL);
            $active = $this->getParam('active', NULL);
            $sortorder = $this->getParam('sortorder', NULL);
            // If we are doing and edit use the update method.
            if ($mode=="edit") {
                $this->update("id", $id, array(
                'location' => $location,
                'blockname' => $blockname,
                'blockcontent' => $blockcontent,
                'blocktype' => $blocktype,
                'context' => $context,
                'active' => $active,
                'sortorder' => $sortorder,
                'datemodified' => $this->now(),
                'modified' => $this->now(),
                'modifierid' => $this->objUser->userId()));
            }
            // If we are doing an add then use the insert method.
            if ($mode=="add") {
                
                #die($blocktype . " --- " . $context);
                $this->insert(array(
                'location' => $location,
                'blockname' => $blockname,
                'blockcontent' => $blockcontent,
                'blocktype' => $blocktype,
                'context' => $context,
                'active' => $active,
                'sortorder' => $sortorder,
                'datecreated' => $this->now(),
                'creatorid' => $this->objUser->userId(),
                'modified' => $this->now()));

            }
            return TRUE;
        } catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }

    /**
    *
    * Get all the blocks for a given creatorId
    * @param string $creatorId The userid of the block owner
    * @param boolean $active Whether the blocks should be active
    * @param string $location Location of the block (left, middle, right)
    * @return string array An array of blocks
    * @access public
    *
    */
    public function getBlocks($creatorId, $active=NULL, $location=NULL)
    {
    	$where = " WHERE creatorid='" . $creatorId . "'";
        if ($active) {
        	$where .= " AND active='" . $active . "'";
        }
        if ($location){
        	$where .= "AND location='" . $location . "'";
        }
        $where .= " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get all the blocks for a given context
    * @param string $context The context of the context block
    * @param boolean $active Whether the blocks should be active
    * @param string $location Location of the block (left, middle, right)
    * @return string array An array of blocks
    * @access public
    *
    */
    public function getBlocksContext($context, $active=NULL, $location=NULL)
    {
        $where = " WHERE context='" . $context . "'";
        if ($active) {
            $where .= " AND active='" . $active . "'";
        }
        if ($location){
            $where .= "AND location='" . $location . "'";
        }
        $where .= " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get only the left panel blocks for a given creatorId
    * @param string $creatorId The userid of the block owner
    * @return string array An array of left panel blocks
    * @access public
    *
    */
    public function getLeftBlocks($creatorId)
    {
        $where = " WHERE creatorid='" . $creatorId
          . "' AND active=1 AND location='left' AND blocktype='personal'"
          . " ORDER BY 'sortorder'";
    	return $this->getAll($where);
    }

    /**
    *
    * Get only the right panel blocks for a given creatorId
    * @param string $creatorId The userid of the block owner
    * @return string array An array of right panel blocks
    * @access public
    *
    */
    public function getRightBlocks($creatorId)
    {
        $where = " WHERE creatorid='" . $creatorId
        . "' AND active=1 AND location='right' AND blocktype='personal'"
        . " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get only the middle panel blocks for a given creatorId
    * @param string $creatorId The userid of the block owner
    * @return string array An array of middle panel blocks
    * @access public
    *
    */
    public function getMiddleBlocks($creatorId)
    {
        $where = " WHERE creatorid='" . $creatorId
        . "' AND active=1 AND location='middle' AND blocktype='personal'"
        . " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get only the left panel blocks for a given context
    * @param string $context The context code of the context
    * @return string array An array of left panel blocks
    * @access public
    *
    */
    public function getLeftBlocksContext($context)
    {
        $where = " WHERE context='" . $context
          . "' AND active=1 AND location='left'"
          . " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get only the right panel blocks for a given context
    * @param string $context The context code of the block context
    * @return string array An array of right panel blocks
    * @access public
    *
    */
    public function getRightBlocksContext($context)
    {
        $where = " WHERE context='" . $context
          . "' AND active=1 AND location='right'"
          . " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Get only the middle panel blocks for a given context
    * @param string $context The context code of the block context
    * @return string array An array of middle panel blocks
    * @access public
    *
    */
    public function getMiddleBlocksContext($context)
    {
        $where = " WHERE context='" . $context
          . "' AND active=1 AND location='middle'"
          . " ORDER BY 'sortorder'";
        return $this->getAll($where);
    }

    /**
    *
    * Validate the user so that one user cannot delete another user's
    * records by entering the id and confirm message directly into a browser.
    *
    * @param string $id The key of the record being deleted
    * @return boolean TRUE|FALSE True if the user and the owner of the record match, false otherwise
    * @access public
    *
    */
    public function validateDelete($id)
    {
    	$ar = $this->getRow('id', $id);
        $creatorId = $ar['creatorid'];
        if ($creatorId == $this->objUser->userId()) {
        	return TRUE;
        } else {
        	return FALSE;
        }
    }

}
?>