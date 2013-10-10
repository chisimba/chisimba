<?php
/* ----------- data class extends dbTable ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_survey_group
 * @author Kevin Cyster
 */

class groups extends dbTable {
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

    /**
     * @var string $tblGroups An associated table to be affected
     * @access private
     */
    private $tblGroups;

    /**
     * @var object $dbSurvey The dbsurvey class in the survey module
     * @access private
     */
    private $dbSurvey;

    /**
     * @var object $objLanguage The language class in the language module
     * @access private
     */
    private $objLanguage;

    /**
     * @var object $objGroupAdmin The groupadminmodel class in the groupadmin module
     * @access private
     */
    private $objGroupAdmin;

    /**
     * @var object $objGroupUsers The groupusersdb class in the groupadmin module
     * @access private
     */
    private $objGroupUsers;

    /**
     * @var object $objUser The user class in the security module
     * @access private
     */
    private $objUser;

    /**
     * @var string $userId The userid of the current user
     * @access private
     */
    private $userId;

    /**
     * Method to construct the class
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_users');
        $this->table = 'tbl_users';
        $this->tblGroups = 'tbl_groupadmin_group';

        $this->dbSurvey = $this->newObject('dbsurvey');

        $this->objLanguage = $this->newObject('language','language');
        $this->objGroupAdmin = $this->newObject('groupadminmodel','groupadmin');
        $this->objGroupUsers = $this->newObject('groupusersdb','groupadmin');
        $this->objUser = $this->newObject('user','security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to search for users
     *
     * @access public
     * @param string $search Item to search for
     * @param string $field Column to search in
     * @param string $order Column to order results by
     * @param int $number Number of Results per page
     * @param int $page Current Page of Results
     * @return array $ret Results
     */
    public function searchUsers($search,$field,$order,$number,$page=0) {
        $sql="SELECT * FROM ".$this->table;
        if($search!='') {
            $sql.=" WHERE ".$field." LIKE '".$search."%' AND userId!='".$this->userId."'";
        }else {
            $sql.=" WHERE userId!='".$this->userId."'";
        }
        $sql.=" ORDER BY ".$order;

        if($number!='all') {
            if($page<0) {
                $page=0;
            }
            $page=$page*$number;
            $sql.=" LIMIT ".$page.", ".$number;
        }
        return $this->getArray($sql);
    }

    /**
     * Method to get the number of total results for a user search
     *
     * @access public
     * @param string $search Item to search for
     * @param string $field Column to search in
     * @return integer Number of Results
     */
    public function countUserResults($search, $field) {
        if($search!='') {
            $where=" WHERE ".$field." LIKE '".$search."%' AND userId!='".$this->userId."'";
        }else {
            $where=" WHERE userId!='".$this->userId."'";
        }
        return $this->getRecordCount($where);
    }

    /**
     * Method to remove the creator from the list of users
     *
     * @access private
     * @param array $data An array containing the user data
     * @return array $ret The An array containing the user data without the creator
     */
    private function removeCreator($data) {
        foreach($data as $key=>$user) {
            $this->userId=$user['userId'];
            unset($data[$key]);
        }
        $ret = $data;
        return $ret;
    }

    /**
     * Method to generate paging for the user search results
     *
     * @access public
     * @param string $search Item to search for
     * @param string $field Column to search in
     * @param string $order Column to order results by
     * @param int $number Number of Results per page
     * @param int $page Current Page of Results
     * @param string $surveyId The survey id
     * @return array Results
     */
    public function generateUserPaging($search,$field,$order,$number,$page,$surveyId) {
        if($number=='all') {
            $output=$this->objLanguage->languageText('mod_survey_page', 'survey').'1';
        }else {
            $countResults=$this->countUserResults($search,$field);

            $output = '';
            $divider = '';

            $this->loadClass('link','htmlelements');

            for($i=1;$i<=(($countResults-($countResults%$number))/$number);$i++) {
                if($i==$page+1) {
                    $output.= $divider.'<em>'.$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$i.'</em>';
                }else {
                    $link=new link($this->uri(array('action'=>'search','search'=>$search,'field'=>$field,'order'=>$order,'number'=>$number,'page'=>($i-1),'survey_id'=>$surveyId)));
                    $link->link = $this->objLanguage->languageText('mod_survey_page', 'survey').' '.$i;

                    $output.=$divider.$link->show();
                }

                $divider=' | ';
            }

            if($countResults%$number!=0) {
                $count=($countResults-($countResults%$number))/$number+1;

                if($count==$page+1) {
                    $output .= $divider.'<em>'.$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$count.'</em>';
                }else {
                    $link=new link($this->uri(array('action'=>'search','search'=>$search,'field'=>$field,'order'=>$order,'number'=>$number,'page'=>($count-1),'survey_id'=>$surveyId)));
                    $link->link=$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$count;

                    $output.=$divider.$link->show();
                }
            }
        }
        return $output;
    }

