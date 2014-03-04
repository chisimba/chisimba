<?php
/* ----------- data class extends dbTable for tbl_forum_default_ratings------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Ratings Specific for Forum Table
* This class controls all functionality relating to the tbl_forum_ratings_forum table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
class dbforum_ratings extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_forum_ratings_forum');
    }
    
    /**
    * Insert a forum into into the database
    *
    * @param string $forum_id:                 Forum Record ID
    * @param string $rating_description:   Description of the Rating
    * @param string $rating_value:           Value of the Rating
    * @param string $userId:                   User Id of the person giving the rating
    * @param string $dateLastUpdated:      Date Rating is added
    */
    function insertSingle($forum_id, $rating_description, $rating_value, $userId, $dateLastUpdated)
    {
        if ($userId == '') {
            $userId = 'init_1';
        }
        $this->insert(array(
                'forum_id' => $forum_id,
                'rating_description' => $rating_description,
                'rating_value' => $rating_value,
                'userId' => $userId,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)
            ));
            
    }#function
    
    /**
    * Method to get the list of ratings forum a forum
    * @param string $forumId Record Id of the forum
    * @return array List of Ratings
    */
    function getForumRatings ($forumId)
    {
        $sql = 'SELECT * FROM tbl_forum_ratings_forum WHERE forum_id = "'.$forumId.'" ORDER BY rating_value DESC';
        
        return $this->getArray($sql);
    
    }




} #end of class
?>
