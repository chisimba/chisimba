<?php
/**
* Class dbessays extends dbTable
* @package essay
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
// end security check

/**
* Class to access database table tbl_essays containing the details for an essay
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package essay
* @version 0.9
*/

class dbessays extends dbTable
{
    /**
    * Constructor method to define the table.
    */
    public function init()
    {
        parent::init('tbl_essays');
        //parent::init('tbl_essay_topics');
        $this->table='tbl_essays';
    }

    /**
    * Method to insert essay information into database.
    * @param array $fields The table fields and values to be inserted in the database
    * @param string $id The id of the table row to be updated if already exists. Default=NULL
    * @return
    */
    public function addEssay($fields,$id=NULL)
    {

        if(!empty($id)){
            $this->update('id',$id,$fields);
        }else{
            $this->insert($fields);
        }
    }

    /**
    * Method to retrieve essay information.
    * @param string $id The id of the essay required. Default=NULL
    * @param string $fields The required table fields. Default=*
    * @return array $rows The essay details
    */
    public function getEssay($id=NULL,$fields=NULL)
    {
        if(!$fields){
            $fields='*';
        }
        $sql='select '.$fields.' from ' .$this->table;
        if($id){
            $sql.=" where id='$id'";
        }
        $rows=$this->getArray($sql);
        return $rows;
    }

   /**
    * Method added to retrieve all essay topics.
    * If a topic id is specified, retrieve the essays in the specified topic.
    * @author Nonhlanhla Gangeni <noegang@gmail.com>
    * @param string $id The essay topic id. Default=NULL
    * @return array $rows The list of essays
    */
    public function getEssayTopics($id=NULL)
    {

        $sql='select e.id as id,et.name as name,e.topic as topic from tbl_essay_topics et,tbl_essays e ';
        if($id){
            $sql.=" where et.context='$id' and e.topicid=et.id";
        }else{
         $sql.=" where e.topicid=et.id";
        }
        $rows=$this->getArray($sql);
        return $rows;
    }

    /**
    * Method to retrieve all essays.
    * If a topic id is specified, retrieve the essays in the specified topic.
    * @param string $id The essay topic id. Default=NULL
    * @return array $rows The list of essays
    */
    public function getEssays($id=NULL)
    {

        $sql='select id,topic,notes from ' .$this->table;
        if($id){
            $sql.=" where topicid='$id'";
        }

        $rows=$this->getArray($sql);
        return $rows;
    }

    /**
    * Method to delete an essay from the table.
    * @param string $id The id of the essay to be deleted
    * @return
    */
    public function deleteEssay($id)
    {
        $this->delete('id',$id);
    }
}
?>
