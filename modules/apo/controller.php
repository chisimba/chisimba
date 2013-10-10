<?php

$id = "";
/**
 *
 *  PHP version 5
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
 * @category  Chisimba
 * @package   apo (Academic Planning Office)

 *
  =
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check

class apo extends controller {

    function init() {
        $this->loadclass('link', 'htmlelements');
        $this->objattach = $this->getObject('mailer', 'mail');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objLog = $this->getObject('logactivity', 'logger');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLog->log();
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objUser = $this->getObject('user', 'security');
        $this->objUtils = $this->getObject('userutils');
        $this->documents = $this->getObject('dbdocuments');
        $this->objformdata = $this->getObject('dbformdata');
        $this->mode = $this->objSysConfig->getValue('MODE', 'apo');
        $this->faculties = $this->getObject('dbfaculties');
        $this->objFormatting = $this->getObject('formatting');
        $this->users = $this->getObject('dbapousers');
    }

    /**
     *
     * The standard dispatch method for the apo module.
     * The dispatch method uses methods determined from the action
     * parameter of the querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     * @access public
     * @param $action
     * @return A call to the appropriate method
     *
     */
    public function dispatch($action) {
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $this->setLayoutTemplate("apo_layout_tpl.php");
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
            return '__' . $action;
        } else {
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
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    public function __home() {
        $selected = "unapproved";
        $documents = $this->documents->getdocuments(0, 10, $this->mode);

        $this->setVarByRef("documents", $documents);
        $this->setVarByRef("selected", $selected);
        return "home_tpl.php";
    }

    public function __newdocument() {
        $selected = $this->getParam('selected');
        $id = $this->getParam("docid");
        $faculties = $this->faculties->getFaculties();
        //$document = $this->documents->getDocument($id);

        $mode = "new";

        $this->setVarByRef("action", $action);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        $this->setVarByRef("departments", $faculties);
        // $this->setVarByRef("document", $document);

        return "addeditdocument_tpl.php";
    }

    function __registerdocument() {

        $document = $this->documents->getDocument($id);
        $faculties = $this->faculties->getFaculties();
        $date = $this->getParam('date_created');
        $number = $this->getParam('number');
        $dept = $this->getParam('department');
        $userid = $this->getParam('userid');
        $currentuserid = $this->getParam('userid');
       
        $title = $this->getParam('title');
        $selectedfolder = $this->getParam('parentfolder');

        $refno = $number . date("Y");
        $contact = $this->getParam('contact', '');

        if ($contact == null || $contact == '') {
            $contact = $this->objUser->fullname();
        }
        $telephone = $this->getParam('telephone');

        $status = $this->getParam('status');
        if ($status == '' || $status == NULL) {
            $status = "0";
        }
        $currentuserid = $this->objUser->userid();
        $groupid = "0";
        $selectedfolder = "/";
        $version = $this->getParam('version', "1");

        $refNo = $this->documents->addDocument(
                        $date,
                        $refno,
                        $dept,
                        $contact,
                        $telephone,
                        $title,
                        $groupid,
                        $selectedfolder,
                        $currentuserid,
                        $mode = "apo",
                        $approved = "N",
                        $status = "0",
                        $currentuserid,
                        $version,
                        $ref_version
        );


        $documents = $this->documents->getdocuments(0, 10, $this->mode);
        $this->setVarByRef("documents", $documents);
        $selected = "unapproved";
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        $this->setVarByRef("refno", $refNo);
        $this->setVarByRef("departments", $faculties);
        $this->setVarByRef("currentuserid", $currentuserid);



        return "home_tpl.php";
    }

    function __updatedocument() {
        $number = $this->getParam('number');
        $dept = $this->getParam('department');
        $date = $this->getParam('date_created');
        $title = $this->getParam('title');
        $group = $this->getParam('group');
        $selectedfolder = $this->getParam('parentfolder');
        $telephone = $this->getParam('telephone');
        $id = $this->getParam('id');
        $contact = $this->getParam('contact');
        $status = $this->getParam('status', "0");
        $currentuserid = $this->getParam('currentuserid');

        //print_r($currentuserid);die();
        $version = $this->getParam('version', "0");
        $data = array(
            "department" => $dept,
            "telephone" => $telephone,
            "docname" => $title,
            "date_created" => $date,
            "contact_person" => $contact,
            "groupid" => $group,
            "topic" => $selectedfolder,
            "status" => $status,
            "currentuserid" => $currentuserid,
            "version" => $version
        );

        $this->documents->updateInfo($id, $data);

        $this->setVarByRef("currentuserid", $currentuserid);
        $this->nextAction('showoverview', array('id' => $id));
    }

    function __showeditdocument() {
        $faculties = $this->faculties->getFaculties();
        $id = $this->getParam('id');
        $document = $this->documents->getDocument($id);

        // print_r($document);die();
        $action = "updatedocument";



        $mode = "edit";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("id", $id);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("departments", $faculties);
        $this->setVarByRef("currentuserid", $currentuserid);

        return "addeditdocument_tpl.php";
    }

    public function __showoverview() {
        $id = $this->getParam('id');
        $document = $this->documents->getDocument($id);

        $selected = $this->getParam('selected');

        $mode = "new";

        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        $this->setVarByRef("document", $document);
        return "overview_tpl.php";
    }

    public function __showrulesandsyllabusone() {
        $id = $this->getParam("id");
        $document = $this->documents->getDocument($id);
        $formname = $this->getParam('formname');

        $a1 = $this->getParam("a1");
        $a2 = $this->getParam("a2");
        $a3 = $this->getParam("a3");
        $a4 = $this->getParam("a4");
        $a5 = $this->getParam("a5");

        $errormessages = array();

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["a1"] = $a1;
        $formdata["a2"] = $a2;
        $formdata["a3"] = $a3;
        $formdata["a4"] = $a4;
        $formdata["a5"] = $a5;

        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $formdata = serialize($formdata);
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("id", $id);
        return "rulesandsyllabusone_tpl.php";
    }

    public function __showrulesandsyllabustwo() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $b1 = $this->getParam("b1");
        $b2 = $this->getParam("b2");
        $b3a = $this->getParam("b3a");
        $b3b = $this->getParam("b3b");
        $b4a = $this->getParam("b4a");
        $b4b = $this->getParam("b4b");
        $b4c = $this->getParam("b4c");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["b1"] = $b1;
        $formdata["b2"] = $b2;
        $formdata["b3a"] = $b3a;
        $formdata["b3b"] = $b3b;
        $formdata["b4a"] = $b4a;
        $formdata["b4b"] = $b4b;
        $formdata["b4c"] = $b4c;

        $formdata = serialize($formdata);

        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("id", $id);
        return "rulesandsyllabustwo_tpl.php";
    }

    public function __showsubsidyrequirements() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $b5a = $this->getParam("b5a");
        $b5b = $this->getParam("b5b");
        $b6a = $this->getParam("b6a");
        $b6b = $this->getParam("b6b");
        $b6c = $this->getParam("b6c");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["b5a"] = $b5a;
        $formdata["b5b"] = $b5b;
        $formdata["b6a"] = $b6a;
        $formdata["b6b"] = $b6b;
        $formdata["b6c"] = $b6c;

        $formdata = serialize($formdata);

        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "subsidyrequirements_tpl.php";
    }

    public function __showoutcomesandassessmentone() {
        $id = $this->getParam("id");
        $document = $this->documents->getDocument($id);
        $formname = $this->getParam('formname');

        $errormessages = array();

        $c1 = $this->getParam("c1");
        $c2a = $this->getParam("c2a");
        $c2b = $this->getParam("c2b");
        $c3 = $this->getParam("c3");
        $c4a = $this->getParam("c4a");
        $c4b1 = $this->getParam("c4b1");
        $c4b2 = $this->getParam("c4b2");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["c1"] = $c1;
        $formdata["c2a"] = $c2a;
        $formdata["c2b"] = $c2b;
        $formdata["c3"] = $c3;
        $formdata["c4a"] = $c4a;
        $formdata["c4b1"] = $c4b1;
        $formdata["c4b2"] = $c4b2;

        $formdata = serialize($formdata);

        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }
