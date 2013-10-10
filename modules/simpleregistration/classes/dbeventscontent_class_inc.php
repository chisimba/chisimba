<?php



/*
 * Responsible for insterting, updating and deleting events content table
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die('You cannot view this page directly');
}
class dbeventscontent extends dbTable{

    public function init()
    {
        parent::init('tbl_simpleregistrationcontent');  //super
        $this->table = 'tbl_simpleregistrationcontent';
        $this->objUser = $this->getObject ( 'user', 'security' );

    }


/**
 *stores the event details
 * @param <type> $eventid
 * @param <type> $eventtitle
 * @param <type> $venue
 * @param <type> $content
 * @param <type> $leftitle1
 * @param <type> $leftitle2
 * @param <type> $footer
 * @return <type>
 */
    public function addEventContent(
        $eventid,
        $venue,
        $content,
        $leftitle1,
        $leftitle2,
        $footer,
        $emailcontact,
        $emailsubject,
        $emailname,
        $emailcontent,
        $emailattachments,
        $staffreg,
        $visitorreg
    ){

        $data = array(
            'event_id'=>$eventid,
            'event_timevenue' => $venue,
            'event_content' => $content,
            'event_lefttitle1'=>$leftitle1,
            'event_lefttitle2'=>$leftitle2,
            'event_footer'=>$footer,
            'event_emailcontact'=>$emailcontact,
            'event_emailsubject'=>$emailsubject,
            'event_emailname'=>$emailname,
            'event_emailcontent'=>$emailcontent,
            'event_emailattachments'=>$emailattachments,
            'event_staffreg'=>$staffreg,
            'event_visitorreg'=>$visitorreg
        );
       
        return $this->insert($data);

    }

     /**
     *
     * Method to event content
     *
     * @access public
     * @param string $eventid The event ID
     * @param string $venue Important instructions pertaining to the event, eg, venue, etc.
     * @param string $content Information about what the event is all about
     * @param string $leftitle1 
     * @param string $leftitle2
     * @param string $footer
     * @param string $emailcontact Event's email contact
     * @param string $emailsubject
     * @param string $emailcontent
     * @param string $emailattachments
     * @param boolean $staffreg Whether to allow staff registration
     * @param boolean $visitorreg Whether to allow visitor registration
     * @return string Event ID
     *
     */

    public function updateEventContent(
        $eventid,
        $venue,
        $content,
        $leftitle1,
        $leftitle2,
        $footer,
        $emailcontact,
        $emailsubject,
        $emailname,
        $emailcontent,
        $emailattachments,
        $staffreg,
        $visitorreg
    ){

        $data = array(
            'event_timevenue' => $venue,
            'event_content' => $content,
            'event_lefttitle1'=>$leftitle1,
            'event_lefttitle2'=>$leftitle2,
            'event_footer'=>$footer,
            'event_emailcontact'=>$emailcontact,
            'event_emailsubject'=>$emailsubject,
            'event_emailname'=>$emailname,
            'event_emailcontent'=>$emailcontent,
            'event_emailattachments'=>$emailattachments,
            'event_staffreg'=>$staffreg,
            'event_visitorreg'=>$visitorreg
        );
        return $this->update('event_id',$eventid, $data);

    }
    /**
     *get the content
     * @param <type> $eventid
     * @return <type>
     */
    public function getEventContent($eventid){
        $row=$this->getRow('event_id', $eventid);
        return $row;
    }

    public function deleteEventContent($id)
    {
        if ($id != '') {
            return $this->delete('event_id', $id);
        }
    }
}
?>