    /**
     * Method to search for groups
     *
     * @access public
     * @param string $search Item to search for
     * @param int $number Number of Results per page
     * @param int $page Current Page of Results
     * @return array $ret Results
     */
    public function searchGroups($search,$number,$page=0) {
        $sql="SELECT * FROM ".$this->tblGroups;
        if($search!='') {
            $sql.=" WHERE group_define_name LIKE '".$search."%'";
        }
        $data=$this->getArray($sql);
        $ret=$this->removeSurveyGroups($data);

        if($number!='all') {
            if($page<0) {
                $page=0;
            }
            $page=$page*$number;
            $ret = array_slice($ret, $page, $number);
        }

        return $ret;
    }

    /**
     * Method to get the number of total results for a group search
     *
     * @access public
     * @param string $search Item to search for
     * @return int Ret Number of Results
     */
    public function countGroupResults($search) {
        $sql="SELECT * FROM ".$this->tblGroups;
        if($search!='') {
            $sql.=" WHERE group_define_name LIKE '".$search."%'";
        }
        $data=$this->getArray($sql);

        $ret=$this->removeSurveyGroups($data);

        if(!empty($ret)) {
            return count($ret);
        }else {
            return 0;
        }
    }

    /**
     * Method to generate paging for the group search results
     *
     * @access public
     * @param string $search Item to search for
     * @param int $number Number of Results per page
     * @param int $page Current Page of Results
     * @param string $surveyId The survey id
     * @return array Results
     */
    public function generateGroupPaging($search,$number,$page,$surveyId) {
        if($number=='all') {
            $output=$this->objLanguage->languageText('mod_survey_page', 'survey').'1';
        }else {
            $countResults=$this->countGroupResults($search);

            $output = '';
            $divider = '';

            $this->loadClass('link','htmlelements');

            for($i=1;$i<=(($countResults-($countResults%$number))/$number);$i++) {
                if($i==$page+1) {
                    $output.= $divider.'<em>'.$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$i.'</em>';
                }else {
                    $link=new link($this->uri(array('action'=>'search','search'=>$search,'field'=>'groups','number'=>$number,'page'=>($i-1),'survey_id'=>$surveyId)));
                    $link->link = $this->objLanguage->languageText('mod_survey_page', 'survey').' '.$i;

                    $output.=$divider.$link->show();
                }

                $divider=' | ';
            }

            if($countResults%$number!=0) {
                $count=($countResults-($countResults%$number))/$number+1;

                if($count==$page+1) {
                    $output .= $divider.'<em>'.$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$count.'</em>';
                }else {
                    $link=new link($this->uri(array('action'=>'search','search'=>$search,'field'=>'groups','number'=>$number,'page'=>($count-1),'survey_id'=>$surveyId)));
                    $link->link=$this->objLanguage->languageText('mod_survey_page', 'survey').' '.$count;

                    $output.=$divider.$link->show();
                }
            }
        }
        return $output;
    }

    /**
    * Method to add the site wide survey group
    *
    * @access private
    * @return string $rootId The id for the survey root group
    */
    public function getRootId()
    {
        $rootId = $this->objGroupAdmin->getLeafId(array('Surveys'));
        if(!empty($rootId)){
            return $rootId;
        }else{
            $rootId = $this->objGroupAdmin->addGroup('Surveys','The group for surveys',NULL);
            return $rootId;
        }
    }

    /**
     * Method to remove the survey groups
     *
     * @access private
     * @param array $data An array containing the group data
     * @return array $ret The An array containing the group data without the survey groups
     */
    private function removeSurveyGroups($data) {
        $rootId = $this->getRootId();
        if($arrGroupIdList != NULL) {
            foreach($data as $key=>$group){
                $groupId=$group['id'];
                if(in_array($groupId,$arrGroupIdList)){
                    unset($data[$key]);
                }
            }
        }
        $ret = $data;
        return $ret;
    }