//$formname = $this->getParam('form');
//$c3 = $this->getParam("c3");
//$c3->label='CEMS (must be 6 characters)';
//$surname->label='Surname (must be less than 15 characters)';
//$formname->addRule(array('name'=>'c3','length'=>6), 'Check CESM manual','maxlength');
//$objForm->addRule(array('name'=>'surname','length'=>6), 'Your surname is too long',*/
        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmentone_tpl.php";
    }

    public function __showoutcomesandassessmentoneScience() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $c1 = $this->getParam("c1");
        $c2a = $this->getParam("c2a");
        $c2b = $this->getParam("c2b");
        $c3 = $this->getParam("c3");
        $c4a = $this->getParam("c4a");
        $c4b1 = $this->getParam("c4b1");
        $c4b2 = $this->getParam("c4b2");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["c1"] = $c1;
        $formdata["c2a"] = $c2a;
        $formdata["c2b"] = $c2b;
        $formdata["c3"] = $c3;
        $formdata["c4a"] = $c4a;
        $formdata["c4b1"] = $c4b1;
        $formdata["c4b2"] = $c4b2;

        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }
//$formname = $this->getParam('form');
//$c3 = $this->getParam("c3");
//$c3->label='CEMS (must be 6 characters)';
//$surname->label='Surname (must be less than 15 characters)';
//$formname->addRule(array('name'=>'c3','length'=>6), 'Check CESM manual','maxlength');
//$objForm->addRule(array('name'=>'surname','length'=>6), 'Your surname is too long',*/
        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmentoneScience_tpl.php";
    }

    public function __showoutcomesandassessmenttwo() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        if ($formname == "outcomesandassessmentone") {
            $d1a = $this->getParam("d1a");
            $d1b = $this->getParam("d1b");
            $d2a = $this->getParam("d2a");
            $d2b = $this->getParam("d2b");
            $d2c = $this->getParam("d2c");
            $d3 = $this->getParam("d3");

            $formdata = array();
            $formdata["docid"] = $id;
            $formdata["d1a"] = $d1a;
            $formdata["d1b"] = $d1b;
            $formdata["d2a"] = $d2a;
            $formdata["d2b"] = $d2b;
            $formdata["d2c"] = $d2c;
            $formdata["d3"] = $d3;

            $formdata = serialize($formdata);
            $issubmit = $this->getParam('next');
            if (!empty($issubmit)) {
                $this->objformdata->saveData($id, $formname, $formdata);
            }
        } else if ($formname == "outcomesandassessmentonescience") {
            $d1 = $this->getParam("d1");
            $d21 = $this->getParam("d21");
            $d22 = $this->getParam("d22");
            $d23 = $this->getParam("d23");
            $d24 = $this->getParam("d24");
            $d25 = $this->getParam("d25");
            $d3 = $this->getParam("d3");

            $formdata = array();
            $formdata['docid'] = $id;
            $formdata["d1"] = $d1;
            $formdata["d21"] = $d21;
            $formdata["d22"] = $d22;
            $formdata["d23"] = $d23;
            $formdata["d24"] = $d24;
            $formdata["d25"] = $d25;
            $formdata["d3"] = $d3;
            $formdata['d4'] = "0";

            $formdata = serialize($formdata);
            $issubmit = $this->getParam('next');
            if (!empty($issubmit)) {
                $this->objformdata->saveData($id, $formname, $formdata);
            }
        }

        $selected = $this->getParam('selected');
        // $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmenttwo_tpl.php";
    }

    public function __showoutcomesandassessmentthree() {

        $selectedOpts = $this->getParam('groups');
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $formdata = array();

        $formdata["docid"] = $id;
        $formdata["id1"] = "0";
        $formdata["id2"] = "0";
        $formdata["id3"] = "0";
        $formdata["id4"] = "0";
        $formdata["id5"] = "0";
        $formdata["id6"] = "0";
        $formdata["id7"] = "0";
        $formdata["id8"] = "0";

        //  foreach ($selectedOpts as $opt) {
        //        $formdata["id" . $opt] = "1";
        //   }


        $formdata = serialize($formdata);
        //   $issubmit = $this->getParam('next');
        //   if (!empty($issubmit)) {
        //         $this->objformdata->saveData($id, $formname, $formdata);
        //       }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmentthree_tpl.php";
    }

    public function __showoutcomesandassessmentthreeScience() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $d4_1 = $this->getParam("1");
        $d4_2 = $this->getParam("2");
        $d4_3 = $this->getParam("3");
        $d4_4 = $this->getParam("4");
        $d4_5 = $this->getParam("5");
        $d4_6 = $this->getParam("6");
        $d4_7 = $this->getParam("7");
        $d4_8 = $this->getParam("8");

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmentthreeScience_tpl.php";
    }

    public function __showresources() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        if ($formname == "outcomesandassessmentthree") {

            $a = $this->getParam("a");
            $b = $this->getParam("b");
            $c = $this->getParam("c");
            $d = $this->getParam("d");
            $e = $this->getParam("e");
            $f = $this->getParam("f");
            $g = $this->getParam("g");
            $h = $this->getParam("h");
            $i = $this->getParam("i");

            $formdata = array();
            $formdata["docid"] = $id;
            $formdata["a"] = $a;
            $formdata["b"] = $b;
            $formdata["c"] = $c;
            $formdata["d"] = $d;
            $formdata["e"] = $e;
            $formdata["f"] = $f;
            $formdata["g"] = $g;
            $formdata["h"] = $h;
            $formdata["i"] = $i;

            $formdata = serialize($formdata);
            $issubmit = $this->getParam('next');
            if (!empty($issubmit)) {
                $this->objformdata->saveData($id, $formname, $formdata);
            }
        } /* else if ($formname == "outcomesandassessmentthreeScience") {
          $a1 = $this->getParam("a1");
          $a2 = $this->getParam("a2");
          $a3 = $this->getParam("a3");
          $a4 = $this->getParam("a4");
          $a5 = $this->getParam("a5");
          $a6 = $this->getParam("a6");
          $a7 = $this->getParam("a7");
          $a8 = $this->getParam("a8");
          $b1 = $this->getParam("b1");
          $b2 = $this->getParam("b2");
          $b3 = $this->getParam("b3");
          $b4 = $this->getParam("b4");
          $b5 = $this->getParam("b5");
          $b6 = $this->getParam("b6");
          $b7 = $this->getParam("b7");
          $b8 = $this->getParam("b8");
          $c1 = $this->getParam("c1");
          $c2 = $this->getParam("c2");
          $c3 = $this->getParam("c3");
          $c4 = $this->getParam("c4");
          $c5 = $this->getParam("c5");
          $c6 = $this->getParam("c6");
          $c7 = $this->getParam("c7");
          $c8 = $this->getParam("c8");
          $d1 = $this->getParam("d1");
          $d3 = $this->getParam("d3");
          $d4 = $this->getParam("d4");
          $d8 = $this->getParam("d8");
          $e1 = $this->getParam("e1");
          $e8 = $this->getParam("e8");
          $other = $this->getParam("other");
          $f1 = $this->getParam("f1");
          $f2 = $this->getParam("f2");
          $f3 = $this->getParam("f3");
          $f4 = $this->getParam("f4");
          $f5 = $this->getParam("f5");
          $f6 = $this->getParam("f6");
          $f7 = $this->getParam("f7");
          $f8 = $this->getParam("f8");
          $g9 = $this->getParam("g9");
          $g10 = $this->getParam("g10");
          $h11 = $this->getParam("h11");
          $h12 = $this->getParam("h12");

          /* if ($a == null) {
          $errormessages[] = "Please provide an answer for a";
          }
          if ($b == null) {
          $errormessages[] = "Please provide an answer for b";
          }
          if ($c == null) {
          $errormessages[] = "Please provide an answer for c";
          }
          if ($d == null) {
          $errormessages[] = "Please provide an answer for d";
          }
          if ($e == null) {
          $errormessages[] = "Please provide an answer for e";
          }
          if ($f == null) {
          $errormessages[] = "Please provide an answer for f";
          }
          if ($g == null) {
          $errormessages[] = "Please provide an answer for g";
          }
          if ($h == null) {
          $errormessages[] = "Please provide an answer for h";
          }
          if ($i == null) {
          $errormessages[] = "Please provide an answer for i";
          }

          if (count($errormessages) > 0) {
          $this->setVarByRef("errormessages", $errormessages);
          $this->setVarByRef("a1", $a1);
          $this->setVarByRef("a2", $a2);
          $this->setVarByRef("a3", $a3);
          $this->setVarByRef("a4", $a4);
          $this->setVarByRef("a5", $a5);
          $this->setVarByRef("a6", $a6);
          $this->setVarByRef("a7", $a7);
          $this->setVarByRef("a8", $a8);
          $this->setVarByRef("b1", $b1);
          $this->setVarByRef("b2", $b2);
          $this->setVarByRef("b3", $b3);
          $this->setVarByRef("b4", $b4);
          $this->setVarByRef("b5", $b5);
          $this->setVarByRef("b6", $b6);
          $this->setVarByRef("b7", $b7);
          $this->setVarByRef("b8", $b8);
          $this->setVarByRef("c1", $c1);
          $this->setVarByRef("c2", $c2);
          $this->setVarByRef("c3", $c3);
          $this->setVarByRef("c4", $c4);
          $this->setVarByRef("c5", $c5);
          $this->setVarByRef("c6", $c6);
          $this->setVarByRef("c7", $c7);
          $this->setVarByRef("c8", $c8);
          $this->setVarByRef("d1", $d1);
          $this->setVarByRef("d3", $d3);
          $this->setVarByRef("d4", $d4);
          $this->setVarByRef("d8", $d8);
          $this->setVarByRef("e1", $e1);
          $this->setVarByRef("e8", $e8);
          $this->setVarByRef("other", $other);
          $this->setVarByRef("f1", $f1);
          $this->setVarByRef("f2", $f2);
          $this->setVarByRef("f3", $f3);
          $this->setVarByRef("f4", $f4);
          $this->setVarByRef("f5", $f5);
          $this->setVarByRef("f6", $f6);
          $this->setVarByRef("f7", $f7);
          $this->setVarByRef("f8", $f8);
          $this->setVarByRef("g9", $g9);
          $this->setVarByRef("g10", $g10);
          $this->setVarByRef("h11", $h11);
          $this->setVarByRef("h12", $h12);
          $mode = "fixup";
          $this->setVarByRef("mode", $mode);
          return "outcomesandassessmentthreeScience_tpl.php";
          }

          $formdata = array();
          $formdata["docid"] = $id;
          $formdata["a1"] = $a1;
          $formdata["a2"] = $a2;
          $formdata["a3"] = $a3;
          $formdata["a4"] = $a4;
          $formdata["a5"] = $a5;
          $formdata["a6"] = $a6;
          $formdata["a7"] = $a7;
          $formdata["a8"] = $a8;
          $formdata["b1"] = $b1;
          $formdata["b2"] = $b2;
          $formdata["b3"] = $b3;
          $formdata["b4"] = $b4;
          $formdata["b5"] = $b5;
          $formdata["b6"] = $b6;
          $formdata["b7"] = $b7;
          $formdata["b8"] = $b8;
          $formdata["c1"] = $c1;
          $formdata["c2"] = $c2;
          $formdata["c3"] = $c3;
          $formdata["c4"] = $c4;
          $formdata["c5"] = $c5;
          $formdata["c6"] = $c6;
          $formdata["c7"] = $c7;
          $formdata["c8"] = $c8;
          $formdata["d1"] = $d1;
          $formdata["d3"] = $d3;
          $formdata["d4"] = $d4;
          $formdata["d8"] = $d8;
          $formdata["e1"] = $e1;
          $formdata["e8"] = $e8;
          $formdata["other"] = $other;
          $formdata["f1"] = $f1;
          $formdata["f2"] = $f2;
          $formdata["f3"] = $f3;
          $formdata["f4"] = $f4;
          $formdata["f5"] = $f5;
          $formdata["f6"] = $f6;
          $formdata["f7"] = $f7;
          $formdata["f8"] = $f8;
          $formdata["g9"] = $g9;
          $formdata["g10"] = $g10;
          $formdata["h11"] = $h11;
          $formdata["h12"] = $h12;
          $formdata = serialize($formdata);
          $this->objformdata->saveData($id, $formname, $formdata);
          } */
        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "resources_tpl.php";
    }

    public function __showcollaborationandcontracts() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');
        $document = $this->documents->getDocument($id);

        $errormessages = array();

        $e1a = $this->getParam("e1a");
        $e1b = $this->getParam("e1b");
        $e2a = $this->getParam("e2a");
        $e2b = $this->getParam("e2b");
        $e2c = $this->getParam("e2c");
        $e3a = $this->getParam("e3a");
        $e3b = $this->getParam("e3b");
        $e3c = $this->getParam("e3c");
        $e4 = $this->getParam("e4");
        $e5a = $this->getParam("e5a");
        $e5b = $this->getParam("e5b");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["e1a"] = $e1a;
        $formdata["e1b"] = $e1b;
        $formdata["e2a"] = $e2a;
        $formdata["e2b"] = $e2b;
        $formdata["e2c"] = $e2c;
        $formdata["e3a"] = $e3a;
        $formdata["e3b"] = $e3b;
        $formdata["e3c"] = $e3c;
        $formdata["e4"] = $e4;
        $formdata["e5a"] = $e5a;
        $formdata["e5b"] = $e5b;

        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("id", $id);
        return "collaborationandcontracts_tpl.php";
    }

    public function __showreview() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $f1a = $this->getParam("f1a");
        $f1b = $this->getParam("f1b");
        $f2a = $this->getParam("f2a");
        $f2b = $this->getParam("f2b");
        $f3a = $this->getParam("f3a");
        $f3b = $this->getParam("f3b");
        $f4 = $this->getParam("f4");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["f1a"] = $f1a;
        $formdata["f1b"] = $f1b;
        $formdata["f2a"] = $f2a;
        $formdata["f2b"] = $f2b;
        $formdata["f3a"] = $f3a;
        $formdata["f3b"] = $f3b;
        $formdata["f4"] = $f4;

        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "review_tpl.php";
    }

    public function __showcontactdetails() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $g1a = $this->getParam("g1a");
        $g1b = $this->getParam("g1b");
        $g2a = $this->getParam("g2a");
        $g2b = $this->getParam("g2b");
        $g3a = $this->getParam("g3a");
        $g3b = $this->getParam("g3b");
        $g4a = $this->getParam("g4a");
        $g4b = $this->getParam("g4b");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["g1a"] = $g1a;
        $formdata["g1b"] = $g1b;
        $formdata["g2a"] = $g2a;
        $formdata["g2b"] = $g2b;
        $formdata["g3a"] = $g3a;
        $formdata["g3b"] = $g3b;
        $formdata["g4a"] = $g4a;
        $formdata["g4b"] = $g4b;

        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        $mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);
        return "contactdetails_tpl.php";
    }

    public function __showcomments() {
        $id = $this->getParam("id");
        $document = $this->documents->getDocument($id);
        $formname = $this->getParam('formname');

        $q1 = $this->getParam("q1");
        $q2 = $this->getParam("q2");
        $q3 = $this->getParam("q3");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["q1"] = $q1;
        $formdata["q2"] = $q2;
        $formdata["q3"] = $q3;

        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        //$mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);

        return "comments_tpl.php";
    }

    public function __showfeedback() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');
        $document = $this->documents->getDocument($id);

        $errormessages = array();

        $h1 = $this->getParam("h1");
        $h2a = $this->getParam("h2a");
        $h2b = $this->getParam("h2b");
        $h3a = $this->getParam("h3a");
        $h3b = $this->getParam("h3b");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["h1"] = $h1;
        $formdata["h2a"] = $h2a;
        $formdata["h2b"] = $h2b;
        $formdata["h3a"] = $h3a;
        $formdata["h3b"] = $h3b;

        /*
          $id = $this->getParam("id");
          $document = $this->documents->getDocument($id);
          $formname = $this->getParam('formname');

          $apo = $this->getParam("apo");
          $subsidy = $this->getParam("subsidy");
          $legal = $this->getParam("legal");
          $library = $this->getParam("library");
          $faculty = $this->getParam("faculty");


          $formdata = array();
          $formdata["docid"] = $id;
          $formdata["apo"] = $apo;
          $formdata["subsidy"] = $subsidy;
          $formdata["library"] = $library;
          $formdata["legal"] = $legal;
          $formdata["faculty"] = $faculty;
         */
        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $selected = $this->getParam('selected');
        //$mode = "new";
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);

        return "feedback_tpl.php";
    }

    public function __finishdocument() {
        $id = $this->getParam("id");
        $document = $this->documents->getDocument($id);
        $formname = $this->getParam('formname');

        $apo = $this->getParam("apo");
        $subsidy = $this->getParam("subsidy");
        $legal = $this->getParam("legal");
        $library = $this->getParam("library");
        $teaching = $this->getParam("teaching");
        $faculty = $this->getParam("faculty");

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["apo"] = $apo;
        $formdata["subsidy"] = $subsidy;
        $formdata["library"] = $library;
        $formdata["legal"] = $legal;
        $formdata["teaching"] = $teaching;
        $formdata["faculty"] = $faculty;


        $formdata = serialize($formdata);
        $issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        return "finishdocument_tpl.php";
    }

    public function __calculatespreedsheet() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $a = $this->getParam("a");
        $b = $this->getParam("b");
        $c = $this->getParam("c");
        $d = $this->getParam("d");
        $e = $this->getParam("e");
        $f = $this->getParam("f");
        $g = $this->getParam("g");
        $h = $this->getParam("h");
        $i = $this->getParam("i");

        if ($a == null) {
            $errormessages[] = "Please provide an answer for a";
        }
        if ($b == null) {
            $errormessages[] = "Please provide an answer for b";
        }
        if ($c == null) {
            $errormessages[] = "Please provide an answer for c";
        }
        if ($d == null) {
            $errormessages[] = "Please provide an answer for d";
        }
        if ($e == null) {
            $errormessages[] = "Please provide an answer for e";
        }
        if ($f == null) {
            $errormessages[] = "Please provide an answer for f";
        }
        if ($g == null) {
            $errormessages[] = "Please provide an answer for g";
        }
        if ($h == null) {
            $errormessages[] = "Please provide an answer for h";
        }
        if ($i == null) {
            $errormessages[] = "Please provide an answer for i";
        }

        if (count($errormessages) > 0) {
            $this->setVarByRef("errormessages", $errormessages);
            $this->setVarByRef("a", $a);
            $this->setVarByRef("b", $b);
            $this->setVarByRef("c", $c);
            $this->setVarByRef("d", $d);
            $this->setVarByRef("e", $e);
            $this->setVarByRef("f", $f);
            $this->setVarByRef("g", $g);
            $this->setVarByRef("h", $h);
            $this->setVarByRef("i", $i);
            $mode = "fixup";
            $this->setVarByRef("mode", $mode);
            return "outcomesandassessmentthree_tpl.php";
        }

        $id = $this->getParam("id");
        $mode = "fixup";

        $totalContactTime = ($b + $c + $d + $e) * $a;
        $totalstudyhoursNoexam = $totalContactTime * $f;
        $totalExamTime = $g * $h;
        $totalstudyhoursExam = $totalstudyhoursNoexam + $totalExamTime + $i;
        $totalSAQAcredits = $totalstudyhoursExam / 10;

        $formdata = array();
        $formdata["docid"] = $id;
        $formdata["a"] = $a;
        $formdata["b"] = $b;
        $formdata["c"] = $c;
        $formdata["d"] = $d;
        $formdata["e"] = $e;
        $formdata["f"] = $f;
        $formdata["g"] = $g;
        $formdata["h"] = $h;
        $formdata["i"] = $i;
        $formdata = serialize($formdata);
        $this->objformdata->saveData($id, $formname, $formdata);

        $this->setVarByRef("a", $a);
        $this->setVarByRef("b", $b);
        $this->setVarByRef("c", $c);
        $this->setVarByRef("d", $d);
        $this->setVarByRef("e", $e);
        $this->setVarByRef("f", $f);
        $this->setVarByRef("g", $g);
        $this->setVarByRef("h", $h);
        $this->setVarByRef("i", $i);

        $this->setVarByRef("totalcontacttime", $totalContactTime);
        $this->setVarByRef("studyhoursnoexam", $totalstudyhoursNoexam);
        $this->setVarByRef("totalexamtime", $totalExamTime);
        $this->setVarByRef("totalstudyhours", $totalstudyhoursExam);
        $this->setVarByRef("saqa", $totalSAQAcredits);
        $this->setVarByRef("id", $id);
        return "outcomesandassessmentthree_tpl.php";
    }

    public function __calculatespreedsheetScience() {
        $id = $this->getParam("id");
        $formname = $this->getParam('formname');

        $errormessages = array();

        $a3 = 0.75;
        $a5 = 0.8;
        $a6 = 1.2;
        $a7 = 1.6;
        $b3 = 0.75;
        $b5 = 1.5;
        $b6 = 1.4;
        $b7 = 1.6;
        $c3 = 3;
        $c5 = 0.6;
        $c6 = 1.2;
        $c7 = 1.6;
        $d3 = 8;
        $f5 = 0.6;
        $f6 = 1.2;
        $f7 = 1.6;

        $a1 = $this->getParam("a1");
        $a2 = $this->getParam("a2");
        $b1 = $this->getParam("b1");
        $b2 = $this->getParam("b2");
        $c1 = $this->getParam("c1");
        $c2 = $this->getParam("c2");
        $d1 = $this->getParam("d1");
        $d8 = $this->getParam("d8");
        $e1 = $this->getParam("e1");
        $e8 = $this->getParam("e8");
        $other = $this->getParam("other");
        $f1 = $this->getParam("f1");
        $f2 = $this->getParam("f2");
        $f3 = $this->getParam("f3");
        $g9 = $this->getParam("g9");
        $g10 = $this->getParam("g10");
        $h11 = $this->getParam("h11");
        $h12 = $this->getParam("h12");

        $d6 = $this->getParam("d6");
        $d7 = $this->getParam("d7");

        if ($a1 == null) {
            $errormessages[] = "Please provide an answer for a1";
        }
        if ($a2 == null) {
            $errormessages[] = "Please provide an answer for a2";
        }
        if ($b1 == null) {
            $errormessages[] = "Please provide an answer for b1";
        }
        if ($b2 == null) {
            $errormessages[] = "Please provide an answer for b2";
        }
        if ($c1 == null) {
            $errormessages[] = "Please provide an answer for c1";
        }
        if ($c2 == null) {
            $errormessages[] = "Please provide an answer for c2";
        }
        if ($d1 == null) {
            $errormessages[] = "Please provide an answer for d1";
        }
        if ($d8 == null) {
            $errormessages[] = "Please provide an answer for d8";
        }
        if ($e1 == null) {
            $errormessages[] = "Please provide an answer for e1";
        }
        if ($e8 == null) {
            $errormessages[] = "Please provide an answer for e8";
        }
        if ($other == null && ($f1 != null || $f2 != null || $f3 != null)) {
            $errormessages[] = "Please specify 'other'";
            if ($f1 == null) {
                $errormessages[] = "Please provide an answer for f1";
            }
            if ($f2 == null) {
                $errormessages[] = "Please provide an answer for f2";
            }
            if ($f3 == null) {
                $errormessages[] = "Please provide an answer for f3";
            }
        }
        if ($other != null) {
            if ($f1 == null) {
                $errormessages[] = "Please provide an answer for f1";
            }
            if ($f2 == null) {
                $errormessages[] = "Please provide an answer for f2";
            }
            if ($f3 == null) {
                $errormessages[] = "Please provide an answer for f3";
            }
        }
        if ($g9 == null) {
            $errormessages[] = "Please provide an answer for g9";
        }
        if ($g10 == null) {
            $errormessages[] = "Please provide an answer for g10";
        }
        if ($h11 == null) {
            $errormessages[] = "Please provide an answer for h11";
        }
        if ($h12 == null) {
            $errormessages[] = "Please provide an answer for h12";
        }
        if ($d6 == null) {
            $errormessages[] = "Please provide an answer for d6";
        }
        if ($d7 == null) {
            $errormessages[] = "Please provide an answer for d7";
        }

        if (count($errormessages) > 0) {
            $this->setVarByRef("errormessages", $errormessages);
            $this->setVarByRef("a1", $a1);
            $this->setVarByRef("a2", $a2);
            $this->setVarByRef("b1", $b1);
            $this->setVarByRef("b2", $b2);
            $this->setVarByRef("c1", $c1);
            $this->setVarByRef("c2", $c2);
            $this->setVarByRef("d1", $d1);
            $this->setVarByRef("d8", $d8);
            $this->setVarByRef("e1", $e1);
            $this->setVarByRef("e8", $e8);
            $this->setVarByRef("other", $other);
            $this->setVarByRef("f1", $f1);
            $this->setVarByRef("f2", $f2);
            $this->setVarByRef("f3", $f3);
            $this->setVarByRef("g9", $g9);
            $this->setVarByRef("g10", $g10);
            $this->setVarByRef("h11", $h11);
            $this->setVarByRef("h12", $h12);
            $this->setVarByRef("d6", $d6);
            $this->setVarByRef("d7", $d7);
            $mode = "fixup";
            $this->setVarByRef("mode", $mode);
            return "outcomesandassessmentthreeScience_tpl.php";
        }

        $id = $this->getParam("id");
        $mode = "fixup";

        $a4 = $a1 * $a3;
        switch ($a2) {
            case 1:
                $a8 = $a4 * $a5;
                break;
            case 2:
                $a8 = $a4 * $a6;
                break;
            case 3:
                $a8 = $a4 * $a7;
                break;
            default:
                $errormessages[] = "Level of course must be 1, 2 or 3";
        }
        $a13 = $a4 + $a8;
        $a13 = round($a13);

        $b4 = $b1 * $b3;
        switch ($b2) {
            case 1:
                $b8 = $b4 * $b5;
                break;
            case 2:
                $b8 = $b4 * $b6;
                break;
            case 3:
                $b8 = $b4 * $b7;
                break;
            default:
                $errormessages[] = "Level of course must be 1, 2 or 3";
        }
        $b13 = $b4 + $b8;
        $b13 = round($b13);

        $c4 = $c1 * $c3;
        switch ($c2) {
            case 1:
                $c8 = $c4 * $c5;
                break;
            case 2:
                $c8 = $c4 * $c6;
                break;
            case 3:
                $c8 = $c4 * $c7;
                break;
            default:
                $errormessages[] = "Level of course must be 1, 2 or 3";
        }
        $c13 = $c4 + $c8;
        $c13 = round($c13);

        $d4 = $d1 * $d3;
        $d13 = $d4 + $d8;
        $d13 = round($d13);

        $e13 = $e8;

        $f4 = $f1 * $f3;
        switch ($f2) {
            case 1:
                $f8 = $f4 * $f5;
                break;
            case 2:
                $f8 = $f4 * $f6;
                break;
            case 3:
                $f8 = $f4 * $f7;
                break;
            default:
                $errormessages[] = "Level of course must be 1, 2 or 3";
        }
        $f13 = $f4 + $f8;
        $f13 = round($f13);

        $g13 = $g9 + $g10;
        $g13 = round($g13);

        $h13 = $h11 + $h12;
        $h13 = round($h13);

        $i13 = $a13 + $b13 + $c13 + $d13 + $e13 + $f13 + $g13 + $h13;

        $formdata = array();
        $formdata["a1"] = $a1;
        $formdata["a2"] = $a2;
        $formdata["b1"] = $b1;
        $formdata["b2"] = $b2;
        $formdata["c1"] = $c1;
        $formdata["c2"] = $c2;
        $formdata["d1"] = $d1;
        $formdata["d8"] = $d8;
        $formdata["e1"] = $e1;
        $formdata["e8"] = $e8;
        $formdata["other"] = $other;
        $formdata["f1"] = $f1;
        $formdata["f2"] = $f2;
        $formdata["f3"] = $f3;
        $formdata["g9"] = $g9;
        $formdata["g10"] = $g10;
        $formdata["h11"] = $h11;
        $formdata["h12"] = $h12;
        $formdata["d6"] = $d6;
        $formdata["d7"] = $d7;

        $formdata = serialize($formdata);
        $$issubmit = $this->getParam('next');
        if (!empty($issubmit)) {
            $this->objformdata->saveData($id, $formname, $formdata);
        }

        $this->setVarByRef("a1", $a1);
        $this->setVarByRef("a2", $a2);
        $this->setVarByRef("b1", $b1);
        $this->setVarByRef("b2", $b2);
        $this->setVarByRef("c1", $c1);
        $this->setVarByRef("c2", $c2);
        $this->setVarByRef("d1", $d1);
        $this->setVarByRef("d8", $d8);
        $this->setVarByRef("e1", $e1);
        $this->setVarByRef("e8", $e8);
        $this->setVarByRef("other", $other);
        $this->setVarByRef("f1", $f1);
        $this->setVarByRef("f2", $f2);
        $this->setVarByRef("f3", $f3);
        $this->setVarByRef("g9", $g9);
        $this->setVarByRef("g10", $g10);
        $this->setVarByRef("h11", $h11);
        $this->setVarByRef("h12", $h12);
        $this->setVarByRef("d6", $d6);
        $this->setVarByRef("d7", $d7);

        $this->setVarByRef("a4", $a4);
        $this->setVarByRef("a8", $a8);
        $this->setVarByRef("a13", $a13);
        $this->setVarByRef("b4", $b4);
        $this->setVarByRef("b8", $b8);
        $this->setVarByRef("b13", $b13);
        $this->setVarByRef("c4", $c4);
        $this->setVarByRef("c8", $c8);
        $this->setVarByRef("c13", $c13);
        $this->setVarByRef("d4", $d4);
        $this->setVarByRef("d13", $d13);
        $this->setVarByRef("e13", $e13);
        $this->setVarByRef("f4", $f4);
        $this->setVarByRef("f8", $f8);
        $this->setVarByRef("f13", $f13);
        $this->setVarByRef("g13", $g13);
        $this->setVarByRef("h13", $h13);
        $this->setVarByRef("i13", $i13);

        $this->setVarByRef("id", $id);
        return "outcomesandassessmentthreeScience_tpl.php";
    }

    public function __showeditCourseProposal() {

        $telephone = $this->getParam("telephone");
        $title = $this->getParam("title");
        $owner = $this->getParam("contact");
        $department = $this->getParam("department");
        $id = $this->getParam('docid');

        $this->setVarByRef("telephone", $telephone);
        $this->setVarByRef("title", $title);
        $this->setVarByRef("contact", $contact);
        $this->setVarByRef("department", $department);
        $this->setVarByRef("id", $id);

        /* $selected = $this->getParam('selected');
          $mode = "new";
          $this->setVarByRef("mode", $mode);
          $this->setVarByRef("selected", $selected); */
        return "editCourseProposal_tpl.php";
    }

    public function __forwarding() {
        $id = $this->getParam('id');
        $mode = $this->getParam('mode');
        $from = $this->getParam('from');
        $selected = $this->getParam('selected');
        $document = $this->documents->getDocument($id);
        $faculties = $this->faculties->getFaculties();
       // $department = $this->getParam("department");
        $faculty = $this->faculties->getFaculty($document['department']);
    
        $this->setVarByRef("users", $users);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("faculty",$faculty);
        $this->setVarByRef("departments", $faculties);
        $this->setVarByRef("department", $department);
        $this->setVarByRef('from', $from);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);

        return "showforwarding_tpl.php";
    }

    public function __sendDoc() {
        $users = $this->getParam("selectedusers");
        $id = $this->getParam("id");
        $comments = $this->getParam('comments');
        $from = $this->getParam('from');
        $link = new link($this->uri(array("action" => "showcomments", "id" => $id)));
        /*
          if (count($users) > 0) {
          $recipientUserId = $users[0];
          $recipientEmailAddress = $this->objUser->email($recipientUserId);
          $recipientEmailAddress = 'palesa.mokwena@students.wits.ac.za';
          $subject = $this->objSysConfig->getValue('FWD_DOC_EMAIL_SUB', 'apo');
          $subject = str_replace("{sender}", $this->objUser->fullname(), $subject);
          $subject = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $subject);

          $body = $this->objSysConfig->getValue('FWD_DOC_EMAIL_BD', 'apo');

          $body = str_replace("{link}", $link->href, $body);
          $body = str_replace("{sender}", $this->objUser->fullname(), $body);
          $body = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $body);


          $this->users->sendEmail($subject, $body, $recipientEmailAddress);
          //now update the current user
          $this->documents->changeCurrentDocumentUser($id, $recipientUserId);
          $message = "Document forwarded. Email has been sent to " . $this->objUser->fullname($recipientUserId);
          $this->setVarByRef("message", $message);
          }
         */
        return $from;
    }

    /**
     *
     * The standard dispatch method for the apo module.
     * The dispatch method uses methods determined from the action
     * parameter of the querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     * q
     * @access public
     * @param $action
     * @return A call to the appropriate method
     *
     */
    public function __facultymanagement() {
        $selected = "facultymanagement";
        $faculties = $this->faculties->getFaculties(0, 10, $this->mode);
        $this->setVarByRef("faculties", $faculties);
        $this->setVarByRef("selected", $selected);

        return "facultymanagement_tpl.php";
    }

    /*
     * This method is used to add a new faculty
     * @param none
     * @access public
     * @return the form that will be used to capture the information for the new
     * faculty
     */

    public function __newfaculty() {
        $selected = $this->getParam('selected');
        $mode = "new";
        $action = "registerfaculty";
        $this->setVarByRef("action", $action);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);

        return "addeditfaculty_tpl.php";
    }

    /*
     * This method is used to add a new faculty
     * @param none
     * @access public
     * @return the form that will be used to edit the information for the faculty
     */

    public function __editfaculty() {
        $selected = $this->getParam('selected');
        $mode = "edit";
        $action = "editfaculty";
        $id = $this->getParam('id');
        $data = $this->faculties->getFaculty($id);

        $this->setVarByRef("action", $action);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("faculties", $data);
        $this->setVarByRef("id", $id);

        return "addeditfaculty_tpl.php";
    }

    public function __registerfaculty() {
        $name = $this->getParam('faculty');
        $contact = $this->getParam('contact');
        $telephone = $this->getParam('telephone');

        if ($this->faculties->exists($name)) {
            $errormessage = "Faculty Name already exists";
            die();
        } else {
            $parentid = $this->getParam('parentfolder');
            $fac = $this->faculties->getFaculty($parentid);
            $parent = $fac['path'];
            $path = "";
            if ($parent) {
                $path .= $parent . '/' . $name;
            } else {
                $path .= $name;
            }
            $data = array("name" => $name, "contact" => $contact, "telephone" => $telephone);

            $this->faculties->addFaculty($data, $path);

            return $this->nextAction('facultymanagement');
        }
    }

    public function __updatefaculty() {
        $faculty = $this->getParam('faculty');
        $contact = $this->getParam('contact');
        $telephone = $this->getParam('telephone');

        if (empty($contact)) {
            // using this user id, get the full name and compare it with contact person!
            $contact = $this->objUser->fullname($userid);
        }

        $data = array("name" => $faculty, "contact_person" => $contact, "telephone" => $telephone, "userid" => $this->objUser->userId());
        $this->faculties->editFaculty($this->getParam('id'), $data);

        return $this->nextAction('facultymanagement', array('folder' => '0'));
    }

    public function __deletefaculty() {
        $id = $this->getParam('id');
        $this->faculties->deleteFaculty($id);

        return $this->nextAction('facultymanagement', array('folder' => '0'));
    }

    /*
     * This method is used to display the information for a document on a pdf.
     * @param none
     * @access public
     * @return the data exported to a pdf
     */

    public function __makepdf() {
        $id = $this->getParam('id');
        $all = $this->getParam('all');
        $overview = $this->getParam('overview');
        $rulesandsyllabusone = $this->getParam('rulesandsyllabusone');
        $rulesandsyllabustwo = $this->getParam('rulesandsyllabustwo');
        $subsidy = $this->getParam('subsidy');
        $outcomesandassessmentone = $this->getParam('outcomesandassessmentone');
        $outcomesandassessmenttwo = $this->getParam('outcomesandassessmenttwo');
        $outcomesandassessmentthree = $this->getParam('outcomesandassessmentthree');
        $resources = $this->getParam('resources');
        $collaborations = $this->getParam('collaborations');
        $review = $this->getParam('review');
        $comments = $this->getParam('comments');
        $feedback = $this->getParam('feedback');

        $documents = $this->documents->getDocument($id);

        $myid = $this->objUser->userId();
        //$documents = $this->documents->getdocuments(0, 20, $this->mode, "N", $myid);
        $createPdf = False;
        $fullnames = $this->objUser->fullName() . "'s Document";
        if (count($documents) > 1) {
            $fullnames .= "s";
        }

        if (!empty($documents)) {
            $createPdf = True;
            // get all the data for these documents
            $text1 = "";

            //foreach ($documents as $row) {
            $row = $documents; // in case i need to modify my code late to use foreach.
            if ($overview == 'on' || $all == 'on') {
                $overview = $this->objformdata->getFormData("overview", $row['id']);
                $overviewTable = $this->objFormatting->getOviewviewTable($overview);
            }
            if ($rulesandsyllabusone == 'on' || $all == 'on') {
                $rulesandsyllabusone = $this->objformdata->getFormData("rulesandsyllabusone", $row['id']);
                $rulesAndSyllabusoneTable = $this->objFormatting->getRulesAndSyllabusOne($rulesandsyllabusone);
            }
            if ($rulesandsyllabustwo == 'on' || $all == 'on') {
                $rulesandsyllabustwo = $this->objformdata->getFormData("rulesandsyllabustwo", $row['id']);
                $rulesAndSyllabustwoTable = $this->objFormatting->getRulesAndSyllabusTwo($rulesandsyllabustwo);
            }
            if ($subsidy == 'on' || $all == 'on') {
                $subsidyRequirements = $this->objformdata->getFormData("subsidyrequirements", $row['id']);
                $subsidyRequirementsTable = $this->objFormatting->getSubsidyRequirements($subsidyRequirements);
            }
            if ($outcomesandassessmentone == 'on' || $all == 'on') {
                $outcomesandassessmentone = $this->objformdata->getFormData("outcomesandassessmentone", $row['id']);
                $outcomesandassessmentoneTable = $this->objFormatting->getOutcomesAndAssessmentsOne($outcomesandassessmentone);
            }
            if ($outcomesandassessmenttwo == 'on' || $all == 'on') {
                $outcomesandassessmenttwo = $this->objformdata->getFormData("outcomesandassessmenttwo", $row['id']);
                $outcomesandassessmenttwoTable = $this->objFormatting->getOutcomesAndAssessmentsTwo($outcomesandassessmenttwo);
            }
            if ($outcomesandassessmentthree == 'on' || $all == 'on') {
                $outcomesandassessmentthree = $this->objformdata->getFormData("outcomesandassessmentthree", $row['id']);
                $outcomesandassessmentthreeTable = $this->objFormatting->getOutcomesAndAssessmentsThree($outcomesandassessmentthree);
            }
            if ($resources == 'on' || $all == 'on') {
                $resources = $this->objformdata->getFormData("resources", $row['id']);
                $resourcesTable = $this->objFormatting->getResources($resources);
            }
            if ($collaborations == 'on' || $all == 'on') {
                $collaborations = $this->objformdata->getFormData("collaborationandcontracts", $row['id']);
                $colloborationsTable = $this->objFormatting->getCollaborationAndContracts($collaborations);
            }
            if ($review == 'on') {
                $review = $this->objformdata->getFormData("review", $row['id']);
                $reviewTable = $this->objFormatting->getReview($review);
            }
            if ($comments == 'on' || $all == 'on') {
                $comments = $this->objformdata->getFormData("comments", $row['id']);
                $commentsTable = $this->objFormatting->getComments($comments);
            }
            if ($feedback == 'on' || $all == 'on') {
                $feedback = $this->objformdata->getFormData("feedback", $row['id']);
                $feedbackTable = $this->objFormatting->getFeedback($feedback);
            }

            //get the pdfmaker classes
            $text1 .= '<h1>' . $fullnames . "</h1><br><br>\r\n"
                    . $overviewTable
                    . $rulesAndSyllabusoneTable
                    . $rulesAndSyllabustwoTable
                    . $subsidyRequirementsTable
                    . $outcomesandassessmentoneTable
                    . $outcomesandassessmenttwoTable
                    . $outcomesandassessmentthreeTable
                    . $resourcesTable
                    . $colloborationsTable
                    . $reviewTable
                    . $contactTable
                    . $commentsTable
                    . $feedbackTable;
        }
        //}
        $objPdf = $this->getObject('tcpdfwrapper', 'pdfmaker');
        //Write pdf
        $objPdf->initWrite();
        if ($createPdf == True) {
            $objPdf->partWrite($text1);
        }
        if ($createPdf == True) {
            return $objPdf->show();
        } else {
            echo 'Nothing to display in pdf.';
        }
    }

    /*
     * This method is used to determine the content that the user wants to display
     * in a pdf that they will want to download or print.
     * @access public
     * @param $id The id of the document that the user would like to print
     * @return template page where the user can customize the data
     */

    public function __selectpdf() {
        $id = $this->getParam('id');
        $document = $this->documents->getDocument($id);

        $this->setVarByRef("id", $id);
        $this->setVarByRef("document", $document);

        return "selectpdf_tpl.php";
    }

    /*
     * This method is used to manage users that are involved in the process
     * document handling for the academic policy office.
     * @param none
     * @access public
     * @return the template displaying how to add, delete, edit users, and also shows
     * the number of users together with their roles
     */

    public function __usermanagement() {
        $users = $this->users->getUsers();
        $faculties = $this->faculties->getFaculties();

        $this->setVarByRef("departments", $faculties);
        $this->setVarByRef("users", $users);
        $this->setVarByRef("selected", $selected);

        return "usermanagement_tpl.php";
    }

    /*
     * This method is used to add a user to the database
     * @access public
     * @return the user template that displays all the users
     */

    public function __registeruser() {
        $name = $this->getParam('name');
        $role = $this->getParam('role');
        $faculties = $this->faculties->getFaculties();
        $department = $this->getParam('department');
        $email = $this->getParam('email');
        $telephone = $this->getParam('telephone');

        if ($this->users->exists($name)) {
            $errormessage = "User Name already exists";
            die();
        } else {
            /* $parentid = $this->getParam('parentfolder');
              $fac = $this->faculties->getFaculty($parentid);
              $parent = $fac['path'];
              $path = "";
              if ($parent) {
              $path .= $parent . '/' . $name;
              } else {
              $path .= $name;
              } */

            $data = array("name" => $name, "role" => $role, "email" => $email, "telephone" => $telephone, "department" => $department);

            $this->users->addUser($data);

            $this->setVarByRef("departments", $faculties);

            return $this->nextAction('usermanagement');
        }
    }

    public function __fowarddocument() {
        $users = $this->getParam("selectedusers");
        $id = $this->getParam("id");
        $from = $this->getParam('from');
        $role = $this->getParam('role');

        $link = new link($this->uri(array("action" => "showcomments", "id" => $id)));


        if (count($users) > 0) {
            $recipientUserId = $users[0];
            $recipientEmailAddress = $this->objUser->email($recipientUserId);
            $recipientEmailAddress = 'palesa.mokwena@students.wits.ac.za';
            $subject = $this->objSysConfig->getValue('FWD_DOC_EMAIL_SUB', 'apo');
            $subject = str_replace("{sender}", $this->objUser->fullname(), $subject);
            $subject = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $subject);

            $body = $this->objSysConfig->getValue('FWD_DOC_EMAIL_BD', 'apo');

            $body = str_replace("{link}", $link->href, $body);
            $body = str_replace("{sender}", $this->objUser->fullname(), $body);
            $body = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $body);


            $this->users->sendEmail($subject, $body, $recipientEmailAddress);
            //now update the current user
            $this->documents->changeCurrentDocumentUser($id, $recipientUserId);
            $message = "Document forwarded. Email has been sent to " . $this->objUser->fullname($recipientUserId);
            $this->setVarByRef("message", $message);
        }

        return $from;
    }

    function __showSection() {
        $from = $this->getParam('from');
        $id = $this->getParam('id');
        $mode = $this->getParam('mode');
        $document = $this->documents->getDocument($id);
        $selected = $this->getParam('selected');

        $this->setVarByRef("id", $id);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("document", $document);

        return $from;
    }

    public function __forwardtoAPO() {
        $id = $this->getParam('id');

        $mode = $this->getParam('mode');
        $selected = $this->getParam('selected');
        $document = $this->documents->getDocument($id);
        $users = $this->users->getUsers();
        $faculties = $this->faculties->getFaculties();
        $department = $this->getParam("department");

        $this->setVarByRef("users", $users);
        $this->setVarByRef("departments", $faculties);
        $this->setVarByRef("document", $document);
        $this->setVarByRef("department", $department);
        $this->setVarByRef("mode", $mode);
        $this->setVarByRef("selected", $selected);
        $this->setVarByRef("id", $id);

        return "commenting_tpl.php";
    }

    public function __forwardDocAPO() {

        $id = $this->getParam("id");
        $from = $this->getParam('from');
        $role = $this->getParam('role');

        $link = new link($this->uri(array("action" => "showcomments", "id" => $id)));

        $recipientEmailAddress = $this->objUser->email($recipientUserId);
        $senderEmailAddress = $this->objUser->email($senderUserId);
        $recipientEmailAddress = 'palesa.mokwena@students.wits.ac.za';
        $subject = $this->objSysConfig->getValue('FWD_DOC_EMAIL_SUB', 'apo');
        $subject = str_replace("{sender}", $this->objUser->fullname(), $subject);
        $subject = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $subject);

        $body = $this->objSysConfig->getValue('FWD_DOC_EMAIL_BD', 'apo');

        $body = str_replace("{link}", $link->href, $body);
        $body = str_replace("{sender}", $this->objUser->fullname(), $body);
        $body = str_replace("{receiver}", $this->objUser->fullname($recipientUserId), $body);


        $this->users->sendEmail($subject, $body, $recipientEmailAddress);
        //now update the current user
        $this->documents->changeCurrentDocumentUser($id, $recipientUserId);
        $message = "Document forwarded. Email has been sent to " . $this->objUser->fullname($recipientUserId);
        $this->setVarByRef("message", $message);

        return $this->nextAction('confirmation');
    }

    public function __confirmation() {

        return "confirmation_tpl.php";
    }

}

?>