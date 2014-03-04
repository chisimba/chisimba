<?php

class gift extends controller {

    public $id;
    public $msg;
    public $giftPolicyAccepted = "false";
    public $clickedAdd = "false";

    function init() {
        // Importing classes for use in controller
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject("language", "language");
        $this->objDbGift = $this->getObject("dbgift");
        $this->objGift = $this->getObject("giftops");
        $this->objDepartments = $this->getObject("dbdepartments");
        $this->objAttachments = $this->getObject("dbattachments");
        $this->objHome = $this->getObject("home");
        $this->objGroupAdminModel = & $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject("user", "security");
        $this->objEdit = $this->getObject("edit");
        $this->objGiftUser = $this->getObject("dbuserstbl");
        $this->objConfig = $this->getObject('altconfig', 'config');
        if ($this->objGiftUser->policyAccepted() == 'Y') {
            $this->giftPolicyAccepted = "true";
        }
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->divisionLabel = $this->objSysConfig->getValue('DIVISION_LABEL', 'gift');
        $this->adminEmail = $this->objSysConfig->getValue('DIVISION_LABEL', 'gift');
        $this->minAmountToAlert = $this->objSysConfig->getValue('MIN_AMOUNT_FOR_ALERT', 'gift');
        $this->minAmount = $this->objSysConfig->getValue('MIN_AMOUNT', 'gift');
        $test = "test";
        // Initialising $data (holds the data from database when edit link is called)
        $this->data = array();
    }

    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $this->setLayoutTemplate("gift_layout_tpl.php");
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
        $departmentid = $this->getParam("departmentid");
        $departmentname = $this->objDepartments->getDepartmentName($departmentid);
        if ($departmentid == '') {
            $depts = $this->objDepartments->getDepartments();

            if (count($depts) > 0) {
                $defaultDept = $depts[0];

                $departmentid = $defaultDept['id'];
                $departmentname = $defaultDept['name'];
                $this->setVarByRef("departmentname", $departmentname);
                $this->setVarByRef("departmentid", $departmentid);
                $this->setSession("departmentid", $departmentid);
            }
        } else {
            $this->setSession("departmentid", $departmentid);
            $this->setVarByRef("departmentname", $departmentname);
            $this->setVarByRef("departmentid", $departmentid);
        }
        $gifts = $this->objDbGift->getGifts($departmentid);
        $this->setVarByRef("gifts", $gifts);
        return "home_tpl.php";
    }

    public function __editdepartment() {
        $departmentid = $this->getParam("id");
        $departmentname = $this->objDepartments->getDepartmentName($departmentid);
        $this->setVarByRef("departmentname", $departmentname);
        $this->setVarByRef("departmentid", $departmentid);
        return "editdepartment_tpl.php";
    }

    public function __updatedepartment() {
        $id = $this->getParam("id");
        $name = $this->getParam("name");

        if ($name == '') {
            $errormessage = "Department Name required";
            $this->setVarByRef("errormessage", $errormessage);
            $this->setVarByRef("departmentname", $name);
            $this->setVarByRef("departmentid", $id);
            return "editdepartment_tpl.php";
        }

        $this->objDepartments->updateDepartment($id, $name);
        $this->nextAction("home");
    }

    public function __createdepartment() {
        $name = $this->getParam('departmentname');
        if ($name == '') {
            $errormessage = "Department Name required";
            $this->setVarByRef("errormessage", $errormessage);
            $gifts = $this->objDbGift->getGifts($departmentid);
            $departmentid = $this->getParam('selecteddepartment');
            if ($departmentid == '') {
                $departmentid = $this->getSession("departmentid");
            }
            $departmentname = $this->objDepartments->getDepartmentName($departmentid);
            $this->setVarByRef("departmentname", $departmentname);
            $this->setVarByRef("departmentid", $departmentid);

            $this->setVarByRef("gifts", $gifts);
            return "home_tpl.php";
        } else if ($this->objDepartments->exists($name)) {
            $errormessage = "Department Name already exists";
            $this->setVarByRef("errormessage", $errormessage);
            $gifts = $this->objDbGift->getGifts($departmentid);
            $departmentid = $this->getParam('selecteddepartment');
            if ($departmentid == '') {
                $departmentid = $this->getSession("departmentid");
            }
            $departmentname = $this->objDepartments->getDepartmentName($departmentid);
            $this->setVarByRef("departmentname", $departmentname);
            $this->setVarByRef("departmentid", $departmentid);
            $this->setVarByRef("editdepartmentname", $name);
            $this->setVarByRef("gifts", $gifts);
            return "home_tpl.php";
        } else {
            $parentid = $this->getParam('selecteddepartment');
            $dept = $this->objDepartments->getDepartment($parentid);
            $parent = $dept['path']; // $this->objGift->getParent($dept['path']);
            $path = "";
            if ($parent) {
                $path = $parent . '/' . $name;
            } else {
                $path = $name;
            }
            $this->objDepartments->addDepartment($name, $path);
            $this->objGroupAdminModel->addGroup($name, $name);
            return $this->nextAction("home");
        }
    }

    function __search() {
        $query = $this->getParam("query");
        if ($query == '') {
            return $this->nextAction("home");
        }
        $gifts = $this->objDbGift->searchGifts($query);
        $this->setVarByRef("gifts", $gifts);

        return "home_tpl.php";
    }

    function __confirmdeletedepartment() {
        $id = $this->getParam("id");
        $giftsCount = $this->objDbGift->getGiftCountByDepartment($id);
        $subdeptscount = $this->objDepartments->getSubDepartmentsCount($id);
        $departmentname = $this->objDepartments->getDepartmentName($id);
        $deletevalid = true;
        $errormessage = "";
        if ($giftsCount > 0) {
            $errormessage .= "<br/>Cannot delete " . strtolower($this->divisionLabel) . " named $departmentname, delete all the gifts that belong to this $this->divisionLabel first";
            $deletevalid = false;
        }
        if ($subdeptscount > 1) {
            $errormessage .= "<br/>Cannot delete " . strtolower($this->divisionLabel) . " named $departmentname, delete  the following sub-" . strtolower($this->divisionLabel) . "s that belong to this " . strtolower($this->divisionLabel) . " first<br/>" . $subdepts;
            $deletevalid = false;
        }
        if (!$deletevalid) {
            $this->setVarByRef("errormessage", $errormessage);
            $gifts = $this->objDbGift->getGifts($departmentid);
            $this->setVarByRef("gifts", $gifts);
            return "home_tpl.php";
        } else {

            $this->setVarByRef("departmentid", $id);
            return "deletedepartment_tpl.php";
        }
    }

    /*
     * Search for a gift
     */

    public function __searchGift() {
        //echo $action;
        $searchkey = $this->getParam('giftname');

        $this->setVarByRef('searchStr', $searchkey);
        return "home_tpl.php";
    }

    /**
     * Add link clicked from home page, calls this method
     * @return string
     */
    function __add() {

        $mode = "add";
        $this->setVarByRef("mode", $mode);
        if ($this->giftPolicyAccepted == "true") {

            return "addeditgift_tpl.php";
        } else {
            return "giftpolicy_tpl.php";
        }
    }

    function __showuseractivity() {
        $action = "retrieveuseractivity";
        $this->setVarByRef("action", $action);
        return "selectdates_tpl.php";
    }

    function __retrieveuseractivity() {
        $startDate = $this->getParam('startdate');
        $endDate = $this->getParam('enddate');
        $module = "gift";


        $data = $this->objDbGift->getUserActivity($startDate, $endDate, $module);
        $this->setVarByRef("data", $data);
        $this->setVarByRef("startdate", $startDate);
        $this->setVarByRef("enddate", $endDate);
        $this->setVarbyRef("modulename", $module);
        return "useractivity_tpl.php";
    }

    /**
     * Submits the addition of a new gift to the database
     * and returns to the home page
     * @return string
     */
    function __save() {
        $errormessages = array();
        $name = $this->getParam('giftname');                // Gift's name

        if ($name == '') {
            $errormessages[] = "Gift Name";
        }

        

        $donor = $this->getParam('donor');             // Donor name
        if ($donor == '') {
            $errormessages[] = "Donors";
        }
        $recipient = $this->objUser->userid();         // Recipient name

        $description = $this->getParam('giftdescription');  // Description

        $value = $this->getParam('giftvalue');

        if ($value < $this->minAmount) {
            $errormessages[] = "Value should be greater or equal to ZAR" . $this->minAmount . "";
        }
        /*if (!is_numeric($value)) {
            $errormessages[] = "Value must be integer";
        }*/

        $division = $this->getSession('departmentid');


        if( $this->objDbGift->exists($name,$division)){
            $errormessages[]="Gift name supplied already used";
        }
        $type = $this->getParam('type');
        if ($type == "Select ...") {
            $errormessages[] = "Gift type ";
        }

        $comments = $this->getParam("comments");

        $date_recieved = $this->getParam("date_recieved");
        $includeattachment = $this->getParam("includeattachments");
        
        if (count($errormessages) > 0) {
            $this->setVarByRef("errormessages", $errormessages);
            $mode = "fixup";
            $action = "save";
            $this->setVarByRef("action", $action);
            $this->setVarByRef("mode", $mode);
            $this->setVarByRef("name", $name);
            $this->setVarByRef("donor", $donor);
            $this->setVarByRef("type", $type);
            $this->setVarByRef("comments", $comments);
            $this->setVarByRef("value", $value);
            $this->setVarByRef("description", $description);

            return "addeditgift_tpl.php";
        }

        $result = $this->objDbGift->addInfo(
                        $donor,
                        $recipient,
                        $name,
                        $description,
                        $value,
                        $listed,
                        $division,
                        $type,
                        $comments,
                        $date_recieved);



        if ($value > $this->minAmountToAlert) {
            $link = new link($this->uri(array("action" => "view", "id" => $result)));
            $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $subject = $objSysConfig->getValue('EMAIL_SUBJECT', 'gift');
            $departmentName = $this->objDepartments->getDepartmentName($division);
            $subject = str_replace("{department}", $departmentName, $subject);
            $subject = str_replace("{names}", $this->objUser->fullname(), $subject);
            $body = $objSysConfig->getValue('EMAIL_BODY', 'gift');

            $body = str_replace("{department}", $departmentName, $body);
            $body = str_replace("{names}", $this->objUser->fullname(), $body);
            $body = str_replace("{giftname}", "'" . $name . "'", $body);
            $body.=" " . $link->href;

            $groupName = $objSysConfig->getValue('EMAIL_GROUP', 'gift');
            $groupOps = $this->getObject('groupops', 'groupadmin');
            $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $groupId = $objGroups->getId($groupName);
            $users = $groupOps->getUsersInGroup($groupId);
            $objMailer = $this->getObject('mailer', 'mail');
            $recipients = array();
            foreach ($users as $user) {
                $recipients[] = $this->objUser->email($user['auth_user_id']);
            }
            $objMailer->setValue('to', $recipients);
            $objMailer->setValue('from', $this->adminEmail);
            $objMailer->setValue('fromName', $this->objUser->fullnames);

            $objMailer->setValue('subject', $subject);

            $objMailer->setValue('body', strip_tags($body));
            $objMailer->setValue('AltBody', strip_tags($body));

            $objMailer->send();
        }

        if ($result) {
            if ($includeattachment == 'on') {
                return $this->nextAction("attach", array("id" => $result));
            }
        }
        return $this->nextAction('home', array("departmentid" => $division));
    }

    /**
     * Used to do the actual upload
     *
     */
    function __doajaxupload() {
        $dir = $this->objSysConfig->getValue('UPLOADS_DIR', 'gift');


        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objMkDir = $this->getObject('mkdir', 'files');

        $giftid = $this->getParam('giftid');
        $destinationDir = $dir . '/' . $giftid;



        $objMkDir->mkdirs($destinationDir);
        //@chmod($destinationDir, 0777);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            'all'
        );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir . '/';

        $result = $objUpload->doUpload(TRUE, $docname);


        if ($result['success'] == FALSE) {

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';

            return $this->nextAction('erroriframe', array('message' => 'Unsupported file extension.Only use txt, doc, odt, ppt, pptx, docx,pdf', 'file' => $filename, 'id' => $generatedid));
        } else {

            $filename = $result['filename'];
            $this->objAttachments->addAttachment($giftid, $filename);

            /*
              $myFile = "/dwaf/giftattachments/testFile.txt";
              $fh = fopen($myFile, 'w') or die("can't open file");
              $stringData = "dest == $destinationDir";

              fwrite($fh, $stringData);
              fclose($fh); */

            //  $result = $this->gift->update($id, $data);

            return $this->nextAction('ajaxuploadresults', array('id' => $generatedid, 'fileid' => $id, 'filename' => $filename));
        }
    }

    function __attach() {
        $id = $this->getParam("id");
        $this->setVarByRef('id', $id);
        return "upload_tpl.php";
    }

    function __downloadattachment() {
        $filename = $this->getParam("filename");
        $giftid = $this->getParam("giftid");
        $filepath = $giftid . '/' . $filename;
        return $this->objGift->downloadFile($filepath, $filename);
    }

    /**
     * Used to push through upload results for AJAX
     */
    function __ajaxuploadresults() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $fileid = $this->getParam('fileid');
        $this->setVarByRef('fileid', $fileid);

        $filename = $this->getParam('filename');
        $this->setVarByRef('filename', $filename);

        return 'ajaxuploadresults_tpl.php';
    }

    /**
     * Depending on the archived parameter, finds all gifts donated
     * to the specific owner based on whether the gift is archived
     * or not archived
     * @return string
     */
    function __result() {
        $recipient = $this->objUser->fullName();     // Recipient name

        $qry = "SELECT * FROM tbl_gift"; // WHERE recipient = '$recipient'";
        $this->data = $this->objDbGift->getInfo($qry);

        return "edit_tpl.php";
    }

    function __view() {
        $id = $this->getParam("id");
        $gift = $this->objDbGift->getGift($id);
        $this->setVarByRef("gift", $gift);

        return "viewgift_tpl.php";
    }

    /**
     * Edit link from edit template, calls this method
     * @return string
     */
    function __editgift() {
        $id = $this->getParam("id");
        $gift = $this->objDbGift->getGift($id);
        $this->setVarByRef("gift", $gift);
        $mode = "edit";
        $this->setVarByRef("mode", $mode);
        return "addeditgift_tpl.php";
    }

    /**
     * Updates a record in the database dependent on the gift that
     * was edited and returns to the home page.
     * @return string
     */
    function __update() {
        $errormessages = array();
        $name = $this->getParam('giftname');                // Gift's name

        if ($name == '') {
            $errormessages[] = "Gift Name required";
        }

        $donor = $this->getParam('donor');             // Donor name
        if ($donor == '') {
            $errormessages[] = "Donor required";
        }
        $recipient = $this->objUser->userid();         // Recipient name

        $description = $this->getParam('giftdescription');  // Description

        $value = $this->getParam('giftvalue');

        if (!is_numeric($value)) {
            $errormessages[] = "Value must be integer";
        }

        $division = $this->getParam('selecteddepartment');

        if ($division == "-1") {
            $errormessages[] = "Select departments";
        }
        $type = $this->getParam('type');
        if ($type == "Select ...") {
            $errormessages[] = "Select gift type ";
        }

        $comments = $this->getParam("comments");

        if (count($errormessages) > 0) {
            $this->setVarByRef("errormessages", $errormessages);
            $mode = "fixup";
            $this->setVarByRef("mode", $mode);
            $this->setVarByRef("name", $name);
            $this->setVarByRef("donor", $donor);
            $this->setVarByRef("type", $type);
            $this->setVarByRef("value", $value);
            $this->setVarByRef("comments", $comments);
            $this->setVarByRef("description", $description);
            $this->setVarByRef("department", $this->objDepartments->getDepartment($division));
            return "addeditgift_tpl.php";
        }
        $id = $this->getParam('id');

        $result = $this->objDbGift->updateInfo(
                        $donor, $recipient, $name, $description, $value, $listed, $id, $comments);

        return $this->nextAction('home');
    }

    //shows the gift policy template
    function __viewPolicy() {
        return 'giftpolicy_tpl.php';
    }

    function __userExists() {
        $userid = $this->objUser->userId();
        return $this->objGiftUser->userExists($userid);
    }

    function __saveUser() {
        $this->clickedAdd = "true";
        //save the user info in the database
        $data = array('userid' => $this->objUser->userId(), 'time' => strftime('%Y-%m-%d %H:%M:%S', mktime()));
        $this->objGiftUser->addUser($data);
        if ($this->objGiftUser->policyAccepted() == 'Y') {
            $this->nextAction('home');
            $this->giftPolicyAccepted = "true";
        } else {
            $this->nextAction('viewPolicy');
        }
    }

    function __acceptPolicy() {
        $this->objGiftUser->acceptPolicy();
        $this->nextAction('add');
    }

    function __deletedepartment() {
        $id = $this->getParam("id");
        $this->objDepartments->deleteDepartment($id);
        return $this->nextAction("home"); //, array("departmentid" => $this->getSession("departmentid")));
    }

    function __confirmdeletegift() {
        $id = $this->getParam("id");
        $this->setVarByRef("giftid", $id);
        return "deletegift_tpl.php";
    }

    function __deletegift() {
        $id = $this->getParam("id");
        $this->objDbGift->deleteGift($id);
        return $this->nextAction("home", array("departmentid" => $this->getSession("departmentid")));
    }

    function __filter() {

        $filter = $this->getParam("filter");
        switch ($filter) {
            case 'By Date':
                return "filterbydate_tpl.php";
            case 'Gift Type':
                return "filterbygifttype_tpl.php";
            case 'Value':
                return "filterbyvalue_tpl.php";
            case 'Donor':
                return "filterbydonor_tpl.php";
        }
    }

    function __filterbydonor() {
        $donor = $this->getParam("donor");
        $gifts = $this->objDbGift->searchGiftsByDonor($donor);
        $this->setVarByRef("gifts", $gifts);
        return "home_tpl.php";
    }

    function __filterbygifttype() {
        $type = $this->getParam("type");
        $gifts = $this->objDbGift->searchGiftsByType($type);
        $this->setVarByRef("gifts", $gifts);
        return "home_tpl.php";
    }

    function __filterbyvalue() {
        $giftminvalue = $this->getParam('giftminvalue');
        $giftmaxvalue = $this->getParam('giftmaxvalue');
        $gifts = $this->objDbGift->searchGiftsByValue($giftminvalue, $giftmaxvalue);
        $this->setVarByRef("gifts", $gifts);
        return "home_tpl.php";
    }

    function __searchbydate() {
        $dateFrom = $this->getParam("date_from");
        $dateTo = $this->getParam("date_to");
        $gifts = $this->objDbGift->searchGiftsByDate($dateFrom, $dateTo);
        $this->setVarByRef("gifts", $gifts);

        return "home_tpl.php";
    }

    function __deleteattachment() {
        $id = $this->getParam("id");
        $this->objAttachments->deleteAttachment($id);
        $giftid = $this->getParam("giftid");
        $gift = $this->objDbGift->getGift($giftid);
        $this->setVarByRef("gift", $gift);
        $mode = "edit";
        $this->setVarByRef("mode", $mode);
        return "addeditgift_tpl.php";
    }

    function __exportospreadsheet() {
        $ex = $this->getObject('excelgenerator');

        $departmentid = $this->getSession("departmentid");
        $departmentname = $this->objDepartments->getDepartmentName($departmentid);

        $ex->generateExel($departmentid, $departmentname);
    }

    function __exportopdf() {
        $ex = $this->getObject('pdfgenerator');

        $departmentid = $this->getSession("departmentid");
        $departmentname = $this->objDepartments->getDepartmentName($departmentid);

        $ex->generatePdf($departmentid, $departmentname);
    }

}

?>
