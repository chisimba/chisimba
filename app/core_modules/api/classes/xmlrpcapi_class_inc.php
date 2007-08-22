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
     * Blogger API object
     * @var    object
     * @access public
     */
	public $objBlogger;
	
	/**
     * MetaWebLog API object
     * @var    object
     * @access public
     */
	public $objMetaWebLog;
	
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
			// database abstraction object
        	$this->objDbBlog = $this->getObject('dbblog', 'blog');
        	// User Object
        	$this->objUser = $this->getObject('user', 'security');
        	
        	// API abstraction objects
        	// Blogger
        	$this->objBlogger = $this->getObject('bloggerapi');
        	// MetaWebLog API
        	$this->objMetaWebLog = $this->getObject('metaweblogapi');
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
   					array('blogger.newPost' => array('function' => array($this->objBlogger, 'bloggerNewPost'),
   											      'signature' => array(
                         											array('string', 'string', 'string', 'string','string', 'string', 'boolean'),
                     											 ),
                								  'docstring' => 'new post'),
                		  
                		  'blogger.editPost' => array('function' => array($this->objBlogger, 'bloggerEditPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'string', 'string', 'boolean'),
                     											 ),
                								  'docstring' => 'edit post'),
                		  
                		  'blogger.getPost' => array('function' => array($this->objBlogger, 'bloggerGetPost'),
   											      'signature' => array(
                         											array('struct', 'string', 'string', 'string', 'string'),
                     											 ),
                								  'docstring' => 'get post'),
                		  
                		  'blogger.getRecentPosts' => array('function' => array($this->objBlogger, 'bloggerGetRecentPosts'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get recent posts'),
                		  
                		  'blogger.getCategories' => array('function' => array($this->objBlogger, 'bloggerGetCategories'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get categories'),
                		  
                		  'blogger.getUsersBlogs' => array('function' => array($this->objBlogger, 'bloggerGetUsersBlogs'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user blogs'),
                		   						   
                		   'blogger.getUserInfo' => array('function' => array($this->objBlogger, 'bloggerGetUserInfo'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user info'),
                		   						   
                		   'blogger.deletePost' => array('function' => array($this->objBlogger, 'bloggerDeletePost'),
                		   						   'signature' => array(
                		   						   					array('boolean', 'string', 'string', 'string', 'string', 'boolean'),
                		   						   					),
                		   						   'docstring' => 'delete a post'),
                		   						   
                		   						   
                		   // metaweblog section
                		   'metaWeblog.newPost' => array('function' => array($this->objMetaWebLog, 'metaWeblogNewPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'struct', 'boolean'),
                     											 ),
                								  'docstring' => 'new post'),
                		  
                		  'metaWeblog.editPost' => array('function' => array($this->objMetaWebLog, 'metaWeblogEditPost'),
   											      'signature' => array(
                         											array('boolean', 'string', 'string', 'string', 'struct', 'boolean'),
                     											 ),
                								  'docstring' => 'edit post'),
                		  
                		  'metaWeblog.getPost' => array('function' => array($this->objMetaWebLog, 'metaWeblogGetPost'),
   											      'signature' => array(
                         											array('struct', 'string', 'string', 'string'),
                     											 ),
                								  'docstring' => 'get post'),
                		  
                		  'metaWeblog.getRecentPosts' => array('function' => array($this->objMetaWebLog, 'metaWeblogGetRecentPosts'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get recent posts'),
                		  
                		  'metaWeblog.getCategories' => array('function' => array($this->objMetaWebLog, 'metaWeblogGetCategories'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get categories'),
                		  
                		  'metaWeblog.getUsersBlogs' => array('function' => array($this->objMetaWebLog, 'metaWeblogGetUsersBlogs'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get user blogs'),
                		   						   
                		  'metaWeblog.deletePost' => array('function' => array($this->objMetaWebLog, 'metaWeblogDeletePost'),
                		   						   'signature' => array(
                		   						   					array('boolean', 'string', 'string', 'string', 'string', 'boolean'),
                		   						   					),
                		   						   'docstring' => 'delete a post'),
                		   						   
   					), 1, 0);
   					

		return $server;
	}
}
?>