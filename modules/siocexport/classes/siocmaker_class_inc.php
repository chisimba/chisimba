<?php
/**
 * SIOC maker class
 *
 * Class to make SIOC data available in Chisimba
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
 * @package   sioc
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * SIOC class
 *
 * Class to make SIOC data available in Chisimba
 *
 * @category  Chisimba
 * @package   sioc
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class siocmaker extends object {
    public $objSiocApi;

    public $siocSite;
    public $siocFora;
    public $siocUsers;

    public $siocForum;
    public $siocUser;
    public $siocPost;


    public $siocExport;
    public $siocObject;

    public function init() {
        require_once($this->getResourcePath('sioc_inc.php','siocexport'));
        $this->siocExport = new SIOCExporter();
    }

    /**
     * SIOCObject is a virtual class that is not designed to be instanciated, but is a base to SIOC / PHP Classes:
     * Site, Forum, Post and User.
     * The function addNote($note) can be used to append notes to any SIOCObject which will be exported as a rdfs:comment.
     */

    /**
     * SIOCSite Class
     * SIOCSite($url, $name, $description) :
     * * $url : URL of sioc:Site
     * * $name : Name of the sioc:Site
     * * $description : A short description of the sioc:Site
     *
     * function addForum($id, $url) :
     * * $id : ID of the sioc:Forum
     * * $url : URL of the sioc:Forum (HTML page)
     * * NB: You can add as many forums as needed
     *
     * function addUser($id, $url) :
     * * $id : ID of the sioc:User
     * * $url : URL of the User (HTML page)
     * * NB: You can add as many users as needed
     * * NB: This method will create a sioc:Usergroup
     */

    /**
     * Method to create the top level site container
     *
     * @param array $siteData
     * @param array $fora - should be a multidimensional array of blogs, forums etc
     * @param array $users - multidimensional array of users, containing an id and a url for each user NOTE: multiple users will create a usergroup
     *
     * @return object site Object
     */
    public function setSite($siteData) {
        $this->siocSite= new SIOCSite($siteData['url'], $siteData['name'], $siteData['description']);

        return $this->siocSite;
    }

    public function setFora($fora) {
        foreach($fora as $fdata) {
            $this->siocSite->addForum($fdata['id'], $fdata['url']);
        }

        return $this->siocSite;
    }

    public function setUsers($users) {
        foreach ($users as $user) {
            $this->siocSite->addUser($user['id'], $user['url']);
        }

        return $this->siocSite;
    }

    /**
     * Forum classes
     */

    public function createForum($id, $url, $page, $title = "", $descr = "") {
        // define('PER_PAGE', '10');
        $this->siocForum = new SIOCForum($id, $url, $page, $title, $descr);

        return $this->siocForum;
    }

    public function forumPosts($posts) {
        foreach ($posts as $post) {
            $this->siocForum->addPost($post['id'], $post['url']);
        }

        return $this->siocForum;
    }

    public function forumSetNextPage($pagenum) {
        $this->siocForum->setNextPage($pagenum);
    }

    /**
     * User Classes
     */
    public function createUser($user) {
        $this->siocUser = new SIOCUser($user['id'], $this->author_uri($user['id'], $user['url']), $user['name'], $user['email'], $user['homepage'], $this->foaf_uri($user['id'], $user['url']), $user['role']);

        return $this->siocUser;
    }

    private function author_uri($user, $blog_url) {
       return htmlspecialchars($blog_url) . '#' . $user;
    }

    private function foaf_uri($user, $blog_url) {
       return htmlspecialchars($blog_url) . '#foaf_' . $user;
    }

    /**
     * Post
     */
    public function createPost($url, $subject, $content, $encoded, $created, $updated = "", $tags = array(), $links = array()) {
        $siocPost = new SIOCPost($url, $subject, $content, $encoded, $this->siocUser, $created, $updated, $tags, $links);
        $this->siocExport->addObject($siocPost);
    }

    public function addPostComment($postid, $url) {
        return $this->siocPost->addComment($id, $url);
    }

    public function addPostReplyOf($postid, $url) {
        return $this->siocPost->addReplyOf($id, $url);
    }

    /**
     * Method to dump the SIOC generated data to a url
     *
     * This method should be called last!
     *
     * @param array $siocData
     *
     * <pre>
     * $siocData array should contain the following information for validity
     *
     * $title : The title of your website / blog
     * $url : The URL of your website / blog
     * $sioc_url : URL of its SIOC export (without additionnal parameters) (/index.php?module=siocexport&action=export)
     * $encoding : Output encoding (UTf-8)
     * $generator : The URL of your exporter plugin (so that people know where to look for additional exporter information) (as above with anchor #)
     * </pre>
     *
     * @return string formatted RDF
     */
    public function dumpSioc($siocData) {
        // $this->siocExport = new SIOCExporter();

        $this->siocExport->setParameters($siocData['title'], $siocData['url'], $siocData['sioc_url'], $siocData['encoding'], $siocData['generator']);
        // add the SIOC objects that we have created for the site, posts etc
        // site data
        $this->siocExport->addObject($this->siocSite);
        // forum data
        $this->siocExport->addObject($this->siocFora);
        // User data
        $this->siocExport->addObject($this->siocUsers);
        // Forum data
        $this->siocExport->addObject($this->siocForum);
        // user data
        $this->siocExport->addObject($this->siocUser);
        // posts data
        //$this->siocExport->addObject($this->siocPost);

        // export the lot
        return $this->siocExport->export();
    }
}
?>