<?php

/**
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
 * @version    0.001
 * @package    oer
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com

 */
class groupmanager extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
    }

    /**
     * saves group step one by creating a context out of it, then splitting the
     * rest of the fields into own table
     */
    function saveGroupStep1() {

        $contextCode = $this->genRandomString();
        $title = $this->getParam("name");
        $status = 'Published';
        $access = 'Public';
        $about = $this->getParam("description");
        $showcomment = 0;
        $alerts = 0;
        $canvas = 'None';

        $objContext = $this->getObject('dbcontext', 'context');
        $objContext->createContext($contextCode, $title, $status, $access, $about, '', $showcomment, $alerts, $canvas);


        $address = $this->getParam('address');
        $city = $this->getParam('city');
        $state = $this->getParam('state');
        $country = $this->getParam('country');
        $postalcode = $this->getParam('postalcode');
        $website = $this->getParam('website');
        $email = $this->getParam("email");
        $data = array(
            "contextcode" => $contextCode,
            "email" => $email,
            "address" => $address,
            "city" => $city,
            "state" => $state,
            "postalcode" => $postalcode,
            "website" => $website,
            "country" => $country
        );
        $dbGroups = $this->getObject("dbgroups", "oer");
        $id = $dbGroups->saveNewGroup($data);
        $dbForum = $this->getObject("dbforum", "forum");
        $dbForum->autoCreateWorkgroupForum($contextCode, $id, $title);

        return $contextCode;
    }

    /**
     * We update group details here. First, the context is updated, since a group
     * is actually a context. Extra params that cant go into a context are updated
     * in tbl_oer_group table
     * @return type 
     */
    function updateGroupStep1() {

        $contextCode = $this->getParam("contextcode");
        $title = $this->getParam("name");
        $status = 'Published';
        $access = 'Public';
        $about = $this->getParam("description");
        $showcomment = 0;
        $alerts = 0;
        $canvas = 'None';
        $objContext = $this->getObject('dbcontext', 'context');


        $objContext->updateContext($contextCode, $title, $status, $access, $about, '', $showcomment, $alerts, $canvas);


        $address = $this->getParam('address');
        $city = $this->getParam('city');
        $state = $this->getParam('state');
        $country = $this->getParam('country');
        $postalcode = $this->getParam('postalcode');
        $website = $this->getParam('website');
        $email = $this->getParam("email");
        $data = array(
            "email" => $email,
            "address" => $address,
            "city" => $city,
            "state" => $state,
            "postalcode" => $postalcode,
            "website" => $website,
            "country" => $country
        );
        $dbGroups = $this->getObject("dbgroups", "oer");
        $dbGroups->updateGroup($data, $contextCode);
        return $contextCode;
    }

    /**
     * geographics info of the groyp
     * @return type 
     */
    function updateGroupStep2() {
        $contextCode = $this->getParam("contextcode");
        $loclat = $this->getParam('loclat');
        $loclong = $this->getParam('loclong');
        $country = $this->getParam('country');
        $data = array(
            "loclat" => $loclat,
            "loclong" => $loclong,
            "country" => $country
        );
        $dbGroups = $this->getObject("dbgroups", "oer");
        $dbGroups->updateGroup($data, $contextCode);
        return $contextCode;
    }

    /**
     * updates the step three of group details: linking selected institutions
     * to a group 
     */
    public function updateGroupStep3() {

        $dbGroupInstitutions = $this->getObject("dbgroupinstitutions", "oer");
        $institutions = $this->getParam("rightList");
        $contextCode = $this->getParam("contextcode");

        $dbGroupInstitutions->updateGroupInstitutions($institutions, $contextCode);
    }

    /**
     * This creates a grid of groups. Each cell has a thumbnail, and a title, 
     * each when clicked leads to details of the group
     * 
     * @return type Returns a table with 3 columns, each cell representing a group
     */
    public function getGroupListing() {
        $dbGroups = $this->getObject("dbgroups", "oer");
        $objContext = $this->getObject('dbcontext', 'context');

        $groups = $dbGroups->getAllGroups();


        $newgrouplink = new link($this->uri(array("action" => "creategroupstep1")));
        $newgrouplink->link = $this->objLanguage->languageText('mod_oer_group_new', 'oer');


        $controlBand =
                '<div id="groups_controlband">';

        /* $controlBand.='<br/>&nbsp;' . $this->objLanguage->languageText('mod_oer_viewas', 'oer') . ': ';
          $gridthumbnail = '<img src="skins/oeru/images/sort-by-grid.png"/>';
          $gridlink = new link($this->uri(array("action" => "home")));
          $gridlink->link = $gridthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_grid', 'oer');
          $controlBand.=$gridlink->show();

          $listthumbnail = '&nbsp;|&nbsp;<img src="skins/oeru/images/sort-by-list.png"/>';
          $listlink = new link($this->uri(array("action" => "showproductlistingaslist")));
          $listlink->link = $listthumbnail . '&nbsp;' . $this->objLanguage->languageText('mod_oer_list', 'oer');
          $controlBand.=$listlink->show(); */

        if ($this->objUser->isLoggedIn()) {
            $newthumbnail = '&nbsp;<img src="skins/oeru/images/document-new.png" width="19" height="15"/>';
            $controlBand.= $newthumbnail . $newgrouplink->show();
        }


        $sortbydropdown = new dropdown('sortby');
        $sortbydropdown->addOption('', $this->objLanguage->languageText('mod_oer_none', 'oer'));

        //$controlBand.='<br/><br/>' . $this->objLanguage->languageText('mod_oer_sortby', 'oer');
        //$controlBand.=$sortbydropdown->show();

        $controlBand.= '</div> ';
        $startNewRow = TRUE;
        $count = 1;
        $table = $this->getObject('htmltable', 'htmlelements');
        $table->attributes = "style='table-layout:fixed;'";
        $table->cellspacing = 10;
        $table->cellpadding = 10;
        $objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $groupId = $objGroups->getId("ProductCreators");
        $objGroupOps = $this->getObject("groupops", "groupadmin");
        $userId = $this->objUser->userId();
        $maxCol = 2;
        $editImg = '<img src="skins/oeru/images/icons/edit.png" class="groupedit" align="top" valign="top">';
        $deleteImg = '<img src="skins/oeru/images/icons/delete.png">';

        foreach ($groups as $group) {
            if ($startNewRow) {
                $startNewRow = FALSE;
                $table->startRow();
            }
            $context = $objContext->getContext($group['contextcode']);
            $editControls = "";
            if ($this->objUser->isLoggedIn()) {
                $editLink = new link($this->uri(array("action" => "editgroupstep1", "contextcode" => $group['contextcode'])));
                $editLink->link = $editImg;
                $editLink->cssClass = "editgroup";
                $editControls = "" . $editLink->show();
            }

            $titleLink = new link($this->uri(array("action" => "viewgroup", "contextcode" => $group['contextcode'])));
            $titleLink->cssClass = 'group_listing_title';
            $titleLink->link = $context['title'] . $editControls;
            $thumbnail = '<img src="usrfiles/' . $group['thumbnail'] . '"  width="79" height="101" align="bottom"/>';
            if ($group['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="bottom"/>';
            }


            $thumbnailLink = new link($this->uri(array("action" => "viewgroup", "contextcode" => $group['contextcode'])));
            $thumbnailLink->link = $thumbnail;
            $thumbnailLink->cssClass = 'group_listing_thumbail';


            $groupStr = $thumbnailLink->show() . '<br/>' . $titleLink->show();


            $joinGroupLink = new link($this->uri(array("action" => "joincontext", "contextcode" => $group['contextcode'])));
            $joinGroupLink->link = $this->objLanguage->languageText('mod_oer_join', 'oer');
            $joinGroupLink->cssClass = 'joingroup';
            $groupStr.='<br/>' . $joinGroupLink->show();

            $table->addCell($groupStr, null, "top", "left", "view_group");

            if ($count == $maxCol) {

                $table->endRow();
                $startNewRow = TRUE;
                $count = 0;
            }
            $count++;
        }

        $totalGroups = count($groups);
        $reminder = $totalGroups % $maxCol;

        if ($reminder != 0) {

            $table->endRow();
        }
        return $controlBand . $table->show();
    }

    public function genRandomString() {
        $length = 5;
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }

    /**
     * Used fo uploading product thumbnail
     * @todo this will be renamed to a meaningful name
     */
    function doajaxupload() {
        $dir = $this->objConfig->getcontentBasePath();

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');

        $objMkDir = $this->getObject('mkdir', 'files');

        $contextcode = $this->getParam('itemid');
        $destinationDir = $dir . '/oer/groups/' . $contextcode;

        $objMkDir->mkdirs($destinationDir);
        // @chmod($destinationDir, 0777);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            'all'
        );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir . '/';
        $result = $objUpload->doUpload(TRUE, $filename);
        if ($result['success'] == FALSE) {
            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';
            $error = $this->objLanguage->languageText('mod_oer_uploaderror', 'oer');
            return array('message' => $error, 'file' => $filename, 'id' => $generatedid);
        } else {
            $filename = $result['filename'];
            $data = array("thumbnail" => "/oer/groups/" . $contextcode . "/" . $filename);
            $dbGroup = $this->getObject("dbgroups", "oer");
            $dbGroup->updateGroup($data, $contextcode);
            $params = array('action' => 'showthumbnailuploadresults', 'id' => $generatedid, 'fileid' => $id, 'filename' => $filename);

            return $params;
        }
    }

    /**
     * this returns current context members 
     */
    function getContextMembers($contextCode) {
        if (!$this->objUser->isLoggedIn()) {
            $loginlink = new link($this->uri(array("action" => "login"), "security"));
            $loginlink->link = $this->objLanguage->languageText("mod_oer_logintoseemembers", "oer");
            return '<div id="login_to_see_members">' . $loginlink->show() . '</div>';
        }
        $objManageGroups = $this->getObject('managegroups', 'contextgroups');
        $groupEditors = $objManageGroups->contextUsers('Lecturers', $contextCode, array('tbl_users.userId', 'firstName', 'surname'));
        $groupReadOnly = $objManageGroups->contextUsers('Students', $contextCode, array('tbl_users.userId', 'firstName', 'surname'));

        $str = '';


        $str .= '<p><strong>' . ucwords($this->objLanguage->code2Txt('word_lecturers', 'system')) . '</strong></p>';

        if (count($groupEditors) == 0) {
            $str .= '<p>' . $this->objLanguage->code2Txt('mod_contextgroups_nolecturers', 'contextgroups') . '<p>';
        } else {
            $str .= '<p>';

            foreach ($groupEditors as $lecturer) {
                $str .= $this->objUser->getSmallUserImage($lecturer['userid'], $lecturer['firstname'] . ' ' . $lecturer['surname']) . ' ';
            }

            $str .= '</p>';
        }

        $str .= '<p><strong>' . ucwords($this->objLanguage->code2Txt('word_students', 'system')) . '</strong></p>';

        if (count($groupReadOnly) == 0) {
            $str .= '<p>' . $this->objLanguage->code2Txt('mod_groupadmin_nostuds', 'groupadmin') . '<p>';
        } else {
            $str .= '<p>';

            foreach ($groupReadOnly as $student) {
                $str .= $this->objUser->getSmallUserImage($student['userid'], $student['firstname'] . ' ' . $student['surname']) . ' ';
            }

            $str .= '</p>';
        }

        $link = new link($this->uri(NULL, 'contextgroups'));
        $link->link = $this->objLanguage->code2Txt('mod_contextgroups_toolbarname', 'contextgroups');

        $str .= '<p>' . $link->show();
        return $str;
    }

    /**
     * gets a list of a adaptations by this group. First, we get list of instutions
     * that are members of this group, then get adapations from each of the 
     * institutions 
     */
    private function getGroupAdaptations($contextcode) {
        $dbGroupInstitutions = $this->getObject("dbgroupinstitutions", "oer");
        $institutions = $dbGroupInstitutions->getGroupInstitutions($contextcode);

        $productManager = $this->getObject("productmanager", "oer");
        $content = "";
        foreach ($institutions as $institution) {
            $content.=$productManager->getAdaptationsByInstitution($institution['institution_id']);
        }
        return $content;
    }

    /**
     * this gets list of institution in this group
     * @param type $contextcode
     * @return type 
     */
    private function getInstitutionsByGroup($contextcode) {
        $dbGroupInstitutions = $this->getObject("dbgroupinstitutions", "oer");
        $institutions = $dbGroupInstitutions->getGroupInstitutions($contextcode);
        $dbInstitution = $this->getObject("dbinstitution", "oer");
        $content = '<table>';

        foreach ($institutions as $xinstitution) {
            $institution = $dbInstitution->getInstitutionById($xinstitution['institution_id']);
            $thumbnail = '<img src="usrfiles/' . $institution['thumbnail'] . '"  width="45" height="49"  align="left"/>';
            if ($institution['thumbnail'] == '') {
                $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg" width="45" height="49"  align="left"/>';
            }
            $instName = $institution['name'];
            $instNameLink = new link($this->uri(array("action" => "viewinstitution", "id" => $institution['id'])));
            $instNameLink->link = $instName;

            $instNameLk = $thumbnail . $instNameLink->show();
            $content.= '<tr><td class="viewgroup_institution" align="left" valign="top">' . $instNameLk . '</td></tr>';
        }
        $content.='</table>';
        return $content;
    }

    /**
     * returns post in this grouo
     * @param type $workgroup
     * @param type $contextcode
     * @return type 
     */
    function getGroupForums($workgroup, $contextcode) {
        $link = new link($this->uri(array(), "forum"));
        $link->link = $this->objLanguage->languageText("mod_forum", "forum");

        $forum = $this->getObject("forum", "oer");
        return '<h1>' . $link->show() . '</h1>' . $forum->showLastNPosts(10);


        /* $dbPost = $this->getObject("dbpost", "forum");
          return $link->show() . '' . $dbPost->getWorkGroupPosts($workgroup, $contextcode);
         */
    }

    /**
     * contructs group view details. Since a group is essentially a context,
     * everything is done based on contextcode. This allows us to plug in modules
     * that make use of context
     * @param type $contextcode
     * @return string 
     */
    function buildViewGroupDetails($contextcode) {

        $dbGroup = $this->getObject("dbgroups", "oer");
        $dbContext = $this->getObject("dbcontext", "context");
        $group = $dbGroup->getGroupByContextCode($contextcode);
        $context = $dbContext->getContext($contextcode);
        $thumbnail = '<img src="usrfiles/' . $group['thumbnail'] . '"  width="79" height="101" align="left "/>';
        if ($group['thumbnail'] == '') {
            $thumbnail = '<img src="skins/oeru/images/product-cover-placeholder.jpg"  width="79" height="101" align="left"/>';
        }

        $table = $this->getObject("htmltable", "htmlelements");

        $table->startRow();
        $table->addCell('<div id="group_thumbnail">' . $thumbnail . '</div>');
        $table->addCell('<div id="group_about">' . $context['about'] . '</div>');
        $table->endRow();
        $editImg = '<img src="skins/oeru/images/icons/edit.png" class="groupedit" align="top" valign="top">';

        $editControls = "";
        if ($this->objUser->isLoggedIn()) {
            $editLink = new link($this->uri(array("action" => "editgroupstep1", "contextcode" => $group['contextcode'])));
            $editLink->link = $editImg;
            $editLink->cssClass = "editgroup";
            $editControls = "" . $editLink->show();
        }
        $content = '<h2>' . $context['title'] . $editControls . '</h2>';

        $content.=$table->show();
        $content .= '<div id="group_plugins">
<div class="tabber">

     <div class="tabbertab">
	  <h2 class="members">' . $this->objLanguage->languageText('mod_oer_members', 'oer') . '</h2>'
                . $this->getContextMembers($contextcode) . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostrated">' . $this->objLanguage->languageText('mod_oer_word_adaptations', 'oer') . '</h2>'
                . $this->getGroupAdaptations($contextcode) . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostcommented">' . $this->objLanguage->languageText('mod_oer_discussions', 'oer') . '</h2>'
                . $this->getGroupForums($group['id'], $contextcode) . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostcommented">' . $this->objLanguage->languageText('mod_oer_institutions', 'oer') . '</h2>'
                . $this->getInstitutionsByGroup($contextcode) . '
     </div>

</div>
</div>            

';
        return $content;
    }

}

?>
