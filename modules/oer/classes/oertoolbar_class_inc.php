<?php
/**
 * This class contructs the tool bar used  in the platform. Each menu forms 
 * a tab, which has associated actions makes the tab selected if they 
 * correspond to current action
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
 * @version    0.001
 * @package    oer
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security

class oertoolbar extends object {

    /**
     * Constructor
     */
    public function init() {

        $this->loadClass('link', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Method to show the Toolbar
     * @return string
     */
    public function show() {
        $objUser = $this->getObject('user', 'security');
        $userIsLoggedIn = $objUser->isLoggedIn();

        $menuOptions = array(
            array('action' => 'home', 
                'text' => $this->objLanguage->languageText('mod_oer_products', 'oer'),
                'actioncheck' => array(
                    "home","vieworiginalproduct","filteroriginalproduct",
                    "showproductlistingaslist","editoriginalproductstep1","editoriginalproductstep2",
                    "editoriginalproductstep3","editoriginalproductstep4","filteroriginalproduct",
                    "deleteoriginalproduct"), 
                'module' => 'oer', 'status' => 'both', 'isDefaultSelected' => 'true'),
            
            array('action' => 'adaptationlist', 
                'text' => $this->objLanguage->languageText('mod_oer_adaptations', 'oer'), 
                'actioncheck' => array("adaptationlist","viewadaptation",
                    "editadaptationstep1","editadaptationstep2","editadaptationstep3","editadaptationstep4",
                    "viewinstitution","filteradaptation","saveadaptationstep1","saveadaptationstep2","saveadaptationstep3",
                    "saveadaptationstep4","viewadaptationbymap"
                    ),
                
                'module' => 'oer', 'status' => 'both', 'isDefaultSelected' => 'false'),
            array('action' => 'viewgroups', 
                'text' => $this->objLanguage->languageText('mod_oer_groups', 'oer'),
                'actioncheck' => array("viewgroups","viewgroup","editgroupstep1","editgroupstep2","editgroupstep3"
                    ), 
                'module' => 'oer', 'status' => 'both', 'isDefaultSelected' => 'false'),
            array('action' => 'viewreports', 
                'text' => $this->objLanguage->languageText('mod_oer_reporting', 'oer'), 
                'actioncheck' => array("viewreports"),
                'module' => 'oer', 'status' => 'both', 'isDefaultSelected' => 'false'),
            array('action' => 'view', 
                'text' => $this->objLanguage->languageText('mod_oer_about', 'oer'),
                'actioncheck' => array("about","view","edit"), 
                'module' => 'about', 'status' => 'both', 'isDefaultSelected' => 'false'),
            
            array('action' => 'contacts', 
                'text' => $this->objLanguage->languageText('mod_oer_contacts', 'oer'), 
                'actioncheck' => array("contacts"), 
                
                'module' => 'oer', 'status' => 'both', 'isDefaultSelected' => 'false'),
            array('action' => 'cpanel',
                'text' => $this->objLanguage->languageText('mod_oer_admin', 'oer'), 
                'actioncheck' => array(
                    "cpanel","modulecatalogue","toolbar","institutionlisting",
                    "institutionedit","modulecatalogue"), 
                
                'module' => 'oer', 'status' => 'admin', 'isDefaultSelected' => 'false'),
        );


        $str = '';

        foreach ($menuOptions as $option) {
            // First Step, Check whether item will be added to menu
            // 1) Check Items to be Added whether user is logged in or not
            if ($option['status'] == 'both') {
                $okToAdd = TRUE;

                // 2) Check Items to be added only if user is not logged in
            } else if ($option['status'] == 'login' && !$userIsLoggedIn) {
                $okToAdd = TRUE;

                // 3) Check Items to be added only if user IS logged in
            } else if ($option['status'] == 'loggedin' && $userIsLoggedIn) {
                $okToAdd = TRUE;

                // 4) Check if User is Admin
            } else if ($option['status'] == 'admin' && $objUser->isAdmin() && $userIsLoggedIn) {
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

                // Check whether Navigation has Current/Highlighted item
                if ($this->getParam("action") =='') {
                    if ($option['isDefaultSelected'] == 'true') {
                        $isDefault = TRUE;
                    }
                }
                // Add to Navigation
                $str .= $this->generateItem($option['action'], $option['module'], $option['text'], $isDefault);
            }
        }




        // Return Toolbar
        return '<div id="modernbricksmenu"><ul>' . $str . '</ul></div>';
    }

    /**
     * this generates a menu with is displayed as a tab in the platform
     * @param type $action the action hooked to tab when clicked
     * @param type $module the module to map action to
     * @param type $text what to display in tab
     * @param type $isActive whether this tab should be selected or not
     * @return type 
     */
    private function generateItem($action = '', $module = 'oer', $text, $isActive = FALSE) {
        switch ($module) {
            case '_default' : $isRegistered = TRUE;
                break;
            default: $isRegistered = $this->objModules->checkIfRegistered($module);
                break;
        }

        if ($isRegistered) {
            $link = new link($this->uri(array('action' => $action), $module));
            $link->link = $text;

            $isActive = $isActive ? ' id="current"' : '';


            return '<li' . $isActive . '>' . $link->show() . '</li>';
        } else {
            return '';
        }
    }

}

?>