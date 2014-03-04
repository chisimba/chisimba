<?php



/*
 * Responsible for insterting, updating and deleting events table
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
class dbcomments extends dbTable{

    public function init()
    {
        parent::init('tbl_simpleregistrationcomments');  //super
        $this->table = 'tbl_simpleregistrationcomments';
        $this->objUser = $this->getObject ( 'user', 'security' );

    }

/**
 * adds an event
 * @param <type> $eventtitle
 * @param <type> $eventdate
 * @return <type>
 */
    public function addComment(
            $comments
    ){
        $data = array(
            'comments' => $comments,
            'comment_date' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            'userid' => $this->objUser->userid()
        );
        return $this->insert($data);

    }

 public function deleteEventComments($id)
    {
        if ($id != '') {
            return $this->delete('event_id', $id);
        }
    }
  

    /**
     * selects events created by me
     * @return <type>
     */
    public function getComments(){
        $sql=
        "select * from ".$this->table."";
        $rows=$this->getArray($sql);
        return $rows;
    }
}
?>
