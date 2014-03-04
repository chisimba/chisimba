<?php

/**
 * This module is for a simple essay marking tool to be used used as part of the
 * assessment tools.
 *
 * PHP version 5
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

 * @author
 * @copyright  2009 AVOIR
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class efl extends controller {


    function init() {
    //Instantiate the language object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->essays=$this->getObject('dbessays');
        $this->objUser=$this->getObject('user','security');
        $this->essayutil=$this->getObject('essayutil');
        $this->objEssays = $this->getObject('dbessays','efl');
        $this->objStudentEssays = $this->getObject('dbstudentessays','efl')        ;
    }


    public function dispatch($action) {
    /*
    * Convert the action into a method (alternative to
    * using case selections)
    */
        $method = $this->getMethod($action);
    /*
    * Return the template determined by the method resulting
    * from action
    */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__'.$action;
        }
        else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     * landing page. This will return list of essays
     * @return <type>
     */
    function __home() {
        $this->essayutil->generateJNLP();
        return "home_tpl.php";
    }

    /**
     * returns a form for a lecturer to create a new essay
     * @return <type>
     */
    function __addessay() {
        return "addeditessay_tpl.php";
    }

    /**
     * this returns a screen showing submissions for a specific
     * essay
     * @return <type>
     */
    function __viewsubmittedessays() {
        $essayid=$this->getParam('essayid');
        //$this->essayutil->generateJNLP();

        return 'submittedessays_tpl.php';
    }


    function __viewessayasstudent() {
        $essayid=$this->getParam('essayid');
        $this->setVarByRef('essayid',$essayid);

        return 'studentessay_tpl.php';
    }
   /**
    * used to invoke teh marker tool
    * @return <type>
    */
    function __markessay() {
        $essayid=$this->getParam('essayid');
        $this->setVarByRef('essayid',$essayid);
        //$this->essayutil->generateJNLP();

        return "essaymarker_tpl.php";

    }
    /**
     *saves a students essay
     *
     */
    function __addstudentessay() {
        $userid=$this->objUser->userId();
        $essayid=$this->getParam('essayid');
        $content=$this->getParam('content');
        $this->setVarByRef('essayid',$essayid);

        if($this->objStudentEssays->addstudentEssay($userid,$essayid,$content)) {
            $this->essayutil->generateJNLP();
            return "studentessaylist_tpl.php";
        } else {
            $this->essayutil->generateJNLP();
            return "studentessaylist_tpl.php";
        }

    }

    /**
     *updates a students essay
     *
     */
    function __editessay(){
        $userid=$this->objUser->userId();
        $essayid=$this->getParam('essayid');
        $content=$this->getParam('content');
        
        return "essayedit_tpl.php";
        
    }
    
    function __updatestudentessay() {

    }

    /**
     *saves the essay topics in a database
     *
     */
    function __saveessay(){
        $userid=$this->objUser->userId();
        $title=$this->getParam('titlefield');
        $content=$this->getParam('pagecontent');
        $contextcode="ABCD";/*$this->getParam('contextcode');*/
        $active=$this->getParam('active');
        $multisubmit=$this->getParam('multiSubmit');

        if ($this->objEssays->addEssay($title,$content,$contextcode,$active,$multisubmit))
        {
            return "home_tpl.php";
        }
        else
        {
            return "essayedit_tpl.php";
        }
    }

    function __previewessay(){
        $storyid=$this->getParam('storyid');
        $this->setVarByRef('storyid',$storyid);
        
        return 'previewessay_tpl.php';
    }

    function __essaymembers(){
        return 'essaymembers_tpl.php';
    }

}
?>
