<?php

/**
 * MetaWebLog interface class
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
 * MetaWebLog XML-RPC Class
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
class metaweblogapi extends object
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
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->isReg = $this->objModules->checkIfRegistered('blog');
            if($this->isReg === TRUE)
            {
                $this->objDbBlog = $this->getObject('dbblog', 'blog');
            }
            $this->objUser = $this->getObject('user', 'security');
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
    }
    
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
        //log_debug($postarray);
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
        $bloggerapi = $this->getObject("bloggerapi");
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
        
        // The struct returned contains one struct for each category, containing the following elements: description, htmlUrl and rssUrl.
        
        // lets fetch the categories for the user...
        $userid = $this->objUser->getUserId($username);
        $resarr = $this->objDbBlog->getParentCats($userid);
        $url = $this->uri(array('action'=>'viewsingle', 'postid' => $results['id'], 'userid' => $results['userid']), 'blog');
        foreach($resarr as $res)
        {
            $catStruct[] = new XML_RPC_Value(array(
                "htmlUrl" => new XML_RPC_Value($url, "string"),
                "rssUrl" => new XML_RPC_Value($url, "string"),
                "description" => new XML_RPC_Value($res['cat_name'], "string")), "struct");
        }
        //$arrofStructs = new XML_RPC_Value(array($myStruct), "array");
        //log_debug($catStruct);
        return new XML_RPC_Response($catStruct);
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
                "content" => new XML_RPC_Value($results['post_content'], "string"),
                "title" => new XML_RPC_Value($results['post_title'], "string"),
                "link" => new XML_RPC_Value($url, "string"),
                "permaLink" => new XML_RPC_Value($url, "string"),
                "categories" => new XML_RPC_Value('things', "string"),
                ), "struct");
            $arrofStructs[] = $myStruct; 
        }
        $arrofStructs = new XML_RPC_Value($arrofStructs, "array");
        //log_debug($arrofStructs);
        return new XML_RPC_Response($arrofStructs);
    }
    
    /**
     * Metaweblog get post
     * 
     * Get a post by its ID
     * 
     * @param  object $params Parameters
     * @return object Return 
     * @access public
     */
    public function metaWeblogGetPost($params)
    {
        //log_debug("getting single post..... - metaweblog");
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
        $pass = $param->scalarval();

        //go get the post
        $post = $this->objDbBlog->getPostById($postid);
        $post = $post[0];
        //log_debug($post);
        $postStruct = new XML_RPC_Value(array(
            "description" => new XML_RPC_Value($post['post_content'], "string"),
            "userid" => new XML_RPC_Value($post['userid'], "string"),
            "postid" => new XML_RPC_Value($post['id'], "string"),
            "dateCreated" => new XML_RPC_Value($post['post_date'], "string")), "struct");
        return new XML_RPC_Response($postStruct);
    }
}
?>