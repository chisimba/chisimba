<?php
/**
* Class pblMain extends object.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class determines the existence of a classroom on entry and creates an instance of it.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class pblMain extends object
{
    private $classroom;

    /**
    * Method to construct the class
    */
    public function init()
    {
        $this->classroom = &$this->getObject('pblclassroom');
        $this->dbclassroom = &$this->getObject('dbclassroom');
        $this->dbcases = &$this->getObject('dbcases');
        
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        
        $this->objUser = &$this->getObject('user', 'security');
        $this->objHelp = &$this->newObject('help','help');
        $this->objLanguage = &$this->newObject('language','language');
    }

    /**
    * Method to initiate the classroom.
    * The method checks whether the user exists and the classroom exists.
    *
    * @return
    */
    public function initiate()
    {
        if (!$this->sessionOk()) {
            $this->uri('');
        }
        if (!$this->classSelected()) {
            $this->uri('');
        }
    }

    /**
    * Method to initialise the classroom and get the first scene for display.
    *
    * @return string $option
    */
    public function startCase()
    {
        $sesClass = $this->getSession('classroom');

        if (isset($_POST['option'])){
            $option = $_POST['option'];
        }else{
            $option = "Active";
        }
        $this->classroom->classroom();
        $active = $this->dbclassroom->isActive($sesClass);
        if (!$active) {
            $this->classroom->start();
        }
        return $option;
    }

    /**
    * Method to check whether or not a user exists and set a session variable with the users name.
    *
    * @return bool
    */
    public function sessionOk()
    {
        if ($this->getParam('clid')){
            $fullName = $this->objUser->fullname();
            $this->setSession('pbl_user', $fullName);
        }
        $sesUser = $this->getSession('pbl_user');
        return isset($sesUser);
    }

    /**
    * Method to check whether a classroom has been selected.
    * The method sets the session variables for classroom id, chair, scribe, case and user id.
    *
    * @return bool
    */
    public function classSelected()
    {
        if ($this->getParam('clid')){
            $clId = $this->getParam('clid');
            $this->setSession('classroom', $clId);
        }
        $sesClass = $this->getSession('classroom');
        if (!isset($sesClass)) {
            return FALSE;
        }
        // get case name and class name
        if (isset($sesClass)) {
            $class = $this->dbclassroom->getClass($sesClass);
            if ($class) {
                // Set up class and case variables
                $this->setSession('classname', $class['name']);
                $this->setSession('caseid', $class['caseid']);
                $case = $this->dbcases->getEntry($class['caseid']);
                if ($case){
                    $this->setSession('casename', $case['name']);
                }

                // set up chair and scribe variables
                $this->setSession('facilitator', FALSE);
                $sesUserId = $this->getSession('pbl_user_id');
                if($class['facilitator'] != $this->objLanguage->languageText('word_virtual')){
                    if($class['facilitator'] == $sesUserId)
                        $this->setSession('facilitator', TRUE);
                }
                if($class['chair'] != 'none'){
                    $this->setSession('chair', FALSE);
                    if($class['chair'] == $sesUserId)
                        $this->setSession('chair', TRUE);
                }
                if ($class['scribe'] != 'none'){
                    $this->setSession('scribe', FALSE);
                    if($class['scribe'] == $sesUserId)
                        $this->setSession('scribe', TRUE);
                }
                if ($this->objUser->isAdmin()) {
                    $this->setSession('chair', TRUE);
                    $this->setSession('scribe', TRUE);
                }
            }
        }
        return TRUE;
    }

    /**
    * Method to set up menu button for exit and the help icon.
    *
    * @return string $objForm The form containing the exit button and help icon.
    */
    public function getMenuBar2()
    {
        $helpShow = $this->objHelp->show('classroom', 'pbl');
        $objForm = new form('menuf', $this->uri(array('action' => 'exit')));
        $objButton = new button("Exit", $this->objLanguage->languageText('word_exit'));
        $objButton->setIconClass("cancel");
        $objButton->setToSubmit();
        $exit = $objButton->show();
        $menu = $exit;
        $menu .= "<input type='hidden' name='option' value='Board' />";

        $objForm->addToForm($menu.'&nbsp;&nbsp;&nbsp;'.$helpShow);
        return $objForm->show();
    }
}

?>