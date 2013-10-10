<?php
/**
 * Latest 10 blog posts block
 *
 * Class to show latest 10 posts in a block for addition to the main UI
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
 * @version    $Id: block_lastten_class_inc.php 18297 2010-07-05 10:57:43Z paulscott $
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
 * A block to return the last 10 blog posts
 *
 * @category  Chisimba
 * @author    Megan Watson
 * @version   0.1
 * @copyright 2006-2007 AVOIR
 *
 */
class block_lastten extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * last ten posts box
     *
     * @var    object
     * @access public
     */
    public $display;
    /**
     * Blog operations class
     *
     * @var    object
     * @access public
     */
    public $blogOps;
    /**
     * Description for public
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->blogPosts = &$this->getObject('blogposts', 'blog');
        $this->display = $this->blogPosts->showLastTenPosts(5);
        $this->title = $this->objLanguage->languageText("mod_blog_block_latestblogs", "blog");
        $this->expose=TRUE;
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        $this->loadClass('link', 'htmlelements');
        
        $blogLink = new link ($this->uri(NULL, 'blog'));
        $blogLink->link = $this->objLanguage->languageText('mod_blog_blogs', 'blog', 'Blogs');
        
        return $this->display.'<p>'.$blogLink->show().'</p>';
    }
}
?>
