<?php

class vowfmtoolbar extends object {

    function init() {
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->menuDropDown = $this->newObject('dropdown', 'htmlelements');
    }

    function show() {
        $menu = '
    <div class="rbroundbox">
    <div class="rbtop"><div></div></div>
    <div class="rbcontent">
  ' . $this->createMenu() . '
  
    </div><!-- /rbcontent -->
    <div class="rbbot"><div></div></div>
    </div><!-- /rbroundbox -->';
        return $menu;
    }

    function createMenu() {
        $objUser = $this->getObject('user', 'security');
        $userIsLoggedIn = $objUser->isLoggedIn();

        $menuOptions = array(
            array('action' => 'logoff', 'text' => 'Logout', 'actioncheck' => array(), 'module' => 'security', 'status' => 'loggedin'),
        );

        $usedDefault = FALSE;
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

                if ($isDefault) {
                    $usedDefault = TRUE;
                }

                // Add to Navigation
                $str .= $this->generateItem($option['action'], $option['module'], $option['text'], $isDefault);
            }
        }

        // Check whether Navigation has Current/Highlighted item
        // Invert Result for Home Link
        $usedDefault = $usedDefault ? FALSE : TRUE;

        // Add all links
        $tbar =
                $this->generateItem(NULL, 'vowfm', 'Home', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Music', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'DJs and Shows', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Competitions', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Events and News', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Videos', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Advertise', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Contact Us', $usedDefault) .
                $this->generateItem(NULL, 'vowfm', 'Jobs', $usedDefault);


        // Return Toolbar
        return '<div id="nav"><ul>' . $tbar . $str . '</ul></div>';
    }

    private function generateItem($action='', $module='vowfm', $text, $isActive=FALSE) {
        switch ($module) {
            case 'vowfm' : $isRegistered = TRUE;
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
