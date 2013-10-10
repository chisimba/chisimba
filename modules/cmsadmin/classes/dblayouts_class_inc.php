<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the cmsadmin module. Used to access data in the layouts table. 
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR 
* @license GNU GPL
* @author Wesley Nitsckie
* @author Warren Windvogel
*/

class dblayouts extends dbTable
{

	   /**
	    * Class Constructor
	    *
	    * @access public
	    * @return void
	    */
        public function init()
        {
        	try {                 
                parent::init('tbl_cms_layouts');
           } catch (Exception $e){
       		    echo 'Caught exception: ',  $e->getMessage();
        	    exit();
     	   }
        }

        /**
         * Method to get the layouts
         *
         * @access public
         * @return array $layouts An array associative arrays of all layouts
         */
        public function getLayouts()
        {
            $layouts = $this->getAll();
            return $layouts;
        }

        /**
         * Method to get the layout record
         * 
         * @access public
         * @param string $name The name of the layout
         * @return array $layout An associative array containing the layout details
         */
        public function getLayout($name)
        {
            $layout = $this->getRow('name', $name);
            return $layout;
        }
        /**
         * Method to get the description of a layout by referencing its name
         * 
         * @access public
         * @param string $name The name of the layout
         * @return string $description The layout description
         */
        public function getLayoutDescription($name)
        {
            $layout = $this->getRow('name', $name);
            $description = $layout['description'];
            return $description;
        }
        
        /**
	 * Method to add a RSS feed to the database
	 *
	 * @param string $userid
	 * @param string $name
	 * @param string $desc
	 * @param string $url
	 * @return bool
	 */
	public function addRss($rssarr, $mode = NULL)
	{
		$this->_changeTable("tbl_cms_rss");
		if($mode == NULL)
		{
			return $this->insert($rssarr);
		}
		elseif($mode == 'edit') {
			return $this->update('id', $rssarr['id'], $rssarr, "tbl_cms_rss");
		}
		else {
			return FALSE;
		}
	}

	public function getUserRss($userid)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->getAll("WHERE userid = '$userid'");
	}

	public function getRssById($id)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->getAll("WHERE id = '$id'");
	}

	public function delRss($id)
	{
		$this->_changeTable("tbl_cms_rss");
		return $this->delete('id', $id, "tbl_cms_rss");
	}
/**
	 * Method to dynamically switch tables
	 *
	 * @param string $table
	 * @return boolean
	 * @access private
	 */
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch (customException $e)
		{
			customException::cleanUp();
			return FALSE;
		}
	}
}
?>
