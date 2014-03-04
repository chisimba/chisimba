<?php
/**
 *
 * Manage the type of comment system used by a blog
 *
 * Manage the type of comment system used by a blog by allowing settings to
 * determine whether to use Chisimba comments, Wall, Facebook comments, and may
 * add others by including a method that corresponds to the value of the parameter
 * comment_type.
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
 * Manage the type of comment system used by a blog
 *
 * Manage the type of comment system used by a blog by allowing settings to
 * determine whether to use Chisimba comments, Wall, Facebook comments, and may
 * add others by including a method that corresponds to the value of the parameter
 * comment_type.
*
* @author Derek Keats
* @package blog
*
*/
class dynamiccomment extends object
{

    /**
     * Property to hold the sysconfig object
     *
     * @var    mixed
     * @access public
     */
    public $objSysConfig;

    private $commentsEnabled=FALSE;

    /**
    *
    * Intialiser for the blogcanvas class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //$objGuess = $this->getObject('bestguess', 'utilities');
        //$this->un = $objGuess->guessUserName();
        //$this->uId = $objGuess->guessUserId();
        //$this->mdle = $objGuess->identifyModule();
        $this->objUser = $this->getObject('user', 'security');
        $this->objblogTrackbacks = $this->getObject('blogtrackbacks');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objblogPosts = $this->getObject('blogposts');
    }

    /**
    *
    * Show the appropriate comment block
    *
    * @param string $postid The id of the post to which the comment applies
    * @return string The rendered comment block
    *
    */
    public function show($postid)
    {
        $this->commentsEnabled = $this->objSysConfig->getValue('enabled', 'blogcomments');
        if (!$this->commentsEnabled) {
            return NULL;
        }
        $commentType = "_" . strtolower($this->objSysConfig->getValue('comment_type', 'blog'));
        return $this->$commentType($postid);
    }

    /**
     *
     * The default comment block using the blogcomments module.
     *
     * @param string $postid The id of the post to which the comment applies
     * @return string The rendered comment block
     *
     */
    private function _default($postid)
    {
        $objComments = $this->getObject('commentapi', 'blogcomments');
        $ret = $objComments->showComments($postid);
        $tracks = $this->objblogTrackbacks->showTrackbacks($postid);
        $ret .= $tracks;
        if ($this->objUser->isLoggedIn() == TRUE) {
            $userid = $this->objUser->userId();
            $ret .= $this->objblogPosts->addCommentForm($postid, $userid, FALSE, NULL, NULL);
        } else {
            $ret .= $this->objblogPosts->addCommentForm($postid, NULL, TRUE, NULL, NULL);
        }
        return $ret;
    }

    /**
     *
     * A comment block using the facebook API apps (facebookapps) module.
     *
     * @param string $postid The id of the post to which the comment applies
     * @return string The rendered comment block
     *
     */
    private function _facebook($postid)
    {
        $objApps = $this->getObject('fbapps', 'facebookapps');
        return '<center class="fb_comment_block">'
          . $objApps->getComments() . '<center>';
    }

    /**
     *
     * The wall comment block using the wall module.
     *
     * @param string $postid The id of the post to which the comment applies
     * @return string The rendered comment block
     *
     */
    private function _wall($postid)
    {
        $objWallOps = $this->getObject('wallops', 'wall');
        return $objWallOps->showObjectWall("identifier", $postid);
    }

    /**
     *
     * Set a parameter value
     *
     * @param string $param The parameter to set a value for
     * @param mixed $value The value to set
     * @return VOID
     * 
     */
    public function set($param, $value)
    {
        $this->$param = $value;
    }

}
?>
