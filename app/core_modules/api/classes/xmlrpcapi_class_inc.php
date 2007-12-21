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
     * Packages API object
     * @var    object
     * @access public
     */
	public $objPackages;
	
	/**
     * Wiki API object
     * @var    object
     * @access public
     */
	public $objWikiApi;
	
	/**
     * Chisimba Wiki API object
     * @var    object
     * @access public
     */
	public $objChisWikiApi;
	
	/**
     * Chisimba Web Present API object
     * @var    object
     * @access public
     */
	public $objWebPresentApi;
	
	/**
	 * Chisimba ADM API for high priority requests
	 * @var    object
	 * @access public
	 */
	public $objAdmApi;
	
	/**
	 * Chisimba FFMPEG API 
	 * @var    object
	 * @access public
	 */
	public $objFfmpeg;
	
	/**
	 * Chisimba Screenshot API 
	 * @var    object
	 * @access public
	 */
	public $objScreenShots;
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
        	// Packages module abstraction
        	$this->objPackages = $this->getObject('packagesapi');
        	// Wiki module abstraction
        	$this->objWikiApi = $this->getObject('wikiapi');
        	// Chisimba Wiki module abstraction
        	$this->objChisWikiApi = $this->getObject('chiswikiapi');
        	// Web Present API
        	$this->objWebPresentApi = $this->getObject('webpresentapi');
        	// ADM API
        	$this->objAdmApi = $this->getObject('admapi');
        	// ffmpeg API
        	$this->objFfmpeg = $this->getObject('ffmpegapi');
        	// Screenshot API
        	$this->objScreenShots = $this->getObject('screenapi');
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
                		  
                		  
                		  // Packages module for modulecatalogue functions...
                		  				   
                		  'getModuleZip' => array('function' => array($this->objPackages, 'getModuleZip'),
   											      'signature' =>
                     									array(
                         									array('string', 'string'),
                     									),
                								  'docstring' => 'Grab a module'),
                		  'getMultiModuleZip' => array('function' => array($this->objPackages, 'getMultiModuleZip'),
   											      'signature' =>
                     									array(
                         									array('array', 'string'),
                     									),
                								  'docstring' => 'Grab a set of modules'),
                								  					  
                		  'getModuleDescription' => array('function' => array($this->objPackages, 'getModuleDescription'),
   											      'signature' =>
                     									array(
                         									array('string', 'string'),
                     									),
                								  'docstring' => 'Grab a module description'),

                		  'getModuleList' => array('function' => array($this->objPackages, 'getModuleList'),
                								  'docstring' => 'Grab the module list'),


      			  		  'getModuleDetails' => array('function' => array($this->objPackages, 'getModuleDetails'),
                								  'docstring' => 'Grab the module list'),
                								  
                		  'getMsg' => array('function' => array($this->objPackages, 'getMessage'),
      			  		  					'signature' =>
                     							array(
                         							array('string', 'string'),
                     							),
                								'docstring' => 'What would you like to see?'),
                								
                		  
                		  // wiki xml-rpc interface - see http://jspwiki.org/wiki/WikiRPCInterface
                		  'wiki.getRPCVersionSupported' => array('function' => array($this->objWikiApi, 'getRPCVersionSupported'),
                		  						'signature' => 
                		  						    array(
                		  						        array('int'),
                		  						        ),
                		  						     'docstring' => 'Return the API version'),
                		  						     
                		  'wiki.getRecentChanges' => array('function' => array($this->objWikiApi, 'getRecentChanges'),
                		   						   'signature' => array(
                		   						   					array('array', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki recent changes list'),
                		   	
                		  'wiki.getPage' => array('function' => array($this->objWikiApi, 'getPage'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page'),
                		   						   
                		  'wiki.getPageVersion' => array('function' => array($this->objWikiApi, 'getPageVersion'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get wiki page version'),					   
                		  
                		  'wiki.getPageHTML' => array('function' => array($this->objWikiApi, 'getPageHTML'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page HTML'),	
                		   						   
                		  'wiki.getPageHTMLVersion' => array('function' => array($this->objWikiApi, 'getPageHTMLVersion'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'get wiki page HTML version'),
                		   						   
                		  'wiki.getAllPages' => array('function' => array($this->objWikiApi, 'getAllPages'),
                		   						   'signature' => array(
                		   						   					array('array'),
                		   						   					),
                		   						   'docstring' => 'returns an array of all wiki pages'),	
                		   						   
                		  'wiki.getPageInfo' => array('function' => array($this->objWikiApi, 'getPageInfo'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string'),
                		   						   					),
                		   						   'docstring' => 'returns page info'),
                		   						   
                		  'wiki.getPageInfoVersion' => array('function' => array($this->objWikiApi, 'getPageInfoVersion'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'int'),
                		   						   					),
                		   						   'docstring' => 'returns page info versions'), 		   
                		   						   
                		  'wiki.listLinks' => array('function' => array($this->objWikiApi, 'listLinks'),
                		   						   'signature' => array(
                		   						   					array('array', 'string'),
                		   						   					),
                		   						   'docstring' => 'Lists all links for a given page'),
                		   						   
                		   						   
                		  // chisimba wiki implementation - including wiki namespaces
                		  'chiswiki.getRPCVersionSupported' => array('function' => array($this->objChisWikiApi, 'getRPCVersionSupported'),
                		  						'signature' => 
                		  						    array(
                		  						        array('int'),
                		  						        ),
                		  						     'docstring' => 'Return the API version'),
                		  						     
                		  'chiswiki.getRecentChanges' => array('function' => array($this->objChisWikiApi, 'getRecentChanges'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki recent changes list'),
                		   	
                		  'chiswiki.getPage' => array('function' => array($this->objChisWikiApi, 'getPage'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page'),
                		   						   
                		  'chiswiki.getPageVersion' => array('function' => array($this->objChisWikiApi, 'getPageVersion'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'int', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page version'),					   
                		  
                		  'chiswiki.getPageHTML' => array('function' => array($this->objChisWikiApi, 'getPageHTML'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page HTML'),	
                		   						   
                		  'chiswiki.getPageHTMLVersion' => array('function' => array($this->objChisWikiApi, 'getPageHTMLVersion'),
                		   						   'signature' => array(
                		   						   					array('base64', 'string', 'int', 'string'),
                		   						   					),
                		   						   'docstring' => 'get wiki page HTML version'),
                		   						   
                		  'chiswiki.getAllPages' => array('function' => array($this->objChisWikiApi, 'getAllPages'),
                		   						   'signature' => array(
                		   						   					array('array'),
                		   						   					),
                		   						   'docstring' => 'returns an array of all wiki pages'),	
                		   						   
                		  'chiswiki.getPageInfo' => array('function' => array($this->objChisWikiApi, 'getPageInfo'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'returns page info'),
                		   						   
                		  'chiswiki.getPageInfoVersion' => array('function' => array($this->objChisWikiApi, 'getPageInfoVersion'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string', 'int', 'string'),
                		   						   					),
                		   						   'docstring' => 'returns page info versions'), 		   
                		   						   
                		  'chiswiki.listLinks' => array('function' => array($this->objChisWikiApi, 'listLinks'),
                		   						   'signature' => array(
                		   						   					array('array', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'Lists all links for a given page'), 
                		   						   
                		  // Web Present API
                		  // tagging options
                		  'presentation.getAllTags' => array('function' => array($this->objWebPresentApi, 'getAllTagsAPI'),
                		   						   'signature' => array(
                		   						   					array('array'),
                		   						   					),
                		   						   'docstring' => 'Lists all web present tags'),
                		   						    
                		  'presentation.getTagCloud' => array('function' => array($this->objWebPresentApi, 'getTagCloudAPI'),
                		   						   'signature' => array(
                		   						   					array('string'),
                		   						   					),
                		   						   'docstring' => 'Lists all web present tags as a tag cloud in HTML'),
                		   				 
                		  'presentation.getTagsPerFile' => array('function' => array($this->objWebPresentApi, 'getTagsPerFileAPI'),
                		   						   'signature' => array(
                		   						   					array('array', 'string'),
                		   						   					),
                		   						   'docstring' => 'Lists all web present tags attached to a specific fileid'), 
                		  
                		  'presentation.getFilesPerTag' => array('function' => array($this->objWebPresentApi, 'getFilesPerTagAPI'),
                		   						   'signature' => array(
                		   						   					array('array', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets all the files associated with a tag'), 
                		   						   
                		   'presentation.getFile' => array('function' => array($this->objWebPresentApi, 'getFileAPI'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets info on a file'), 
                		   						   
                		   'presentation.getLatest' => array('function' => array($this->objWebPresentApi, 'getLatestAPI'),
                		   						   'signature' => array(
                		   						   					array('struct'),
                		   						   					),
                		   						   'docstring' => 'gets latest list (10)'), 
                		   						   
                		   'presentation.getByUser' => array('function' => array($this->objWebPresentApi, 'getByUserAPI'),
                		   						   'signature' => array(
                		   						   					array('struct', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets latest list by userid'),
                		   						   
                		   'presentation.getThumbnail' => array('function' => array($this->objWebPresentApi, 'getThumbnailAPI'),
                		   						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets thumbnail of specific file'),
                		   						   
                		   'presentation.getNumSlides' => array('function' => array($this->objWebPresentApi, 'getNumSlidesAPI'),
                		   						   'signature' => array(
                		   						   					array('int', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets number of slides of specific file'),
                		   						   
                		   'presentation.getSlides' => array('function' => array($this->objWebPresentApi, 'getSlidesAPI'),
                		   						   'signature' => array(
                		   						   					array('array', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets slides of specific file'),
                		   						   
                		   'presentation.getSlideThumbnail' => array('function' => array($this->objWebPresentApi, 'getSlideThumbnailAPI'),
                		   						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets a specific slide thumbnail'),
                		   						   
                		   'presentation.getPresentationSlidesFormatted' => array('function' => array($this->objWebPresentApi, 'getPresentationSlidesFormattedAPI'),
                		   						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets formatted slides'),
                		   	
                		   // ADM API Start
                		   'adm.getData' => array('function' => array($this->objAdmApi, 'getDataAPI'),
                		   						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'gets data for specified module'),			
                		   						   
                		   						   
                		  // media API Start
                		   'media.3gp2flv' => array('function' => array($this->objFfmpeg, 'convert3GPtoFLV'),
                		   						   'signature' => array(
                		   						   					array('string', 'string', 'string'),
                		   						   					),
                		   						   'docstring' => 'converts a 3gp file to a flv and returns a base64 encoded string.'),						   
                		   						   
                		   						   
                		  // screenshot API Start
                		  'screenshot.requestShot' => array('function' => array($this->objScreenShots, 'requestShot'),
                		  						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'Request a screenshot of a URL'),			
                		   						   
                		  'screenshot.grabShot' => array('function' => array($this->objScreenShots, 'grabShot'),
                		  						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'Grabs a screenshot of a URL'),
                		   						   
                		  'screenshot.grabHiResShot' => array('function' => array($this->objScreenShots, 'grabHiResShot'),
                		  						   'signature' => array(
                		   						   					array('string', 'string'),
                		   						   					),
                		   						   'docstring' => 'Grab a hi res screenshot of a URL'),									   
                		  		
   					), 1, 0);
   					

		return $server;
	}
}
?>