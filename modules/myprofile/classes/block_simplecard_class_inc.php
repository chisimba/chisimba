<?php
/**
 *
 * Simple card
 *
 * The simple card block renders the user's profile card (the default card
 * shown by Chisimba). This is not the full digital business card.
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
 * @version
 * @package    myprofile
 * @author     Derek Keats derek@dkeats.com
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * Upper profile block
 *
 * The upper profile block renders the content at the top-centre of the user's
 * profile.
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_simplecard extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->title = "Simple card";
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        // Figure out whose profile it is.
        $objGuessUser = $this->getObject('bestguess', 'utilities');
        $ownerId = $objGuessUser->guessUserId();
        $objUser = $this->getObject('user', 'security');
        // We need to get the user key for the owner
        $objUserAdmin = $this->getObject('useradmin_model2','security');
        $user = $objUserAdmin->getUserDetails($objUser->PKId($ownerId));

        $objBizCard = $this->getObject('userbizcard', 'useradmin');
        $objBizCard->setUserArray($user);
        $objBizCard->showResetImage = FALSE;
        $objBizCard->resetModule = 'userdetails';

        return $objBizCard->show();
    }
}
?>
