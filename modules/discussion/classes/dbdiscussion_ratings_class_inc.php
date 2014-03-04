<?php
/* ----------- data class extends dbTable for tbl_discussion_default_ratings------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Ratings Specific for Discussion Table
* This class controls all functionality relating to the tbl_discussion_ratings_discussion table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbdiscussion_ratings extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_discussion_ratings_discussion');
    }
    
    /**
    * Insert a discussion into into the database
    *
    * @param string $discussion_id:                 Discussion Record ID
    * @param string $rating_description:   Description of the Rating
    * @param string $rating_value:           Value of the Rating
    * @param string $userId:                   User Id of the person giving the rating
    * @param string $dateLastUpdated:      Date Rating is added
    */
    function insertSingle($discussion_id, $rating_description, $rating_value, $userId, $dateLastUpdated)
    {
        if ($userId == '') {
            $userId = 'init_1';
        }
        $this->insert(array(
                'discussion_id' => $discussion_id,
                'rating_description' => $rating_description,
                'rating_value' => $rating_value,
                'userId' => $userId,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
            ));
            
    }#function
    
    /**
    * Method to get the list of ratings discussion a discussion
    * @param string $discussionId Record Id of the discussion
    * @return array List of Ratings
    */
    function getDiscussionRatings ($discussionId)
    {
        $sql = 'SELECT * FROM tbl_discussion_ratings_discussion WHERE discussion_id = "'.$discussionId.'" ORDER BY rating_value DESC';
        
        return $this->getArray($sql);
    
    }




} #end of class
?>
