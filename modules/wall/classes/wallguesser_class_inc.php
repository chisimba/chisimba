<?php
/**
 *
 * A simple wall module guesser object to guess which wall to display
 *
 * A simple wall module guesser object to guess which wall to display by looking
 * to see:
 * 1. Are we in a context?
 * 2. Is there a userId in the querystring?
 * 3. Are we in a module such as blog?
 * 4. Are we in the wall module itself
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
 * @package   wall
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbwall.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
 * A simple wall module operations object
 *
 * A simple wall module that makes use of OEMBED and that tries to look a bit
 * like Facebook's wall. This is the operations class.
*
* @author Derek Keats
* @package wall
*
*/
class wallguesser extends object
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
     * @return integer The wall type (0,1,2)
     * @access public
     *
     */
    public function guessWall()
    {
        
        // First check if walltype is set in querystring.
        $wallType = $this->getParam('walltype', FALSE);
        if ($wallType) {
            return $this->getNumeric($wallType);
        }
        
        // If they are in the wall module, then it should be the site wall.
        $objBestGuess = $this->getObject('bestguess', 'utilities');
        $currentModule = $objBestGuess->identifyModule();
        if ($currentModule == "wall") {
            return 1;
        }

        // If they are not logged in, it must be the site wall.
        if (!$this->objUser->isLoggedIn() == TRUE) {
            return 1;
        }

        // Next check if they are in a context.
        $objContext = $this->getObject('dbcontext', 'context');
        if($objContext->isInContext()){
            $this->currentContextCode = $objContext->getcontextcode();
            // Returning a wall type of context (3).
            return 3;
        } else {
            $objBestGuess = $this->getObject('bestguess', 'utilities');
            $userId = $objBestGuess->guessUserId();
            $this->ownerId = $userId;
            return 2;
        }

        // As a last resort, return site wall
        return 1;

    }

    /**
     *
     * Translate the querystring text into the numeric wall type
     *
     * @param <type> $wallType
     * @return integer The wall type (0,1,2)
     * @access public
     */
    private function getNumeric($wallType) {
        switch($wallType) {

            case "site":
            case "1":
                return 1;
                break;
            case "personal":
            case "2":
                return 2;
                break;
            // Wall is being used in a context (e.g. a course)
            case "context":
            case "3":
                return 3;
                break;
            // Wall is being used as a comment mechanism in simpleblog
            case "simpleblog":
            case "4":
                return 4;
                break;
            default:
                // Default to site wall
                return 1;
        }
    }
}
?>