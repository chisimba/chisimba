<?php

/**
 * @package mcqtests
 * @filesource
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class for providing access to the table tbl_tag in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.3
 */
class dbtag extends dbtable {

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;
    public $objUser;
    public $userId;

    public function init() {
        parent::init('tbl_test_tag');
        $this->table = 'tbl_test_tag';
        $this->objUser = &$this->getObject('user', 'security');
        $this->dbTagInstance = $this->newObject('dbtag_instance');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a tag in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the tag to be edited. Default=NULL.
     * @return array $id The id of the inserted or updated tag.
     * @return array $qnId The qnId of the inserted or updated question.
     */
    public function addTag($fields, $id = Null, $qnId = Null) {
        $othertags = explode(",", $fields["tags"]);
        if (!empty($othertags)) {
            $count = 0;
            $idArr = array();
            if (!empty($id))
                $idArr[$count] = $id;
            foreach ($othertags as $ot) {
                if (!empty($ot)) {
                    $count++;
                    $fieldsT['name'] = $ot;
                    $fieldsT['userid'] = $this->userId;
                    $fieldsT['rawname'] = $ot;
                    $fieldsT['tagtype'] = "default";

                    $fieldsT['timemodified'] = date('Y-m-d H:i:s');
                    if ($id) {
                        $fieldsT['timemodified'] = date('Y-m-d H:i:s');
                        $fieldsT['modifiedby'] = $this->userId;
                        $this->update('id', $id, $fieldsT);
                    } else {
                        //Add only if tag does not exist
                        //Check if tag already exists
                        $tagExists = $this->getTags("name='" . $ot . "'");
                        //If tag exists, check if there exists its instance for this question, else add tag
                        if (!empty($tagExists)) {
                            $tagid = $tagExists[0]["id"];
                            //Check if there is an instance of this tag for this particular question, if not add instance
                            $tagInstanceExists = $this->dbTagInstance->getInstances($itemId = $qnId, $filter = "tagid = '" . $tagid . "'");
                            if (empty($tagInstanceExists)) {
                                $idArr[$count] = $tagid;
                                $fieldsTI = array();
                                $fieldsTI['tagid'] = $tagid;
                                $fieldsTI['itemtype'] = "question";
                                $fieldsTI['itemid'] = $qnId;
                                $this->dbTagInstance->addInstance($fieldsTI, Null);
                            }
                        } else {
                            $fieldsT['timecreated'] = date('Y-m-d H:i:s');
                            $fieldsT['createdby'] = $this->userId;
                            $tagid = $this->insert($fieldsT);
                            $idArr[$count] = $tagid;
                            $fieldsTI = array();
                            $fieldsTI['tagid'] = $tagid;
                            $fieldsTI['itemtype'] = "question";
                            $fieldsTI['itemid'] = $qnId;
                            $this->dbTagInstance->addInstance($fieldsTI, Null);
                        }
                    }
                }
            }
        }
        return $id;
    }

    /**
     * Method to get all tags.
     *
     * @access public
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of tags.
     */
    public function getTags($filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter != NULL) {
            $sql.= " WHERE " . $filter;
        } else {
            $sql .= "";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a specific tag.
     *
     * @access public
     * @param string $id The id of the tag.
     * @return array $data The details of the tag.
     */
    public function getTag($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a tag.
     * The sort order of the following tags is decreased by one.
     *
     * @access public
     * @param string $id The id of the tag.
     * @return
     */
    public function deleteTag($id) {
        $this->delete('id', $id);
    }

}

// end of class
?>