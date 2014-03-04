<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Interface class defining methods that must be present in a code generator
* class that implement this interface.
*
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
interface ifgenerator
{
    public function generate($className=NULL);
}
?>