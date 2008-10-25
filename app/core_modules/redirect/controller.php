<?php
/**
* redirect class extends controller
* @package redirect
* @filesource
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
                                                                                                                                             
/**
* Module class to handle redirects.
* The module displays a template when redirected for incorrect permissions,
* not in a context and not a registered module.
* 
* The module is used by implementing the next action function: 
* $this->nextAction($action, array(), 'redirect');
* - If the user is a student and needs to be lecturer or admin: 
* $action = 'nopermission'.
* The function accepts parameters for title and message using the permissions as
* the default title and message.
*
* - If the module is context dependent and the user is outside of context:
* $action = 'nocontext'.
*
* - If the module hasn't been registered:
* $action = 'notregistered'.
* The function accepts a parameter for the module name and then presents an admin
* user with a link to register the module. Otherwise a link to moduleadmin is
* provided.
*
* @author Megan Watson
*
* @copyright (c) 2004 UWC
* @package redirect
* @version 1.0
*/

class redirect extends controller
{
    /**
     * Method to construct the class.
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDBContext = $this->getObject('dbcontext', 'context');
        $this->objUser = $this->getObject('user', 'security');
        $this->objManageGroups = $this->getObject('managegroups', 'contextgroups');
    }

    /**
     * Standard dispatch method
     */
    function dispatch($action)
    {
        switch($action) {
            case 'noaction':
                return $this->noAction();

            case 'nopermission':
                return $this->noPermission();
                
            case 'nocontext':
                return $this->noContext();
            
            case 'notregistered':
                return $this->registerMod();
            
            default:
                return $this->noPermission();
        }
    }

    /**
     * Method to display a template if the user does not have permission
     * to invoke the action in the module.
     * @author Jonathan Abrahams
     */
    function noAction()
    {
        $subhead = $this->newObject('link', 'htmlelements');

        $modname = $this->getParam('modname');
        $actionname = $this->getParam('actionname');

        $heading = $this->objLanguage->languageText('mod_redirect_noaction', 'redirect')
            .' <em>'.$actionname.'</em>';

        $subheadTxt = $this->objLanguage->languageText('word_back');
        $subhead->link = $subheadTxt;
        $subhead->link('javascript: history.back();');

        $this->setVarByRef('heading', $heading);
        $this->setVarByRef('subhead', $subhead->show());

        return 'redirect_tpl.php';
    }
    
    /**
     * Method to display a template if the user does not have permission
     * to access the module.
     * A default heading and message are set if the module does not provide one.
     */
    function noPermission()
    {        
        $title = $this->getParam('title');
        $msg = $this->getParam('msg');
        $menu = $this->getParam('menu');
        $modname = $this->getParam('modname');

        if (isset($title)) {
            $heading = $this->objLanguage->languageText($title);
        } else {
            $heading = $this->objLanguage->languageText('mod_redirect_nopermission', 'redirect')
                .' '.$this->objLanguage->languageText('mod_'.$modname.'_name');
        }

        if (isset($msg)) {
            $subhead = $this->objLanguage->languageText($msg);
        } else {
            $subhead = $this->objLanguage->languageText('mod_redirect_contactadminaccess', 'redirect');
        }

        if (isset($menu)) {
            $this->setVarByRef('menu', $menu);
        }

        $this->setVarByRef('heading', $heading);
        $this->setVarByRef('subhead', $subhead);

        return 'redirect_tpl.php';
    }
    
    /**
     * Method to display a template if the user is not in a context.
     * The method is called for modules that are context dependent.
     * A default heading and message are set if the module does not provide one.
     */
    function noContext()
    {        
        $objLabel = $this->newObject('label', 'htmlelements');
        
        $title = $this->getParam('title');
        $msg = $this->getParam('msg');
        $menu = $this->getParam('menu');
        $modname = $this->getParam('modname');
        
        if (isset($title)) {
            $heading = $this->objLanguage->languageText($title);
        } else {
            $heading = $this->objLanguage->languageText('mod_redirect_nocourse', 'redirect').' '.
                $this->objLanguage->languageText('mod_'.$modname.'_name');
        }

        $subhead = $this->objLanguage->languageText('mod_redirect_entercourse', 'redirect');
        $objLabel->label($subhead, 'input_contextDropdown');
        $subhead = $objLabel->show();
        
        $contexts = $this->showDropDown();
        
        if (!$contexts) {
            $subhead = $this->objLanguage->languageText('mod_redirect_nousercourses', 'redirect');
        }
        
        if (isset($menu)) {        
            $this->setVarByRef('menu', $menu);
        }
        
        $this->setVarByRef('heading', $heading);
        $this->setVarByRef('subhead', $subhead);
        $this->setVarByRef('actions', $contexts);

        return 'redirect_tpl.php';
    }
    
    /**
     * Method to get a list of contexts.
     * The method returns a dropdown list of contexts in which the user is registered.
     */
    function showDropDown()
    {
        $objForm = $this->newObject('form', 'htmlelements');
        $objButton = $this->newObject('button', 'htmlelements');
        $objDropDown = $this->newObject('dropdown', 'htmlelements');
        
        $go = $this->objLanguage->languageText('word_go');
        
        $objDropDown = new dropdown('context_dropdown');
        $objDropDown->cssClass = 'coursechooser';
        
        $contexts = $this->objManageGroups->userContextCodes();
        if (!empty($contexts)) {
            foreach ($contexts as $code){
                $menu = $this->objDBContext->getMenuText($code);
                $objDropDown->addOption($code, $menu);
            }
            $objDropDown->extra = 'onchange="document.enter.submit();"';
            
            // submit button
            $objButton = new button('go', $go);
            $objButton->setToSubmit();
            
            // Build form
            $objForm->form('enter', $this->uri(array('action'=>'joincontext'), 'context'));
            $objForm->addToForm($objDropDown->show());
            $objForm->addToForm($objButton->show());

            return $objForm->show();
        }

        return FALSE;        
    }
    
    /**
    * Method to display a template if a module is not registered.
    */
    function registerMod()
    {
        $modname = $this->getParam('modname');
        $title = $this->getParam('title');
        $msg = $this->getParam('msg');
        $menu = $this->getParam('menu');
        
        $objLink = $this->newObject('link','htmlelements');
        
        if (isset($title)) {
            $heading = $this->objLanguage->languageText($title);
        } else {
            $heading = $modname.' '.
            $this->objLanguage->languageText('mod_redirect_notregistered');
        }

        if (isset($msg)) {
            $subhead = $this->objLanguage->languageText($msg);
        } else {
            if ($this->objUser->isAdmin()) {
                $subhead = $this->objLanguage->languageText('mod_redirect_registermodule', 'redirect');
                if (isset($modname)) {
                    $linkAction = $this->uri(array('action'=>'register', 'modname'=>$modname), 'moduleadmin');
                    $link = $this->objLanguage->languageText('mod_redirect_register', 'redirect')
                        .' '.$modname;
                } else {
                    $linkAction = $this->uri(array(), 'moduleadmin');
                    $link = $this->objLanguage->languageText('mod_moduleadmin_name');
                }
                $objLink = new link($linkAction);
                $objLink->link = $link;
                $actions = $objLink->show();
                $this->setVarByRef('actions', $actions);
            } else {
                $subhead = $this->objLanguage->languageText('mod_redirect_contactadmin', 'redirect');
            }
        }        
        
        if (isset($menu)) {
            $this->setVarByRef('menu', $menu);
        }
        
        $this->setVarByRef('heading', $heading);
        $this->setVarByRef('subhead', $subhead);

        return 'redirect_tpl.php';
    }
}

?>
