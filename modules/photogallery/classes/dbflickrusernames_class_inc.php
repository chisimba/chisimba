<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* class to control the utilty method for the events calendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package photogallery
* @version 1
*
*
*/

require_once('phpFlickr.php');
class dbflickrusernames extends dbTable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_photogallery_flickr_users');
        $this->_objUser = $this->getObject('user', 'security');
       
        
    }
    
    
    /**
    * Method to add a username and password
    * 
    * @access public
    */
    public function addUsername()
    {
		
		
		$fields = array( 'userid' => $this->_objUser->userId(),
						 'flickr_username' => $this->getParam('username'),
						 'flickr_password' => $this->getParam('password'),
						 'isreal' => 1);
						 
		$this->insert($fields);
	}
	
	/**
	* Method to get the user names for a user
	*@return array
	*/
	public function getUsernames()
	{
		return $this->getAll("WHERE userid = '".$this->_objUser->userId()."'");
		
	}
	
	/**
	* Method to get all the albums from flickr
	*
	* @return array
	*/
	public function getFlickrSharedAlbums()
	{
	 	$this->_objFlickr = new phpFlickr("0b4628c77c757049831c6d873107e533", "e71b890ec35750fb");
		$bigSet = array();
		$users = $this->getAll();
		foreach($users as $user)
		{
			$user['sets'] = array();
			$usr = $this->_objFlickr->people_findByUsername($user['flickr_username']);
			$sets = $this->_objFlickr->photosets_getList($usr['id']);		
			array_push($user['sets'], $sets);
			$bigSet[]=$user;
		}
		
		return $bigSet;
	}
	
	/**
	* Method to delete
	* a flickr username
	* 
	* @param string $username
	* @return boolean
	*/
	public function deleteUsername($username)
	{
	
		return $this->delete('flickr_username',$username);
	}
	
	/**
	* Method to check if a username exists in the database
	*
	* @return boolean
	* @param string $username The flickr username
	*/
	public function usernameExist($username)
	{
		return $this->valueExists('flickr_username', $username);
	}
}