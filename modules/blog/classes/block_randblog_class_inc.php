<?php
/**
 * random blog posts block
 *
 * Block to handle a random post in a block to be added to main UI
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
 * @version    $Id: block_randblog_class_inc.php 11076 2010-02-09 07:25:41Z pwando $
 * @package    blog
 * @subpackage blocks
 * @author     Paul Mungai <pwando@gmail.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * A block to return the last blog entry
 *
 * @author Paul Mungai based on a block by Paul Scott
 *
 *         $Id: block_latest_class_inc.php 11076 2010-02-09 06:58:10
 *
 */
class block_randblog extends object
{
    /**
     * The title of the block
     *
     * @var    string
     * @access public
     */
    public $title;
    /**
     * Database abstraction object
     *
     * @var object
     */
    public $objDbBlog;
    /**
     * Database abstraction object
     *
     * @var object
     */
    public $objSysConfig;
    /**
     * Blog posts object
     *
     * @var    object
     * @access public
     */
    public $objblogPosts;
    /**
     * Language object
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    /**
     * Standard init function
     *
     * Instantiates language and user objects and creates title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $userid = $this->objUser->userid();
        //database abstraction object
        $this->objDbBlog = $this->getObject('dbblog');
        $this->objSysConfig = $this->getObject( 'dbsysconfig', 'sysconfig' );
        $this->objblogPosts = $this->getObject('blogposts');
        $this->title = $this->objLanguage->languageText("mod_blog_block_latestblogs", "blog");
        $this->expose=TRUE;
    }
    /**
     * Standard block show method.
     *
     * It builds the output based on data obtained via the getlast class
     *
     * @return string the box rendered
     */
    public function show() 
    {
      $suuserid = $this->objSysConfig->getValue('blog_singleuserid', 'blog');
      $userid = $suuserid;
      //get the categories
      $catarr = $this->objDbBlog->getCatsTree($suuserid);
      //get the link categories
      $linkcats = $this->objDbBlog->getAllLinkCats($suuserid);
      //get all the posts by this user
      $postcount = $this->objDbBlog->getMonthPostCount(time() , $suuserid);
      if ($postcount <= 2 || $postcount >= 20) {
          $posts = $this->objDbBlog->getLastPosts(10, $suuserid);
      } else {
          $posts = $this->objDbBlog->getPostsMonthly(time() , $suuserid);
      }
      //get the sticky posts too
      $latestpost[0] = $this->objDbBlog->getLatestPost($suuserid);
      $rss = $this->objDbBlog->getUserRss($suuserid);
      $stickypost = $this->objDbBlog->getStickyPosts($suuserid);

      $this->loadClass('href', 'htmlelements');
      $cssLayout = $this->newObject('csslayout', 'htmlelements');
      $objUi = $this->getObject('blogui');
      $middleColumn = NULL;
      //check for sticky posts
      if (!is_null($stickypost)) {
          $middleColumn.= $this->objblogPosts->showPosts($stickypost, TRUE);
      }
      if (!empty($latestpost) && !empty($posts)) {
          $this->loadClass('htmlheading', 'htmlelements');
          $header = new htmlheading();
          $header->type = 3;
          $header->str = $this->objLanguage->languageText("mod_blog_latestpost", "blog")
            . ": " . $this->objDbBlog->getCatById($latestpost[0]['post_category']);
          $middleColumn.= $header->show();
          if ($posts[0]['id'] == $latestpost[0]['id']) {
              unset($posts[0]);
          }
          $middleColumn.= $this->objblogPosts->showPosts($latestpost);
          $middleColumn.= "<hr />";
          $headerprev = new htmlheading();
          $headerprev->type = 3;
          $headerprev->str = $this->objLanguage->languageText("mod_blog_previousposts", "blog");
          $middleColumn.= $headerprev->show();
          $middleColumn.= ($this->objblogPosts->showPosts($posts));
      } else {
          $middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blog_nopostsincat", "blog") . "</center></em></h1>";
          if (($this->objUser->userId() == $userid)&&($this->approvedBlogger())) {
              $linker = new href($this->uri(array(
                  'module' => 'blog',
                  'action' => 'blogadmin',
                  'mode' => 'writepost'
              )) , $this->objLanguage->languageText("mod_blog_writepost", "blog") , NULL); 
              $middleColumn.= "<center>" . $linker->show() . "</center>";
          }
      }
        return $middleColumn;
    }
}
?>
