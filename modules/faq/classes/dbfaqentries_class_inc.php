<?php

/* ----------- data class extends dbTable for tbl_blog------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_faq_entries
 * @author Jeremy O'Connor
 * @copyright 2004 University of the Western Cape
 */
class dbFaqEntries extends dbTable {

    /**
     * 
     * Constructor method to define the table
     * @access public 
     * @return VOID
     * 
     */
    public function init() {
        parent::init('tbl_faq_entries');
    }

    /**
     * 
     * Insert a record
     * 
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @param string $question The question
     * @param string $answer The answer
     * @param string $userId The user ID
     * @param string $dateLastUpdated Date last updated
     * @access public
     * @return string Result of insert
     */
    public function insertSingle($contextId, $categoryId, $question, $answer, $userId, $dateLastUpdated, $tags) {
        $ins = $this->insert(array(
            'contextid' => $contextId,
            'categoryid' => $categoryId,
            '_index' => $this->getNextIndex($contextId, $categoryId),
            'question' => $question,
            'answer' => $answer,
            'userid' => $userId,
            'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
        ));

        $idresults = $this->getIdByLastUpdateDate(strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated));

        $objTags = $this->getObject('dbfaqtags');
        $objTags->addFaqTags($idresults[0]['id'], $tags);

        $this->objDbFaqCategories = & $this->getObject('dbfaqcategories');
        $categoryRow = $this->objDbFaqCategories->getRow('id', $categoryId);

        // Add to Search
        //   $objIndexData = $this->getObject('indexdata', 'search');
        // Prep Data
        $docId = 'faq_entry_' . $ins;
        $docDate = strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated);
        $url = $this->uri(array('action' => 'view', 'category' => $categoryId), 'faq');
        $title = $question;
        $contents = $question . ': ' . $answer;
        $teaser = $question;
        $module = 'faq';
        $userId = $userId;
        $context = $categoryRow['contextid'];

        // Add to Index
        //$objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);
        return $ins;
    }

    /**
     * Get id of specific entry

     */
    public function getIdByLastUpdateDate($dateLastUpdated) {
        $sql = "SELECT id FROM tbl_faq_entries  WHERE dateLastUpdated='" . $dateLastUpdated . "'";
        return $this->getArray($sql);
    }

    /**
     * Get FAQ entries
     * @author Nonhlanhla Gangeni <noegang@gmail.com>
     */
    public function getEntries($contextId, $categoryId) {
        $sql = "SELECT fc.categoryname as categoryname, fe.question as qn, fe.answer FROM tbl_faq_entries fe,tbl_faq_categories fc WHERE fe.contextid='" . $contextId . "' and fc.id= fe.categoryid";
        return $this->getArray($sql);
    }

    /**
     * Return all records
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @return array The FAQ entries
     */
    public function listAll($contextId, $categoryId) {
        if ($categoryId == "All Categories") {
            return $this->getAll("WHERE contextid='" . $contextId . "' ORDER BY _index");
        } else {
            return $this->getAll("WHERE contextid='" . $contextId . "' AND categoryid='" . $categoryId . "' ORDER BY _index");
        }
    }
    
    /**
     * Return all records
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @return array The FAQ entries
     */
    public function getMostRecent($contextId) {
        $sql = "SELECT tbl_faq_entries.id AS id, 
            tbl_faq_entries.contextid, 
            tbl_faq_entries.categoryid, 
            tbl_faq_entries.question, 
            tbl_faq_entries.userid, 
            tbl_faq_entries.datelastupdated,
            tbl_faq_categories.id AS categorycode,
            tbl_faq_categories.categoryname
            FROM tbl_faq_entries, tbl_faq_categories
            WHERE tbl_faq_entries.contextid='{$contextId}' AND 
            tbl_faq_entries.categoryid = tbl_faq_categories.id
            ORDER BY  tbl_faq_entries.datelastupdated DESC ";
        return $this->getArrayWithLimit($sql, 0, 4);
    }

    /**
     * Method to get the number of items a category has
     * @param string $categoryId Category Id
     * @return int Number of Items
     */
    public function getNumCategoryItems($categoryId) {
        return $this->getRecordCount("WHERE categoryid='{$categoryId}'");
    }

    /**
     * Return a single record
     * @param string $id ID
     * @return array
     * @return array The FAQ entrry
     */
    public function listSingle($id) {
        return $this->getRow('id', $id);
    }

    /**
     * Get the next index
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @return int The next index
     */
    public function getNextIndex($contextId, $categoryId) {
        $array = $this->getArray("SELECT MAX(_index) AS _max FROM {$this->_tableName} WHERE contextid='$contextId' AND categoryid='$categoryId'");
        return $array[0]['_max'] + 1;
    }

    /**
     * Update a record
     * @param string $id ID
     * @param string $question The question
     * @param string $answer The answer
     * @param string $userId The user ID
     */
    public function updateSingle($id, $question, $answer, $categoryId, $userId, $dateLastUpdated) {
        $this->update("id", $id,
                array(
                    'question' => $question,
                    'answer' => $answer,
                    'categoryid' => $categoryId,
                    'userid' => $userId,
                    'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
                )
        );

        $this->objDbFaqCategories = & $this->getObject('dbfaqcategories');
        $categoryRow = $this->objDbFaqCategories->getRow('id', $categoryId);

        // Add to Search
        //$objIndexData = $this->getObject('indexdata', 'search');
        // Prep Data
        $docId = 'faq_entry_' . $id;
        $docDate = strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated);
        $url = $this->uri(array('action' => 'view', 'category' => $categoryId), 'faq');
        $title = $question;
        $contents = $question . ': ' . $answer;
        $teaser = $question;
        $module = 'faq';
        $userId = $userId;
        $context = $categoryRow['contextid'];

        // Add to Index
        // $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);
    }

    /**
     * Delete a record
     * @param string $id ID
     */
    public function deleteSingle($id) {
        $this->delete("id", $id);
        //$objIndexData = $this->getObject('indexdata', 'search');
        //$objIndexData->removeIndex('faq_entry_'.$id);
    }

