<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Discussion Posts Text Table
* This class controls all functionality relating to the tbl_discussion_post_text table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
/**
* To enable multi-lingualisation, post text was separated from post info. This class details with the post text
*/
class dbPostText extends dbTable
 {


	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		parent::init('tbl_discussion_post_text');
        $this->objTextStats =& $this->getObject('textstats', 'utilities');
        $this->objReadingEase =& $this->getObject('readingease', 'utilities');
    }


    /**
    * Insert a post into the database
    *
    * @param string $post_id Record Id of the post
    * @param string $post_title Title of the Post
    * @param string $post_text Text of the Post
    * @param string $language Language the post was made in
    * @param int $original_post Either 0 or 1 to indicate whether this is the original post, or a translation
    * @param string $userId Record Id of the User
    * @param string $id Id - Optional, used by API
    * @return string Last Insert Id
    */
    function insertSingle($post_id, $post_title, $post_text,  $language, $original_post, $userId, $id=NULL)
    {
    	// Interim measure. Alternative, use regexp and replace with space
        $post_title = strip_tags($post_title);

        // Clean up &nbsp;'s
        $post_text = $this->cleanUpPostText($post_text);

        $this->insert(array(
    		'id'                    => $id,
    		'post_id'               => $post_id,
    		'post_title'            => stripslashes($post_title),
    		'post_text'             => stripslashes('<p>'.$post_text.'</p>'),
    		'language'              => $language,
            'original_post'         => $original_post,
            'userId'                => $userId,
            'wordcount'             => $this->objTextStats->count_words(strip_tags($post_text)),
    		'dateLastUpdated'       => strftime('%Y-%m-%d %H:%M:%S', mktime())
    	));

    	return $this->getLastInsertId();
    }

    /**
    * Method to update a post's text and title - for editing
    * @param string $post_id Record Id of the post
    * @param string $post_title Title of the Post
    * @param string $post_text Text if the Post
    */
    function updatePostText($post_id, $post_title, $post_text)
    {
        $filter = ' WHERE post_id = "'.$post_id.'" LIMIT 1';
        $results = $this->getAll($filter);

        if (count($results) > 0) {
            $id = $results[0]['id'];
            $post_text = stripslashes($post_text);
            // unhtmlentities $document
            $table = array_flip(get_html_translation_table(HTML_ENTITIES));
            //$post_text = strtr($post_text, $table);
            return $this->update('id', $id, array('post_title' => $post_title, 'post_text' => $post_text));
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get all languages a post is in, or other languages the post is in.
    * @param string $id Record Id of the Post
    * @param string $language Language NOT to check post for
    * @return array List of languages the post is in.
    */
    function getPostLanguages($id, $language = NULL)
    {
        $sql = 'SELECT id, language FROM tbl_discussion_post_text WHERE post_id = "'.$id.'" ';
        if (isset($language)) {
            $sql .= ' AND language != "'.$language.'" ';
        }

        return $this->getArray($sql);
    }

    /**
    * This function cleans up a the &nbsp; that may occur in front of the text due to the WYSIWYG editor
    * It was affecting the word count. It tries to remove the &nbsp; ONLY if it starts in the first 5 letters - default
    * This, hopefully, takes into account users
    * @param string $postText Text to clean up
    * @param int $start - last point where the &nbsp; should start
    */
    function cleanUpPostText($postText, $start = 5)
    {
        // Check if there is a &nbsp; after tags has been stripped
        $firstCheckPost = strpos(strip_tags($postText), '&nbsp;');

        // Check if this is STARTs in the first five letters
        if ($firstCheckPost < $start) {

            // Now get TRUE position
            $pos = strpos($postText, '&nbsp;');

            // Get the first portion of the text that contains the &nbsp;
            $portion = substr ($postText, 0, $pos+6);
            // Store the rest of the text elsewhere
            $rest = substr($postText, $pos+6);

            // Remove the &nbsp in the first portion
            $portion = str_replace("&nbsp;", "", $portion);

            // Concatenate back
            $postText = $portion.$rest;
        }

        return $postText;
    }

    /**
    * Function to get a post in a particular language
    * @param string $postId Record Id of the Post
    * @param string $language Two letter code of the language
    * @return Array if translation found, else FALSE
    */
    function getTranslatedPost($postId, $language)
    {
        $result = $this->getAll(' WHERE post_id=\''.$postId.'\' AND language=\''.strtolower($language).'\'');

        if (count($result) == 0) {
            return FALSE;
        } else {
            return $result[0];
        }
    }


}



 ?>