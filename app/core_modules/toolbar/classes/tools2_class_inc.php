<?php

// security check - must be included in all scripts
if (! $GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}

/**
 * The toolbar class provides functions used in displaying the navigation for KEWL.nextgen.
 *
 * The class provides the following methods:
 * 1) check() A method to determine whether the user is logged in.
 * 2) checkPermissions($module, $context) A method to check the modules permissions.
 * 3) addIM() A method to display an icon on the toolbar for access to instant messaging.
 * 4) addPause() Method to add the keep session alive icon.
 * 5) userRole() A method to display the current user name and role (in the footer).
 * 6) addToBreadCrumbs($links) Method to add a string to the breadcrumbs.
 * 7) insertBreadCrumb($links) Method to insert a list of links or strings into the breadcrumbs.
 * 8) navigation() A method to display the breadcrumbs for the site.
 * 9) makeBreadCrumbs($home, $module, $moduleInfo) Method to make the breadcrumbs.
 * 10) addText($module) Method to get the module name in the correct language.
 * 11) addLink($module, $text = NULL, $divider = ' &raquo; ') Method to add a link to the breadcrumbs, add the divider in front.
 * 12) divide($link, $divider = ' &raquo; ') Method to add a specified divider in front of a link or text.
 *
 * @author Megan Watson
 * @author Tohir Solomons
 * @author Paul Scott <pscott@uwc.ac.za>
 * @copyright (c)2004 UWC
 * @package toolbar
 * @version 0.9
 */
class tools extends object
{
    /**
     * @var $params Holds additional parameters for the header / body onload of the page template
     * @access public
     */
    public $params = array ();

    /**
     * Method to construct the class
     */

