<?php
/* -------------------- accesscontrol class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * Baseclass for a module's access control class
 *
 * @author Jonathan Abrahams
 * @package core
 */
class access extends object
{
    /**
     * Constructor for the access class.
     */
    public function __construct(&$objEngine, $moduleName)
    {
        parent::__construct($objEngine, $moduleName);
        //$this->getPermissions( $moduleName ); // To do: Fix redirect limit reached.
    }

    /**
     * Method to controll access to the module.
     * Called by engine before the dispatch method.
     * @param object The module controller.
     * @param string The action param passed to the dispatch method
     */
    function dispatchControl( &$module, $action )
    {
        // Extract isRegistered
        extract( $this->getModuleInformation( 'decisiontable' ) );
        // Safty net if the decision table module has not been registered.
        if( !$isRegistered ) {
            return $module->dispatch( $action );
        }
        // Get an instance of the decisiontable object.
        $this->objDT = &$this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        $this->objDT->create( $this->moduleName );
        // Collect information from the database.
        $this->objDT->retrieve( $this->moduleName );
        // Test the current action being requested, to determine if it requires access control.
        if( $this->objDT->hasAction( $action ) ) {
            // Is the action allowed?
            if ( !$this->isValid( $action ) ) {
                // redirect and indicate the user does not have sufficient access.
                return $this->nextAction( 'noaction', array('modname'=>$this->moduleName, 'actionname'=>$action), 'redirect' );
            }
        }
        // Action allowed continue.
        return $module->dispatch($action);
    }

    /**
    * Method to test if the action is valid.
    * @param string the action.
    * @param string the default to be used if action does not exist.
    * @return true|false True if action valid, otherwise False.
    */
    function isValid( $action, $default = TRUE )
    {
        return $this->objDT->isValid($action, $default);
    }
    /**
     * Method to gather information about the given module.
     * @param string The module name.
     */
    function getModuleInformation($moduleName)
    {
        $objModAdmin = &$this->getObject( 'modulesadmin', 'modulelist' );
        $array = $objModAdmin->getArray('SELECT isAdmin, dependsContext FROM tbl_modules WHERE module_id=\''.$moduleName.'\'');
        $info =array();
        $info['isRegistered'] = isset( $array[0] );
        $info['isAdminMod'] = $info['isRegistered'] ? $array[0]['isAdmin'] : NULL;
        $info['isContextMod'] = $info['isRegistered'] ? $array[0]['dependsContext'] : NULL;
        return $info;
    }

    /**
     * Method to control access to the module based on the modules configuration parameters.
     * @param string The module name.
     */
    function getPermissions($moduleName)
    {
        // Extract isRegistered, isAdminMod, isContextMod
        extract( $this->getModuleInformation( $moduleName ) );
        // The module is not registered redirect with option to register.
        if( !$isRegistered ){
            return $this->nextAction( 'notregistered', array('modname'=>$moduleName), 'redirect' );
        }
        // The module is admin only, allow only admin users.
        if( $isAdminMod ) {
            $objUser =& $this->getObject('user', 'security');
            if(!$objUser->isAdmin()){
                return $this->nextAction( 'nopermission', array('modname'=>$moduleName), 'redirect' );
            }
        }
        // The module depends on being in a context, redirect if not in a context.
        if( $isContextMod ) {
            $objContext =& $this->getObject('dbcontext','context');
            if(!$this->objContext->isInContext()){
                return $this->nextAction( 'nocontext', array('modname'=>$moduleName), 'redirect' );
            }
        }
    }
}

?>