<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

//Preventing Notices
if (!isset($stats)) {
    $stats = '';
}

echo $stats;

?>
