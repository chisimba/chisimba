<?php

/**
 *
 * Assignments
 *
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
 * @category  Chisimba
 * @package   assignment2
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbassignment_class_inc.php 24801 2012-12-09 12:00:21Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database accesss class for Chisimba for the module assignment
 *
 * @author Tohir Solomons
 * @package assignment
 *
 */
class dbassignment extends dbtable {

    /**
     *
     * Intialiser for the assignment2 controller
     * @access public
     *
     */
    public function init() {
        //Set the parent table here
        parent::init('tbl_assignment');
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    public function getAssignment($id) {
        return $this->getRow('id', $id);
    }


    /**
     * Method to search assignments and return the results.
     * @param string $field The table field in which to search.
     * @param string $value The value to search for.
     * @param string $context The current context.
     * @return array $data The results of the search.
     */
    public function search($field, $value, $context) {
        $sql = "SELECT * FROM tbl_assignment";
        $sql .= " WHERE $field LIKE '$value%'";
        $sql .= " AND context='$context'";
        $sql .= ' ORDER BY closing_date';

        $data = $this->getArray($sql);

        if ($data) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get an assignment from the database.
     * @param string $context The current context.
     * @param string $filter
     * @return array $data List of assignments
     */
    public function getAssignments($context, $filter=NULL) {
        $sql = " WHERE context='" . $context . "'";

        if ($filter) {
            $sql .= ' AND ' . $filter;
        }
        $sql .= ' ORDER BY closing_date DESC';

        return $this->getAll($sql);
    }

    /**
     *  Add new
     * @param <type> $name
     * @param <type> $context
     * @param <type> $description
     * @param <type> $resubmit
     * @param <type> $format
     * @param <type> $mark
     * @param <type> $percentage
     * @param <type> $opening_date
     * @param <type> $closing_date
     * @param <type> $assesment_type
     * @param <type> $emailAlert
     * @param <type> $filename_conversion
     * @return <type>
     */
    public function addAssignment(
    $name, $context, $description, $resubmit, $format, $mark, $percentage, $opening_date, $closing_date, $assesment_type, $emailAlert, $filename_conversion, $visibility, $emailalert_onsubmit,$usegroups,$usegoals
    ) {

        $id = $this->insert(array(
                    'name' => $name,
                    'context' => $context,
                    'description' => $description,
                    'resubmit' => $resubmit,
                    'format' => $format,
                    'mark' => $mark,
                    'percentage' => $percentage,
                    'opening_date' => $opening_date,
                    'closing_date' => $closing_date,
                    'assesment_type' => $assesment_type,
                    'email_alert' => $emailAlert,
                    'email_alert_onsubmit' => $emailalert_onsubmit,
                    'visibility' => $visibility,
                    'usegroups'=>$usegroups,
                    'usegoals'=>$usegoals,
                    'filename_conversion' => $filename_conversion,
                    'userid' => $this->objUser->userId(),
                    'last_modified' => date('Y-m-d H:i:s', time()),
                    'updated' => date('Y-m-d H:i:s', time())
                ));
        if ($emailAlert == '1') {
            $subject = "'".$name."' " . $this->objLanguage->languageText('mod_assignment_emailsubject', 'assignment', " assignment has been created in") . ' \'' . $this->objContext->getTitle($context) . '\'';
            $contextredirecturi = html_entity_decode($this->uri(array('action'=>'view', 'id'=>$id), 'assignment'));
            $link = new link($this->uri(array('action'=>'joincontext', 'contextcode'=>$this->objContext->getContextCode(), 'contextredirecturi'=> $contextredirecturi), 'context'));
            $message = $this->objLanguage->languageText('mod_assignment_emailbody', 'assignment', "To view the assignment, click on this link") . ' ' .
                    $link->href;
            $this->sendEmail($subject, $message, $this->getContextRecipients($context));
        }
        $this->addReminderToCalendar(
                $name,
                $description,
                $opening_date,
                $closing_date, 
                $id);
        return $id;
    }

    /**
     * adds an assignment to a calendar as a reminder
     * @param <type> $name
     * @param <type> $desciption
     * @param <type> $opening_date
     * @param <type> $closing_date
     * @param <type> $id
     */
    private function addReminderToCalendar(
    $name, $description, $opening_date, $closing_date, $id
    ) {

        $objModule = $this->getObject('modules', 'modulecatalogue');

        $eventsurl = $this->uri(array("action" => 'view', 'module' => 'assignment', 'id' => $id));
        $eventsurl = ' ' . str_replace("amp;", "", $eventsurl);
        //See if the calendar module is registered
        $isRegistered = $objModule->checkIfRegistered('calendar');
        if ($isRegistered) {
            $calendar = $this->getObject('contextcalendar', 'calendar');
            $eventsurl = $this->uri(array("action" => 'home'));
            $eventsurl = ' ' . str_replace("amp;", "", $eventsurl);
            $calendar->addEvent(
                    $opening_date,
                    $closing_date,
                    $name,
                    $description,
                    $eventsurl,
                    '0',
                    date('Y-m-d H:i:s', time()),
                    date('Y-m-d H:i:s', time()));
        }
    }

    /**
     * Method to get the list of email addresses for users belong to a context
     * @param string $contextCode Context Code
     * @return array Email Addresses of Context Users
     */
    private function getContextRecipients($contextCode) {
        $objGroups = $this->getObject('managegroups', 'contextgroups');

        $lecturers = $objGroups->contextUsers('Lecturers', $contextCode, array('emailAddress'));
        $students = $objGroups->contextUsers('Students', $contextCode, array('emailAddress'));
        $guests = $objGroups->contextUsers('Guests', $contextCode, array('emailAddress'));

        return array_merge($lecturers, $students, $guests);
    }

    /**
     * Method to email an assignment to users
     *
     * @param string $subject Subject of the assignment
     * @param string $message The assignment
     * @param array $recipients List of Recipients (array of email addresses);
     */
    private function sendEmail($subject, $message, $recipients) {

        $objMailer = $this->getObject('mailer', 'mail');
        $message = html_entity_decode($message);
        $message = strip_tags($message);
        $list = array();

        foreach ($recipients as $recipient) {
            $list[] = $recipient['emailaddress'];
        }

        $objMailer->setValue('to', $list);
        $objMailer->setValue('from', $this->objUser->email());
        $objMailer->setValue('fromName', $this->objUser->fullname());
        $objMailer->setValue('subject', $subject);
        $objMailer->setValue('body', $message);
        $objMailer->setValue('AltBody', $message);
        $objMailer->send();
    }

    /**
     *
     * @param <type> $id
     * @param <type> $name
     * @param <type> $description
     * @param <type> $resubmit
     * @param <type> $format
     * @param <type> $mark
     * @param <type> $percentage
     * @param <type> $opening_date
     * @param <type> $closing_date
     * @param <type> $assesment_type
     * @return <type>
     */
    public function updateAssignment($id, $name, $description, $resubmit, $format, $mark, $percentage, $opening_date, $closing_date, $assesment_type, $emailAlert, $filename_conversion, $visibility, $emailalert_onsubmit,$usegroups,$usegoals) {

        $id = $this->update('id', $id, array(
                    'name' => $name,
                    'description' => $description,
                    'resubmit' => $resubmit,
                    'format' => $format,
                    'mark' => $mark,
                                'usegroups'=>$usegroups,
                    'usegoals'=>$usegoals,

                    'percentage' => $percentage,
                    'opening_date' => $opening_date,
                    'closing_date' => $closing_date,
                    'email_alert' => $emailAlert,
                    'email_alert_onsubmit' => $emailalert_onsubmit,
                    'visibility' => $visibility,
                    'filename_conversion' => $filename_conversion,
                    'assesment_type' => $assesment_type,
                    'userid' => $this->objUser->userId(),
                    'last_modified' => date('Y-m-d H:i:s', time()),
                    'updated' => date('Y-m-d H:i:s', time())
                ));

        return $id;
    }

    /**
     * delete an sssignment
     * @param <type> $id
     * @return <type>
     */
    public function deleteAssignment($id) {
        $result = $this->delete('id', $id);

        return $result;
    }

}

?>
