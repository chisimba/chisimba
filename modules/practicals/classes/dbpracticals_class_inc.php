<?php

/**
 *
 * Practicals
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
 * @package   Practicals
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:
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
 * Database accesss class for Chisimba for the module practicals
 *
 * @author Tohir Solomons
 * @package practical
 *
 */
class dbpracticals extends dbtable {

    /**
     *
     * Intialiser for the practical controller
     * @access public
     *
     */
    public function init() {
        //Set the parent table here
        parent::init('tbl_practicals');
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    public function getPractical($id) {
        return $this->getRow('id', $id);
    }


    /**
     * Method to search practicals and return the results.
     * @param string $field The table field in which to search.
     * @param string $value The value to search for.
     * @param string $context The current context.
     * @return array $data The results of the search.
     */
    public function search($field, $value, $context) {
        $sql = "SELECT * FROM tbl_practicals";
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
     * Method to get an practical from the database.
     * @param string $context The current context.
     * @param string $filter
     * @return array $data List of practicals
     */
    public function getPracticals($context, $filter=NULL) {
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
    public function addPractical(
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
            $title = "'" . $name . "' " . $this->objLanguage->languageText('mod_practicals_emailsubject', 'practicals', " practical  has been created in '") . $this->objContext->getTitle($context) . "'";
            $link = new link($this->uri(array("action" => "view", "id" => $id)));

            $message = $this->objLanguage->languageText('mod_practicals_emailbody', 'practicals', "To view the practical, click on this link") . ' ' .
                    $link->href;
            $this->sendEmail($title, $message, $this->getContextRecipients($context));
        }
        $this->addReminderToCalendar(
                $name,
                $desciption,
                $opening_date,
                $closing_date, $id);
        return $id;
    }

    /**
     * adds an practical to a calendar as a reminder
     * @param <type> $name
     * @param <type> $desciption
     * @param <type> $opening_date
     * @param <type> $closing_date
     * @param <type> $id
     */
    private function addReminderToCalendar($name, $desciption, $opening_date, $closing_date, $id) {

        $objModule = $this->getObject('modules', 'modulecatalogue');

        $eventsurl = $this->uri(array("action" => 'view', 'module' => 'practicals', 'id' => $id));
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
     * Method to email an practical to users
     *
     * @param string $title Title of the practical
     * @param string $message The practical
     * @param array $recipients List of Recipients (array of email addresses);
     */
    private function sendEmail($title, $message, $recipients) {

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
        $objMailer->setValue('subject', $title);
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
    public function updatePractical($id, $name, $description, $resubmit, $format, $mark, $percentage, $opening_date, $closing_date, $assesment_type, $emailAlert, $filename_conversion, $visibility, $emailalert_onsubmit,$usegroups,$usegoals) {

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
    public function deletePractical($id) {
        $result = $this->delete('id', $id);

        return $result;
    }
}
?>