    public function init()
    {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objConfig = $this->getObject ( 'altconfig', 'config' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->objIcon = $this->newObject ( 'geticon', 'htmlelements' );
        $this->objLink = $this->newObject ( 'link', 'htmlelements' );
        $this->objSkin = $this->newObject ( 'skin', 'skin' );
        $this->moduleCheck = $this->newObject ( 'modules', 'modulecatalogue' );
        $this->objHelp = $this->getObject ( 'help', 'help' );
        //$this->objCond = $this->newObject('contextCondition','contextpermissions');
        // $this->objPerm = $this->newObject('permissions_model','permissions');
        $this->contextObject = $this->getObject ( 'dbcontext', 'context' );
        $this->contextCode = $this->contextObject->getContextCode ();
        $this->contextTitle = $this->contextObject->getTitle ();
        $this->contextMenu = $this->contextObject->getMenuText ();
        $this->loadClass ( 'link', 'htmlelements' );
        $this->objPerms = $this->getObject('perms', 'permissions');
        $this->objPerms->outputRights();
    }

    /**
     * Method to perform a security check to ensure that user is logged in before toolbar is visible.
     * @return bool $var
     */
    public function check()
    {
        $requiresLogin = $this->objSysConfig->getValue ( 'TOOLBAR_REQUIRES_LOGIN', 'toolbar' );
        if ($requiresLogin == 'FALSE') {
            return TRUE;
        }
        $mod = $this->getParam ( 'module' );
        $act = $this->getParam ( 'action' );
        if (! ($mod == "security" && $act == "logoff")) {
            $objSecurity = $this->getObject ( "user", "security" );
            $var = $this->objLu->isLoggedIn();

            return $var;
        }
        return false;
    }

    /**
     * Method to check whether the user has permission to access the module.
     * @param array $module The module to be displayed.
     * @param bool $context True when in a context, false if not.
     */
    public function checkPermissions($module, $context) {
        // $check = $this->objPerms->checkRule($rule, $module);
        return TRUE;
    }

    /**
     * Method to add the instant messaging icon.
     * The onclick method for the icon opens a new window containing the instant messaging page.
     * @return string $objLink The linked icon
     */
    public function addIM() {
        $showIM = $this->objSysConfig->getValue ( 'DISPLAY_IM', 'messaging' );
        if ($this->moduleCheck->checkIfRegistered ( 'messaging' ) && $showIM == 'TRUE') {
            $objIm = $this->getObject ( 'chatdisplay', 'messaging' );
            // Get the additional parameters (headerparams and bodyonload), save in a global variable
            $this->params = $objIm->imParams ();
            return $objIm->divShowIM ();
        }
        else {
            return FALSE;
        }
    }

    /**
     * Method to add the keep session alive icon.
     * The onclick method for the icon opens a new window containing the page that keeps the
     * session alive.
     * @return string $objLink The linked icon
     */
    public function addPause() {
        if ($this->moduleCheck->getRow ( 'module_id', 'keepsessionalive' )) {
            $this->objIcon->setIcon ( 'keep_alive' );
            $this->objIcon->alt = 'mod_pause_stayonline';
            $this->objIcon->title = $this->objLanguage->languageText ( 'mod_keepsessionalive_stayonline', 'keepsessionalive' );

            $this->objLink = new link ( 'javascript:;' );
            $this->objLink->link = $this->objIcon->show ();
            $url = $this->uri ( array ('action' => 'stayonline' ), 'keepsessionalive', '', FALSE, TRUE );
            $this->objLink->extra = " onclick=\"javascript:openWindow('{$url}','stayon','scrollbars=yes,width=340,height=130')\" ";

            return $this->objLink->show ();
        }
        else {
            return FALSE;
        }
    }

    /**
     * Method to display the help icon for accessing the viewlets.
     */
    public function getHelp()
    {
        // Get module name and current action
        $module = $this->getParam ( 'module', FALSE );
        $action = $this->getParam ( 'action', '' );
        if ($module == '' || $module == '_default') {
            $module = $this->objConfig->getdefaultModuleName ();
        }
        if ($module === FALSE) {
            return FALSE;
        }
        // Return the linked help icon
        $help = $this->objHelp->show ( $action, $module );
        $layer = $this->newObject ( 'layer', 'htmlelements' );
        $layer->cssClass = 'bannerhelp';
        $layer->id = 'bannerhelp';
        $layer->str = $help;
        $layer->display = NULL;
        $layer->cursor = NULL;

        return $layer->show ();
    }

    /**
     * Method to display the user name and role/status in the page footer.
     */
    public function userRole()
    {
        $login = $this->objLanguage->languageText ( 'mod_toolbar_loggedin', 'toolbar' );
        $role = $this->objLanguage->languageText ( 'mod_toolbar_role', 'toolbar' );
        $user = $this->objUser->fullname ();

        $this->objLink = new link ( $this->uri ( array ('action' => 'logout' ), 'security' ) );
        $this->objLink->link = $this->objLanguage->languageText ( 'word_logout', 'security', 'Logout' );
        $logout = $this->objLink->show ();
        $roleName = $this->objLanguage->languageText ( 'mod_toolbar_guest', 'toolbar' );
        if ($this->objUser->isAdmin ()) {
            $roleName = $this->objLanguage->languageText ( 'mod_toolbar_administrator', 'toolbar' );
        } elseif ($this->contextObject->isInContext ()) {
            if ($this->objPerms->isContextMember ( 'Lecturers' )) {
                $roleName = $this->objLanguage->languageText ( 'mod_toolbar_lecturer', 'toolbar' );
            } else if ($this->objPerms->isContextMember ( 'Students' )) {
                $roleName = $this->objLanguage->languageText ( 'mod_toolbar_student', 'toolbar' );
            }
        } else if ($this->objPerms->isMember ( 'Lecturers' )) {
            $roleName = $this->objLanguage->languageText ( 'mod_toolbar_lecturer', 'toolbar' );
        } else if ($this->objPerms->isMember ( 'Students' )) {
            $roleName = $this->objLanguage->languageText ( 'mod_toolbar_student', 'toolbar' );
        }

        return "$login <b>$user</b> ($logout) | $role <b>$roleName</b>";
    }

    /**
     * Method to add a list of links or strings to the breadcrumbs.
     * List replaces the action.
     *
     * @param array $links A list of strings to be added to the breadcrumbs.
     */
    public function addToBreadCrumbs($links)
    {
        $divider = ' &raquo; ';
        if (! empty ( $links )) {
            $list = implode ( $divider, $links );
        }
        $this->setSession ( 'breadcrumbs', $list );
    }

    /**
     * Method to add a list of links or strings to the breadcrumbs.
     * List replaces the action.
     *
     * @param array $links A list of strings to be added to the   breadcrumbs.
     */
    public function replaceBreadCrumbs($links)
    {
        $divider = ' &raquo; ';
        if (! empty ( $links )) {
            $list = implode ( $divider, $links );
        }
        $this->setSession ( 'replacebreadcrumbs', $list );
    }

    /**
     * Method to insert a list of links or strings into the breadcrumbs before the module name.
     *
     * @param array $links A list of strings to be inserted into the breadcrumbs.
     */
    public function insertBreadCrumb($links)
    {
        $divider = ' &raquo; ';
        if (! empty ( $links )) {
            $list = implode ( $divider, $links );
        }
        $this->setSession ( 'crumb', $list );
    }

    /**
     * Method to disable the link for the module name.
     */
    public function disableModuleLink()
    {
        $this->setSession ( 'modlink', TRUE );
    }

    /**
     * Method to provide the breadcrumbs on the menu.
     *
     * @return string $nav The breadcrumbs
     */
    public function navigation()
    {
        $replaceCrumbs = $this->getSession ( 'replacebreadcrumbs' );
        if (isset ( $replaceCrumbs ) && ! empty ( $replaceCrumbs )) {
            $this->unsetSession ( 'replacebreadcrumbs' );
            return $replaceCrumbs;
        }
        // Language
        $home = $this->objLanguage->languageText ( 'word_home', 'system', 'Home' );
        $welcome = $this->objLanguage->languageText ( 'mod_toolbar_welcome', 'toolbar' );
        // Get the module name
        $module = $this->getParam ( 'module' );
        // Get settings information for the module
        $moduleInfo = $this->moduleCheck->getRow ( 'module_id', $module );
        if (empty ( $moduleInfo ) && ! ($module == '_default' || $module == 'postlogin' || $module == '')) {
            $noModule = $this->objLanguage->code2Txt ( 'mod_toolbar_modnotfound', 'toolbar', array ('module' => "<b>$module</b>" ) );
            return $noModule;
        }
        // If the module is the default module
        if ($module == '_default' || $module == 'postlogin' || $module == '') {
            //$nav = $welcome.' ';
            if ($this->objUser->isLoggedIn ()) {
                $nav = $welcome . ' ' . $this->objUser->fullname ();
            } else {
                $nav = '';
            }
        } else {
            // set the link to the default module
            $this->objLink = new link ( $this->uri ( '', '_default' ) );
            $this->objLink->link = $home;
            $this->objLink->cssClass = 'homelink';
            $home = $this->objLink->show ();
            $nav = $this->makeBreadCrumbs ( $home, $module, $moduleInfo );
        }

        return $nav;
    }

    /**
     * Method to make the breadcrumbs.
     * The context title is displayed if the module is context aware. The title is a link back to
     * the context module.
     * The name of the current module is displayed as text or as a link when an action has been
     * performed.
     *
     * @param object $home A link to the home or front page of the site.
     * @param string $module The module or string to be added to the breadcrumbs.
     * @param array $moduleInfo The module information - to check context sensitivity, etc.
     */
    public function makeBreadCrumbs($home, $module, $moduleInfo)
    {
        $nav = $home;
        $action = $this->getParam ( 'action' );
        $extra = $this->getSession ( 'breadcrumbs', NULL );
        $insert = $this->getSession ( 'crumb', NULL );
        $modlink = $this->getSession ( 'modlink', FALSE );
        // Add extra links if available
        if (! empty ( $extra )) {
            $action = $extra;
        }
        // If this is the context module and the user is in a context
        if (isset ( $this->contextCode ) && $module == 'context') {
            if (! empty ( $action )) {
                $nav .= $this->addLink ( 'context', $modlink, $this->contextMenu ) . $this->divide ( $action );
            } else {
                $nav .= $this->divide ( $this->contextTitle );
            }
        // if in a context and module is context aware
        } else if (isset ( $this->contextCode ) && $moduleInfo ['iscontextaware'] == '1') {
            if (! empty ( $action )) {
                $moduleLink = $this->addLink ( $module, $modlink ) . $this->divide ( $action );
            } else {
                $moduleLink = $this->divide ( $this->addText ( $module ) );
            }
            $nav .= $this->addLink ( 'context', FALSE, $this->contextMenu );
            if (! empty ( $insert )) {
                $nav .= $this->divide ( $insert );
            }
            $nav .= $moduleLink;
        // if not in a context or module is not context aware/dependent
        } else {
            if (! empty ( $action )) {
                $moduleLink = $this->addLink ( $module, $modlink ) . $this->divide ( $action );
            } else {
                $moduleLink = $this->divide ( $this->addText ( $module ) );
            }
            if (! empty ( $insert )) {
                $nav .= $this->divide ( $insert );
            }
            $nav .= $moduleLink;
        }
        $this->unsetSession ( 'breadcrumbs' );
        $this->unsetSession ( 'crumb' );
        $this->unsetSession ( 'modlink' );
        $this->unsetSession ( 'replaceBreadcrumbs' );

        return $nav;
    }

    /**
     * Method to get the module name in the correct language.
     * Function uses code2Txt if the module name contains the word context.
     *
     * @param string $module The module.
     */
    public function addText($module)
    {
        if (! (strpos ( strtolower ( $module ), 'context' ) === FALSE)) {
            $text = $this->objLanguage->code2Txt ( 'mod_' . $module . '_toolbarname', $module );
        } else {
            $text = $this->objLanguage->code2Txt ( 'mod_' . $module . '_name', $module );
        }

        return ucwords ( $text );
    }

    /**
     * Method to add a link to the breadcrumbs, add the divider in front.
     *
     * @param string $module The module being linked.
     * @param string $text The text for the link, if not the module name.
     * @param string $divider The divider. Default = >>
     */
    public function addLink($module, $modlink = FALSE, $text = NULL, $divider = ' &raquo; ')
    {
        if (! $text) {
            $text = $this->addText ( $module );
        }
        if (! $modlink) {
            $link = new link ( $this->uri ( null, $module ) );
            $link->link = $text;
            $link = $link->show ();
        } else {
            $link = $text;
        }

        return $divider . $link;
    }

    /**
     * Method to add a specified divider in front of a link or text.
     *
     * @param string $link The text.
     * @param string $divider The divider. Default = >>
     */
    public function divide($link, $divider = ' &raquo; ')
    {
        return $divider . $link;
    }

    /**
     * Method to provide the breadcrumbs for the page title.
     *
     * @return string $nav The breadcrumbs
     */
    public function siteNavigation()
    {
        // Language
        $home = $this->objLanguage->languageText ( 'word_home', 'system', 'Home' );
        // Get the module name
        $module = $this->getParam ( 'module' );
        // Get settings information for the module
        $moduleInfo = $this->moduleCheck->getRow ( 'module_id', $module );
        if (empty ( $moduleInfo ) && ! ($module == '_default' || $module == 'postlogin' || $module == '')) {
            return '';
        }
        // If the module is the default module
        if ($module == '_default' || $module == 'postlogin' || $module == '') {
            $nav = '';
        } else {
            // set the link to the default module
            $nav = $this->makeSiteBreadCrumbs ( $home, $module, $moduleInfo );
        }
        return $nav;
    }

    /**
     * Method to make the site breadcrumbs.
     * The context title is displayed if the module is context aware. The title is a link back to
     * the context module.
     * The name of the current module is displayed as text or as a link when an action has been
     * performed.
     *
     * @param object $home A link to the home or front page of the site.
     * @param string $module The module or string to be added to the breadcrumbs.
     * @param array $moduleInfo The module information - to check context sensitivity, etc.
     */
    public function makeSiteBreadCrumbs($home, $module, $moduleInfo)
    {
        $nav = $home;
        $action = $this->getParam ( 'action' );
        $extra = $this->getSession ( 'breadcrumbs', NULL );
        $insert = $this->getSession ( 'crumb', NULL );
        $modlink = $this->getSession ( 'modlink', FALSE );
        // Add extra links if available
        if (! empty ( $extra )) {
            $action = $extra;
        }
        // If this is the context module and the user is in a context
        if (isset ( $this->contextCode ) && $module == 'context') {
            if (! empty ( $action )) {
                $nav .= $this->siteDivide ( $this->contextMenu ) . $this->siteDivide ( $action );
            } else {
                $nav .= $this->siteDivide ( $this->contextTitle );
            }
        // if in a context and module is context aware
        } else if (isset ( $this->contextCode ) && $moduleInfo ['iscontextaware'] == '1') {
            if (! empty ( $action )) {
                $modulePart = $this->siteDivide ( $this->addText ( $module ) ) . $this->siteDivide ( $action );
            } else {
                $modulePart = $this->siteDivide ( $this->addText ( $module ) );
            }
            $nav .= $this->siteDivide ( $this->contextMenu );
            if (! empty ( $insert )) {
                $nav .= $this->siteDivide ( $insert );
            }
            $nav .= $modulePart;
        // if not in a context or module is not context aware/dependent
        } else {
            if (! empty ( $action )) {
                $modulePart = $this->siteDivide ( $this->addText ( $module ) ) . $this->siteDivide ( $action );
            } else {
                $modulePart = $this->siteDivide ( $this->addText ( $module ) );
            }
            if (! empty ( $insert )) {
                $nav .= $this->divide ( $insert );
            }
            $nav .= $modulePart;
        }

        return $nav;
    }

    /**
     * Method to add a specified divider in front of a link or text.
     *
     * @param string $link The text.
     * @param string $divider The divider. Default = >>
     */

    public function siteDivide($crumb, $divider = ' | ')
    {
        return $divider . $crumb;
    }
}
?>