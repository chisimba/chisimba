<?php
/*
 *
 * A class to display the toolbar for the elsi skin.
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
 * @package   elsiskin
 * @author    Nguni Phakela nonkululeko.phakela@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: elsiskintoolbar_class_inc.php,v 1.1 2011-01-12 09:13:27 nguni52 Exp $
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

class podcastertoolbar1 extends object {

    /**
     * Constructor
     */
    public function init() {
        $this->loadClass('link', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
    }

    /**
     * Method to show the Toolbar
     * @return string $retstr containing all the toolbar links.
     */
    public function show() {
        $objUser = $this->getObject('user', 'security');
        $userIsLoggedIn = $objUser->isLoggedIn();
        $menuOptions = array(
            array('action'=>'steponeupload', 'text'=>'Upload', 'actioncheck'=>array('upload'), 'module'=>'podcaster', 'status'=>'loggedin'),
            array('action'=>'search', 'text'=>'Search', 'actioncheck'=>array('search'), 'module'=>'podcaster', 'status'=>'both'),
            array('action'=>NULL, 'text'=>'Admin', 'actioncheck'=>array(), 'module'=>'toolbar', 'status'=>'admin'),
            array('action'=>NULL, 'text'=>'My Details', 'actioncheck'=>array(), 'module'=>'userdetails', 'status'=>'loggedin'),
            array('action'=>'login', 'text'=>'Login', 'actioncheck'=>array('login'), 'module'=>'podcaster', 'status'=>'login'),
            array('action'=>'login', 'text'=>'Register', 'actioncheck'=>array(), 'module'=>'userregistration', 'status'=>'login'),
            array('action'=>'logoff', 'text'=>'Logout', 'actioncheck'=>array(), 'module'=>'security', 'status'=>'loggedin')
        );
        $str = "";
        $usedDefault = FALSE;
        $count = 1;
        foreach ($menuOptions as $option) {
            if ($option['status'] == 'both') {
                $okToAdd = TRUE;
            } else if ($option['status'] == 'login' && !$userIsLoggedIn) {
                // 2) Check Items to be added only if user is not logged in
                $okToAdd = TRUE;
            } else if ($option['status'] == 'loggedin' && $userIsLoggedIn) {
                // 3) Check Items to be added only if user IS logged in
                $okToAdd = TRUE;
            } else if ($option['status'] == 'admin' && $objUser->isAdmin() && $userIsLoggedIn) {
                // 4) Check if User is Admin
                $okToAdd = TRUE;
            } else {
                $okToAdd = FALSE; // ELSE FALSE
            }

            // IF Ok To Add
            if ($okToAdd) {
                // Do a check if current action matches possible actions
                if (count($option['actioncheck']) == 0) {
                    $actionCheck = TRUE; // No Actions, set TRUE, to enable all actions and fo module check
                } else {
                    $actionCheck = in_array($this->getParam('action'), $option['actioncheck']);
                }

                // Check whether Module of Link Matches Current Module
                $moduleCheck = ($this->getParam('module') == $option['module']) ? TRUE : FALSE;

                // If Module And Action Matches, item will be set as current action
                $isDefault = ($actionCheck && $moduleCheck) ? TRUE : FALSE;


                /* if($this->getParam('action') == $option['action']) {
                  $isDefault = TRUE;
                  }
                 */
                if ($isDefault) {
                    $usedDefault = TRUE;
                }

                // Add to Navigation
                $str .= $this->generateItem($option['action'], $option['module'], $option['text'], $isDefault, $count);
                $count++;
            }
        }
        // Check whether Navigation has Current/Highlighted item
        // Invert Result for Home Link
        $usedDefault = $usedDefault ? FALSE : TRUE;

        // Add Home Link
        $home = $this->generateItem('home', 'podcaster', 'Home', $usedDefault);

        // Return Toolbar
        $retstr = $home . $str;

        return $retstr;
    }

    /*
     * Method to generate each item on the toolbar based on different attributes
     * @param $action the action to which this menu item is associated
     * @param $module the module to which this menu item is associated
     * @param $text The actual text that will appear on the page
     * @param isActive whether the menuitem is active or not
     * @param $count keeps track of how many tabs so far
     * @access private
     * @return string toolbar
     */
    private function generateItem($action='', $module='_default', $text, $isActive=FALSE, $count=NULL) {
        switch ($module) {
            case '_default' : $isRegistered = TRUE;
                break;
            default: $isRegistered = $this->objModules->checkIfRegistered($module);
                break;
        }
        $tabClass = "";
        if ($count > 0) {
            $tabClass .= ' class="level1-li"';
        }

        if ($action == "home") {
            $tabClass .= ' class="level1-li"';
        }

        if ($isRegistered) {
            $link = new link($this->uri(array('action' => $action), $module));
            $link->link = $text;
            $link->cssClass = "level1-a";

            return '<li' . $tabClass . '>' . $link->show() . '</li>';
        } else {
            return '';
        }
    }
}

?>