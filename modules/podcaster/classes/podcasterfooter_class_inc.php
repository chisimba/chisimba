<?php

/*
 *
 * A class to display the footer of the wits podcaster skin.
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
 * @package   podcaster
 * @author    Nguni Phakela nonkululeko.phakela@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: podcasterfooter_class_inc.php,v 1.1 2011-03-28 09:13:27 nguni52 Exp $
 * @link      http://avoir.uwc.ac.za
 *
 *
 *
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security

class podcasterfooter extends object {

    // news stories object
    private $objNews;
    // stories for rotating banners at the top
    private $stories;
    // path to root folder of skin
    private $skinpath;
    // object for user details
    private $objUser;

    /**
     * Constructor
     */
    public function init() {
        $this->objCategory = $this->getObject('dbnewscategories', 'news');
        $this->objNews = $this->getObject('dbnewsstories', 'news');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Method to show the Toolbar
     * @param string $skinpath the default skinpath for elsi skin
     * @return none
     * @access public
     */
    public function setSkinPath($skinpath) {
        $this->skinpath = $skinpath;
    }

    /*
     * Method to display the footer of elsiskin
     * @return string $retstr which has the footer for the skin
     * @access public
     */

    public function showLogo() {
        $chisimbaLink = new link("http://www.chisimba.com");
        $chisimbaLink->link = '<img src ="' . $this->skinpath . 'images/powered_by_chisimba.png" alt="Powered By Chisimba" title="Powered By Chisimba" />';

        $retstr = '
           <!-- Start: Footer -->
            <div id="Footer">

                <div id="chisimbapower">
                ' . $chisimbaLink->show() . '
                </div>
                <!-- end .grid_4 -->
                <div class="clear">&nbsp;</div>
                <div class="grid_4">
                </div>
            </div>
            <!-- End: Footer -->
            <div class="clear">&nbsp;</div>';

        return $retstr;
    }

    /*
     * Method to display the footer of elsiskin
     * @return string $retstr which has the footer for the skin
     * @access public
     */

    public function show() {
        if ($this->objUser->isLoggedIn()) {
            $links = array(
                'home' => 'Home',
                'upload' => 'Upload',
                'search' => 'Search',
                'admin' => 'Admin',
                'mydetals' => 'My Details',
                'postlogin' => 'Login'
            );
        } else {
            $links = array(
                'home' => 'Home',
                'search' => 'Search',
                'postlogin' => 'Login',
                'news' => 'Register'
            );
        }


        $retstr = '
           <!-- Start: Footer -->
            <div id="Footer">
                <!-- end .grid_4 -->
                <div class="clear">&nbsp;</div>
                <div class="grid_4"> ';
        $retstr .= " | ";
        foreach ($links as $key => $index) {

            $eachLink = new link($this->uri(array('action' => $key)));
            $eachLink->link = $index;
            if ($key == 'postlogin') {
                $eachLink = $this->objUser->isLoggedIn() ?
                        new link($this->uri(array("action" => "logoff"), "security")) :
                        new link($this->uri(array('action' => 'home'), 'postlogin'));
                $eachLink->link = $this->objUser->isLoggedIn() ? "Log Out" : "Log In";
            }


            $retstr .= $eachLink->show() . " | ";
        }
        $retstr .= '
                </div>

            </div>
            <!-- End: Footer -->
            <div class="clear">&nbsp;</div>';

        return $retstr;
    }

}