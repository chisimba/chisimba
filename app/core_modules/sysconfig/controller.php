<?php
/* -------------------- sysconfig class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
*
* Sysconfig module controller for KEWL.NextGen. The logger
* module is responsible for recording and displaying user
* activity.
*
*
*
* @author Derek Keats
*
*/
class sysconfig extends controller {

    /**
    * var object Property to hold sysconfig object
    */
    var $objSysConfig;

    /**
    * var object Property to hold text abstract object
    * @author Kevin Cyster
    */
    var $objAbstract;

    /**
    * var object Property to hold language object
    */
    var $objLanguage;

    /**
    * var object Property to hold user object
    */
    var $objUser;

    /**
    * Standard init function
    */
    function init()
    {
        //Get an instance of the configuration object
        $this->objSysConfig = & $this->getObject('dbsysconfig');
        //Get an instance of the interface helper object
        $this->objInterface = & $this->getObject('sysconfiginterface');
        //Get an Instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language');
        //Get an instance of the user object
        $this->objUser = & $this->getObject('user', 'security');
        //Get the text abstract object
        //Kevin Cyster
        //$this -> objAbstract =& $this -> getObject('systext_facet', 'systext');
    }

    /**
    * Dispatch method for sysconfig class. It selects the steps in
    */
    function dispatch()
    {
        //Require the user to be admin
        if (!$this->objUser->isAdmin()) {
            $this->setVar('str', $this->objLanguage->languageText("mod_sysconfig_reqadmin"));
            return 'main_tpl.php';
        }
        $action = $this->getParam('action', NULL);
        $title=$this->objLanguage->languageText("mod_sysconfig_title");
        switch ($action) {
            case NULL:
            case 'step1':
                 // Create page title
                $pgTitle =& $this->getObject('htmlheading', 'htmlelements');
                $pgTitle->type = 1;
                $pgTitle->str = $this->objLanguage->languageText("mod_sysconfig_firstep");
                //Set the title for the table
                $this->setVar('title', $pgTitle->show());
                //Set the text instructions for the table
                $this->setVar('step1', $this->objLanguage->languageText("mod_sysconfig_step1"));
                //Get list of registered modules
                $this->objMods = & $this->getObject('modules', 'modulecatalogue');
                //Return an array of all modules
                $this->setVar('ary', $this->objMods->getModules(1));
                //Set the action for the form
                $formaction = $this->uri(array('action' => 'step2'));
                $this->setVar('formaction', $formaction);

                // Get the list of modules with parameters
                $modulesList = $this->objSysConfig->getModulesParamList();
                $this->setVarByRef('modules', $modulesList);

                return "step1_tpl.php";
                break;
            case 'step2':
                $pmodule = $this->getParam('pmodule_id', NULL);

                if ($this->objSysConfig->getValue("add_disabled", "sysconfig")=="TRUE") {
                    $this->setVar('disableadd',TRUE);
                } else {
                    $this->setVar('disableadd',FALSE);
                }
                if ($pmodule !== NULL) {
                    //Set the pmodule code in the template
                    $this->setVar('pmodule', $pmodule);
                    //Get an array of data for the module whose params are being set
                    $ary = $this->objSysConfig->getProperties($pmodule);
                    //Set the text instructions for the table
                    $this->setVar('step2', $this->objLanguage->languageText("mod_sysconfig_step2"));
                    if (count($ary) >=1) {
                        //Send through the array
                        $this->setVarByRef('ary', $ary);
                    } else {
                        $this->setVar('str', "<h3>" .
                        $this->objLanguage->languageText("mod_sysconfig_noconfprop")
                        ."</h3>");
                    }
                    // update the session variable 'systext' if $pmodule = systext
                    // Kevin Cyster
                    if($pmodule == 'systext'){
                       // $this->objAbstract->updateSession();
                    }
                    return "step2_tpl.php";
                } else {
                    $this->setVar('str', $this->objLanguage->languageText("mod_sysconfig_nomoduleset"));
                    return "dump_tpl.php";
                }
                break;
            case 'save':
                //Get the module for the parameter
                $pmodule = TRIM($_POST['pmodule']);
                $this->objSysConfig->updateSingle();

                $checkobject = $this->getObject('checkobject');

                $record = $this->objSysConfig->getRow('id', $_POST['id']);

                // THIS IS NOT WORKING - Commented out for the time being
                // if ($checkobject->objectFileExists('sysconfig_'.strtolower($record['pname']), $pmodule)) {
                    // $customSysConfig = $this->getObject(strtolower('sysconfig_'.$record['pname']), $pmodule);
                    // $customSysConfig->postUpdateActions();
                // }

                return $this->nextAction('step2', array('pmodule_id'=> $pmodule));
                break;
            case 'edit':
                //Get the module for the parameter
                $pmodule = $this->getParam("pmodule", NULL);
                //Set the text instructions for the table
                $this->setVar('step', $this->objLanguage->languageText("mod_sysconfig_edlabel"));
                //Set the mode variable to edit
                $this->setVar('mode', 'edit');
                //Get the form
                $this->setVar('str', $this->objInterface->showEditAddForm($pmodule, "edit"));
                //Return the edit template
                return "edit_add_tpl.php";
                break;
            default:
                die($this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                break;
        } #switch

    }


} # end of class

?>