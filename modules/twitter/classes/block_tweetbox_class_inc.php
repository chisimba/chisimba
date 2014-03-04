<?php
/**
 *
 * A tweet block for posting to Twitter
 *
 * Class to create a tweet block for posting to Twitter. If you
 * have latitude and longitude set, it will post your coordinates
 * using the Twitter Geo API.
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: block_tweetbox_class_inc.php 16137 2010-01-05 14:33:58Z dkeats $
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
 * A tweet block for posting to Twitter
 *
 * Class to create a tweet block for posting to Twitter. If you
 * have latitude and longitude set, it will post your coordinates
 * using the Twitter Geo API.
*
* @author Derek Keats
*
*/
class block_tweetbox extends object
{
    /**
    * Standard block title
    * 
    * @var string $title The title of the block
    *
    */
    public $title;

    /**
    *
    * Constructor for the class, it just sets the title.
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the title -
        $this->title='Twitter';
    }

    /**
    *
    * Method to output a Tweet block
    *
    * @return string The tweetbox or an error string
    * @access public
    *
    */
    public function show()
    {
        $objGuess = $this->getObject('bestguess', 'utilities');
        $uid = $objGuess->guessUserId();
        $objUser = $this->getObject('user', 'security');
        $myUid = $objUser->userId();
        if ($uid == $myUid) {
            $objWidjet = $this->getObject("tweetbox","twitter");
            return $objWidjet->show();
       } else {
            $objLanguage = $this->getObject('language', 'language');
            return $objLanguage->languageText("mod_twitter_notyours", "twitter");
       }
    }

}
?>