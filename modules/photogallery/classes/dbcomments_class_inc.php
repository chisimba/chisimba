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
* @package dbcomments
* @version 1
*
*
*/
class dbcomments extends dbTable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_photogallery_comments');
        $this->_objUser = $this->getObject('user', 'security');
        
    }
    
    /**
     *Method to create an album
     *@param string $albumTitle
     *@return string $id
     *@access public
     */
     public function addComment()
     {
        
        
        $fields = array('email' => $this->getParam('email'),
                        'file_id' => $this->getParam('imageid'),
                        'user_id' => $this->_objUser->userId(),
                        'name' => $this->getParam('name'),
                        'website' => $this->getParam('website'),
                        'commentdate' => date("F jS, Y,g:i A"),
                        'comment' => $this->getParam('comment'),
        
        );
        //var_dump($fields); 
        return $this->insert($fields);
        
        
     
     }  
     
     /**
     * Method to update an album
     * @param array $fields
     * @param string $id
     * @access public
     */
     public function saveEdit()
     {
      $fields = array('email' => $this->getParam('email'),                        
                        'name' => $this->getParam('name'),
                        'website' => $this->getParam('website'),
                        'commentdate' => $this->getParam('commentdate'),
                        'comment' => $this->getParam('comment'),
        
        );
		return $this->update('id', $this->getParam('commentid'), $fields);
	 }
	
	
     /**
      *Method to get a list of albums for a user
      *@param
      *@access public
      */
      public function getImageComments($imageId)
      {
          return $this->getAll("WHERE file_id='".$imageId."' ORDER BY id ASC");
      	
      }         
	  
	  
	  /**
	  * Method to get the list of commnet for the
	  * user images
	  * @return array
	  * @access public
	  */
	  public function getUserComments()
	  {
	   		return $this->getAll("WHERE user_id='".$this->_objUser->userId()."'");
	   
		
	  }
	
	  /**
	  * Method to get the list of commnet for the
	  * user images
	  * @return array
	  * @access public
	  */  
	  public function getTenRecentComments()
	  {
		
	  		return $this->getAll("WHERE user_id='".$this->_objUser->userId()."'  ORDER BY id DESC LIMIT 10");
	   
		
	  }
	                         
     
}
?>
