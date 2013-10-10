<?php
/**
* Class dbessay_topics extends dbtable
*
* @package essay
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
// end security check

/**
* Class to access database table tbl_essay_topics containing topic information.
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package essay
* @version 0.9
*/

class dbessay_topics extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_essay_topics');
        $this->table='tbl_essay_topics';
    }

    /**
    * Insert topic information into the database, or update topic if an id is provided.
    * @param array $fields The table fields and values to be inserted in the database
    * @param string $id The id of the topic to be updated, default=NULL
    * @return
    */
    public function addTopic($fields,$id=NULL)
    {
        $fields['last_modified'] = date('Y-m-d H:i:s', time());
        if(!empty($id)){
            $this->update('id',$id,$fields);
            return $id;
        }else{
            $id = $this->insert($fields);
            return $id;
        }
    }

    /**
    * Method to retrieve topic information from database.
    * @param string $id The id of the topic required. Default=NULL
    * @param string $fields The table fields to be returned. Default=NULL
    * @param string $filter Default=NULL
    * @return array Topic info
    */
    public function getTopic($id=NULL, $fields=NULL, $filter=NULL)
    {
        if(!$fields){
            $fields = '*';
        }
        $sql = 'select '.$fields.' from ' .$this->table;
        if($id){
            $sql .= " where id='$id'";
        }else if($filter){
                $sql .= ' where '.$filter;
        }
        $sql .= ' ORDER BY closing_date';
        //echo "[$sql]";
        $rows = $this->getArray($sql);

        if(!empty($rows)){
            return $rows;
        }
        return FALSE;
    }

    /**
    * Delete a topic from the table.
    * @param string $id The id of the topic to be deleted
    * @return
    */
    public function deleteTopic($id)
    {
        $this->delete('id',$id);
    }
}
?>