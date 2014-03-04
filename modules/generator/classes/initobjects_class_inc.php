<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* Class to return the init strings for common objects that can be instantiated
* in the init method of a class being generated
* 
* Usaeage: $iO = this->getObject('initobjects');
*          $iO->objectList=array('$objUser', '$objConfig');
*          echo $this->iO->show();
*
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class initobjects
{
	/**
	 * Method to generate the class for the controller
	 */
	function show($arObjs=array())
	{
		$ret = "";
	    foreach (arObjs as $obj) {
	        $ret .= $this->formatObject($obj);
	    }
	    return $ret;
	}
	
	/**
	 * 
	 * Method to get the user
	 * 
	 */
	 function formatObject($obj)
	 {
	     return "    /**\n\    *\n    * @public string object \$" . $obj 
	       . " to hold an instance of the $obj \n*\n*/\n"
	       . "    public \$$obj\n\n";

	 }
}
?>