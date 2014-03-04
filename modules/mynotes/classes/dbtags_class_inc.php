<?php

/**
 * @package mynotes
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class for providing access to the table tbl_tag in the database
 * @author Nguni
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mynotes
 * @version 0.1
 */
class dbtags extends dbtable {

    /*
     * The name of the table for which this class does operations
     * 
     * @var string
     * @access public
     * 
     */
    public $table;
    
    /*
     * The user object
     * 
     * @var object
     * @access private
     */
    private $objUser;
    
    /*
     * The current user id
     * 
     * @var String
     * @access private
     * 
     */
    private $userId;

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_mynotes_tags');
        $this->table = 'tbl_mynotes_tags';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a tag in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @return array $id The id of the inserted or updated tag.
     * @return array $qnId The qnId of the inserted or updated question.
     * 
     */
    public function addTag($data) {
        $othertags = explode(",", $data["tags"]);
        $fields = Array();
        $count = 1;
        $id = NULL;

        if (!empty($othertags)) {
            foreach ($othertags as $othertag) {
                if (!empty($othertag)) {
                    $fields['name'] = $othertag;
                    $fields['userid'] = $this->userId;
                    $fields['datecreated'] = date('Y-m-d H:i:s');

                    $tagExists = $this->getTags("name='" . $othertag . "'");
                    if (!empty($tagExists)) {
                        $tagid = $tagExists[0]["id"];
                        $count = $tagExists[0]['count'];

                        // increase tag count
                        ++$count;
                        $fields['count'] = $count;
                        $fields['datemodified'] = date('Y-m-d H:i:s');
                        $fields['modifiedby'] = $this->userId;

                        $this->update('id', $tagid, $fields);
                    } else {
                        $fields['count'] = $count;
                        $this->insert($fields);
                    }
                }
            }
        }

        return $id;
    }
    
    /**
     * 
     * Remove existing tags in preparation for an update
     * DWK: This is a shitty design, but it was what I inherited
     * 
     * @param type $existingTags
     * 
     */
    public function removeTags($existingTags)
    {
        return NULL;
        $tags = explode(",", $existingTags);
        if (!is_array($tags) || count($tags) == 0) {
            $tags=array($existingTags);
        }
        foreach($tags as $tag) {
            $tag = trim($tag);
            $sql = 'SELECT id,name,count FROM ' . $this->table . " WHERE name='" . $tag . '"';
            $res= $this->getArray($sql);
            $id = $res[0]['id'];
            if (count($res) > 0) {
                $num = $res[0]['count'];
                if($num>1) {
                    // decrement its occurrence
                    $num=$num-1;
                    $this->update('id', $id, array(
                        'count' => $num
                    ));
                } else {
                    // just delete it
                    $this->delete('id', $id);
                }
                unset($num);
                unset($id);
            }
        }
    }
    
    /**
     * Method to get all tags.
     *
     * @access public
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of tags.
     * 
     */
    public function getTags($filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;

        if ($filter != NULL) {
            $sql.= " WHERE " . $filter;
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
     * 
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
     * @return VOID
     * 
     */
    public function deleteTag($id) {
        $this->delete('id', $id);
    }
}
// end of class
?>