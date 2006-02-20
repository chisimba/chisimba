<?php
/**
* This class provides a set of methods and properties related to 
* assigning and using permissions on KEWL.NextGen. All permissions
* should make use of this class.
*/

class permission {

	/**
	* Method do unpack a permission string into an array to
	* be used in allocating permissions
	* @param string $permissionString: a string of permissions 
	* in the form ....TBD
	*/
	function unpack($permissionString) {
		$permArray=explode(",", $permissionString);
		return $permArray;
	}

}
?>