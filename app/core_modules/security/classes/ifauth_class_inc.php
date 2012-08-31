<?php
/* -------------------- IFAUTH INTERFACE CLASS ----------------*/

/**
* 
* Interface class defining methods that must be present in an authentication
* plugin class that implements this interface.
*
* @author Derek Keats
* @category Chisimba
* @package security
* @copyright AVOIR
* @licence GNU/GPL
*
*/
interface ifauth
{
    public function authenticate($username, $password);
    public function getUserDataAsArray($username);
}
?>