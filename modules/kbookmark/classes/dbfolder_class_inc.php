<?php
/**
 * Abstract base class bookmark folders.
 * @author James Kariuki Njenga, Qhamani Fenama
 * @version $Id: dbgroup_class_inc.php 6231 2007-04-26 15:42:08Z dkeats $
 * @copyright 2005, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
*/

class dbfolder extends dbTable
{

    /**
    * Initialise objects used in the module
    */

    function init()
    {
        parent::init('tbl_bookmarks_folders');
    }
    
    /**
    * Function to return the name of a group or folder
    *
    * @param folderId string
    * return name
    */
    
    function folderByName($folderId)
    {
        return $this->getRow('id',$folderId);
    }


    /**
    * Function to return the id of a group or folder
    *
    * @param foldername string, parentid string, userid string
    * return boolean
    */
    function isFolderExist($fname, $parentid, $userid)
	{
		$res = TRUE;
		$sql = "SELECT * FROM tbl_bookmarks_folders WHERE fname = '{$fname}' AND parentid = '{$parentid}' AND userid = 			'{$userid}'";
		$sqlres = $this->getArray($sql);
		error_log(var_export(count($sqlres), true));
		if(count($sqlres) == 0)
		{
			$res = FALSE;
		}
		return $res;
	}
    
    /**
    * Method to insert a single record in the database table
	* @param foldername
	* @param folderparentid
	* @param $userid
    *
	* return id string
    */
    function insertSingle($foldername, $folderparentid, $userid)
    {
        $arrayOfRecords =  array(
            'userid'    => $userid,
            'fname'	    => $foldername,
            'parentid' 	=> $folderparentid
             );
    	return $this->insert($arrayOfRecords, 'tbl_bookmarks_folders');
    }
    

	/**
    * Method to get the user's folder
	* @param userid
	* @param parentid
    *
	* return array
    */
	function getUserFolders($userid, $parentid)
	{
		$sql = "SELECT * FROM tbl_bookmarks_folders WHERE userid = '{$userid}' AND parentid = '{$parentid}'";
		return $this->getArray($sql);
	}

	/**
    * Method to delete the folder a user with any field
    * @param id
	* @param field default set to 'id'
    *
	*/
	function deleteFolder($id, $field = 'id')
	{
		return $this->delete($field, $id, 'tbl_bookmarks_folders');
	}
   
    /**
    * Function to get the default folder for display
    * checks if the default folder is created, if not creates it
    *
    * returns the folder Id
    */
    function getDefaultId($userId)
    {
        $list = $this->getRow('isdefault','1','creatorid',$userId);
        if ($list['id']==Null) {
            $this->createDefaultFolder($userId);
            $this->getDefaultId($userId);
        }
        return $list['id'];
    }
    
    /**
    * Method to unset the default folder
    *
    *
    */
    function resetDefault()
    {
        return $this->update('isdefault','1',array('isdefault'=>'0'));
    }
    /**
    * Function to set the default folder for display
    *
    */
    
    function setDefault($folderId)
    {
        //unset the default folder
        $this->resetDefault();
        //set the new one
        //return $this->query("update tbl_bookmarks_groups set isdefault=1 where id='$folderId'");
        return $this->update('id', $folderId, array('isdefault'=>'1'));
    }
	
	/**
	* function to get all the shared folders that have bookmarks
	*
	* returns array
	*/
	function getSharedWithBookmarks()
	{
	    $sql="SELECT DISTINCT(tbl_bookmarks_groups.id), tbl_bookmarks_groups.creatorid, tbl_bookmarks_groups.isprivate, tbl_bookmarks_groups.title 
	    FROM tbl_bookmarks_groups 
	    LEFT JOIN tbl_bookmarks ON tbl_bookmarks_groups.id=tbl_bookmarks.groupid 
	    WHERE (tbl_bookmarks_groups.isprivate='0')";
		return $this->getArray($sql);
	}
    
	/**
	* function to get all the users who have shared folders that have bookmarks
	*
	* returns array
	*/
	function getUsersWithSharedBookmarks()
	{
	    $sql="SELECT DISTINCT(tbl_bookmarks_groups.creatorid) FROM tbl_bookmarks_groups 
	    LEFT JOIN tbl_bookmarks ON tbl_bookmarks_groups.id=tbl_bookmarks.groupid 
	    WHERE (tbl_bookmarks_groups.isprivate='0')";
		return $this->getArray($sql);
	}
    
	/**
	* function to get all the users who have shared folders that have bookmarks
	*
	* returns array
	*/
	function getShared4User($userId)
	{
	    $sql="SELECT DISTINCT(tbl_bookmarks_groups.id), tbl_bookmarks_groups.creatorid, tbl_bookmarks_groups.isprivate, tbl_bookmarks_groups.title 
	    FROM tbl_bookmarks_groups 
	    LEFT JOIN tbl_bookmarks ON tbl_bookmarks_groups.id=tbl_bookmarks.groupid 
	    WHERE (tbl_bookmarks_groups.isprivate='0' AND tbl_bookmarks_groups.creatorid='$userId')";
		return $this->getArray($sql);
	}

}
?>
