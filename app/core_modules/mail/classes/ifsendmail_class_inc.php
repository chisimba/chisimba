<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Abstract class defining methods and properties that must be present
* in mail sending classes that implement it.
*
* @author    Derek Keats
* @category  Chisimba
* @package   mail
* @copyright AVOIR
* @licence   GNU/GPL
*            
*/
interface ifsendmail
{

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @access public
     */
    function send();

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $file Parameter description (if any) ...
     * @access public 
     */
    function attach($file);

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @access public
     */
    function clearAttachments();
}
?>