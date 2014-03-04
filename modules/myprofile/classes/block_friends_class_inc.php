<?php
/**
 *
 * Friends via FOAF
 *
 * Show the friends of the owner of the profile you are viewing.
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
 * Friends via FOAF
 *
 * Show the friends of the owner of the profile you are viewing.
 *
 * @category  Chisimba
 * @author    Derek Keats
 * @version
 * @copyright 2010 AVOIR
 *
 */
class block_friends extends object
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
        $this->title = "Friends";
        
    }

    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return $this->getFriendsList();
    }

    public function getFriendsList()
    {
        // Guess the user whose profile we are on.
        $objGuessUser = $this->getObject('bestguess', 'utilities');
        $userId = $objGuessUser->guessUserId();
        $objDb = $this->getObject('dbfriends', 'myprofile');
        $friends = $objDb->getFriends($userId, 1);
        // Instantiate user object to look up user image
        $objUser = $this->getObject('user', 'security');
        $ret = "<div class='myprofile_friends_wrapper'>\n<ul class='myprofile_friends'>";
        foreach ($friends as $friend) {
            $img = "<div class='userimage_cropped'>"
              . $objUser->getUserImage($friend['fuserid'], FALSE)
              . '</div>';
            $linkData = array(
                'username' => $friend['username']
            );
            $uri = $this->uri($linkData, 'myprofile');
            $link = "<a href=" . $uri . " class='profilelink'>"
              . $friend['firstname'] . " " . $friend['surname']
              . "</a><br />" . $friend['friends'];
            $ret .= "<li class='myprofile_friend'>"
              . $img . "<br />"
              . $link;
        }
        $ret = $ret . "</ul></div>";
        return $ret;
    }
}
?>
