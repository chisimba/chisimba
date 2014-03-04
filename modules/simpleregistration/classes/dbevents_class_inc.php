<?php

/*
 * Responsible for insterting, updating and deleting events table
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}

class dbevents extends dbTable {

    public function init() {
        parent::init('tbl_simpleregistrationevents');  //super
        $this->table = 'tbl_simpleregistrationevents';
        $this->objUser = $this->getObject('user', 'security');
    }


    /**
     * adds an event
     * @param <type> $eventtitle
     * @param <type> $eventdate
     * @return <type>
     */
    public function addEvent(
    $eventtitle, $shortname, $maxNumberOfPeople, $eventdate
    ) {
        $data = array(
            'event_title' => $eventtitle,
            'short_name' => $shortname,
	    'max_people' => $maxNumberOfPeople,
            'event_date' => $eventdate, 
            'userid' => $this->objUser->userid()
        );
        return $this->insert($data);
    }

    /**
     *
     * Method to update event
     *
     * @access public
     * @param string $eventid The event ID
     * @param string $eventtitle The name of the event
     * @param string $shortname Event's short name
     * @param string $maxNumberOfPeople  Maximum number of registrations allowed for the event
     * @param string $eventdate Event's date
     * @return string Event ID
     *
     */
    public function updateEvent(
    $eventid,$eventtitle, $shortname, $maxNumberOfPeople, $eventdate
    ) {
        $data = array(
            'event_title' => $eventtitle,
            'short_name' => $shortname,
	    'max_people' => $maxNumberOfPeople,
            'event_date' => $eventdate, 
            'userid' => $this->objUser->userid()
        );

	 return $this->update('id', $eventid, $data);
    }

    public function getEventIdByShortname($shortname) {
        $sql =
                "select id from " . $this->table . " where short_name = '" . $shortname . "'";
        $rows = $this->getArray($sql);
        return $rows;
    }

     /**
     *get the event by eventid
     * @param <type> $eventid
     * @return <type>
     */
    public function getEvent($id){
        $row=$this->getRow('id', $id);
        return $row;
    }

    /**
     *
     * Method to get maximum number of registrations allowed for the event
     *
     * @access public
     * @param string $id The event ID
     * @return interger maximum number of registrations allowed for the event
     *
     */
    public function getMaxRegistrations($id){
          $sql =
                "select max_people from " . $this->table . " where id = '" . $id . "'";
	$row=$this->getArray($sql);
        return $row[0]['max_people'];
    }


 public function deleteEvent($id)
    {
        if ($id != '') {
            return $this->delete('id', $id);
        }
    }

    /**
     * selects events created by me
     * @return <type>
     */
    public function getMyEvents() {
        $sql =
                "select * from " . $this->table . " where userid = '" . $this->objUser->userid() . "'";

        if ($this->objUser->isAdmin()) {
            $sql =
                    "select * from " . $this->table;
        }
        $rows = $this->getArray($sql);
        return $rows;
    }

}

?>
