<?php
/* ----------- data class extends dbTable for tbl_discussion_post_ratings------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Post Ratings Table
* This class controls all functionality relating to the tbl_discussion_post_ratings table and gets the ratings for posts
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbpost_ratings extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_discussion_post_ratings');
    }

    /**
    * Save a rating for a post
    *
    * @param string $post_id: Post being rated
    * @param string $rating: Rating given to the post
    * @param string $userId: User Id of the person giving the rating
    * @param string $dateLastUpdated: Date Rating is added
    */
    function insertRecord($post_id, $rating,  $userId)
    {
        // First delete the existing records for a post
        // Implemented here so that one doesn't have to explicitly do so in the controller
        $this->deletePostRatings($post_id, $userId);
        
        $this->insert(array(
    		'post_id'                 => $post_id,
    		'rating'                   => $rating,
    		'userId'                  => $userId,
    		'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
    	));
        
        return;
    }
    
    /**
    *  Method to delete existing ratings of a user for posts.
    * 
    * Before ratings are posted for a post, existing ones are delete
    * This prevents the need to search for ratings that exist and need to use update
    * Called from the insert function.
    *
    * @param string $topic_id: the id for the topic
    * @param string $userId: userId of the person
    */
    function deletePostRatings($post, $userId)
    {
        $sql =  'DELETE tbl_discussion_post_ratings FROM tbl_discussion_post_ratings 
                    WHERE tbl_discussion_post_ratings.post_id = "'.$post.'" AND tbl_discussion_post_ratings.userId = "'.$userId.'"';
                    
        return $this->query($sql);
    
    }
    
    /**
     * get post rating using a post ID
     */
    function getPostRatings($post_id){
            $sql = "SELECT rating FROM tbl_discussion_post_ratings WHERE post_id='{$post_id}'";
            $rating = $this->getRow('post_id',$post_id);
            if($rating['rating'] == '' || $rating['rating'] == NULL){
                    return 0;
            }  else {
                    return $rating['rating'];
            }
    }



} #end of class
?>