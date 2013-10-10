<?php
/**
 *
 * Count the comments in a blog post and provide link to view
 *
 * Count the comments in a blog post and provide link to view whether we use
 * Chisimba comments, Wall, Facebook comments, or another system that can be
 * added just by adding the method.
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
 * @package   blog
 * @author    Derek Keats dere@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: blogcanvas_class_inc.php,v 0.1 2010-10-10 08:46:00 dkeats Exp $
 * @link      http://www.chisimba.com
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Count the comments in a blog post and provide link to view
*
* Count the comments in a blog post and provide link to view whether we use
* Chisimba comments, Wall, Facebook comments, or another system that can be
* added just by adding the method.
*
* @author Derek Keats
* @package blog
*
*/
class dynamiccommentcounter extends object
{

    /**
     * Property to hold the sysconfig object
     *
     * @var    mixed
     * @access public
     */
    public $objSysConfig;

    /**
     *
     * Whether comments are enabled or not
     *
     * @var    boolean TRUE|FALSE
     * @access public
     */
    private $commentsEnabled=FALSE;

    /**
    *
    * Intialiser for the dynamic block class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
    *
    * Show the appropriate comment block
    *
    * @param string $postid The id of the post to which the comment is attached
    *
    *
    */
    public function show($postid)
    {
        $this->commentsEnabled = $this->objSysConfig->getValue('enabled', 'blogcomments');
        if (!$this->commentsEnabled) {
            return NULL;
        }
        $comments = "_" . strtolower($this->objSysConfig->getValue('comment_type', 'blog'));
        return $this->$comments($postid);
    }

    /**
     * Method corresponding to the default comment type. Returns default comment
     * count using blogcomment module
     *
     * @param string $postid The id of the post to which the comment is attached
     * @return string The formatted comments
     *
     */
    private function _default($postid)
    {
        $objComments = $this->getObject('commentapi', 'blogcomments');
        $commentCount = $objComments->getCount($postid);
        return $commentCount;
    }

    /**
     * Method corresponding to the facebook comment type. Returns facebook comment
     * count
     *
     * @param string $postid The id of the post to which the comment is attached
     * @return string The formatted comments
     *
     */
    private function _facebook($postid)
    {
        $objApps = $this->getObject('fbapps', 'facebookapps');
        return $objApps->insertCommentCount();
    }

    /**
     * Method corresponding to the wall comment type. Returns comment
     * counts using the wall module
     *
     * @param string $postid The id of the post to which the comment is attached
     * @return string The formatted wall
     *
     */
    private function _wall($postid)
    {
        $objWallOps = $this->getObject('wallops', 'wall');
        //return $objWallOps->showPostCount("identifier", $postid);
        return "Wall";
    }
}
?>