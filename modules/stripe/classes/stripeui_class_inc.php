<?php
/**
 *
 * Build the stripe 
 *
 * Build the stripe for use to create a bar that can be installed at the 
 * top of the page or bottom of the page to integrate messaging and 
 * related functionality.
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
 * @package   stripe
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Build the stripe 
 *
 * Build the stripe for use to create a bar that can be installed at the 
 * top of the page or bottom of the page to integrate messaging and 
 * related functionality.
*
* @package   stripe
* @author    Derek Keats derek@dkeats.com
*
*/
class stripeui extends object
{
    /**
     *
     * @var string The prepared DIV for the stripe
     * @access private
     *
     */
    private $stripeDiv;
    private $objLanguage;
    private $objUser;
    private $objModule;

    /**
    *
    * Intialiserfor the stripe
     * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Setup the initial stripe with its placeholders.
        $this->getRawStripe();
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject("user", "security");
        $this->objModule = $this->getObject('modules','modulecatalogue');

    }
    
    public function show()
    {
        $this->showNumberOfUsers();
        $this->showInternalMails();
        $this->showSiteName();
        
        $this->showHomeLink();
        $this->showMeLink();
        
        
        $this->stripeDiv = str_replace('#LEFTPLACEHOLDER#', NULL, $this->stripeDiv);
        $this->stripeDiv = str_replace('#RIGHTPLACEHOLDER#', NULL, $this->stripeDiv);
        return $this->stripeDiv;
    }

    /**
     *
     * Sett $this->stripeDiv as the DIV that houses the stripe
     *
     * @return boolean TRUE
     * @access private
     *
     */
    private function getRawStripe()
    {
        $this->stripeDiv = "<div id='chisimba_stripe'>"
          . "<div id='stripe_leftside'>#LEFTPLACEHOLDER#</div>"
          . "<div id='stripe_rightside'>#RIGHTPLACEHOLDER#</div>"
          . "</div>";
        return TRUE;
    }
    
    public function addItem($string, $position='left')
    {
        switch ($position) {
            case 'left':
                $replacement = '#LEFTPLACEHOLDER#' . $string;
                $this->stripeDiv = str_replace(
                  '#LEFTPLACEHOLDER#', $replacement, $this->stripeDiv
                );
                break;
            case 'right':
            default:
                $replacement = $string . '#RIGHTPLACEHOLDER#';
                $this->stripeDiv = str_replace(
                  '#RIGHTPLACEHOLDER#', $replacement, $this->stripeDiv
                );
                break;
        }
    }
    
    private function showSpacer($side='left')
    {
        $this->addItem('<span>&nbsp;&nbsp;</span>',  $side);
    }
    
    /**
     * 
     * Show an item on the stripe that displays internal emails.
     * @access private
     * @return string Adds the internal mails to the raw stripe
     */
    public function showInternalMails()
    {
        $mailDiv = "<div id='stripe_mail'><p>1</p></div>";
        $ret = $this->addToSpan($mailDiv);
        $this->addItem($ret, 'left');
    }
    
    public function showNumberOfUsers()
    {
        
        $objDb = $this->getObject('loggedinusers', 'security');
        $users = $objDb->getActiveUserCount();
        $arUsers = $objDb->getLastFiveOnlineUsers();
        $whoAreThey = NULL;
        if (!empty ($arUsers)) {
           foreach ($arUsers as $user) {
               $theUser = $user['firstname'] . " " . $user['surname'] . "<br />";
               $whoAreThey .= $theUser;
           }
        } else {
            $whoAreThey .= "Aint nobody on now, dude. What, you too dumb to see the zero?";
        }
        $usersDiv = "<div id='stripe_users'><p>$users</p></div>";
        $ret = $this->addToSpan($usersDiv);
        $ret .= "<div id='stripe_userson'>$whoAreThey</div>";
        $this->addItem($ret, 'left');
    }
    
    /**
     * 
     * Show the site name in the stripe
     * 
     * @access private
     * @return void
     * 
     */
    private function showSiteName()
    {
        $homeIcon = $this->getResourceUri('icons/sitehome.png', 'stripe');
        $homeIcon = "<img src='$homeIcon'/>";
        $objConfig = $this->getObject('altconfig', 'config');
        $siteName = $objConfig->getsiteName();
        $homeUri = $this->uri(array(),'_default');
        $homeLink = new link($homeUri);
        $homeLink->link = $siteName;
        $homeLink->cssClass = 'stripe_sitename';
        $ret = $this->addToSpan($homeIcon . " " . $homeLink->show());
        $this->addItem($ret, 'left');
    }
    
    private function showHomeLink()
    {
        $homeIcon = $this->getResourceUri('icons/home.png', 'stripe');
        $homeIcon = "<img src='$homeIcon'/>";
        $homeUri = $this->uri(array(),'_default');
        $homeLink = new link($homeUri);
        $homeLink->link = $homeIcon . $this->objLanguage->languageText('word_home', 'system');
        $ret = $this->addToSpan($homeLink->show());
        $this->addItem($ret, 'right');
    }
    
    /**
     * 
     * Show the logged in user on the stripe
     * @access private
     * @return VOID
     * 
     */
    private function showMeLink()
    {
        $me = $this->objUser->fullName();
        if ($this->objUser->isLoggedIn()) {
            $uid = $this->objUser->userId();
            $meIcon = $this->objUser->getSmallUserImage($uid, FALSE);
            $isRegistered = $this->objModule->checkIfRegistered('userdetails', 'userdetails');
            if ($isRegistered) {
                $myUri = $this->uri(array(),'userdetails');
                $meLink = new link($myUri);
                $meLink->link = $meIcon. " " . $me;
                $me = $meLink->show();
            }
        } else {
            $meIcon = $this->getResourceUri('icons/user.png', 'stripe');
            $meIcon = "<img src='$meIcon'/>";
            $me = $meIcon . $me;
        }
        $me = $this->addToSpan($me);
        $this->addItem($me, 'right');
    }
    
    /**
     * 
     * Add an item to the span that can be used to format margins, etc.
     * 
     * @param type $itemTxt
     * @return type 
     * 
     */
    private function addToSpan($itemTxt)
    {
        return "<div class='stripe_item'>" 
          . $itemTxt . "</div>";
    }

}
?>