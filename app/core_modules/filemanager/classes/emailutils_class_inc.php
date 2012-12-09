<?php

/*

 * This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 

 */

/**
 * This class contails util methods for sending emails to users. It is mainly
 * used to send alerts to users of changes in a folder on which the alerts are enabled.
 *
 * @author davidwaf
 */
class emailutils extends object {

    function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objManageGroups = $this->getObject('managegroups', 'contextgroups');
    }

    function sendFolderEmailAlert($folderid, $contextcode, $title) {

        $students = $this->objManageGroups->contextUsers('Students', $contextcode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
        $lecturers = $this->objManageGroups->contextUsers('Lecturers', $contextcode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
        $allusers = array();
        $allusers = array_merge($students, $lecturers);

        $subject = $this->objSysConfig->getValue('FILECREATE_ALERT_SUB', 'filemanager');
        $subject = str_replace("{course}", $title, $subject);
        $objMailer = $this->getObject('mailer', 'mail');
        $objWashout = $this->getObject('washout', 'utilities');

        foreach ($allusers as $user) {

            $body = $this->objSysConfig->getValue('FLDRCREATE_ALERT_BDY', 'filemanager');
            $body = $objWashout->parseText($body);

            $linkUrl = $this->uri(array('action' => 'viewfolder', 'folder' => $folderid));

            $linkUrl = str_replace("amp;", "", $linkUrl);
            $body = str_replace("{link}", $linkUrl, $body);

            $body = str_replace("{firstname}", $user['firstname'], $body);
            $body = str_replace("{lastname}", $user['surname'], $body);
            $body = str_replace("{course}", "'" . $title . "'", $body);
            $body = str_replace("{instructor}", $this->objUser->getTitle() . '. ' . $this->objUser->fullname() . ',', $body);
            $objMailer->setValue('to', array($user['emailaddress']));
            $objMailer->setValue('from', $this->objUser->email());
            $objMailer->setValue('fromName', $this->objUser->fullname());
            $objMailer->setValue('subject', $subject);
            $objMailer->setValue('body', strip_tags($body));
            $objMailer->setValue('AltBody', strip_tags($body));
            $objMailer->send();
        }
    }

    function sendFileEmailAlert($fileid, $contextcode, $title) {

        $students = $this->objManageGroups->contextUsers('Students', $contextcode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
        $lecturers = $this->objManageGroups->contextUsers('Lecturers', $contextcode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
        $allusers = array();
        $allusers = array_merge($students, $lecturers);

        $subject = $this->objSysConfig->getValue('FILECREATE_ALERT_SUB', 'filemanager');
        $subject = str_replace("{course}", $title, $subject);
        $objMailer = $this->getObject('mailer', 'mail');
        $objWashout = $this->getObject('washout', 'utilities');

        foreach ($allusers as $user) {

            $body = $this->objSysConfig->getValue('FILECREATE_ALERT_BDY', 'filemanager');
            $body = $objWashout->parseText($body);

            $linkUrl = $this->uri(array('action' => 'viewfolder', 'folder' => $fileid));

            $linkUrl = str_replace("amp;", "", $linkUrl);
            $body = str_replace("{link}", $linkUrl, $body);

            $body = str_replace("{firstname}", $user['firstname'], $body);
            $body = str_replace("{lastname}", $user['surname'], $body);
            $body = str_replace("{course}", "'" . $title . "'", $body);
            $body = str_replace("{instructor}", $this->objUser->getTitle() . '. ' . $this->objUser->fullname() . ',', $body);
            $objMailer->setValue('to', array($user['emailaddress']));
            $objMailer->setValue('from', $this->objUser->email());
            $objMailer->setValue('fromName', $this->objUser->fullname());
            $objMailer->setValue('subject', $subject);
            $objMailer->setValue('body', strip_tags($body));
            $objMailer->setValue('AltBody', strip_tags($body));
            $objMailer->send();
        }
      }
}

?>