    /**
     * Method to get the subgroups
     *
     * @param <type> $groupId
     * @return <type>
     *
     */
    function getRawSubGroups($groupId) {
        $sql='SELECT subgroup_id FROM tbl_perms_group_subgroups WHERE group_id = '.$groupId;
        $arrGroupIdList = $this->objGroupAdmin->getArray($sql);
        return $arrGroupIdList;

    }


    /**
     * Method to create user groups for each survey
     *
     * @access public
     * @param string $surveyId The id of the survey the groups are being created for
     * @return BOOl $isSuccessful TRUE on success false on failure
     */
    public function addGroups($surveyId) {
        $rootId = $this->getRootId();
        $arrSurveyData = $this->dbSurvey->getSurvey($surveyId);
        $surveyName = $arrSurveyData[0]['survey_name'];
        $groupId = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId));
        //var_dump($groupId); die();
        if($groupId!=FALSE){
            $this->objGroupAdmin->deleteGroup($groupId);
        }
        $groupId=$this->objGroupAdmin->addGroup($surveyId,$surveyName,$rootId);
        if($groupId===FALSE){
            $isSuccessful=FALSE;
            return $isSuccessful;
        }
        $this->objGroupAdmin->addSubGroups($surveyId, $groupId, $grps = array('Observers', 'Collaborators', 'Respondents'));
        $isSuccessful = TRUE;
        
        return $isSuccessful;
    }

    /*
     * Method to add subgroups
     *
     * @param string $surveyId
     * @param string $surveyGroupId
    */

    public function addSubGroups($surveyId, $surveyGroupId) {
        // create the subgroups first
        $grps = array("Observers", "Collaborators", "Respondents");
        foreach($grps as $grp) {
            $grpid = $this->objGroupAdmin->addGroup($surveyId."^".$grp);
            // then add them as subGroups of the parent Group.
            $data = array(
                    'group_id' => $surveyGroupId,
                    'subgroup_id' => $grpid
            );
            $assign = $this->objLuAdmin->perm->assignSubGroup($data);
        }

    }

    /**
     * Method to delete survey groups
     *
     * @access public
     * @param string $surveyId The id of the survey for which the groups must be deleted
     * @return NULL
     */
    public function deleteGroups($surveyId) {
        $groupId=$this->objGroupAdmin->getLeafId(array($surveyId));
        $arrGroupIdList = $this->getSubGroupIds($surveyId);
        $this->objGroupAdmin->deleteGroup($groupId);
        if(!empty($arrGroupIdList)) {
            foreach($arrGroupIdList as $groupId) {
                $this->objGroupUsers->delete('group_id',$groupId);
                $this->objGroupAdmin->deleteGroup($groupId);
            }
        }
    }

    /**
     * Method to copy the survey groups
     *
     * @access public
     * @param string $surveyId The id of the survey being copied
     * @param string $newSurveyId The id of the new survey
     * @return NULL
     */
    public function copyGroups($surveyId,$newSurveyId) {
        $this->addGroups($newSurveyId);
    }

    /**
     * Method to get the ids of the subgroups for the current survey
     *
     * @access private
     * @param string $surveyId The id of the current survey
     * @return array $arrGroupIdList An array containing the sub group ids
     */
    private function getSubGroupIds($surveyId) {
        $arrGroupIdList = array();
        $arrGroupIdList['Observers'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Observers'));
        $arrGroupIdList['Collaborators'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Collaborators'));
        $arrGroupIdList['Respondents'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Respondents'));
        if(!isset($arrGroupIdList['Observers'])) {
            $this->addGroups($surveyId);
            $arrGroupIdList=array();
            $arrGroupIdList['Observers'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Observers'));
            $arrGroupIdList['Collaborators'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Collaborators'));
            $arrGroupIdList['Respondents'] = $this->objGroupAdmin->getLeafId(array('Surveys',$surveyId,'Respondents'));
        }

        return $arrGroupIdList;
    }

    /**
    * Method to return the group the user is in
    *
    * @access puplic
    * @param string $userId The UserId of the user
    * @param string $surveyId The id of the current survey
    * @return string $group none|Observers|Collaborators|Respondents The group the user is in
    */
    public function getUserGroup($userId,$surveyId)
    {
        $pkId = $this->objUser->PKId($userId);
        //var_dump($pkId); //die('come back here');
        $arrGroupIdList = $this->getSubGroupIds($surveyId);
        $inGroup='None';
        foreach($arrGroupIdList as $group => $groupId){
            $isInGroup = $this->objGroupAdmin->isSubGroupMember($pkId,$groupId);
            if($isInGroup){
                $inGroup = $group;
                return $inGroup;
            }
        }
        $arrSurveyData = $this->dbSurvey->getSurvey($surveyId);
        if($userId == $arrSurveyData[0]['creator_id']){
            $inGroup = 'Creator';
        }
        
        return $inGroup;
    }

    /**
     * Method to assign users to survey groups
     *
     * @access public
     * @param string $surveyId The id of the current survey
     * @param array $arrAssignList An associative array with userId=>surveyGroup
     * @return
     */
    public function assignUsers($surveyId,$arrAssignList) {

        $arrGroupIdList = $this->getSubGroupIds($surveyId);
        foreach($arrAssignList as $userId=>$assign) {
            $permUserId = $this->objGroupAdmin->getPermUserId($userId);
            $inGroup = $this->getUserGroup($userId,$surveyId);
            if($assign == 'Observers') {
                if($inGroup != 'Observers') {
                    $this->objGroupAdmin->addGroupUser($arrGroupIdList['Observers'],$permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Collaborators'], $permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Respondents'], $permUserId);
                }
            }elseif($assign == 'Collaborators') {
                if($inGroup != 'Collaborators') {
                    $this->objGroupAdmin->addGroupUser($arrGroupIdList['Collaborators'], $permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Observers'], $permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Respondents'], $permUserId);
                }
            }elseif($assign == 'Respondents') {
                if($inGroup != 'Respondents') {
                    $this->objGroupAdmin->addGroupUser($arrGroupIdList['Respondents'], $permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Observers'], $permUserId);
                    $this->objGroupAdmin->deleteGroupUser($arrGroupIdList['Collaborators'], $permUserId);
                }
            }else {
                foreach($arrGroupIdList as $group=>$groupId) {
                    $this->objGroupAdmin->deleteGroupUser($groupId, $permUserId);
                }
            }
        }
    }

    /**
     * Method to delete a user from a survey group
     *
     * @access public
     * @param string $surveyId The id of the current survey
     * @param array $arrUserIdList The list of userId's to be removed from the group
     * @param string $group The group the users are in
     */
    public function deleteGroupUsers($surveyId, $arrUserIdList, $group) {
        $groupId = $this->objGroupAdmin->getLeafId(array($surveyId,$group));
        foreach($arrUserIdList as $userId) {
            $permUserId = $this->objGroupAdmin->getPermUserId($userId);
            $this->objGroupAdmin->deleteGroupUser($groupId, $permUserId);
        }
    }

    /**
     * Method to add group members to the survey groups
     *
     * @access public
     * @param string $surveyId The id of the current survey
     * @param array $arrGroupAssignList An associative array with groupId=>surveyGroup
     * @return
     */
    public function assignGroups($surveyId,$arrGroupAssignList) {

        foreach($arrGroupAssignList as $groupId=>$group) {
            $arrAssignList = array();
            $arrGroupMembers = $this->objGroupAdmin->getGroupUsers($groupId,array('userId','username'));

            foreach($arrGroupMembers as $user) {
                if($user['userid'] != $this->userId) {
                    $arrAssignList[$user['userid']] = $group;
                }
            }
            $this->assignUsers($surveyId,$arrAssignList);
        }
    }

    /*
     * Method to get all the group users
     *
     * @access public
     * @param <type> $surveyId
     * @return <array>
    */
    public function getAllGroupUsers($surveyId) {

        $subgrpIds = $this->getSubGroupIds($surveyId);

        $arr = array();
        $arr['Observers'] = $this->objGroupAdmin->getGroupUsers($subgrpIds['Observers'], array('surname', 'firstName'));
        $arr['Collaborators'] = $this->objGroupAdmin->getGroupUsers($subgrpIds['Collaborators'], array('surname', 'firstName'));
        $arr['Respondents'] = $this->objGroupAdmin->getGroupUsers($subgrpIds['Respondents'], array('surname', 'firstName'));
        return $arr;
    }
}
?>
