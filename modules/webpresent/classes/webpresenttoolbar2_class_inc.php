<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security

class webpresenttoolbar2 extends object
{

    /**
    * Constructor
    */
    public function init()
    {

        $this->loadClass('link', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
    }

    /**
    * Method to show the Toolbar
    * @return string
    */
    public function show()
    {
        $objUser = $this->getObject('user', 'security');
        $userIsLoggedIn = $objUser->isLoggedIn();
        $objConf = $this->getObject('altconfig', 'config');
        if($objConf->getallowSelfRegister() == 'TRUE') {
            $menuOptions = array(
                array('action'=>'upload', 'text'=>'Upload', 'actioncheck'=>array('upload'), 'module'=>'webpresent', 'status'=>'both'),
                array('action'=>'home', 'text'=>'V-Meetings', 'actioncheck'=>array(), 'module'=>'realtime', 'status'=>'both'),
                array('action'=>'search', 'text'=>'Search', 'actioncheck'=>array('search'), 'module'=>'webpresent', 'status'=>'both'),
                array('action'=>NULL, 'text'=>'Blog', 'actioncheck'=>array(), 'module'=>'blog', 'status'=>'both'),
                array('action'=>NULL, 'text'=>'Admin', 'actioncheck'=>array(), 'module'=>'toolbar', 'status'=>'admin'),
                array('action'=>NULL, 'text'=>'My Details', 'actioncheck'=>array(), 'module'=>'userdetails', 'status'=>'loggedin'),
                array('action'=>'login', 'text'=>'Login', 'actioncheck'=>array('login'), 'module'=>'webpresent', 'status'=>'login'),
                array('action'=>'login', 'text'=>'Register', 'actioncheck'=>array(), 'module'=>'userregistration', 'status'=>'login'),
                array('action'=>'logoff', 'text'=>'Logout', 'actioncheck'=>array(), 'module'=>'security', 'status'=>'loggedin'),
             );
        } else { 
            $menuOptions = array(
                array('action'=>'upload', 'text'=>'Upload', 'actioncheck'=>array('upload'), 'module'=>'webpresent', 'status'=>'both'),
                array('action'=>'home', 'text'=>'V-Meetings', 'actioncheck'=>array(), 'module'=>'realtime', 'status'=>'both'),
                array('action'=>'search', 'text'=>'Search', 'actioncheck'=>array('search'), 'module'=>'webpresent', 'status'=>'both'),
                array('action'=>NULL, 'text'=>'Blog', 'actioncheck'=>array(), 'module'=>'blog', 'status'=>'both'),
                array('action'=>NULL, 'text'=>'Admin', 'actioncheck'=>array(), 'module'=>'toolbar', 'status'=>'admin'),
                array('action'=>NULL, 'text'=>'My Details', 'actioncheck'=>array(), 'module'=>'userdetails', 'status'=>'loggedin'),
                array('action'=>'login', 'text'=>'Login', 'actioncheck'=>array('login'), 'module'=>'webpresent', 'status'=>'login'),
                array('action'=>'logoff', 'text'=>'Logout', 'actioncheck'=>array(), 'module'=>'security', 'status'=>'loggedin'),
            );
        }
        $usedDefault = FALSE;
        $str = '';

        foreach ($menuOptions as $option)
        {
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
        $usedDefault = $usedDefault ? FALSE: TRUE;

        // Add Home Link
        $home = $this->generateItem(NULL, '_default', 'Home', $usedDefault);

        // Return Toolbar
        return '<div id="modernbricksmenum"><ul>'.$home.$str.'</ul>';


    }

    private function generateItem($action='', $module='webpresent', $text, $isActive=FALSE)
    {
        switch ($module)
        {
            case '_default' : $isRegistered = TRUE; break;
                default: $isRegistered = $this->objModules->checkIfRegistered($module); break;
            }

            if ($isRegistered) {
                $link = new link ($this->uri(array('action'=>$action), $module));
                $link->link = $text;

                $isActive = $isActive ? ' id="current"' : '';

                return '<li'.$isActive.'>'.$link->show().'</li>';
            } else {
                return '';
            }
        }


    }
    ?>