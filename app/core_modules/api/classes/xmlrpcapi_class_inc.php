<?php

/**
 * XML-RPC interface class
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
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
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
 * XML-RPC Class
 * 
 * Class to provide XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class xmlrpcapi extends object
{

    /**
     * Blog DB object
     * @var    object
     * @access public
     */
	public $objDbBlog;
	
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
			require_once($this->getPearResource('XML/RPC/Server.php'));
			require_once($this->getPearResource('XML/RPC/Dump.php'));
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
			//database abstraction object
        	$this->objDbBlog = $this->getObject('dbblog', 'blog');
        	$this->objUser = $this->getObject('user', 'security');
		}
		catch (customException $e)
		{
			// garbage collection
			customException::cleanUp();
			// die, as we are screwed anyway
			exit;
		}
	}
	
    /**
     * server method
     * 
     * Create and deploy the XML-RPC server for use on an URL
     * 
     * @return object server object
     * @access public
     */
	public function serve()
	{
		// map web services to methods
		$server = new XML_RPC_Server(
   					array('blogger.newPost' => array('function' => array($this, 'bloggerNewPost'),
   											      'signature' => array(
                         											array('string', 'string', 'string', 'string','string', 'string', 'boolean'),
                     											 ),
                								  'docstring' => 'new post'),
                		  
                		  'blogger.editPost' => array('function' => array($this, 'bloggerEditPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'string', 'string', 'boolean'),
                     											 ),
                								  'docstring' => 'edit post'),
                		  
                		  'blogger.getPost' => array('function' => array($this, 'bloggerGetPost'),
   											      'signature' => array(
                         											array('struct', 'string', 'string', 'string', 'string'),
                     											 ),
                								  'docstring' => 'get post'),
                		  
                		  'blogger.getRecentPosts' => array('function' => array($this, 'bloggerGetRecentPosts'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get recent posts'),
                		  
                		  'blogger.getCategories' => array('function' => array($this, 'bloggerGetCategories'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get categories'),
                		  
                		  'blogger.getUsersBlogs' => array('function' => array($this, 'bloggerGetUsersBlogs'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user blogs'),
                		   						   
                		   'blogger.getUserInfo' => array('function' => array($this, 'bloggerGetUserInfo'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user info'),
                		   						   
                		   'blogger.deletePost' => array('function' => array($this, 'bloggerDeletePost'),
                		   						   'signature' => array(
                		   						   					array('boolean', 'string', 'string', 'string', 'string', 'boolean'),
                		   						   					),
                		   						   'docstring' => 'delete a post'),
                		   						   
                		   						   
                		   // metaweblog section
                		   'metaWeblog.newPost' => array('function' => array($this, 'metaWeblogNewPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'struct', 'boolean'),
                     											 ),
                								  'docstring' => 'new post'),
                		  
                		  'metaWeblog.editPost' => array('function' => array($this, 'metaWeblogEditPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'struct', 'boolean'),
                     											 ),
                								  'docstring' => 'edit post'),
                		  
                		  'metaWeblog.getPost' => array('function' => array($this, 'metaWeblogGetPost'),
   											      'signature' => array(
                         											array('struct', 'string', 'string', 'string'),
                     											 ),
                								  'docstring' => 'get post'),
                		  
                		  'metaWeblog.getRecentPosts' => array('function' => array($this, 'metaWeblogGetRecentPosts'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get recent posts'),
                		  
                		  'metaWeblog.getCategories' => array('function' => array($this, 'metaWeblogGetCategories'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get categories'),
                		  
                		  'metaWeblog.getUsersBlogs' => array('function' => array($this, 'metaWeblogGetUsersBlogs'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user blogs'),
                		   						   
                		  'metaWeblog.deletePost' => array('function' => array($this, 'metaWeblogDeletePost'),
                		   						   'signature' => array(
                		   						   					array('boolean', 'string', 'string', 'string', 'string', 'boolean'),
                		   						   					),
                		   						   'docstring' => 'delete a post'),
                		   						   
   					), 1, 0);
   					

		return $server;
	}
	
    /**
     * blogger new post
     * 
     * Create a new post
     * 
     * @param  object $params parameters
     * @return object Return 
     * @access public
     */
	public function bloggerNewPost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$blogid = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$content = $param->scalarval();
    	
    	$param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$publish = $param->scalarval();
    	if($publish)
    	{
    		$published = 0;
    	}
    	else {
    		$published = 1;
    	}
    	
    	$userid = $this->objUser->getUserId($username);
    	
    	//insert to the db now and return the generated id as a string
    	$postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes($content) , 
                'post_title' => $this->objLanguage->languageText("mod_blog_word_apipost", "blog") ,
                'post_category' => '0',
                'post_excerpt' => '',
                'post_status' => $published,
                'comment_status' => 'on',
                'post_modified' => date('r'),
                'comment_count' => '0',
                'post_ts' => time() ,
                'post_lic' => '',
                'stickypost' => '0',
                'showpdf' => '1'
            );
        
    	$ret = $this->objDbBlog->insertPostAPI($userid, $postarray);
		$val = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($val);
	}
	
    /**
     * blogger edit post
     * 
     * Edit a post
     * 
     * @param  object $params Parameters
     * @return object Return
     * @access public
     */
	public function bloggerEditPost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$blogid = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$content = $param->scalarval();
    	
    	$param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$publish = $param->scalarval();
    	if($publish)
    	{
    		$published = 0;
    	}
    	else {
    		$published = 1;
    	}
    	
    	$userid = $this->objUser->getUserId($username);
    	//insert to the db now and return the generated id as a string
    	$postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes($content) , 
                'post_title' => $this->objLanguage->languageText("mod_blog_word_apipost", "blog") ,
                'post_category' => '0',
                'post_excerpt' => '',
                'post_status' => $published,
                'comment_status' => 'on',
                'post_modified' => date('r'),
                'comment_count' => '0',
                'post_ts' => time() ,
                'post_lic' => '',
                'stickypost' => '0',
                'showpdf' => '1'
            );
    	$ret = $this->objDbBlog->updatePostAPI($blogid, $postarray);
		$val = new XML_RPC_Value(TRUE, 'boolean');
   		return new XML_RPC_Response($val);
	}
	
    /**
     * blogger get post
     * 
     * Get a post by its ID
     * 
     * @param  object $params Parameters
     * @return object Return 
     * @access public
     */
	public function bloggerGetPost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$blogid = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	//go get the post
    	$post = $this->objDbBlog->getPostById($blogid);
    	$post = $post[0];
    	//log_debug($post);
		$postStruct = new XML_RPC_Value(array(
    		"content" => new XML_RPC_Value($post['post_content'], "base64"),
    		"userid" => new XML_RPC_Value($post['userid'], "string"),
    		"postid" => new XML_RPC_Value($post['id'], "string"),
    		"dateCreated" => new XML_RPC_Value($post['post_date'], "string")), "struct");
    	return new XML_RPC_Response($postStruct);
	}
	
    /**
     * Recent posts
     * 
     * Get recent posts
     * 
     * @param  object $params Parameters
     * @return object Return
     * @access public
     */
	public function bloggerGetRecentPosts($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$blogid = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$noPosts = $param->scalarval();
    	$userid = $this->objUser->getUserId($username);
    
		$recentposts = $this->objDbBlog->getLastPosts($noPosts, $userid);
		foreach($recentposts as $recentpost)
		{
			$myStruct = new XML_RPC_Value(array(
    			"content" => new XML_RPC_Value($recentpost['post_content']),
    			"userId" => new XML_RPC_Value($recentpost['userid'], "string"),
    			"postId" => new XML_RPC_Value($recentpost['id'], "string"),
    			"dateCreated" => new XML_RPC_Value($recentpost['post_date'], "string")), "struct");
    	
    		$arrofStructs[] = $myStruct;
		}
    	$ret = new XML_RPC_Value($arrofStructs, "array");
    	return new XML_RPC_Response($ret);
	}
	
    /**
     * get user info
     * 
     * gets the user info - email address, url, etc
     * 
     * @param  object $params Parameters
     * @return object Return 
     * @access public
     */
	public function bloggerGetUserInfo($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$userid = $this->objUser->getUserId($username);
    	$email = $this->objUser->email($userid);
    	$firstname = $this->objUser->getFirstname($userid);
    	//we are using the username as the nickname here...
    	$url = $this->uri(array('action' => 'randblog', 'userid' => $userid), 'blog');
    	$lastname = $this->objUser->getSurname($userid);
    	
    	//return a struct of members about the user
    	$userStruct = new XML_RPC_Value(array(
    		"userid" => new XML_RPC_Value($userid, 'string'),
    		"email" => new XML_RPC_Value($email, "string"),
    		"firstname" => new XML_RPC_Value($firstname, "string"),
    		"nickname" => new XML_RPC_Value($username, "string"),
    		"url" => new XML_RPC_Value($url, "string"),
    		"lastname" => new XML_RPC_Value($lastname, "string"),
    		), "struct");
    	return new XML_RPC_Response($userStruct);	
        
	}

    /**
     * get Categories
     * 
     * Gets a list of blog categories for a user
     * 
     * @return object 
     * @access public
     */
	public function bloggerGetCategories()
	{
		$val = new XML_RPC_Value('Not implemented', 'string');
		return new XML_RPC_Response($val);
	}
	
    /**
     * get users blogs
     * 
     * Gets a list of the users blogs
     * 
     * @param  object $params Parameters
     * @return object Return
     * @access public
     */
	public function bloggerGetUsersBlogs($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
		$userid = $this->objUser->getUserId($username);
		$prf = $this->objDbBlog->checkProfile($userid);
		$prf = $prf['blog_name']; 
		if(!$prf)
		{
			$prf = htmlentities($this->objUser->fullname($userid));
		}
		else {
			$prf = htmlentities($prf);
		}
		$url = $this->uri(array('action' => 'randblog', 'userid' => $userid), 'blog');
		$myStruct = new XML_RPC_Value(array(
    		"blogid" => new XML_RPC_Value($userid, 'string'),
    		"blogName" => new XML_RPC_Value($prf, "string"),
    		"url" => new XML_RPC_Value($url, "string")), "struct");
    	
    	$arrofStructs = new XML_RPC_Value(array($myStruct), "array");
    	return new XML_RPC_Response($arrofStructs);
	}
	
    /**
     * delete post
     * 
     * Deletes a post
     * 
     * @param  object $params Parameters 
     * @return object Return
     * @access public
     */
	public function bloggerDeletePost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$appkey = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$postid = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$publish = $param->scalarval();
    	
    	$this->objDbBlog->deletePost($blogid);
		$val = new XML_RPC_Value(TRUE, 'boolean');
		return new XML_RPC_Response($val);
	}
	
	/**
	 * Metaweblog section - functions
	 */
	
	
	public function metaWeblogNewPost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$blogid = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pass = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$content = $param->serialize($param);
    	//$cont = simplexml_load_string($content);
    	$cont = new SimpleXMLElement($content);
    	$cont = $cont->struct;
    	foreach($cont->member as $members)
    	{
    		if($members->name == 'title')
    		{
    			$title = $members->value;
    		}
    		elseif($members->name == 'description')
    		{
    			$postcontent = $members->value;
    		}
    		elseif($members->name == 'mt_excerpt')
    		{
    			$excerpt = $members->value;
    		}
    	}
    	//log_debug($member);
    	

    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$publish = $param->scalarval();
    	
    	if($publish)
    	{
    		$published = 0;
    	}
    	else {
    		$published = 1;
    	}
    	
    	$userid = $this->objUser->getUserId($username);
    	
    	//insert to the db now and return the generated id as a string
    	$postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes(nl2br($postcontent->string)) , 
                'post_title' => addslashes($title->string),
                'post_category' => '0',
                'post_excerpt' => $excerpt->string,
                'post_status' => $published,
                'comment_status' => 'on',
                'post_modified' => date('r'),
                'comment_count' => '0',
                'post_ts' => time() ,
                'post_lic' => '',
                'stickypost' => '0',
                'showpdf' => '1'
            );
        //log_debug($postarray);
    	$ret = $this->objDbBlog->insertPostAPI($userid, $postarray);
		$val = new XML_RPC_Value($ret, 'string');
		return new XML_RPC_Response($val);
	}
	
	/**
     * metaWeblog edit post
     * 
     * Edit a post
     * 
     * @param  object $params Parameters
     * @return object Return
     * @access public
     */
	public function metaWeblogEditPost($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$postid = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$password = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$content = $param->serialize($param);
    	$cont = new SimpleXMLElement($content);
    	$cont = $cont->struct;
    	foreach($cont->member as $members)
    	{
    		if($members->name == 'title')
    		{
    			$title = $members->value;
    		}
    		elseif($members->name == 'description')
    		{
    			$postcontent = $members->value;
    		}
    		elseif($members->name == 'mt_excerpt')
    		{
    			$excerpt = $members->value;
    		}
    	}
    	$param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$publish = $param->scalarval();
    	if($publish)
    	{
    		$published = 0;
    	}
    	else {
    		$published = 1;
    	}
    	log_debug($postcontent.$publish);
    	$userid = $this->objUser->getUserId($username);
    	//insert to the db now and return the generated id as a string
    	$postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes($postcontent->string) , 
                'post_title' => $title->string, //$this->objLanguage->languageText("mod_blog_word_apipost", "blog") ,
                'post_category' => '0',
                'post_excerpt' => $excerpt->string,
                'post_status' => $published,
                'comment_status' => 'on',
                'post_modified' => date('r'),
                'comment_count' => '0',
                'post_ts' => time() ,
                'post_lic' => '',
                'stickypost' => '0',
                'showpdf' => '1'
            );
        log_debug($postarray);
    	$ret = $this->objDbBlog->updatePostAPI($postid, $postarray);
		$val = new XML_RPC_Value(TRUE, 'boolean');
   		return new XML_RPC_Response($val);
	}
	
    /**
     * delete a post
     * 
     * Delete a post from the users blog
     * 
     * @param  unknown $params Parameters 
     * @return object  Return
     * @access public 
     */
	public function metaWeblogDeletePost($params)
	{
		$val = new XML_RPC_Value(TRUE, 'boolean');
		return new XML_RPC_Response($val);
	}
	
    /**
     * get categories
     * 
     * Gets a list of categories for a user
     * 
     * @param  unknown $params Parameters
     * @return object  Return
     * @access public 
     */
	public function metaWeblogGetCategories($params)
	{
		log_debug("getting metaweblog categories!");
		//$val = new XML_RPC_Value('a returned string', 'string');
		//return new XML_RPC_Response($val);
		
		$myStruct = new XML_RPC_Value(array(
    		"blogid" => new XML_RPC_Value($userid, 'string'),
    		"blogName" => new XML_RPC_Value($prf, "string"),
    		"url" => new XML_RPC_Value($url, "string")), "struct");
    	
    	//$arrofStructs = new XML_RPC_Value(array($myStruct), "array");
    	return new XML_RPC_Response($myStruct);
	}
	
	public function metaWeblogGetRecentPosts($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$postid = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$username = $param->scalarval();
    	
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$password = $param->scalarval();
    	
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$numposts = $param->scalarval();
    	$userid = $this->objUser->getUserId($username);
    	$resarr = $this->objDbBlog->getLastPosts($numposts, $userid);
    	foreach($resarr as $results)
    	{
    		$url = $this->uri(array('action'=>'viewsingle', 'postid' => $results['id'], 'userid' => $results['userid']), 'blog');
    		// returns an array of structs...so build one.
    		$myStruct = new XML_RPC_Value(array(
    			"dateCreated" => new XML_RPC_Value($results['post_date'], 'string'),
    			"userid" => new XML_RPC_Value($userid, "string"),
    			"postid" => new XML_RPC_Value($results['id'], "string"),
    			"description" => new XML_RPC_Value($results['post_content'], "string"),
    			"title" => new XML_RPC_Value($results['post_title'], "string"),
    			"link" => new XML_RPC_Value($url, "string"),
    			"permaLink" => new XML_RPC_Value($url, "string"),
    			"categories" => new XML_RPC_Value('things', "string"),
    			), "struct");
    		$arrofStructs[] = $myStruct; 
    	}
    	$arrofStructs = new XML_RPC_Value($arrofStructs, "array");
    	return new XML_RPC_Response($arrofStructs);
	}
	
	
	
}
?>