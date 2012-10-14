<?php

/**
 * Blogger 1.0 interface class
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
 * @version   $Id$
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
 * Blogger 1.0 XML-RPC Class
 * 
 * Class to provide Blogger API 1.0 XML-RPC functionality to Chisimba
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
class bloggerapi extends object
{
    /**
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
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->isReg = $this->objModules->checkIfRegistered('blog');
            if ($this->isReg === TRUE) {
                $this->objDbBlog = $this->getObject('dbblog', 'blog');
            }
            $this->objUser = $this->getObject('user', 'security');
        } catch (customException $e) {
            customException::cleanUp();
            exit;
        }
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
        if ($publish) {
            $published = 0;
        } else {
            $published = 1;
        }
        
        $userid = $this->objUser->getUserId($username);
        
        //insert to the db now and return the generated id as a string
        $postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes($content) , 
                'post_title' => $this->objLanguage->languageText('mod_blog_word_apipost', 'blog'),
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
        if ($publish) {
            $published = 0;
        } else {
            $published = 1;
        }
        
        $userid = $this->objUser->getUserId($username);
        //insert to the db now and return the generated id as a string
        $postarray = array(
                'userid' => $userid,
                'post_date' => date('r') ,
                'post_content' => addslashes($content) , 
                'post_title' => $this->objLanguage->languageText('mod_blog_word_apipost', 'blog'),
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
            'content' => new XML_RPC_Value($post['post_content'], 'base64'),
            'userid' => new XML_RPC_Value($post['userid'], 'string'),
            'postid' => new XML_RPC_Value($post['id'], 'string'),
            'dateCreated' => new XML_RPC_Value($post['post_date'], 'string')), 'struct');

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
        foreach ($recentposts as $recentpost) {
            $myStruct = new XML_RPC_Value(array(
                'content' => new XML_RPC_Value($recentpost['post_content']),
                'userId' => new XML_RPC_Value($recentpost['userid'], 'string'),
                'postId' => new XML_RPC_Value($recentpost['id'], 'string'),
                'dateCreated' => new XML_RPC_Value($recentpost['post_date'], 'string')), 'struct');
        
            $arrofStructs[] = $myStruct;
        }
        $ret = new XML_RPC_Value($arrofStructs, 'array');

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
            'userid' => new XML_RPC_Value($userid, 'string'),
            'email' => new XML_RPC_Value($email, 'string'),
            'firstname' => new XML_RPC_Value($firstname,'string'),
            'nickname' => new XML_RPC_Value($username, 'string'),
            'url' => new XML_RPC_Value($url, 'string'),
            'lastname' => new XML_RPC_Value($lastname, 'string'),
            ), 'struct');

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
    public function bloggerGetCategories($params)
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
        
        // lets fetch the categories for the user...
        $userid = $this->objUser->getUserId($username);
        $resarr = $this->objDbBlog->getParentCats($userid);
        $url = $this->uri(array('action'=>'viewsingle', 'postid' => $results['id'], 'userid' => $results['userid']), 'blog');
        foreach ($resarr as $res) {
            $catStruct[] = new XML_RPC_Value(array(
                'htmlUrl' => new XML_RPC_Value($url, 'string'),
                'rssUrl' => new XML_RPC_Value($url, 'string'),
                'description' => new XML_RPC_Value($res['cat_name'], 'string')), 'struct');
        }

        return new XML_RPC_Response($catStruct);
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
        if (!$prf) {
            $prf = htmlentities($this->objUser->fullname($userid));
        } else {
            $prf = htmlentities($prf);
        }
        $url = $this->uri(array('action' => 'randblog', 'userid' => $userid), 'blog');
        $myStruct = new XML_RPC_Value(array(
            'blogid' => new XML_RPC_Value($userid, 'string'),
            'blogName' => new XML_RPC_Value($prf, 'string'),
            'url' => new XML_RPC_Value($url, 'string')), 'struct');
        
        $arrofStructs = new XML_RPC_Value(array($myStruct), 'array');

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
        
        $this->objDbBlog->deletePost($postid);
        
        $val = new XML_RPC_Value(TRUE, 'boolean');

        return new XML_RPC_Response($val);
    }

}
?>
