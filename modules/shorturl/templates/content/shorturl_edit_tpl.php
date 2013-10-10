<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


//For debuging shorturl edit results

log_debug(var_export($_POST, true));
log_debug(var_export($_REQUEST, true));
log_debug(var_export($_GET, true));

?> 
