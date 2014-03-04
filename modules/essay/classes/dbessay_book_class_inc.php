<?php
/**
* Class dbessay_book extends dbTable
* @package essay
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
// end security check

/**
* Class to access database table tbl_essay_book
* Saves studentid and essayid of the essay booked by the student
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package essay
* @version 0.9
*/

class dbessay_book extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_essay_book');
        $this->table = 'tbl_essay_book';
        $this->topicTable = 'tbl_essay_topics';
    }

    /**
    * Method to book an essay by inserting booking information into the database.
    * @param array $fields The table fields and values to be inserted in the database
    * @param string $id The id of the table row to be updated
    * @return
    */
    public function bookEssay($fields,$id=NULL)
    {
        if(!empty($id)){
            $this->update('id',$id,$fields);
        }else{
            $this->insert($fields);
        }
    }

    /**
    * Method to retrieve the booking information for one or more essays.
    * @param string $filter Default = NULL
    * @param string $fields Default = NULL
    * @return array $rows The information obtained from the table
    */
    public function getBooking($filter=NULL,$fields=NULL)
    {
        if(!$fields){
            $fields='*';
        }
        $sql='select '.$fields.' from ' .$this->table;
        if($filter){
            $sql.=' '.$filter;
        }
        //trigger_error($sql);
        $rows=$this->getArray($sql);
        return $rows;
    }

    /**
    * Method to delete a booking from the database table.
    * @param string $id The id of the booking to be deleted
    * @param string $filter Allows deletion of multiple records using an alternate field
    * @return
    */
    public function deleteBooking($id=NULL,$filter=NULL)
    {
        if($filter){
            $sql='select id from ' .$this->table .' '.$filter;
            $rows=$this->getArray($sql);
            if($rows){
                foreach($rows as $val){
                    $this->delete('id',$val['id']);
                }
            }
        }else{
            $this->delete('id',$id);
        }
    }

    /**
    * Method to get a list of topics is the context.
    * Each topic shows number of submissions and number marked.
    * @param string $context The current context
    */
    public function getContextSubmissions($context)
    {
        $sql = 'SELECT topic.id, topic.name, topic.closing_date, book.submitDate, book.mark ';
        $sql .= 'FROM '.$this->table.' AS book ';
        $sql .= 'LEFT JOIN '.$this->topicTable.' as topic ON topic.id = book.topicId ';
        $sql .= "WHERE topic.context = '$context' ORDER BY topic.id, topic.closing_date";

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * added by otim samuel, sotim@dicts.mak.ac.ug: 13th Jan 2006
    * for specific use within the gradebook module
    * Method to get all student grades from essays
    * as a percentage of the total year's mark
    * @param string $filter
    * @param string $fields The required fields. Default = * (all);
    * @param string $tables The tables to be queried
    * @return array $data The result.
    */
    public function getGrades($filter, $fields='*', $tables='tbl_essay_book')
    {
        $sql = "SELECT $fields FROM ".$tables;
        $sql .= " WHERE $filter";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>