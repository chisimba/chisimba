<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * This class logs every user activity in the system, but limits to only recording
 * userid, module and action as the most important things
 *
 * @author davidwaf
 */
class useractivity extends dbtable {

    function init() {
        parent::init('tbl_useractivity');
    }

    /**
     * log the activity
     * @param <type> $fields
     */
    function log($fields) {
        $this->insert($fields);
    }

}

?>
