<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security

class podcastertoolbar2 extends object {

    /**
     * Constructor
     */
    public function init() {

        $this->loadClass('link', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject("user", "security");
    }

    /**
     * Method to show the Toolbar
     * @return string
     */
    public function show() {        
        $userIsLoggedIn = $this->objUser->isLoggedIn();

        $menuOptions = array(
            array('action' => 'steponeupload', 'text' => 'Upload', 'actioncheck' => array('upload'), 'module' => 'podcaster', 'status' => 'loggedin'),
            array('action' => 'myuploads', 'text' => 'My Uploads', 'actioncheck' => array('myuploads'), 'module' => 'podcaster', 'status' => 'loggedin'),
            array('action' => 'myevents', 'text' => 'My Events', 'actioncheck' => array('myevents'), 'module' => 'podcaster', 'status' => 'loggedin'),
            array('action' => 'openevents', 'text' => 'Open events', 'actioncheck' => array('openevents'), 'module' => 'podcaster', 'status' => 'loggedin'),
            array('action' => 'publicevents', 'text' => 'Public events', 'actioncheck' => array('publicevents'), 'module' => 'podcaster', 'status' => 'both'),
            array('action' => 'viewcategories', 'text' => 'Categories', 'actioncheck' => array('viewcategories'), 'module' => 'podcaster', 'status' => 'admin'),
            array('action' => 'search', 'text' => 'Search', 'actioncheck' => array('search'), 'module' => 'podcaster', 'status' => 'both'),
            array('action' => 'search', 'text' => 'Search', 'actioncheck' => array('search'), 'module' => 'podcaster', 'status' => 'both'),
            array('action' => 'search', 'text' => 'Search', 'actioncheck' => array(), 'module' => 'podcaster', 'status' => 'both'),
            array('action' => 'viewtranslation', 'text' => 'Public', 'id'=>'gen10Srv19Nme48_21453_1303304657', 'actioncheck' => array(), 'module' => 'stories', 'status' => 'both'),
            array('action' => 'viewtranslation', 'text' => 'Open', 'id' => 'gen10Srv19Nme48_9544_1303305608', 'actioncheck' => array(), 'module' => 'stories', 'status' => 'both'),
            array('action' => 'viewtranslation', 'text' => 'Private', 'id' => 'gen10Srv19Nme48_82952_1303305695', 'actioncheck' => array(), 'module' => 'stories', 'status' => 'both'),
            array('action' => NULL, 'text' => 'Admin', 'actioncheck' => array(), 'module' => 'toolbar', 'status' => 'admin'),
            array('action' => NULL, 'text' => 'My Details', 'actioncheck' => array(), 'module' => 'userdetails', 'status' => 'loggedin'),
            array('action' => 'login', 'text' => 'Login', 'actioncheck' => array('login'), 'module' => 'podcaster', 'status' => 'login'),
            array('action' => 'login', 'text' => 'Register', 'actioncheck' => array(), 'module' => 'userregistration', 'status' => 'login'),
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
            } else if ($option['status'] == 'admin' && $this->objUser->isAdmin() && $userIsLoggedIn) {
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

        // Add Home Link
        $home = $this->generateItem(NULL, 'podcaster', 'Home', $usedDefault);


        // Return Toolbar
        return '<div id="modernbricksmenum"><ul>' . $home . $str . '</ul>';
    }

    private function generateItem($action='', $module='podcaster', $text, $isActive=FALSE) {
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