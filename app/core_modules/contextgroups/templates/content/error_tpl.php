<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage template
* @version 0.1
* @since 15 February 2005
* @author Jonathan Abrahams
* @filesource
*/
?>
<?php
// Page headers and layout template
$this->setLayoutTemplate('contextgroups_layout_tpl.php');
?>
<SPAN id='confirm'>
    <?php echo $errMessage; ?>
</SPAN>
