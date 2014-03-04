<?php

/**
 * This contains utility methods to be consumed by interested parties
 *
 * @author davidwaf
 */
class manager extends object {

    function init() {

        $this->objManageGroups = $this->getObject('managegroups', 'contextgroups');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->objDbContextInstructor = $this->getObject("dbcontextinstructor");
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject("user", "security");
        $this->loadClass("link", "htmlelements");
    }

    function show() {
        $mainInstructorId = $this->objDbContextInstructor->getMainInstructor($this->contextCode);
        $lecturers = $this->objManageGroups->contextUsers('Lecturers', $this->contextCode, array('tbl_users.userId', 'email', 'firstName', 'surname'));
        $total = count($lecturers);
        $userlist = "";
        $instructorexists = false;
        foreach ($lecturers as $row) {
            if ($row['userid'] == $mainInstructorId) {
                $instructorexists = true;
            }
            $userlist.="[";
            $userlist.="'" . $row['userid'] . "',";
            $userlist.="'" . $row['surname'] . " " . $row['firstname'] . "'";
            $userlist.="]";
            $index++;
            if ($index <= $total - 1) {
                $userlist.=',';
            }
        }

        if (!$mainInstructorId) {
            $mainInstructorId = $lecturers[0]['userid'];
        }

        if ($instructorexists == false) {
            $mainInstructorId = $lecturers[0]['userid'];
        }
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->align = 'absmiddle';


        $content.= "<div id=\"buttons-layer\"></div><script type=\"text/javascript\">" . $mainjs . "</script>";

        $objIcon->setIcon('edit');
        $editIcon = $objIcon->show();
        $changeURL = '<a href="#" onclick="showSelectInstructorWin();return false;">' . $editIcon . '<a/>';
        $changeLink = "";
        $changeMemberUrl = $this->uri(array("action" => "changeinstructor"));
        $instructorProfile = "";
        if ($this->objUser->isCourseAdmin($this->contextCode)) {
            $mainjs = "
                Ext.onReady(function(){
                var changeInstructorUrl='" . str_replace("amp;", "", $changeMemberUrl) . "';
                var userlist=[$userlist];
                initChangeInstructor(userlist,changeInstructorUrl);
                });
                ";
            $renderSurface = '<div id="addsession-win" class="x-hidden">
        <div class="x-window-header">' . $this->objLanguage->code2Txt('mod_contextinstructor_authors', 'contextinstructor',null,'[-authors-]') . '</div>
        </div>';
            $js = '<script language="JavaScript" src="' . $this->getResourceUri('js/lecturers.js') . '" type="text/javascript"></script>';
            $instructorProfile .=$renderSurface . $js . "<script type=\"text/javascript\">" . $mainjs . "</script>";
            $changeLink = $changeURL;
        }
        $objFeatureBox = $this->newObject('featurebox', 'navigation');

        $instructor = $this->objUser->getUserDetails($mainInstructorId);

        if (count($instructor) > 0) {
            $photo = $this->objUser->getUserImage($instructor['userid']);
            $email = $instructor['emailaddress'];
            $names = $instructor['firstname'] . ' ' . $instructor['surname'];
            $title = $instructor['title'];
            $cellnumber = $instructor['cellnumber'];
            $boxtitle = $this->objLanguage->code2Txt('mod_contextinstructor_author', 'contextinstructor',null,'[-author-]');
            $content = '<center class="instructorcenter">' . $photo . '<br/>' . $title . ' ' . $names . '<br/>' . '<a href="mailto:' . $email . '">' . $email . '</a><br/>' . $cellnumber . '</center><br/>' . $changeLink;
            $block = "competitions" . $index++;
            $hidden = 'default';
            $showToggle = true;
            $showTitle = true;
            $cssClass = "featurebox";

            $instructorProfile .= $objFeatureBox->show(
                            $boxtitle,
                            $content,
                            $block,
                            $hidden,
                            $showToggle,
                            $showTitle,
                            $cssClass, '');
        }


        return $instructorProfile;
    }

}

?>
