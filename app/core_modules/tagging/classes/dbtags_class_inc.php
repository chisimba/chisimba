<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access (db model) Class for the tagging module
 *
 * This is a database model class for the tagging module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author Paul Scott
 * @filesource
 * @copyright AVOIR
 * @package tagging
 * @category chisimba
 * @access public
 */

class dbtags extends dbTable
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
        parent::init('tbl_tags');
    }

    public function getAllTags()
    {
        $this->_changeTable('tbl_tags');
        return $this->getAll();
    }

    public function deleteTags($itemId, $module)
    {
        //change tables to the postmeta table to delete the tags
        $this->_changeTable('tbl_tags');
        //get all the entries where the itemid matches the deleted itemid
        $tagstodelete = $this->getAll("WHERE item_id = '$itemId' AND module = '$module'");
        if(!empty($tagstodelete))
        {
            foreach($tagstodelete as $deltags)
            {
                $this->delete('id', $deltags['id'], 'tbl_tags');
            }
        }
    }

    /**
     * Method to get all of the tags associated with a particular post
     *
     * @param string $postid
     * @return array
     */
    public function getPostTags($itemId, $module)
    {
        $this->_changeTable("tbl_tags");
        return $this->getAll("WHERE item_id = '$itemId' AND module = '$module'");
    }

    /**
     * Insert a set of tags into the database associated with the post
     *
     * @param array $tagarray
     * @param string $userid
     * @param String $itemId
     * @param string $module
     * @param string $context Optional - Context item belongs to
     */
    public function insertTags($tagarray, $userid, $itemId, $module, $uri, $context=NULL)
    {
        $this->_changeTable("tbl_tags");
        foreach($tagarray as $tins)
        {
            $tins = trim($tins);
            $tins = addslashes($tins);
            if(!empty($tins))
            {
                $this->insert(array('userid' => $userid, 'item_id' => $itemId, 'meta_key' => 'tag', 'meta_value' => $tins, 'module' => $module, 'uri' => $uri, 'context' => $context));
            }
        }

    }

    /**
     * Insert a set of tags into the database associated with the post
     *
     * @param array $tagarray
     * @param string $userid
     * @param String $itemId
     * @param string $module
     * @param string $context Optional - Context item belongs to
     */
    public function insertHashTags($tagarray, $userid, $itemId, $module, $uri, $context=NULL)
    {
        $this->_changeTable("tbl_tags");
        foreach($tagarray as $tins)
        {
            $tins = trim($tins);
            $tins = addslashes($tins);
            if(!empty($tins))
            {
                $this->insert(array('userid' => $userid, 'item_id' => $itemId, 'meta_key' => 'hashtag', 'meta_value' => $tins, 'module' => $module, 'uri' => $uri, 'context' => $context));
            }
        }

    }

    /**
     * Insert a set of tags into the database associated with the post
     *
     * @param array $tagarray
     * @param string $userid
     * @param String $itemId
     * @param string $module
     * @param string $context Optional - Context item belongs to
     */
    public function insertAtTags($tagarray, $userid, $itemId, $module, $uri, $context=NULL)
    {
        $this->_changeTable("tbl_tags");
        foreach($tagarray as $tins)
        {
            $tins = trim($tins);
            $tins = addslashes($tins);
            if(!empty($tins))
            {
                $this->insert(array('userid' => $userid, 'item_id' => $itemId, 'meta_key' => 'attag', 'meta_value' => $tins, 'module' => $module, 'uri' => $uri, 'context' => $context));
            }
        }

    }

    /**
     * Method to retrieve the tags associated with a userid
     *
     * @param string $userid
     * @return array
     */
    public function getTagsByUser($userid)
    {
        $this->_changeTable("tbl_tags");
        return $this->getAll("WHERE userid = '$userid' and meta_key = 'tag'");
    }


    /**
     * Method to get a tag weight by counting the tags
     *
     * @param string $tag
     * @param string $userid
     * @return integer
     */
    public function getTagWeight($tag, $userid)
    {
        $tag = addslashes($tag);
        $this->_changeTable("tbl_tags");
        $count = $this->getRecordCount("WHERE meta_value = '$tag' AND userid = '$userid'");
        return $count;
    }

    /**
     * Method to get a tag weight by counting the tags
     *
     * @param string $tag
     * @param string $userid
     * @return integer
     */
    public function getSiteTagWeight($tag)
    {
        $tag = addslashes($tag);
        $this->_changeTable("tbl_tags");
        $count = $this->getRecordCount("WHERE meta_value = '$tag'");
        return $count;
    }

    public function migrateBlogTags()
    {
        $this->_changeTable('tbl_blog_postmeta');
        $tags = $this->getAll();
        //print_r($tags); die();
        foreach($tags as $inserts)
        {
            $inserts['module'] = 'blog';
            $inserts['item_id'] = $inserts['post_id'];
            unset($inserts['post_id']);
            $this->insert($inserts, 'tbl_tags');
        }
        return TRUE;
    }

    /**
    * Method to get the tags by Module distinctly
    * @param string $module
    * @return array
    */
    public function getTagsByModule($module)
    {
        $sql = "SELECT DISTINCT meta_value FROM tbl_tags WHERE module='".$module."' and meta_key='tag'";

        return $this->getArray($sql);
    }


    /**
     * Method to dynamically switch tables
     *
     * @param string $table
     * @return boolean
     * @access private
     */
    private function _changeTable($table)
    {
        try {
            parent::init($table);
            return TRUE;
        }
        catch (customException $e)
        {
            customException::cleanUp();
            return FALSE;
        }
    }

    public function getSimilarTags($tag)
    {
        $tag = addslashes($tag);
        $sql = "SELECT DISTINCT meta_value FROM tbl_tags WHERE meta_value LIKE '{$tag}%' ORDER BY meta_value";

        return $this->getArray($sql);
    }
}
?>