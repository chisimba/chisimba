<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


/**
* Model class for the table tbl_homepages
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbHomePages extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_homepages');
        $this->objLanguage =& $this->getObject('language','language');
        $this->objUser =& $this->getObject('user','security');
        $this->objHomePageLog =& $this->getObject('dbhomepageslog');
    }

	/**
    * Method to determine if a Home Page exists for a user or not.
    * @param string $userId Record Id of the User
    * @return boolean True if home page exists, else False
    */
    public function homepageExists($userId)
	{
		$sql = "SELECT count(*) AS count FROM tbl_homepages 
		WHERE userid = '$userId'";
		$results = $this->getArray($sql);
		$count = intval($results[0]['count']);
        if ($count == 0) {
            return FALSE;
        } 
		else {
            return TRUE;
        }
	}
    
    /**
    * Method to get the record Id of a User's Home page
    * @param string $userId Record Id of the User
    * @return string Record Id of the User's Home page
    */
    public function getHomePageId($userId)
    {
        $results = $this->listSingle($userId);
        return $results['id'];
    }
	
    /**
    * Method to get a single user's home page
    * @param string $userId The user ID
    * @return array The contents of the homepage
    */
	public function listSingle($userId)
	{
		return $this->getRow('userid', $userId);
	}

	/**
	* Insert a record
	* @param string $userId The user ID
	* @param string $contents The contents of the homepage
	*/
	public function insertSingle($userId, $contents)
	{
		$this->insert(array(
			'userid' => $userId,
			'contents' => $contents,
		));
		return;	
	}

	/**
	* Update a record
	* @param string $userId The user ID
	* @param string $contents The contents of the homepage
	*/
	public function updateSingle($userId, $contents)
	{
		$this->update("userid", $userId, 
			array(
				'contents' => $contents
			)
		);
	}

	/**
	* Delete a record
	* @param string The user ID
	*/
	public function deleteSingle($userId)
	{
		$this->delete('userid', $userId);
	}
    
    /**
    * NOT USED!
    * Join on the tbl_users and tbl_hompages tables and returns
    * an arraya with the firstnames and surnames.
    * @return array The array of userids with their corresponding firstname 
    * and surnames.
    */
    public function FindFullNames()
    {
        $sql = "SELECT 
			tbl_homepages.userid, 
			tbl_users.firstname, 
			tbl_users.surname 
		  FROM tbl_users, tbl_homepages 
        WHERE tbl_homepages.userid = tbl_users.userid";            
        return $this->getArray($sql);			
    }

	/**
	* Method to get the list of home pages available for a specific letter 
    * @param string $order Ordering pattern
    * @param string $limit Amount of record to limit query
    * @param string $match letter for surname search 
    * @return array List of Home Pages with Hits
    */
    public function getHomePagesWithHitsSingle($match)
    {
        $sql = "SELECT 
		   tbl_homepages.userid, 
			count( homepageid ) AS visitors, 
			firstname, 
			surname, 
			timestamp 
        FROM 
			tbl_homepages 
   	  LEFT JOIN tbl_homepages_log ON ( homepageid = tbl_homepages.id )
        INNER JOIN tbl_users ON ( tbl_homepages.userid = tbl_users.userid )
  		  WHERE surname LIKE '$match%'
	     GROUP BY tbl_homepages.id  ORDER BY firstname";
        
        if (!is_null($limit)) {
            $sql .= ' LIMIT '.'NULL';
		}
        return $this->getArray($sql);
        	//			  WHERE surname LIKE '%$match%'

    }    
    
   
    /**
    * Method to get the list of home pages available with amount of hits for each web page
    * @param string $order Ordering pattern
    * @param string $limit Amount of record to limit query
    * @return array List of Home Pages with Hits
    */
    public function getHomePagesWithHits($order = 'firstname', $limit = NULL)
    {
        $sql = 'SELECT 
			tbl_homepages.userid, 
			count( homepageid ) AS visitors, 
			firstname, 
			surname, 
			timestamp 
        FROM 
			tbl_homepages 
		  LEFT JOIN tbl_homepages_log ON ( homepageid = tbl_homepages.id )
        INNER JOIN tbl_users ON ( tbl_homepages.userid = tbl_users.userid )
        GROUP BY tbl_homepages.id  ORDER BY '.$order;
        if (!is_null($limit)) {
            $sql .= ' LIMIT '.$limit;
		}
        return $this->getArray($sql);
    }
    
    
    /**
    * This method is for the Personal Space Module. It displays a block for a particular User
    * @param string $userId Record Id of the User. If Null, defaults to the current logged in user
    * @param boolean $showLinks Flag to indicate whether to show links home page, preview & create/edit
    */
    public function show($userId = NULL, $showLinks = TRUE)
    {
        // If User Id is NULL, default to current logged in user
        if (is_null($userId)) {
            $userId = $this->objUser->userId();
        }
        $this->loadClass('link', 'htmlelements');
		$return = "";
        // Start a Wrapper Block
        $return .= '<div class="wrapperLightBkg" style="border: 1px solid #c0c0c0;">';
        // Determine if Home Page Exists
        if (!$this->homepageExists($userId)) { // If not, show message that home page has not been created yet.
            $createLink = new link($this->uri(NULL, 'homepage'));
            $createLink->link = $this->objLanguage->languageText('mod_homepage_createyourhomepage', 'homepage');
            $return .= 
			'<p align="center">'
			.$this->objLanguage->languageText('mod_homepage_youhavenotcreatedahomepage', 'homepage')
			.' '
			.$createLink->show()
			.'</p>';
        } 
		else { // Else Show statistics of Home Page
            $homePageId = $this->getHomePageId($userId);
            $return .= '<ul>';
            // Total Number of Hits
            $hits = $this->objHomePageLog->getHits($homePageId);
            $return .= '<li><p><strong>'.$this->objLanguage->languageText('mod_homepage_total_hits', 'homepage').'</strong>: '.$hits.'</p></li>';
            // Total Number of Unique Visitors
				$visitors = $this->objHomePageLog->getUniqueVisitors($homePageId);            
            $return .= '<li><p><strong>'.$this->objLanguage->languageText('mod_homepage_unique_visitors', 'homepage').'</strong>: '.$visitors.'</p></li>';
            // Last Visitor - Under Construction
            //$return .= '<li><p><strong>Last Visitor</strong>: 8'.'</p></li>';
            // Origins of ther User
				$unique = $this->objHomePageLog->getCountriesFlags($homePageId);            
            $return .= '<li><p><strong>'.$this->objLanguage->languageText('mod_homepage_visitor_origins', 'homepage').'</strong>: '.$unique.'</p></li>';
            $return .= '</ul>';
            if ($showLinks) {
                // Link to Home Page
                $visitLink = new link($this->uri(NULL, 'homepage'));
                $visitLink->link = $this->objLanguage->languageText('mod_homepage_gotohomepage', 'homepage');
                // View Home Page
                $viewHomePageLink = new link($this->uri(array('action'=>'viewhomepage', 'userId'=>$userId), 'homepage'));
                $viewHomePageLink->link = $this->objLanguage->languageText('mod_homepage_view', 'homepage');
                // View Home Page
                $editHomePageLink = new link($this->uri(array('action'=>'edithomepage'), 'homepage'));
                $editHomePageLink->link = $this->objLanguage->languageText('mod_homepage_edityourhomepage', 'homepage');
                $return .= 
				'<p align="center">'
				.$visitLink->show()
				.' / '.$viewHomePageLink->show()
				.' /  '.$editHomePageLink->show()
				.'</p>';
            }
        }
        // Close Wrapper
        $return .= '</div>';
        // Return string
        return $return;
    }
}
?>