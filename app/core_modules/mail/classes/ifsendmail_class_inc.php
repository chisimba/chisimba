<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Abstract class defining methods and properties that must be present
* in mail sending classes that implement it.
*
* @author Derek Keats
* @category Chisimba
* @package mail
* @copyright AVOIR
* @licence GNU/GPL
*
*/
interface ifsendmail
{
    function send();
    function attach($file);
    function clearAttachments();
}
?>