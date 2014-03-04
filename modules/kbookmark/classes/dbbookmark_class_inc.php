<?php
/**
 * Abstract base class bookmark.
 * @author James Kariuki Njenga, Qhamani Fenama
 * @version $Id: dbbookmark_class_inc.php 16933 2010-02-24 10:59:39Z qfenama $
 * @copyright 2005, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
*/

class dbBookmark extends dbTable
{

    /**
    * Initialise objects used in the module
    */

    function init()
    {
        parent::init('tbl_bookmarks');
    }
    
    /**
    * Method to insert a single record into the database table
    *
    */
    function insertSingle($folderid, $title, $url, $tags, $description)
    {
        $alldata = array(
             'folderid'    => $folderid,
             'title'       => $title,
             'url'         => $url,
             'description' => $description,
             'tags'		   => $tags,
             'datecreated' => $this->now(),
             'visitcount'  => 0);

         return $this->insert($alldata);
    }

	/**
    * Method to get a all bookmarks of the folder
    *
	* @param $folderid
	*
	* @return array
    */
    function getUserFolderBookmark($folderid)
    {
        $sql = "SELECT * FROM tbl_bookmarks WHERE folderid = '{$folderid}'";
        return $this->getArray($sql);
    }
    
    /**
    * Method to update a bookmark
    * @param id
	* @param title
	* @param url
	* @param description
	* @param tags
	*
    */
    function updateBookmark($id, $title, $url, $description, $tags)
    {
        $fields = array();
	    $fields['title']		= $title;
        $fields['url']			= $url;
        $fields['description']	= $description;
        $fields['tags']			= $tags;
        $fields['datemodified'] = $this->now();
		$this->update('id', $id, $fields);
    }

	/**
    * Method to update the bookmark table on accessing a bookmark
    * Sets the date of access and also increased the hit count
    *
    */
    
    function updateVisitHit($pageId)
    {
		$visitcount=$this->getHits($pageId);
		$visitcount=$visitcount+1;
		return $this->update('id',$pageId, array('visitcount'=>$visitcount));
    }
    
    /**
    * function to check if a given folder is empty
    * @param folderId
    *
    * return bool
    */
    function isEmpty($folderId)
    {
        $filter="WHERE id='$folderId'";
        $rows = $this->getRecordCount($filter);
        if ($rows > 0){
            return False;
        } else {
            return True;
        }
    }

	/**
	* function that delete the bookmark via any field
	*
	* @param id string
	* @param field string
	*
	**/
	function deleteBookmark($id, $field)
	{
		return $this->delete($field, $id, 'tbl_bookmarks');
	}

	/**
	* function that get the single bookmark via the id
	*
	* @param id
	*
	* return array
	**/
	function getSingleBookmark($id)
	{
		$onerec = $this->getRow('id', $id);
        return $onerec;
	}
    
    
    /**
    * Method to get the number of hits a bookmark has
    *
    * @param pageid string
    * return int
    */
    function getHits($pageId)
    {
        $filter="where id='$pageId'";
        $list=$this->getAll($filter);
        foreach ($list as $line)
        {
            $count=$line['visitcount'];
        }
        return $count;
        
    }
}
?>
