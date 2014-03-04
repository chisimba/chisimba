<?php 

// check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run']) {

    die("You cannot view this page directly");

} 



/**

 * Class dbwiki_page for performing operations on tbl_wiki_pages table

 * 

 * @author Warren Windvogel, Alastair Pursch (PHP5) 

 * @package wiki

 */



class dbwiki_page extends dbTable 

{

    /**

     * Method to define the table

     * 

     * @access public

     */

    public function init()

    {

        parent::init('tbl_wiki_page');

        $this->objUser = &$this->getObject('user', 'security');

    } 



    /**

     * Method to return list of wiki pages

     * 

     * @access public

     * @param string $sortby Element by which to order the array returned, default 'datecreated'

     * @return array $pages List of wiki pages

     */

    public function listpagenames($sortby = 'datecreated')

    {

        $sql = "SELECT id,pagename,content,creatorid,datecreated,context FROM tbl_wiki_page ORDER BY '$sortby'";

        $pages = $this->getArray($sql);

        return $pages;

    } 

    

    /**

     * Method to add a new wiki page to the table

     * 

     * @access public

     */

    public function addWiki()

    {

        $fields = array();

        $fields['pagename'] = $this->getParam('pagename');

        $fields['datecreated'] = date('Y-m-d H:i:s');//didn't have seconds previously

        $fields['creatorid'] = $this->objUser->userId();

        $fields['content'] = $this->getParam('content');

        

        $fields['summary'] = $this->getParam('summary');

        $fields['context'] = $this->getParam('context');

        

        $fields['datemodified'] = '';

        $fields['modifierid'] = '';



        $this->insert($fields);

    } 

    

    /**

     * Method to update an entry in the table

     * 

     * @access public

     * @param string $id The id field(pk) of the entry

     */

    public function savePage($id)

    {
    	$fields = array();

    	$fields['content'] = $this->getParam('content');

    	$fields['context'] = $this->getParam('context');

    	$fields['datemodified'] = date('Y-m-d H:i:s');

    	$fields['modifierid'] = $this->objUser->userId();

    	$this->update('id', $id, $fields);

    }

    

    /**

    * Method to return only selected context pages

    *

    * @access public

    * @param string $index Index of selected content

    * @param string $where Selected content

    * @return array $pages List of pages related to context.

    */

    public function getIndex($index, $where)

    {

        $where = addslashes($where);

    

        $sql = "SELECT id,pagename,content,creatorid,datecreated,context FROM tbl_wiki_page WHERE $index = '$where'";

    

        $pages = $this->getArray($sql);

    

        return $pages;

    }

    

    /**

    * Method to get a list of the most recently added wiki pages

    * 

    * @access public

    * @return array $recentlyAddedPages An array of the 5 most recently added pages

    */

    public function getRecentlyAddedPages()

    {

        $sql = "SELECT * FROM tbl_wiki_page ORDER BY datecreated DESC LIMIT 5";

        $recentlyAddedPages = $this->getArray($sql);

        return $recentlyAddedPages;

    }

    

    /**

    * Method to get a list of the most recently updated wiki pages

    * 

    * @access public

    * @return array $recentlyUpdatedPages An array of the 5 most recently updated pages

    */

    public function getRecentlyUpdatedPages()

    {

        $sql = "SELECT * FROM tbl_wiki_page ORDER BY datemodified DESC LIMIT 5";

        $recentlyAddedPages = $this->getArray($sql); 

        return $recentlyAddedPages;

    }

    

    /**

    * Method to return all wiki pages related to a specified topic

    *

    * @access public

    * @param string $topic The specific topic in question

    * @return array $relatedPages An array of associative arrays of all the relevant wiki pages

    */

    public function getPagesByTopic($topic)

    {

    	$sql = "SELECT * FROM tbl_wiki_page WHERE context = '$topic' ORDER BY datecreated";

    	$relatedPages = $this->getArray($sql);

    	return $relatedPages;

    }

     

    /**

    * Method to return a list of all available topics

    *

    * @access public

    * @return array $topics An array of all available topics

    */

    public function getAvailableTopics()

    {

    	$allPages = $this->getAll();

    	$topics = array();

    	foreach($allPages as $data){

    		$topic = $data['context'];

    		if(!in_array($topic, $topics)){

    			$topics[] = $topic;

    		}

    	}

    	return $topics;

    }

    

    /**

    * Method to check if a page name already exists

    *

    * @access public

    * @param string $pagename The page name to check for

    * @return string $exists Returns 'yes' if name exists or 'no' if name does not exist

    */

    public function checkIfPageNameExists($pagename)

    {

    	$sql = "SELECT * FROM tbl_wiki_page WHERE pagename = '$pagename'";

    	$page = $this->getArray($sql);

    	if(!empty($page)){

    		$exists = 'yes';

    	} else {

    		$exists = 'no';

    	}

    	return $exists;

    }

    
    /**
    * Method that returns status of a page
    *
     * @access public
     * @returns boolean Whether page is locked or not
    */
    
    public function isPageLocked ($id)
    {
       	//Get page row from database
    	$check = $this->getRow('id', $id);
      	
      	//Check if page has lock
      	if($check['pagelock'])
      	{
      	  	return 1;
		}
		
		//Did not have a page lock, return false
		return 0;
	}

	
	/**

    * Method that locks a page
    *
    * @access public
    * @returns boolean When page is locked 
    */
    
	public function lockPage ($id)
	{
	  //getting the field from the database
	  $fields = array();

    	$fields['pagelock'] = 1;

    	$this->update('id', $id, $fields);
    	
    
	}
	
	
	/**

    * Method that unlocks a page
    *
    * @access public
    * @returns boolean When page is unlocked
    */
    
	public function unlockPage ($id)
	{
	  $field = array();

    	$field['pagelock'] = 0;

    	$this->update('id', $id, $fields);
    	
    	
	}
	
	
}    
?>