//
    /**
     * Return all records that match given tags
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @return array The FAQ entries
     */

    public function listAllByTag($tag) {

        $sql = 'SELECT tbl_faq_entries.id,
                tbl_faq_entries.contextid,
                tbl_faq_entries.categoryid,
                tbl_faq_entries.question,
                tbl_faq_entries.answer,
                tbl_faq_entries.userid,
                tbl_faq_entries.datelastupdated,
                tbl_faq_entries.updated,
                tbl_faq_entries._index,
                tbl_faq_entries.puid
                FROM tbl_faq_entries, tbl_faq_tags
                WHERE
                tbl_faq_tags.faqid = tbl_faq_entries.id  AND tbl_faq_tags.tag LIKE \'' . $tag . '\'';
        $list = $this->getArray($sql);
        $indexArray = array();
        $count = 0;
        foreach ($list as $num) {
            $temp = array('_index' => $num['_index']);
            $indexArray[] = $temp;
            $count++;
        }
        $listArray = array();
        $index = 1;
        foreach ($list as $element) {
            if ($index > 1) {
                $previndex = $index - 2;
                $prev = $indexArray[$previndex];
                $prevRow = $this->getRow('_index', $prev['_index']);
            } else {
                $prevRow = null;
            }
            if ($index < $count) {
                $next = $indexArray[$index];
                $nextRow = $this->getRow('_index', $next['_index']);
            } else {
                $nextRow = null;
            }
            $newArray = array('id' => $element['id'], 'contextid' => $element['contextid'], 'categoryid' => $element['categoryid'], 'question' => $element['question'], 'answer' => $element['answer'], 'userid' => $element['userid'], 'datelastupdated' => $element['datelastupdated'], 'updated' => $element['updated'], '_index' => $element['_index'], 'puid' => $element['puid'], 'previd' => $prevRow['id'], 'nextid' => $nextRow['id']);
            $listArray[] = $newArray;
            $index++;
        }
        return $listArray;
    }

    /**
     * Return all records previous and next record ids, these ids are used for navigation
     * @param string $contextId The context ID
     * @param string $categoryId The category ID
     * @return array The FAQ entries
     */
    public function listAllWithNav($contextId, $categoryId) {
        if ($categoryId == "All Categories") {
            $list = $this->getAll("WHERE contextid='" . $contextId . "' ORDER BY _index");
        } else {
            $list = $this->getAll("WHERE contextid='" . $contextId . "' AND categoryid='" . $categoryId . "' ORDER BY _index");
        }

        $indexArray = array();
        $count = 0;
        foreach ($list as $num) {
            $temp = array('_index' => $num['_index']);
            $indexArray[] = $temp;
            $count++;
        }

        $listArray = array();
        $index = 1;
        foreach ($list as $element) {
            if ($index > 1) {
                $previndex = $index - 2;
                $prev = $indexArray[$previndex];
                $prevRow = $this->getRow('_index', $prev['_index']);
            } else {
                $prevRow = null;
            }
            if ($index < $count) {
                $next = $indexArray[$index];
                $nextRow = $this->getRow('_index', $next['_index']);
            } else {
                $nextRow = null;
            }

            $newArray = array('id' => $element['id'], 'contextid' => $element['contextid'], 'categoryid' => $element['categoryid'], 'question' => $element['question'], 'answer' => $element['answer'], 'userid' => $element['userid'], 'datelastupdated' => $element['datelastupdated'], 'updated' => $element['updated'], '_index' => $element['_index'], 'puid' => $element['puid'], 'previd' => $prevRow['id'], 'nextid' => $nextRow['id']);

            $listArray[] = $newArray;
            $index++;
        }
        return $listArray;
    }
}
?>