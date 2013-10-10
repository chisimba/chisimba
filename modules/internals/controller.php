<?php

// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author monwabisi
 */
class internals extends controller {

    var $objAltConfig;
    var $objLanguage;

    public function init(){
        $this->objLanguage = $this->getObject('language','language');
    }

    /**
     * 
     * The standard dispatch method for the species module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch() {
        //Get action from query string and set default to view
        $action = $this->getParam('action', 'view');
        /*
         * Convert the action into a method (alternative to 
         * using case selections)
         */
        $method = $this->__getMethod($action);
        // Set the layout template to compatible one
        $this->setLayoutTemplate('layout_tpl.php');
        /*
         * Return the template determined by the method resulting 
         * from action
         */
        return $this->$method();
    }

    /**
     * Default mothod returned by the module
     * 
     * @access public
     * @return string
     */
    private function __view() {
        // All the action is in the blocks
        return "main_tpl.php";
    }

    /**
     * Push the request to the module administrator
     *
     *  @access private
     */
    private function __pushRequest() {
        //get database object
        $objDB = $this->getObject('dbinternals', 'internals');
        $objUser = $this->getObject('user', 'security');
        //get all leaves
        $objDB->_tableName = "tbl_leaves";
        $leaveList = $objDB->getAll();
        //holidays
        $holidays = array(
            date('Y') . '-01-01',
            date('Y') . '-03-21',
            date('Y') . '03-31',
            date('Y') . '-03-29',
            date('Y') . '-04-01',
            date('Y') . '-04-27',
            date('Y') . '-05-05',
            date('Y') . '-06-16',
            date('Y') . '-08-09',
            date('Y') . '-09-24',
            date('Y') . '-12-16',
            date('Y') . '-12-25',
            date('Y') . '-12-26'
        );
        $numberOfDays = 0;
        $carriedOver = 0;
        $minusDays = 0;
        $date = new DateTime();
        $startDate = $this->getParam('startdate', NULL);
        //set the end date
        $endDate = $this->getParam('enddate', NULL);
//        echo $startDate.'<br/>'.$endDate;
        $startYear = substr($startDate, '0', '4');
        $startMonth = substr($startDate, '5', '2');
        $startDay = substr($startDate, '8', '2');
        //set the start date
        $date->setDate($startYear, $startMonth, $startDay);
        for ($index = 0; $date->format('Y-m-d') <= $endDate; $index++) {
            //check if the date is valid
            if (checkdate($date->format('m'), $date->format('d'), $date->format('Y'))) {
                //check if holiday
                if (in_array($date->format('Y-m-d'), $holidays)) {
                    $minusDays++;
                }
                //if it is a holiday and is a Sat or Sun
                if ($date->format('D') == 'Sat' || $date->format('D') == 'Sun') {
                    //check if the day is a holiday
                    if (in_array($date->format('Y-m-d'), $holidays)) {
                        $carriedOver++;
                    }
                } else {
                    $numberOfDays++;
                }
                //if date is not valid increase increase the months
            } else {
                $date->modify('+1 month');
                if (!checkdate($date->fomat('m'), $date->format('d'), $date->format('Y'))) {
                    $date->modify('+1 year');
                }
            }
            //increase day count 
            $date->modify('+1 day');
        }
        $numberOfDays = $numberOfDays - $minusDays;
        //get user id
        $userId = $objUser->getUserId($objUser->userName());
        //get the leave ID
        $leaveID = $this->getParam('leaveid', NULL);
        $leaveID = str_replace('input_', '', $leaveID);
        $objDB->_tableName = "tbl_leaverecords";
        //put the default number of days as days left
        foreach ($leaveList as $leaveItem) {
            if ($leaveItem['id'] == $leaveID) {
                $leaveDefaultDays = $leaveItem['numberofdays'];
            }
        }
        $leaveRecordsDefaultValues = array(
            "id" => $leaveID,
            "userid" => $userId,
            "daysleft" => $leaveDefaultDays
        );
        //insert the values to the database
        $objDB->insert($leaveRecordsDefaultValues);
        //get the remaining number of days and then subtract the requested ones
        //change the table name
        $objDB->_tableName = "tbl_leaverecords";
        $leaveRecords = $objDB->getAll();
        //variable to contain the number of remaining  days
        $remainingDays = 0;
        $totalDaysTaken = 0;
        foreach ($leaveRecords as $recordItem) {
            if ($leaveID == $recordItem['id']) {
                if ($userId == $recordItem['userid']) {
                    $remainingDays = $recordItem['daysleft'];
                    //get the total number of days requested by the user
                    $totalDaysTaken = $recordItem['totaldaystaken'];
                }
            }
        }
        //subtract the requested days from the available days and then update the database
        if ($numberOfDays > 0) {
            $remainingDays = $remainingDays - $numberOfDays;
            if ($remainingDays > 0) {
                $totalDaysTaken = $totalDaysTaken + $numberOfDays;
                $queryString = "UPDATE tbl_leaverecords SET daysleft={$remainingDays}, totaldaystaken={$totalDaysTaken} WHERE id='{$leaveID}' AND userid={$userId}";
                $objDB->_execute($queryString);
                //insert the values into the database
                $dateOfRequest = date('Y-m-d');
                $objDB->postRequest($userId, $leaveID, $startDate, $endDate, $numberOfDays, $dateOfRequest);
            } else {
                //indicate that the number of days have ran out
            }
        } else {
            //indicate the the user cannot apply for less than one day
        }
    }

    /**
     * Accept leave application and output a pdf file with the corresponding information
     * 
     * @access public
     * @return object PDF file
     */
    public function __accept() {
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        require $this->objAltConfig->getModulePath() . 'pdfmaker/resources/tcpdf.php';
        /**
         * start of update request values
         */
        //record Id
        $id = $this->getParam('id', NULL);
        //user ID
        $userID = $this->getParam('x_data', NULL);
        //leave id
        $leaveID = $this->getParam('leaveid', NULL);
        //request status
        $requestStatus = $this->getParam('status', NULL);
        //database object
        $objDB = $this->getObject('dbinternals', 'internals');
        //comments
        $comments = $this->getParam('comments', NULL);
        $statement = "";
        if (strtolower($requestStatus == 'rejected')) {
            if (!empty($comments)) {
                $statement = 'Reason being ' . $comments;
            } else {
                $statement = 'No reason was provided';
            }
        }
        $values = array(
            'status' => $requestStatus,
            'comments' => $comments
        );
        //html to pdf test
        $objMail = & $this->getObject('mailer', 'mail');
        //pdate the database
        $objDB->update('id', $id, $values, 'tbl_internalrequests');
        //prepare to send message
        $objUser = $this->getObject('user', 'security');
        //get the user email address
        $userEmail = $objUser->email($userID);
        //get the user's full name
        $userFullName = $objUser->fullname($userID);
        //get the number of requested fays
        $objDB->_tableName = "tbl_internalrequests";
        $sqlStatement = "SELECT * FROM tbl_internalrequests WHERE leaveid='{$leaveID}' AND userid={$userID}";
        $requestInformation = $objDB->getArray($sqlStatement);
//        print_r($requestInformation);
        $days = $requestInformation[0]['days'];
        //get the start date
        $startDate = $requestInformation[0]['startdate'];
        //get the end date
        $endDate = $requestInformation[0]['enddate'];
        $requestDate = $requestInformation[0]['requestdate'];
        //get the default leave days
        $objDB->_tableName = "tbl_leaves";
        $sqlStatement = "SELECT * FROM tbl_leaves WHERE id='{$leaveID}'";
        $requestInformation = $objDB->getArray($sqlStatement);
        $defaultDays = $requestInformation[0]['numberofdays'];
        //date object for today's date
        //Prepare the message
//        $objMail->setValue('to', "wsifumba@gmail.com");
//        $objMail->setValue('from', 'noreply@hermes');
//        $objMail->setValue('fromName', 'Monwabisi');
//        $objMail->setValue('subject', 'Leaves appliction');
//        $objMail->setValue('body', "{$userFullName} your leave request has been " . $requestStatus . '<br/>' . $statement);
//        $objMail->setValue('htmlbody', "{$userFullName}your leave request has been " . $requestStatus . '<br/>' . $statement);
        /**
         * end of update request values
         */
        /**
         * @todo
         */
        $pdf = new TCPDF();
        $marginTop = 113;
        $marginLeft = 14;
        $htmlTable = "<br/><table>
            <tbody>
            <tr>";
        $leaveList = $objDB->getAll();
        $ndx = 0;
        foreach ($leaveList as $leaveItem) {
            if ($ndx == 2) {
                $htmlTable .= "</tr><tr>";
                $ndx = 0;
            }
            /**
             * @note  spaces are for layout purposes please do not remove them unless relpacing them with a more proffesional alternative
             */
            $htmlTable .= "<td>             " . $leaveItem['name'] . "<br/></td>";
            $ndx++;
        }

        /**
         * get the request information from the databese
         */
        $daysLeft = 0;
        $days --;
        $totalDaysTaken = 0;
        //change the table name
        $objDB->_tableName = "tbl_leaverecords";
        $requestInformation = $objDB->getAll();
        foreach ($requestInformation as $requestItem) {
            if ($requestItem['id'] == $leaveID && $requestItem['userid'] == $userID) {
                $daysLeft = $requestItem['daysleft'];
                $totalDaysTaken = $requestItem['totaldaystaken'];
                if ($requestStatus == 'rejected') {
                    $rejectedDays = $requestItem['daysleft'] + $days;
                    $daysLeft = $daysLeft - $rejectedDays;
                    if ($rejectedDays <= $defaultDays) {
                        $queryStatement = "UPDATE tbl_leaverecords SET daysleft={$rejectedDays}, daysleft={} WHERE id='{$requestItem['id']}'";
                        $objDB->_execute($queryStatement);
                    }
                }
            }
        }
        $formMessage = $this->objLanguage->languageText('mod_formmessage_internals','internals');
        $formMessage = str_replace('@numberofdays',$days,$formMessage);
        $formMessage = str_replace('@startdate','<b>'.$startDate.'</b>',$formMessage);
        $formMessage = str_replace('@enddate', '<b>'.$endDate.'</b>', $formMessage);
        $htmlTable .= "</tr></tbody></table>";
        $html = "
<div><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
<h1 align='center' ><font size='30'  ><u>{$this->objLanguage->languageText('phrase_formheading','system')}</u></font></h1><br/><br/><br/>
<table width='100%'>
<thead>
</thead>
<tbody>
<tr>
<td >
<b>{$this->objLanguage->languageText('word_name','system')}</b> <u>{$userFullName}</u>
</td>
<td>
<b>{$this->objLanguage->languageText('word_date','system')}</b> <u>{$requestDate}</u>
</td>
</tr>
<tr>
<td>
</td>
</tr>
<tr>
<td>
&nbsp;<b>{$this->objLanguage->languageText('word_position','system')}</b>
_____________________
</td>
</tr>
</tbody>
</table>
<br/>
<table>
<tbody>
<tr>
<td>
<div> {$formMessage}</div>
</td>
</tr>
</tbody>
</table>";
        $html .= $htmlTable . "
<br/><br/>
<table border='1'>
<thead>
<tr>
<td align='center'>
No. of Days available
</td>
<td align='center'>
No. of Days leave taken
</td>
<td align='cente'>
No. of Days leave balance
</td>
</tr>
</thead>
<tbody>
<tr>
<td align='center'>
{$defaultDays}
</td>
<td align='center'>
{$totalDaysTaken}
</td>
<td align='center'>
{$daysLeft}
</td>
</tr>
</tbody>
</table>
<br/><br/><br/><br/><br/><br/><br/><br/>
<table width='100%' >
<tbody>
<tr>
<td>
Approved / Rejected By<br/> Operation Department<br/><br/><br/><br/>
<u>_____________________</u>
</td>
<td>
</td>
<td>
Approved / Rejected By<br/> General manager<br/><br/><br/><br/>
<u>_____________________</u>
</td>
</tr>
</tbody>
</table>
</div>";
//        $pdf->SetAuthor("Monwai");
        $pdf->SetSubject("TCPDF Tutorial");
        $pdf->SetKeywords("TCPDF, PDF, example, test, guide");
        $pdf->AddPage('');
        $pdf->setImageScale(5);
        $logoImage = "";
        if (file_exists($this->objAltConfig->getsiteRootPath() . $this->objAltConfig->getContentPath() . 'users/' . $objUser->getUserId($objUser->userName()) . '/logo.png')) {
            $logoImage = $this->objAltConfig->getsiteRootPath() . $this->objAltConfig->getContentPath() . 'users/' . $objUser->getUserId($objUser->userName()) . '/logo.png';
        } elseif (file_exists($this->objAltConfig->getsiteRootPath() . $this->objAltConfig->getContentPath() . 'users/' . $objUser->getUserId($objUser->userName()) . '/logo.jpg')) {
            $logoImage = $this->objAltConfig->getsiteRootPath() . $this->objAltConfig->getContentPath() . 'users/' . $objUser->getUserId($objUser->userName()) . '/logo.jpg';
        } else {
            $logoImage = $this->objAltConfig->getsiteRootPath() . $this->objAltConfig->getContentPath() . 'users/' . $objUser->getUserId($objUser->userName()) . '/logo.gif';
        }
//            echo $logoImage;
        $pdf->Image($logoImage, 55, 5, 100, 30, '', 'http://www.tcpdf.org', '', true, 72);
//            $pdf->Image($this->objAltConfig->getModulePath() . "pdfmaker/resources/images/logo.jpg", 160, 10, 45, 60, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/unchkd.png", 14, 113, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/Untitled.png", 15, 113, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/unchkd.png", 14, 124, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/unchkd.png", 109, 113, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "pdfmaker/resources/images/unchkd.png", 109, 124, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "pdfmaker/resources/images/unchkd.png", 14, 135, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
//        $pdf->Image($this->objAltConfig->getModulePath() . "pdfmaker/resources/images/unchkd.png", 109, 135, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
        $ndx = 0;
        foreach ($leaveList as $leaveItem) {
            if ($ndx == 2) {
                $marginTop += 11;
                $marginLeft = 14;
                $ndx = 0;
            }
            $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/unchkd.png", $marginLeft, $marginTop, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
            if ($leaveItem['id'] == $leaveID) {
                $pdf->Image($this->objAltConfig->getModulePath() . "internals/resources/images/checked.png", $marginLeft + 1, $marginTop - 0.5, 8, 7, '', 'http://www.tcpdf.org', '', true, 72);
            }
            $marginLeft += 94;
            $ndx++;
        }
        $pdf->writeHTML($html);
//        echo $this->objAltConfig->getsiteRootPath().$this->objAltConfig->getContentPath().$userID.'/logo.png';
// get current vertical position
//            $current_y_position = $pdf->getY();
// write the first column
//            $pdf->writeHTMLCell($first_column_width, 0, 0, 0, $left_column, 0, 0, 0, true);
//$pdf->CheckBox('newsletter', 5, true);
// write the second column
//            $pdf->writeHTMLCell($second_column_width, 0, 0, 0, $right_column, 0, 1, 0, true);
// reset pointer to the last page
//            $pdf->lastPage();
//            $pdf->writeHTMLCell(0, 0, 0, 0, $html = '<h1>Hey</h1>', 0, 0, 0, true, '');
//
        $objMail->setValue('to', "wsifumba@gmail.com");
        $objMail->setValue('from', 'noreply@hermes');
        $objMail->setValue('fromName', 'Monwabisi');
        $objMail->setValue('subject', 'Leaves appliction');
        $objMail->setValue('body', "{$userFullName} your leave request has been " . $requestStatus . '<br/>' . $statement);
        $objMail->setValue('htmlbody', "{$userFullName}your leave request has been " . $requestStatus . '<br/>' . $statement);
//        $objMail->send();
        $pdf->Output();
        $pdf->Output();
//        echo $this->objAltConfig->getcontentPath();
//        echo $objUser->getUserId($objUser->userName());
//        echo $this->objAltConfig->getsiteRoot().$this->objAltConfig->getcontentPath().'users/'.$objUser->getUserId($objUser->userName()).'/logo.jpg';
    }

    /**
     * 
     * Method to return an error when the action is not a valid 
     * action method
     * 
     * @access private
     * @return string Redirect to module home page
     * 
     */
    private function __actionError() {
        return header('location:index.php?module=internals');
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
    function __validAction(& $action) {
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to add users to the internals module
     * 
     * @access public
     * @return type
     */
    public function __addUser() {
        //config object
        $objConfig = $this->getObject('altconfig', 'config');
        //dom object
        $domDoc = new DOMDocument('utf-8');
        //get the id parameter
        $userId = $this->getParam('id', NULL);
        //instantiate the user object
        $objUser = $this->getObject('user', 'security');
        //check if user is logged in
        if ($objUser->isLoggedIn()) {
            //instantiate the database object
            $dbObject = $this->getObject('dbinternals', 'internals');
            //call the function to add the user to the internals table
            $dbObject->addUser($userId);
            return header('location:index.php?module=internals');
        }
    }

    /**
     * @access public
     * @param NULL
     */
    public function __removeUser() {
        $userId = $this->getParam('id', NULL);
        $objUser = $this->getObject('user', 'security');
    }

    /**
     * Method for updating a request
     * @access public
     * @return type Description
     */
    public function __updateRequest() {
        //record Id
        $id = $this->getParam('id', NULL);
        //user ID
        $userID = $this->getParam('x_data', NULL);
        //request status
        $requestStatus = $this->getParam('status', NULL);
        //database object
        $objDB = $this->getObject('dbinternals', 'internals');
        //comments
        $comments = $this->getParam('comments', NULL);
        if (!empty($requestStatus)) {
            if (strtolower($requestStatus) == 'reject') {
                if (!empty($comments)) {
                    $statement = 'Reason being ' . $comments;
                } else {
                    $statement = 'No reason was provided';
                }
            }
        }
//                $objDB->insert($values, 'tbl_requests');
        $values = array(
            'status' => $requestStatus,
            'comments' => $comments
        );
        //html to pdf test
        $objMail = & $this->getObject('mailer', 'mail');
        //pdate the database
        $objDB->update('id', $id, $values, 'tbl_internalrequests');
        //prepare to send message
        $objUser = $this->getObject('user', 'security');
        //get the user email address
        $userEmail = $objUser->email($userID);
        //get the user's full name
        $userFullName = $objUser->fullname($userID);
        //Prepare the message
        $objMail->setValue('to', $userEmail);
        $objMail->setValue('from', 'noreply@hermes');
        $objMail->setValue('fromName', $userFullName);
        $objMail->setValue('subject', 'Leave appliction');
        $objMail->setValue('body', "{$userFullName} your leave request has been " . $requestStatus . '<br/>' . $statement);
        $objMail->setValue('htmlbody', "{$userFullName} your leave request has been " . $requestStatus . '<br/>' . $statement);
        //send the message
        if ($objMail->send()) {
//            header('location:index.php?module=internals');
        }
    }

    /**
     * Add leeave type
     * 
     * @access public
     */
    public function __addLeavetype() {
        $objDB = $this->getObject('dbinternals', 'internals');
        $leaveName = $this->getParam('leavename');
        $numberOfDays = $this->getParam('numberofdays');
        $objDB->addLeaveType($leaveName, $numberOfDays);
//        echo $this->getParam('numberofdays').'<br />'.$this->getParam('leavename');
//        header('location:index.php');
    }

    /**
     * 
     * Method to convert the action parameter into the name of 
     * a method of this class.
     * 
     * @access private
     * @param string $action The action parameter passed byref
     * @return stromg the name of the method
     * 
     */
    function __getMethod(& $action) {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }

    /**
     *
     * This is a method to determine if the user has to 
     * be logged in or not. Note that this is an example, 
     * and if you use it view will be visible to non-logged in 
     * users. Delete it if you do not want to allow annonymous access.
     * It overides that in the parent class
     *
     * @access public
     * @return boolean TRUE|FALSE
     *
     */
    public function requiresLogin() {
        $action = $this->getParam('action', 'view');
        switch ($action) {
            case 'view':
                return TRUE;
                break;
            case 'pushRequest':
                return TRUE;
                break;
            case 'updateRequest':
                return TRUE;
                break;
            case 'addUser':
                return TRUE;
                break;
            default:
                return TRUE;
                break;
        }
    }

}

?>