<?php
/**
 *
 * A simple blog guesser object to guess which blog to display or edit
 *
* A simple blog guesser object to guess which blog to display or edit by looking
 * to see:
 * 1. Are we in a context?
 * 2. Is there a userId in the querystring?
 * 3. Is there a blogid in the querystring
 *
 * etc
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
 * @package   simpleblog
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
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
 * A simple blog guesser object to guess which blog to display or edit
 *
* A simple blog guesser object to guess which blog to display or edit by looking
 * to see:
 * 1. Are we in a context?
 * 2. Is there a userId in the querystring?
 * 3. Is there a blogid in the querystring
*
* @author Derek Keats
* @package simpleblog
*
*/
class guesser extends object
{

   // public $objUser;
    public $currentContextCode;


    /**
    *
    * Intialiser for the wall database connector
    * @access public
    *
    */
    public function init()
    {
        // Instantiate the user object
       $this->objUser = $this->getObject('user', 'security');
    }

    /**
     *
     * Use some parameters to guess the wall type.
     *
     * @param BOOLEAN $resetSession Whether we want to reset the session blog
     * @return integer The wall type (0,1,2)
     * @access public
     *
     */
    public function guessBlogId()
    {        
        // Then check if blogid is set in querystring.
        $blogId = $this->getParam('blogid', FALSE);
        if ($blogId) {
            return $blogId;
        }
        
        // If they are in a context, then display the context blog
        $objContext = $this->getObject('dbcontext', 'context');
        if($objContext->isInContext()){
            return $objContext->getcontextcode();
        }
        
        // If they are not logged in, then display the default blog.
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $defaultBlog = $objSysConfig->getValue('simpleblog_defaultblog', 'simpleblog');
        if (!$this->objUser->isLoggedIn()) {
            return $defaultBlog;
        } else {
            //@TODO this will break under some circumstances
            if ($defaultBlog == 'site') {
                return $defaultBlog;
            } else {
                // It must be their blog. ( @TODO need to add check if they have blog)
                $userId = $this->objUser->userId();
                return $userId;
            }
        }
    }
}
?>