<?php
/**
 *
 * Enable a canvas (theme) for a blog
 *
 * Make use of the Chisimba canvas functionality to enable a canvas (theme) for
 * a blog. This class gets the blog user, checks for a canvas, and then loads it.
 * The functionality needs to be in the page template for the site. It is not
 * used automatically.
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
 * Enable a canvas (theme) for a blog
 *
 * Make use of the Chisimba canvas functionality to enable a canvas (theme) for
 * a blog. This class gets the blog user, checks for a canvas, and then loads it.
 * The functionality needs to be in the page template for the site. It is not
 * used automatically.
*
* @author Derek Keats
* @package blog
*
*/
class blogcanvas extends object
{

    public $uid;
    public $un;
    public $mdle;
    /**
     * Property to hold the sysconfig object
     *
     * @var    mixed
     * @access public
     */
    public $objSysConfig;

    /**
    *
    * Intialiser for the blogcanvas class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $objGuess = $this->getObject('bestguess', 'utilities');
        $this->un = $objGuess->guessUserName();
        $this->uId = $objGuess->guessUserId();
        $this->mdle = $objGuess->identifyModule();
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
    *
    * Go into the blog config and see if we allow users
    * to set themes.
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function allowThemes()
    {
        $this->objSysConfig->getValue('allowthemes', 'blog');
        if (strtolower($allowThemes) == 'true') {
            return TRUE;
        } else {
            return FALSE;
        }
        
        
    }

    /**
    *
    * Check if the user has a blog theme the blogthemes directory
    *
    * @access public
    * @return boolean TRUE|FALSE
    *
    */
    public function hasBlogTheme()
    {

    }

    /**
    *
    * Clear any set blog themes. This will need to be called when changing
    * blogs, or when leaving the blog module.
    *
    * @access public
    * @return VOID
    *
    */
    public function clearBlogTheme()
    {

    }

    /**
    *
    * Set the blog theme
    *
    * @access public
    * @return VOID
    */
    public function setBlogTheme()
    {

    }

    /**
    *
    * Executed when the user viewing a blog changes the blogger, and moves
    * to another blog.
    *
    * @access public
    * @return VOID
    *
    */
    public function changeBlogger()
    {
        
    }

}
?>
