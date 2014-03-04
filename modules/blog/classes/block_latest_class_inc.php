<?php
/**
 * Latest blog posts block
 *
 * Class to show latest n posts in a block for addition to the main UI
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
 * @version    $Id: block_latest_class_inc.php 18297 2010-07-05 10:57:43Z paulscott $
 * @package    blog
 * @subpackage blocks
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
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
 * @author Paul Scott based on a block by Derek Keats
 *
 *         $Id: block_latest_class_inc.php 18297 2010-07-05 10:57:43Z paulscott $
 *
 */
class block_latest extends object
{
    /**
     * The title of the block
     *
     * @var    string
     * @access public
     */
    public $title;
    /**
     * String to hold the lastblog object
     *
     * @var    object
     * @access public
     */
    public $objLastBlog;
    /**
     * Object to display the quick blog box
     *
     * @var    object
     * @access public
     */
    public $quickBlog;
    /**
     * Blog operations class
     *
     * @var    object
     * @access public
     */
    public $blogOps;
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
        $this->blogOps = $this->getObject('blogposts', 'blog');
        $this->quickBlog = $this->blogOps->quickPost($userid, FALSE);
        $this->objLastBlog = NULL;
        $this->title = $this->objLanguage->languageText("mod_blog_block_quickpost", "blog");
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
        return $this->quickBlog;
    }
}
?>
