<?php

/* ----------- data class extends dbTable for tbl_podcaster_events------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_podcaster_events
 * @author Paul Mungai
 * @copyright 2011 University of the Witwatersrand
 */
class dbPodcaster_Events extends dbTable {

    /**
     * Constructor method to define the table
     */
    function init() {
        parent::init('tbl_podcaster_events');
        $this->objUser = &$this->getObject('user', 'security');
    }

    /**
     * Return all records with a certain access level
     * @param string $access The Access level to search for (public|open|private)
     * @return array The events
     */
    function listByAccess($access) {
        return $this->getAll("WHERE access='" . $access . "'");
    }
    /**
     * Return all records with a certain access level
     * @param string $access The Access level to search for (public|open|private)
     * @param string $publish_status The Publish Status to search for (published|unpublished)
     * @return array The events
     */
    function listByAccessPublishStatus($access, $publish_status='published') {
        return $this->getAll("WHERE access='" . $access . "' AND publish_status='" . $publish_status . "'");
    }

    /**
     * Return a single record
     * @param string $id ID
     * @return array The values
     */
    function listById($id) {
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /**
     * Return a single record as per the specified event
     * @param string $eventId The Event ID
     * @return array The values
     */
    function listByEvent($eventId) {
        return $this->getAll("WHERE eventid='" . $eventId . "'");
    }

    /**
     * Return a single record based on issued publish status
     * @param string $publish Publish state
     * @return array The values
     */
    function listByPublishStatus($publish) {
        return $this->getAll("WHERE publish_status='" . $publish . "'");
    }

    /**
     * Return a single record as per the specified category
     * @param string $categoryId The Category ID
     * @return array The values
     */
    function listByCategory($categoryId) {
        return $this->getAll("WHERE categoryid='" . $categoryId . "'");
    }

    /**
     * Insert a record
     * @param string $eventId The event Id
     * @param string $categoryId The category Id
     * @param string $access The Access level(public|open|private)
     * @param string $publish_status The Publish state(published|unpublished)
     */
    function insertSingle($eventId, $categoryId, $access, $publish_status) {
        $id = $this->insert(array(
                    'eventid' => $eventId,
                    'categoryid' => $categoryId,
                    'access' => $access,
                    'publish_status' => $publish_status
                ));
        return $id;
    }

    /**
     * Update a record
     * @param string $eventId The event Id
     * @param string $categoryId The category Id
     * @param string $access The Access level(public|open|private)
     * @param string $publish_status The Publish state(published|unpublished)
     */
    function updateSingle($eventId, $categoryId, $access, $publish_status) {
        $userid = $this->objUser->userId();
        $checkIfExists = $this->listByEvent($eventId);
        if (empty($checkIfExists)) {
            $this->insertSingle($eventId, $categoryId, $access, $publish_status);
        } else {
            $this->update("eventid", $eventId, array(
                'categoryid' => $categoryId,
                'access' => $access,
                'publish_status' => $publish_status
            ));
        }
    }

    /**
     * Delete a record
     * @param string $id ID
     */
    function deleteSingle($id) {
        $this->delete("id", $id);
    }

}

?>