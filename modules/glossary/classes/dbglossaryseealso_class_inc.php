<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Glossary Terms Table
* This class controls all functionality relating to the tbl_glossary table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package glossary
* @version 1
*/
/**
* See Alsos is a means to relate two terms in a glossary to each other.
* Whenever one of the terms is being showed, it will have a link that says: See Also: {other term}
*/
class dbGlossarySeeAlso extends dbTable
{

    /**
    * Constructor method to define the table(default)
    */
    public function init()
    {
        parent::init('bridge_glossary_seealso');

        $this->objGlossary = $this->getObject('dbglossary');
    }

    /**
    * Method to fetch all linked (Se Also) terms in a context.
    *
    * If no parameter is specified, returns terms for all contexts.
    *
    * This function returns the record in the bridge table, and also inner joins tbl_glossary
    * to give the id and term of the records
    *
    * Since the term can be in either item_id or item_id2, it checks for both
    *
    * @param string $item: Id of the item.
    * @param string $context: ContextCode to filter records
    * @return array consisting of the Two terms, their specific record IDs, and record ID of the join between them
    */
    public function fetchAllRecords($item, $context=FALSE)
    {
        $sql = 'SELECT bridge_glossary_seealso.* , tbl1.id AS item1, tbl2.id AS item2, tbl1.term AS term1, tbl2.term AS term2 ';
        $sql.= 'FROM bridge_glossary_seealso ';
        $sql.= 'INNER JOIN tbl_glossary AS tbl1 ON bridge_glossary_seealso.item_id = tbl1.id ';
        $sql.= 'INNER JOIN tbl_glossary AS tbl2 ON bridge_glossary_seealso.item_id2 = tbl2.id ';
        $sql.= "WHERE (bridge_glossary_seealso.item_id ='".$item."'";
        $sql.= " OR bridge_glossary_seealso.item_id2 = '".$item."') " ;

        if ($context !== FALSE) {
            $sql.="AND tbl1.context='".$context."' AND tbl2.context='".$context."'";
        }

        return $this->getArray($sql);
    }

    /**
    * Method to fetch finds all the other terms that a particular term is not linked to within a context (optional)
    * This method is called to list the the other terms when editing
    *
    * @param string $item: ID of the term
    * @param string $context: ContextCode to filter records
    *
    * @return array Matching Terms, and their recordIds
    */
    public function findNotLinkedTo($item, $context=FALSE)
    {
        $sql = 'SELECT distinct tbl_glossary.id, tbl_glossary.term ';
        $sql.= 'FROM (tbl_glossary LEFT JOIN bridge_glossary_seealso ON ';
        $sql.= '(tbl_glossary.id = bridge_glossary_seealso.item_id OR ';
        $sql.= 'tbl_glossary.id = bridge_glossary_seealso.item_id2) ';
        $sql.= "AND (bridge_glossary_seealso.item_id='".$item."'";
        $sql.= " OR bridge_glossary_seealso.item_id2='".$item."')) ";
        $sql.= "WHERE (bridge_glossary_seealso.id IS NULL AND tbl_glossary.id != '".$item."')";

        if ($context !== FALSE) {
            $sql .= " AND (tbl_glossary.context = '{$context}')";
        }

        $sql.= ' ORDER BY tbl_glossary.term';

        return $this->getArray($sql);
    }
    /**
    * This method determines the number of records the term is not linked to.
    * Used to indicate messages (e.g. Linked to all terms)
    *
    * @param string $item: ID of the term
    * @param string $context Context
    * @return int of number of terms not linked
    */
    public function findNotLinkedToNum($item, $context=FALSE)
    {
        $sql = 'SELECT COUNT(*) AS cnt '; //rc
        $sql.= 'FROM (tbl_glossary LEFT JOIN bridge_glossary_seealso ON ';
        $sql.= '(tbl_glossary.id = bridge_glossary_seealso.item_id OR ';
        $sql.= 'tbl_glossary.id = bridge_glossary_seealso.item_id2) ';
        $sql.= "AND (bridge_glossary_seealso.item_id='".$item."'";
        $sql.= " OR bridge_glossary_seealso.item_id2='".$item."')) ";
        $sql.= "WHERE (bridge_glossary_seealso.id IS NULL AND tbl_glossary.id != '".$item."')";

        if ($context !== FALSE) {
            $sql .= " AND (tbl_glossary.context = '{$context}')";
        }

        $rs = $this->query($sql);
        if (is_array($rs) && isset($rs[0]) && isset($rs[0]['cnt'])) {
            return (int)$rs[0]['cnt'];
        } else {
            return FALSE;
        }
        //$line = $rs[0]['rc'];//$rs->fetchRow();
        //return $line;//['rc'];
    }
    /**
    * This method determines the number of records the term IS linked to.
    * Used to indicate messages (e.g. Linked to all terms)
    *
    * @param string $item: ID of the term
    * @param string $context: ContextCode to filter records
    * @return int FALSE|number of terms linked to
    */
    public function getNumRecords($item, $context=FALSE)
    {
        $sql = 'SELECT COUNT(*) AS cnt '; //bridge_glossary_seealso.* , tbl1.id AS item1, tbl2.id AS item2, tbl1.term AS term1, tbl2.term AS term2
        $sql.= 'FROM bridge_glossary_seealso ';
        $sql.= 'INNER JOIN tbl_glossary AS tbl1 ON bridge_glossary_seealso.item_id = tbl1.id ';
        $sql.= 'INNER JOIN tbl_glossary AS tbl2 ON bridge_glossary_seealso.item_id2 = tbl2.id ';
        $sql.= "WHERE (bridge_glossary_seealso.item_id ='".$item."'";
        $sql.= " OR bridge_glossary_seealso.item_id2 = '".$item."') " ;
        if ($context !== FALSE) {
            $sql.="AND tbl1.context='".$context."' AND tbl2.context='".$context."'";
        }

        $rs = $this->getArray($sql);
        if (is_array($rs) && isset($rs[0]) && isset($rs[0]['cnt'])) {
            return (int)$rs[0]['cnt'];
        } else {
            return FALSE;
        }
        //return $this->getRecordCount(" WHERE item_id='".$item."' or item_id2 = '".$item."'");
    }

    /**
    * Method to insert a new record into the table
    *
    * @param string $item_id: Id of the first term
    * @param string $item_id2: Id of the second term
    * @param string $userId: Person making the change
    * @param datetime $dateLastUpdated: Date / Time of the Update
    */
    public function insertSingle($item_id, $item_id2, $userId, $dateLastUpdated)
    {
        $this->insert(array(
        'item_id'         => $item_id,
        'item_id2'        => $item_id2,
        'userid'          => $userId,
        'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
        ));

        return;
    }


    /**
    * Method to delete a link (see Also) between two terms
    *
    * @param string $id: Id of the record
    */
    public function deleteSingle($id)
    {
        $this->delete('item_id2', $id);
        return;
    }

    public function deleteSingleLink($id)
    {
        $this->delete('id', $id);
        return;
    }

}  #end of class

?>