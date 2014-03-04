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
class dbalbum extends dbTable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_photogallery_albums');
        $this->_objUser = $this->getObject('user', 'security');
        
    }
    
    /**
     *Method to create an album
     *@param string $albumTitle
     *@return string $id
     *@access public
     */
     public function createAlbum()
     {
        if (!isset($contextCode)) {
	    $contextCode = '';	    
	}
        $albumTitle = $this->getParam('albumtitle');
        if($albumTitle == '')
        {
			return FALSE;
		}
        $isShared = $this->getParam('isshared');
        
        $fields = array('title' => $albumTitle,
                        'user_id' => $this->_objUser->userId(),
                        'no_pics' => 0,
                        'no_views' => 0,
                        'is_shared' => $isShared,
                        'contextcode' => $contextCode,
        
        );
        return $this->insert($fields);
        
        
     
     }  
     
     /**
     * Method to update an album
     * @param array $fields
     * @param string $id
     * @access public
     */
     public function updateAlbum($id, $fields)
     {
		return $this->update('id', $id, $fields);
	 }
	
	
     /**
      *Method to get a list of albums for a user
      *@param
      *@access public
      */
      public function getUserAlbums()
      {
          return $this->getAll("WHERE user_id='".$this->_objUser->userId()."' ORDER BY position");
      
      }  
	  
	  /**
      *Method to get a list of albums that are shared
      *@param
      *@access public
      */
      public function getSharedAlbums()
      {
          return $this->getAll("WHERE is_shared=1 ORDER BY position");
      
      }       
	  
	  /**
	  * Method to get the Album title
	  * @param string $fileId
	  * @access public
	  */
	  public function getAlbumTitle($albumId)
	  {
		$title = $this->getRow('id',$albumId);
		return $title['title'];
	  }   
	  
	  /**
	  * Method to get the Album title
	  * @param string $fileId
	  * @access public
	  */
	  public function getAlbumDescription($albumId)
	  {
		$title = $this->getRow('id',$albumId);
		return $title['description'];
	  }                        
     
     
    /**
	  * Method to get the Album title
	  * @param string $fileId
	  * @access public
	  */
	  public function  hasThumb($albumId)
	  {
		$thumb = $this->getRow('id',$albumId);
		return ($thumb['thumbnail'] != '') ? TRUE :FALSE; 
		
	     
	}
	
	/**
	* Increase the hit count for an album
	* @param string $albumId
	* @access public
	*/
	public function incrementHitCount($albumId)
	{
	 	$album = $this->getRow('id', $albumId);
	 	$views = array('no_views' => intval($album['no_views']) + 1 );
		$this->update('id', $albumId,$views);
	}
	
	/**
	* Method to reorder the ablums
	* @access public
	* 
	*/
	public function reOrderAlbums()
	{
		$albumOrder=$this->getParam('albumOrder',NULL);
                if ($albumOrder==NULL){
                    return;
                }
		$order = str_replace('albumList[]=','',$this->getParam('albumOrder'));
		$newOrder = split('&',$order);
		$albums = $this->getUserAlbums();
		$cnt = 0;
		
		foreach($newOrder  as $arr)
		{
		 	$cnt++;	
			// print $arr;	
			$this->update('id', $albums[$arr-1]['id'], array('position' => $cnt));
		}
	}
	
	/**
	* Method to save an album
	* @param string $albumId
	* @param string $description
	*/
	public function saveDescription($albumId, $description='')
	{echo $description;
		return $this->update('id', $albumId, array('description' => $description));
	}
}
?>
