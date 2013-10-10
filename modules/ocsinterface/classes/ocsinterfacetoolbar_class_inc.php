<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security

class ocsinterfacetoolbar extends object {

    /**
     * Constructor
     */
    public function init() {

        $this->loadClass('link', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->storyparser = $this->getObject('ocsstoryparser');
    }

    /**
     * Method to show the Toolbar
     * @return string
     */
    public function show() {
        $objUser = $this->getObject('user', 'security');
        $userIsLoggedIn = $objUser->isLoggedIn();

        $menuOptions = array();

        $topcatid = $this->objDbSysconfig->getValue('TOP_NAV_CATEGORY', 'ocsinterface');
        $topnavs = $this->storyparser->getStoryByCategory($topcatid);

        foreach ($topnavs as $nav) {

            $menuOptions[] = array('action' => 'viewstory', 'storyid' => $nav['id'], 'text' => $nav['title'], 'actioncheck' => array(), 'module' => 'ocsinterface', 'status' => 'both');
        }

        $menuOptions[] = array('action' => NULL, 'text' => 'Content Admin', 'actioncheck' => array(), 'module' => 'stories', 'status' => 'admin');
        $menuOptions[] = array('action' => NULL, 'text' => 'File manager', 'actioncheck' => array(), 'module' => 'filemanager', 'status' => 'loggedin');
        $menuOptions[] = array('action' => NULL, 'text' => 'Admin', 'actioncheck' => array(), 'module' => 'toolbar', 'status' => 'admin');
        $menuOptions[] = array('action' => NULL, 'text' => 'My details', 'actioncheck' => array(), 'module' => 'userdetails', 'status' => 'loggedin');

        $menuOptions[] = array('action' => 'logoff', 'text' => 'Logout', 'actioncheck' => array(), 'module' => 'security', 'status' => 'loggedin');



        $usedDefault = FALSE;
        $str = '';
        $menuCount = 2;
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
                $str .= $this->generateItem($option['action'], $option['module'], $option['text'], $isDefault, $option['storyid'], $menuCount);

                $menuCount++;
            }
        }

        // Check whether Navigation has Current/Highlighted item
        // Invert Result for Home Link
        $usedDefault = $usedDefault ? FALSE : TRUE;

        // Add Home Link
        $home = $this->generateItem(NULL, '_default', 'Home', $usedDefault, 1);


        // Return Toolbar
        return '<ul id="navigationbar">' . $home . $str . '</ul>';
    }

    private function generateItem($action='', $module='ocsinterface', $text, $isActive=FALSE, $storyid='', $menucount=1) {
        switch ($module) {
            case '_default' : $isRegistered = TRUE;
                break;
            default: $isRegistered = $this->objModules->checkIfRegistered($module);
                break;
        }

        if ($isRegistered) {
            $link = new link($this->uri(array('action' => $action, 'storyid' => $storyid), $module));

            $link->extra = 'onMouseOver="mopen(\'m' . $menucount . '\')"   onMouseOut="mclosetime()"';
            $link->link = $text;
            $isActive = $isActive ? ' id="current"' : '';

            $xmenu = '<li' . $isActive . '>';
            $xmenu.=$link->show();

            $submenus = $this->storyparser->getStoryByCategory($text);
            $xmenu.='<div id="m' . $menucount . '"  onMouseOver="mcancelclosetime()" onMouseOut="mclosetime()">';

            foreach ($submenus as $submenu) {
                $menulink = new link($this->uri(array("action" => "viewstory", "storyid" => $submenu['id'])));
                $menulink->link = $submenu['title'];
                $xmenu.= $menulink->show();
            }
            $xmenu.=' </div>';

            $xmenu.='</li > ';

            return $xmenu;
        } else {
            return '';
        }
    }

}

?>