<?php

/**
 * xulforum 1.0 interface class
 * 
 * XML-RPC (Remote Procedure call) class
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   api
 * @author    Brent van Rensburg
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * xulmail 1.0 XML-RPC Class
 * 
 * Class to provide forum API 1.0 XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Brent van Rensburg>
 * @copyright 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class forumapi extends object
{

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
			//database abstraction object
        	$this->objdbforum = $this->getObject('dbForum', 'forum');
        	$this->objdbtopic = $this->getObject('dbtopic', 'forum');
        	$this->objUser = $this->getObject('user', 'security');
        	$this->objPost =& $this->getObject('dbpost', 'forum');
        	$this->objsearch =& $this->getObject('forumsearch', 'forum');
        	$this->objPostText =& $this->getObject('dbposttext', 'forum');
        	$this->objDateTime =& $this->getObject('dateandtime', 'utilities');
        	$this->objdbtopicRead = $this->getObject('dbtopicread', 'forum');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	


    /**
     * get all forums
     * 
     * Gets a list of all forums for a user
     * 
     * @return object 
     * @access public
     */
	public function forumGetAll($params)
	{
		try{

	    	$forumStruct = array();
	    	$resarr = $this->objdbforum->getContextForums(null);
	    	//var_dump($resarr);
	      	foreach($resarr as $res)
	    	{
	    		$lastPost = $this->objPost->getLastPost($res['id']);
	    		if (preg_match('/<p>(.*)<\/p>/i',$lastPost['post_text'],$match)) {
	    			$subject = $match[1];
	    		} else {
	    			$subject = '';
	    		}
	    		
	    		
	    		if ($lastPost['firstname'] != '') {
	                $user = 'By: '.$lastPost['firstname'].' '.$lastPost['surname'].' - ';
	       		} else {
	            	$user = '';
		        }
		        
		        if ($this->objDateTime->formatDateOnly($lastPost['datelastupdated']) == date('j F Y')) {
		            $datefield = $this->objLanguage->languageText('mod_forum_todayat', 'forum').' '.$this->objDateTime->formatTime($lastPost['datelastupdated']);
		        } else {
		            $datefield = $this->objDateTime->formatDateOnly($lastPost['datelastupdated']).' - '.$this->objDateTime->formatTime($lastPost['datelastupdated']);
		        }
	    		
		        $postDetails = $user.$datefield;
	
				$struct = new XML_RPC_Value(array(			
					new XML_RPC_Value($res['id'], "string"),
					new XML_RPC_Value($res['forumlocked'], "string"),
	    			new XML_RPC_Value($res['forum_name'], "string"),
	    			new XML_RPC_Value($res['defaultforum'], "string"),
	    			new XML_RPC_Value($res['forum_description'], "string"),
	    			new XML_RPC_Value($res['topics'], "string"),
	    			new XML_RPC_Value($res['post'], "string"),
	    			new XML_RPC_Value($lastPost['post_title'], "string"),
	    			new XML_RPC_Value($subject, "string"),
	    			new XML_RPC_Value($lastPost['firstname'], "string"),
	    			new XML_RPC_Value($lastPost['surname'], "string"),
	    			new XML_RPC_Value($postDetails, "string")), "array");
	    		$forumStruct[] = $struct;
	    	}
	    	$forumArray = new XML_RPC_Value($forumStruct,"array");
			//var_dump($forumArray);
	    	return new XML_RPC_Response($forumArray);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	
  /**
     * get topics
     * 
     * Gets a list of all topics for a forum
     * 
     * @return object 
     * @access public
     */
	public function forumGetAllTopics($params)
	{
		try{
			$objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
			$param = $params->getParam(0);
			if (!XML_RPC_Value::isValue($param)) {
	            log_debug($param);
	    	}
	    	$forumId = $param->scalarval();
	    	
	    	$topicStruct = array();
	    	$resarr = $this->objdbtopic->showTopicsInForum($forumId, "1");
	    	
			//$resarr = $this->objdbtopic->showTopicsInForum("gen15Srv47Nme54_3108_1204717733", "1");
	    	//var_dump($resarr);
			foreach($resarr as $res)
	    	{
	    		$datefield = $objTranslatedDate->getDifference_no_html($res['datelastupdated']);
				$struct = new XML_RPC_Value(array(
					new XML_RPC_Value($res['topic_id'], "string"),
	    			new XML_RPC_Value($res['topicstatus'], "string"),
	    			new XML_RPC_Value($res['readtopic'], "string"),    			
	    			new XML_RPC_Value($res['type_icon'], "string"),
	    			new XML_RPC_Value($res['post_title'], "string"),
	    			new XML_RPC_Value($res['firstname'], "string"),
	    			new XML_RPC_Value($res['surname'], "string"),
	    			new XML_RPC_Value($res['replies'], "string"),
	    			new XML_RPC_Value($res['views'], "string"),
	    			new XML_RPC_Value($res['last_post'], "string"),
	    			new XML_RPC_Value($datefield, "string")), "array");
	    		$topicStruct[] = $struct;
	    	}
	    	$topicArray = new XML_RPC_Value($topicStruct,"array");
			//var_dump($topicStruct);
	    	return new XML_RPC_Response($topicArray);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	
	/**
     * get topics
     * 
     * Gets a list of all topics for a forum
     * 
     * @return object 
     * @access public
     */
	public function forumSearch($params)
	{
		try{
			
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$term = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$forum = $param->scalarval();
    	
    	$searchStruct = array();
    	$resarr = $this->objsearch->searchForumAPI($term, $forum);
    	//var_dump($resarr);
		foreach($resarr as $res)
    	{
    		if (preg_match('/<p>(.*)<\/p>/i',$res['post_text'],$match)) {
    			$subject = $match[1];
    		} else {
    			$subject = '';
    		}
    		$countTitle = substr_count($res['post_title'], "test");
    		$countSub = substr_count($subject, "test");
    		$matches = $countTitle + $countSub;
    		
			$struct = new XML_RPC_Value(array(
				new XML_RPC_Value($res['id'], "string"),
    			new XML_RPC_Value($res['post_title'], "string"),
    			new XML_RPC_Value($matches, "string"),
    			new XML_RPC_Value($subject, "string")), "array");
    		$searchStruct[] = $struct;
    	}
    	$searchArray = new XML_RPC_Value($searchStruct,"array");
	//var_dump($searchArray);
    	return new XML_RPC_Response($searchArray);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	/**
     * get post
     * 
     * Gets a list of all the posts in that specific topic
     * 
     * @return object 
     * @access public
     */
	public function forumGetPosts($params)
	{
		try{
			
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$topicId = $param->scalarval();
    	
    	$postStruct = array();
    	//$resarr = $this->objPost->getThread("gen12Srv21Nme58_4746_1203320003");
    	$this->objdbtopicRead->markTopicRead($topicId, "1");
    	
		$resarr = $this->objPost->getThread($topicId);
    	//var_dump($resarr);
		foreach($resarr as $res)
    	{
    		if (preg_match('/<p>(.*)<\/p>/i',$res['post_text'],$match)) {
    			$subject = $match[1];
    		} else {
    			$subject = '';
    		}
    		$dateofpost = substr($res['datelastupdated'], 0, 10);
    		$time = substr($res['datelastupdated'], -8);
    		$username = "by ".$res['firstname']."  ".$res['surname']." - ".$dateofpost." at ".$time;
    		$struct = new XML_RPC_Value(array(
				new XML_RPC_Value($res['id'], "string"),
    			new XML_RPC_Value($res['topic_id'], "string"),
    			new XML_RPC_Value($res['post_title'], "string"),    			
    			new XML_RPC_Value($subject, "string"),
    			new XML_RPC_Value($username, "string"),
    			new XML_RPC_Value($res['datelastupdated'], "string"),
    			new XML_RPC_Value($res['level'], "string")), "array");
    		$postStruct[] = $struct;
    	}
    	$postArray = new XML_RPC_Value($postStruct,"array");
	//var_dump($topicStruct);
    	return new XML_RPC_Response($postArray);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	/**
     * insert new topic
     * 
     * insert new topic into database as well as the post info
     * 
     * @return object 
     * @access public
     */
	public function forumInsertTopic($params)
	{
		try{
			
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$post_title = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$post_text = $param->scalarval();
    	$post_text = "<p>".$post_text."</p>";
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$type_id = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$forum_id = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$forum_name = $param->scalarval();
    	
    	$tangentParent = 0;
    	$topic_id = $this->objdbtopic->insertSingleAPI(
            $forum_id,
            $type_id,
            $tangentParent, // tangent parent
            "1"
        );

        
        $this->objdbforum->updateLastTopic($forum_id, $topic_id);
        
        $post_id = $this->objPost->insertSingle(0, 0, $forum_id, $topic_id,  "1");
        
        $this->objPostText->insertSingle($post_id, $post_title, $post_text,  "em", 1, "1");
        
        $this->objdbtopic->updateFirstPost($topic_id, $post_id);
        
        $this->objdbforum->updateLastPost($forum_id, $post_id);
        
        $postStruct = new XML_RPC_Value(array(
				new XML_RPC_Value($topic_id, "string"),
    			new XML_RPC_Value($post_title, "string"),
    			new XML_RPC_Value($forum_name, "string")), "array");
    	//$postStruct[] = $struct;
    	
    	//$postArray = new XML_RPC_Value($postStruct,"array");

        return new XML_RPC_Response($postStruct);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	/**
    * Method to get the title, text, date, etc of any post, by providing the record id of the post.
    * @param string $post Record Id of the post
    * @return array Details of the post
    */
	public function forumGetPostForReply($params)
	{
		try{
			
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$postId = $param->scalarval();
    	//$res = $this->objPost->getPostWithText("gen15Srv47Nme54_9717_1204800381");
    	$res = $this->objPost->getPostWithText($postId);
    	
    	//var_dump($res);

		if (preg_match('/<p>(.*)<\/p>/i',$res['post_text'],$match)) {
			$subject = $match[1];
		} else {
			$subject = '';
		}
		$dateofpost = substr($res['datelastupdated'], 0, 10);
		$time = substr($res['datelastupdated'], -8);
		$username = "by ".$res['firstname']."  ".$res['surname']." - ".$dateofpost." at ".$time;
		
		$struct = new XML_RPC_Value(array(
			new XML_RPC_Value($res['id'], "string"),
			new XML_RPC_Value($res['topic_id'], "string"),
			new XML_RPC_Value($res['post_title'], "string"),    			
			new XML_RPC_Value($subject, "string"),
			new XML_RPC_Value($username, "string"),
			new XML_RPC_Value($res['datelastupdated'], "string"),
			new XML_RPC_Value($res['level'], "string")), "array");
//var_dump($struct);
    	return new XML_RPC_Response($struct);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
	
	/**
     * insert a reply to a post
     * 
     * insert reply information into database
     * 
     * @return object 
     * @access public
     */
	public function forumInsertTopic($params)
	{
		try{
			
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$post_title = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$post_text = $param->scalarval();
    	$post_text = "<p>".$post_text."</p>";
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$type_id = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$forum_id = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$forum_name = $param->scalarval();
    	
    	$param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$topic_id = $param->scalarval();
    	
    	$param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$parentPostId = $param->scalarval();
    	
 		$parentPostDetails = $this->objPost->getRow('id', $parentPostId);
 		$level = $parentPostDetails["level"] + 1;
        
 		$post_id = $this->objPost->insertSingle($parentPostId, 0, $forum_id, $topic_id,  0, $level);
        $this->objPostText->insertSingle($post_id, $post_title, $post_text,  "em", 1, "1");
        
        $this->objTopic->updateLastPost($topic_id, $post_id);
        $this->objForum->updateLastPost($forum_id, $post_id);
        
        // Attachment Handling
        //$this->handleAttachments($post_id, $_POST['temporaryId']);
 		
        $postStruct = new XML_RPC_Value(array(
				new XML_RPC_Value($topic_id, "string"),
    			new XML_RPC_Value($post_title, "string"),
    			new XML_RPC_Value($forum_name, "string")), "array");
    	//$postStruct[] = $struct;
    	
    	//$postArray = new XML_RPC_Value($postStruct,"array");

        return new XML_RPC_Response($postStruct);
		} catch(customException $e) {
            echo customException::cleanUp();
            die($e);
        }
	}
	
}
?>