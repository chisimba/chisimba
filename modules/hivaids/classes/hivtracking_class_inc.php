<?php
/**
* hivtracking class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* hivtracking class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class hivtracking extends object
{
    /**
    * Constructor method
    */
    public function init()
    {
        $this->dbUsers = $this->getObject('dbusers', 'hivaids');
        $this->dbLogCalc = $this->getObject('dbloggercalc', 'hivaids');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objConfirm = $this->getObject('confirm', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objCountries = $this->getObject('languagecode','language');
        $this->objLoginHist = $this->getObject('dbloginhistory','userstats');

        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to display the user statistics for the site
    *
    * @access public
    * @return string html
    */
    public function showUserStats()
    {
        $data = $this->dbUsers->getLoginHistory();
        $numStaff = $this->dbUsers->getStaffInfo();
        $numStudent = $this->dbUsers->getStudentInfo();
        $total = $this->objLoginHist->getTotalLogins();
        $unique = $this->objLoginHist->getUniqueLogins();

        $headerParams = $this->getJavascriptFile('new_sorttable.js','htmlelements');
        $this->appendArrayVar('headerParams',$headerParams);

        $institution = $this->objConfig->getinstitutionShortName();
        $array = array('institution' => $institution);

        $useStudNum = $this->objSysConfig->getValue('USE_STUDENT_NUMBER', 'hivaids');

        $head = $this->objLanguage->languageText('mod_hivaids_userstats', 'hivaids');
        $hdTitle = $this->objLanguage->languageText('word_title');
        $hdName = $this->objLanguage->languageText('word_name');
        $hdCountry = $this->objLanguage->languageText('word_country');
        $hdLanguage = $this->objLanguage->languageText('phrase_homelanguage');
        $hdLastOn = $this->objLanguage->languageText('phrase_laston');
        $hdLogins = $this->objLanguage->languageText('word_logins');
        $hdStaffStud = $this->objLanguage->code2Txt('mod_hivaids_atinstitution', 'hivaids', $array);
        $hdStudNum = $this->objLanguage->languageText('mod_hivaids_studentstaffnum', 'hivaids');
        $hdCourse = $this->objLanguage->languageText('word_course');
        $hdYear = $this->objLanguage->languageText('phrase_yearofstudy');
        $staff = $this->objLanguage->languageText('word_staff');
        $student = $this->objLanguage->languageText('word_student');
        $no = $this->objLanguage->languageText('word_no');
        $lbTotal = $this->objLanguage->languageText('mod_hivaids_totallogins', 'hivaids');
        $lbUnique = $this->objLanguage->languageText('mod_hivaids_uniqueusers', 'hivaids');
        $lbStaff = $this->objLanguage->code2Txt('mod_hivaids_staffatinstitution', 'hivaids', $array);
        $lbStudents = $this->objLanguage->code2Txt('mod_hivaids_studentsatinstitution', 'hivaids', $array);
        $lnMonitor = $this->objLanguage->languageText('phrase_monitoringtools');

        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $head;
        $str = $objHead->show();

        // link to the monitoring tools data
        $objLink = new link($this->uri(array('action' => 'tracking')));
        $objLink->link = $lnMonitor;
        $str .= '<p>'.$objLink->show().'</p>';

        $str .= '<p style="padding-top:10px;"><b>'.$lbTotal.':</b>&nbsp;&nbsp;'.$total;
        $str .= '<br /><b>'.$lbUnique.':</b>&nbsp;&nbsp;'.$unique;
        $str .= '<br /><b>'.$lbStaff.':</b>&nbsp;&nbsp;'.$numStaff;
        $str .= '<br /><b>'.$lbStudents.':</b>&nbsp;&nbsp;'.$numStudent.'</p>';

        if(!empty($data)){

            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->id = 'statstable';
            $objTable->css_class = 'sorttable';

            $hdArr = array();
            $hdArr[] = $hdTitle;
            $hdArr[] = $hdName;
            $hdArr[] = $hdCountry;
            $hdArr[] = $hdLanguage;
            $hdArr[] = $hdStaffStud;
            if($useStudNum) $hdArr[] = $hdStudNum;
            $hdArr[] = $hdCourse;
            $hdArr[] = $hdYear;
            $hdArr[] = $hdLastOn;
            $hdArr[] = $hdLogins;

            $objTable->row_attributes = 'name = "row_'.$objTable->id.'"';
            $objTable->addRow($hdArr, 'heading');

            foreach($data as $item){
                if(empty($item['userid'])){
                    continue;
                }
                $objTable->row_attributes = 'name = "row_'.$objTable->id.'" onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'\';"';

                $row = array();
                $row[] = $item['title'];
                $row[] = $item['surname'].', '.$item['firstname'];
                $row[] = $this->objCountries->getName($item['country']);
                $row[] = isset($item['language']) ? $$item['language'] : '';
                $row[] = isset($item['staff_student']) ? $$item['staff_student'] : '';
                if($useStudNum) $row[] = isset($item['staffnumber']) ? $$item['staffnumber'] : '';
                $row[] = isset($item['course']) ? $item['course'] : '';
                $row[] = isset($item['study_year']) ? $item['study_year'] : '';
                $row[] = $this->objDate->formatDate($item['laston']);
                $row[] = $item['logins'];

                $objTable->addRow($row);
            }
            $str .= $objTable->show();
        }

        return $str;
    }

    /**
    * Method to display the calculations performed using the monitoring tools
    *
    * @access public
    * @return string html
    */
    public function showMonitoring()
    {
        $view = $this->getSession('view_mode', 'all');
        $track = $this->getSession('track_mode', 'everyone');

        // Get data
        $allHits = $this->dbLogCalc->getTotalHits($track, $view);
        $allUsers = $this->dbLogCalc->getTotalUniqueVisitors($track, $view);

        switch($track){
            case 'group':
                $group = $this->getSession('group');
                $subgroup = $this->getSession('subgroup');
                $totalHits = $this->dbLogCalc->getTotalHitsByGroup($view, $group, $subgroup);
                $totalUsers = $this->dbLogCalc->getTotalUniqueVisitorsByGroup($view, $group, $subgroup);
                $data = empty($group) ? $this->dbLogCalc->getModuleHitsByGroup($view) : array();
                $data = !empty($subgroup) ? $this->dbLogCalc->getModuleHitsByGroup($view, $group, $subgroup) : $data;
                break;

            case 'person':
                $userid = $this->getSession('user');
                $totalHits = $this->dbLogCalc->getTotalHitsByUser($userid);
                $data = $this->dbLogCalc->getModuleHitsByUser($userid);
                $totalUsers = 1;
                break;

            default:
                $data = $this->dbLogCalc->getModuleHits();
                $totalHits = $allHits;
                $totalUsers = $allUsers;
        }

        $arrInfo = array('bold' => '<b>', 'closebold' => '</b>', 'totalhits' => $allHits, 'totalusers' => $allUsers);

        $head = $this->objLanguage->languageText('phrase_monitoringtools');
        $personInfo = ($view == 'student') ? $this->objLanguage->code2Txt('mod_hivaids_personstudentstatsinfo', 'hivaids', $arrInfo) : $this->objLanguage->code2Txt('mod_hivaids_personstatsinfo', 'hivaids', $arrInfo);
        $groupInfo = ($view == 'student') ? $this->objLanguage->code2Txt('mod_hivaids_groupstudentstatsinfo', 'hivaids', $arrInfo) : $this->objLanguage->code2Txt('mod_hivaids_groupstatsinfo', 'hivaids', $arrInfo);
        $everyoneInfo = $this->objLanguage->code2Txt('mod_hivaids_everyonestatsinfo', 'hivaids', $arrInfo);
        $lbTotalHits = $this->objLanguage->languageText('phrase_totalhits');
        $lbTotalUsers = $this->objLanguage->languageText('phrase_totaluniqueusers');
        $hdPage = $this->objLanguage->languageText('word_page');
        $hdHits = $this->objLanguage->languageText('word_hits');
        $hdUsers = $this->objLanguage->languageText('phrase_uniqueusers');
        $lbDefault = $this->objLanguage->languageText('phrase_defaultpage');

        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $head;
        $str = $objHead->show();

        $show = $track.'Info';
        $str .= '<p class="dim">'.$$show.'</p>';

        switch($track){
            // Create the dropdown of groups
            case 'group':
                $allReg = ($view == 'student') ? $this->objLanguage->languageText('phrase_allregisteredstudents') : $this->objLanguage->languageText('phrase_allregisteredusers');
                $lbSelectG = $this->objLanguage->languageText('mod_hivaids_selectgroupmonitor', 'hivaids');
                $hdGender = $this->objLanguage->languageText('word_gender');
                $hdLang = $this->objLanguage->languageText('phrase_homelanguage');
                $hdCourse = $this->objLanguage->languageText('word_course');
                $hdYear = $this->objLanguage->languageText('phrase_yearofstudy');

                $selected = $this->getSession('subgroup');
                $selName = '';

                $formStr = $lbSelectG.': ';

                $onChange = "onChange=\"javascript: ";
                $onChange .= (!empty($group)) ? "document.getElementById('input_subgroup').value=''; " : '';
                $onChange .= "document.groupselect.submit();\"";

                $objDrop = new dropdown('group');
                $objDrop->extra = $onChange;

                $objDrop->addOption('', ' -- '.$allReg.' -- ');
                $objDrop->addOption('sex', $hdGender);
                $objDrop->addOption('language', $hdLang);
                $objDrop->addOption('course', $hdCourse);
                $objDrop->addOption('study_year', $hdYear);
                $objDrop->setSelected($group);
                $formStr .= $objDrop->show();

                if(!empty($group)){
                    $groups = $this->dbUsers->getGroupList($view, $group);

                    if(!empty($groups)){
                        $objDrop = new dropdown('subgroup');
                        $objDrop->extra = "onChange=\"javascript: document.groupselect.submit();\"";

                        $objDrop->addOption('', ' -- -- ');
                        foreach($groups as $item){
                            $objDrop->addOption($item['groupid'], $item['name']);
                            $selName = ($item['groupid'] == $selected) ? $item['name'] : $selName;
                        }
                        $objDrop->setSelected($selected);
                        $formStr .= '&nbsp;&nbsp;'.$objDrop->show();
                    }
                }


                $objForm = new form('groupselect', $this->uri(array('action' => 'tracking', 'mode' => 'changegroup')));
                $objForm->addToForm($formStr);
                $str .= '<p>'.$objForm->show().'</p>';

                if(!empty($selected)){
                    $arrGroup = array('bold' => '<b>', 'closebold' => '</b>', 'name' => $selName, 'hits' => $totalHits, 'users' => $totalUsers);
                    $lbNameHits = $this->objLanguage->code2Txt('mod_hivaids_grouphittimes', 'hivaids', $arrGroup);
                    $str .= '<p class="dim">'.$lbNameHits.'</p>';
                }
            break;

            // Create the dropdown list of users
            case 'person':
                $allReg = ($view == 'student') ? $this->objLanguage->languageText('phrase_allregisteredstudents') : $this->objLanguage->languageText('phrase_allregisteredusers');
                $lbSelectU = $this->objLanguage->languageText('mod_hivaids_selectusermonitor', 'hivaids');

                $selected = $this->getSession('user');
                $users = $this->dbUsers->getUserList($view);

                $formStr = $lbSelectU.': ';

                $objDrop = new dropdown('username');
                $objDrop->extra = "onChange=\"javascript: document.userselect.submit();\"";

                $objDrop->addOption('', ' -- '.$allReg.' -- ');
                if(!empty($users)){
                    foreach($users as $item){
                        $objDrop->addOption($item['userid'], $item['username']);
                    }
                }
                $objDrop->setSelected($selected);
                $formStr .= $objDrop->show();

                $objForm = new form('userselect', $this->uri(array('action' => 'tracking', 'mode' => 'changeuser')));
                $objForm->addToForm($formStr);
                $str .= '<p>'.$objForm->show().'</p>';

                if(!empty($selected)){
                    $name = $this->objUser->userName($selected);
                    $arrUser = array('bold' => '<b>', 'closebold' => '</b>', 'name' => $name, 'hits' => $totalHits);
                    $lbNameHits = $this->objLanguage->code2Txt('mod_hivaids_userhittimes', 'hivaids', $arrUser);
                    $str .= '<p class="dim">'.$lbNameHits.'</p>';
                }
            break;
        }

        if(!empty($data)){

            $objTable = new htmltable();
            $objTable->cellpadding = '5';

            $hdArr = array();
            $hdArr[] = $hdPage;
            $hdArr[] = $hdHits;
            if($track != 'person') $hdArr[] = $hdUsers;

            $objTable->addHeader($hdArr);

            foreach($data as $key => $item){
                // Display the module
                $row = array();
                $row[] = '<b>'.$key.'</b>';;
                $row[] = '';
                $row[] = '';

                $objTable->addRow($row, 'heading');

                // Display the actions + stats
                $class = 'even';
                foreach($item as $action => $val){
                    $class = ($class == 'odd') ? 'even' : 'odd';

                    $hits = $val['hits'];
                    $percentHits = round($hits / $totalHits, 3) * 100;

                    $users = count($val['users']);
                    $percentUsers = round($users / $totalUsers, 3) * 100;

                    $row = array();
                    $row[] = (!empty($action)) ? $action : $lbDefault;
                    $row[] = "{$percentHits} % ({$hits})";
                    if($track != 'person') $row[] = "{$percentUsers} % ({$users})";

                    $objTable->addRow($row, $class);
                }
            }

            $str .= $objTable->show();
        }

        return $str;
    }

    /**
    * Method to show the general statistics
    *
    * @access private
    * @return string html
    */
    private function showGeneral()
    {
        $view = $this->getSession('view_mode', 'all');
        $total = $this->dbUsers->getTotalUsers($view);
        $gender = $this->dbUsers->getGenderSplit($view);
        $lang = $this->dbUsers->getHomeLanguages($view);
        $year = $this->dbUsers->getYearStudy($view);
        $courses = $this->dbUsers->getCourses($view);

        $arrInfo = array('bold' => '<b>', 'closebold' => '</b>', 'number' => $total);

        $head = $this->objLanguage->languageText('phrase_generalstatistics');
        $allInfo = $this->objLanguage->code2Txt('mod_hivaids_allstatsinfo', 'hivaids', $arrInfo);
        $studentInfo = $this->objLanguage->code2Txt('mod_hivaids_studentstatsinfo', 'hivaids', $arrInfo);
        $hdGender = $this->objLanguage->languageText('word_gender');
        $hdLang = $this->objLanguage->languageText('phrase_homelanguage');
        $hdCourse = $this->objLanguage->languageText('word_course');
        $hdYear = $this->objLanguage->languageText('phrase_yearofstudy');
        $hdName = $this->objLanguage->languageText('word_name');
        $hdNumber = $this->objLanguage->languageText('word_number');
        $hdPercentage = $this->objLanguage->languageText('word_percentage');
        $lbFemale = $this->objLanguage->languageText('word_female');
        $lbMale = $this->objLanguage->languageText('word_male');

        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $head;
        $str = $objHead->show();

        $show = $view.'Info';
        $str .= '<p class="dim">'.$$show.'</p>';

        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '5';

        $hdArr = array();
        $hdArr[] = $hdName;
        $hdArr[] = $hdNumber;
        $hdArr[] = $hdPercentage;

        $objTable->addHeader($hdArr);

        // gender
        $objTable->startRow();
        $objTable->addCell($hdGender, '','','','heading', 'colspan="3"');
        $objTable->endRow();

        if(!empty($gender)){
            $class = 'even';
            foreach($gender as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';

                $percent = ($item['cnt'] > 0 && $total > 0) ? $item['cnt'] / $total * 100 : 0;
                $row = array();
                $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                $row[] = $item['cnt'];
                $row[] = round($percent, 2).' %';

                $objTable->addRow($row, $class);
            }
        }

        // home language
        $objTable->startRow();
        $objTable->addCell($hdLang, '','','','heading', 'colspan="3"');
        $objTable->endRow();

        if(!empty($lang)){
            $class = 'even';
            foreach($lang as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';

                $percent = ($item['cnt'] > 0 && $total > 0) ? $item['cnt'] / $total * 100 : 0;
                $row = array();
                $row[] = $item['language'];
                $row[] = $item['cnt'];
                $row[] = round($percent, 2).' %';

                $objTable->addRow($row, $class);
            }
        }

        // year of study
        $objTable->startRow();
        $objTable->addCell($hdYear, '','','','heading', 'colspan="3"');
        $objTable->endRow();

        if(!empty($year)){
            $class = 'even';
            foreach($year as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';

                $percent = ($item['cnt'] > 0 && $total > 0) ? $item['cnt'] / $total * 100 : 0;
                $row = array();
                $row[] = $item['study_year'];
                $row[] = $item['cnt'];
                $row[] = round($percent, 2).' %';

                $objTable->addRow($row, $class);
            }
        }

        // faculty
        $objTable->startRow();
        $objTable->addCell($hdCourse, '','','','heading', 'colspan="3"');
        $objTable->endRow();

       if(!empty($courses)){
            $class = 'even';
            foreach($courses as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';

                $percent = ($item['cnt'] > 0 && $total > 0) ? $item['cnt'] / $total * 100 : 0;
                $row = array();
                $row[] = $item['course'];
                $row[] = $item['cnt'];
                $row[] = round($percent, 2).' %';

                $objTable->addRow($row, $class);
            }
        }


        $str .= $objTable->show();

        return $str;
    }

    /**
    * Method to display the statistics for the hivaids discussion forum
    *
    * @access public
    * @return string html
    */
    private function showForum()
    {
        $view = $this->getSession('view_mode', 'all');

        $data = $this->dbLogCalc->getForumCategories($view);
        $viewData = $this->dbLogCalc->getForumCatViews($view);

        $arrInfo = array('bold' => '<b>', 'closebold' => '</b>');
        $head = $this->objLanguage->languageText('mod_hivaids_forummonitoring', 'hivaids');
        $info = $this->objLanguage->code2Txt('mod_hivaids_forummonitoringinfo', 'hivaids', $arrInfo);
        $studentinfo = $this->objLanguage->code2Txt('mod_hivaids_forummonitoringstudentinfo', 'hivaids', $arrInfo);
        $hdCategory = $this->objLanguage->languageText('word_category');
        $hdTopics = $this->objLanguage->languageText('phrase_numtopics');
        $hdReplies = $this->objLanguage->languageText('phrase_numreplies');
        $hdViews = $this->objLanguage->languageText('phrase_numviews');
        $hdViewGroup = $this->objLanguage->languageText('phrase_viewsbygroup');
        $lnViewTopics = $this->objLanguage->languageText('phrase_viewtopics');
        $language = $this->objLanguage->languageText('phrase_homelanguage');
        $sex = $this->objLanguage->languageText('word_gender');
        $study_year = $this->objLanguage->languageText('phrase_yearofstudy');
        $course = $this->objLanguage->languageText('word_course');
        $M = $this->objLanguage->languageText('word_male');
        $F = $this->objLanguage->languageText('word_female');

        $objHead = new htmlheading();
        $objHead->str = $head;
        $objHead->type = 1;
        $str = $objHead->show();

        $str .= ($view == 'student') ? '<p class="dim">'.$studentinfo.'</p>' : '<p class="dim">'.$info.'</p>';

        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellspacing = '2';
            $objTable->cellpadding = '5';

            $hdArr = array();
            $hdArr[] = $hdCategory;
            $hdArr[] = $hdTopics;
            $hdArr[] = $hdReplies;
            $hdArr[] = $hdViews;
            $hdArr[] = $hdViewGroup;
            $hdArr[] = '';

            $objTable->addHeader($hdArr);

            $class = 'even';
            foreach($data as $key => $item){
                $class = ($class == 'even') ? 'odd' : 'even';

                $rows = (isset($viewData[$key])) ? $viewData[$key]['rows']+1 : 1;
                $total = (isset($viewData[$key])) ? $viewData[$key]['total'] : 0;
                $objLink = new link($this->uri(array('action' => 'tracking', 'mode' => 'forum_topics', 'category' => $key)));
                $objLink->link = $lnViewTopics;
                $link = $objLink->show();

                $objTable->startRow();
                $objTable->addCell($item['name'], '','','', $class, "rowspan='{$rows}'");
                $objTable->addCell($item['topics'].' ('.$link.')', '','','', $class, "rowspan='{$rows}'");
                $objTable->addCell($item['replies'], '','','', $class, "rowspan='{$rows}'");
                $objTable->addCell($item['views'], '','','', $class, "rowspan='{$rows}'");
                if($rows == 1) $objTable->addCell('', '','','', $class, "colspan = '2'");
                $objTable->endRow();

                if(isset($viewData[$key]) && !empty($viewData[$key])){
                    $subclass = ($class == 'even') ? 'odd' : 'even';
                    foreach($viewData[$key] as $group => $val){

                        foreach($val as $k=>$v){
                            $subclass  = ($subclass == 'even') ? 'odd' : 'even';

                            if($group == 'sex'){
                                $k = $$k;
                            }
                            $percent = ((int) $v / (int) $total) * 100;
                            $round = round($percent, 2);

                            $row = array();
                            $row[] = $$group.': '.$k;
                            $row[] = $v.' / '.$total.' ('.$round.' %)';

                            $objTable->addRow($row, $subclass);
                        }
                    }
                }
            }
            $str .= $objTable->show();
        }

        return $str;
    }

    /**
    * Method to display the statistics for the hivaids discussion forum topics within a category
    *
    * @access public
    * @return string html
    */
    private function showForumTopics($category)
    {
        $view = $this->getSession('view_mode', 'all');

        $data = $this->dbLogCalc->getForumTopics($category, $view);
        $viewData = $this->dbLogCalc->getForumCatViews($view, 'topic', 25);
        $category = $data[0]['forum_name'];
        $num = count($data);

        $arrInfo = array('bold' => '<b>', 'closebold' => '</b>', 'category' => $category, 'number' => $num);
        $head = $this->objLanguage->languageText('mod_hivaids_forummonitoring', 'hivaids');
        $info = $this->objLanguage->code2Txt('mod_hivaids_forumtopicmonitoringinfo', 'hivaids', $arrInfo);
        $studentinfo = $this->objLanguage->code2Txt('mod_hivaids_forumtopicmonitoringstudentinfo', 'hivaids', $arrInfo);
        $hdTopic = $this->objLanguage->languageText('word_topic');
        $hdReplies = $this->objLanguage->languageText('phrase_numreplies');
        $hdViews = $this->objLanguage->languageText('phrase_numviews');
        $hdViewGroup = $this->objLanguage->languageText('phrase_viewsbygroup');
        $lnViewTopics = $this->objLanguage->languageText('phrase_viewtopics');
        $language = $this->objLanguage->languageText('phrase_homelanguage');
        $sex = $this->objLanguage->languageText('word_gender');
        $study_year = $this->objLanguage->languageText('phrase_yearofstudy');
        $course = $this->objLanguage->languageText('word_course');
        $M = $this->objLanguage->languageText('word_male');
        $F = $this->objLanguage->languageText('word_female');

        $objHead = new htmlheading();
        $objHead->str = $head;
        $objHead->type = 1;
        $str = $objHead->show();

        $str .= ($view == 'student') ? '<p class="dim">'.$studentinfo.'</p>' : '<p class="dim">'.$info.'</p>';

        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellspacing = '2';
            $objTable->cellpadding = '5';

            $hdArr = array();
            $hdArr[] = $hdTopic;
            $hdArr[] = $hdReplies;
            $hdArr[] = $hdViews;
            $hdArr[] = $hdViewGroup;
            $hdArr[] = '';

            $objTable->addHeader($hdArr);

            $class = 'even';
            foreach($data as $key => $item){
                $class = ($class == 'even') ? 'odd' : 'even';

                $rows = (isset($viewData[$item['topicid']])) ? $viewData[$item['topicid']]['rows']+1 : 1;
                $total = (isset($viewData[$item['topicid']])) ? $viewData[$item['topicid']]['total'] : 0;

                $objTable->startRow();
                $objTable->addCell($item['post_title'], '','','', $class, "rowspan='{$rows}'");
                $objTable->addCell($item['replies'], '','','', $class, "rowspan='{$rows}'");
                $objTable->addCell($item['views'], '','','', $class, "rowspan='{$rows}'");
                if($rows == 1) $objTable->addCell('', '','','', $class, "colspan = '2'");
                $objTable->endRow();

                if(isset($viewData[$item['topicid']]) && !empty($viewData[$item['topicid']])){
                    $subclass = ($class == 'even') ? 'odd' : 'even';
                    foreach($viewData[$item['topicid']] as $group => $val){

                        foreach($val as $k=>$v){
                            $subclass  = ($subclass == 'even') ? 'odd' : 'even';

                            if($group == 'sex'){
                                $k = $$k;
                            }
                            $percent = ((int) $v / (int) $total) * 100;
                            $round = round($percent, 2);

                            $row = array();
                            $row[] = $$group.': '.$k;
                            $row[] = $v.' / '.$total.' ('.$round.' %)';

                            $objTable->addRow($row, $subclass);
                        }
                    }
                }
            }
            $str .= $objTable->show();
        }

        return $str;
    }

    /**
    * Method to display the list of students and their participation in the discussions
    *
    * @access private
    * @return string html
    */
    private function showParticipation()
    {
        $data = $this->dbLogCalc->getStudentParticipation();

        $head = $this->objLanguage->languageText('phrase_studentparticipation');
        $info = $this->objLanguage->languageText('mod_hivaids_infostudentparticipation', 'hivaids');
        $hdStudNum = $this->objLanguage->languageText('phrase_studentnumber');
        $hdUsername = $this->objLanguage->languageText('word_username');
        $hdCategory = $this->objLanguage->languageText('word_category');
        $hdTopic = $this->objLanguage->languageText('word_topic');
        $hdViews = $this->objLanguage->languageText('word_views');
        $hdReplies = $this->objLanguage->languageText('word_replies');

        $objHead = new htmlHeading();
        $objHead->str = $head;
        $objHead->type = 1;
        $str = $objHead->show();

        $str .= '<p class="dim">'.$info.'</p>';

        if(!empty($data)){
            $objTable = new htmlTable();
            $objTable->cellpadding = '5';
            $objTable->cellspacing = '2';

            $hdArr = array();
            $hdArr[] = $hdStudNum;
            $hdArr[] = $hdUsername;
            $hdArr[] = $hdCategory;
            $hdArr[] = $hdTopic;
            $hdArr[] = $hdViews;
            $hdArr[] = $hdReplies;

            $objTable->addHeader($hdArr);

            $class = 'even';
            foreach($data as $item){
                $class = ($class == 'even') ? 'odd' : 'even';

                $arrTopics = $item['topics'];
                $cnt = count($arrTopics)+1;

                $objTable->startRow($class);
                $objTable->addCell($item['staffnumber'], '','','',$class, "rowspan='{$cnt}'");
                $objTable->addCell($item['username'], '','','',$class, "rowspan='{$cnt}'");
                $objTable->endRow();


                if(!empty($arrTopics)){
                    foreach($arrTopics as $item){

                        $row = array();
                        $row[] = $item['category'];
                        $row[] = $item['topic'];
                        $row[] = $item['views'];
                        $row[] = $item['replies'];

                        $objTable->addRow($row, $class);
                    }
                }
            }
            $str .= $objTable->show();
        }

        return $str;
    }

    /**
     * Display all the information logged in the system
     *
     * @access private
     * @return string html
     */
    private function showAllInfo()
    {
        // Get objects
        $objCMSSections = $this->getObject('dbsections', 'cmsadmin');
        $objCMSContent = $this->getObject('dbcontent', 'cmsadmin');
        $objDbForum = $this->getObject('dbhivforum', 'hivaidsforum');
        $objDbPodcast = $this->getObject('dbpodcast', 'podcast');

        // Get data
        $cmsData = $this->dbLogCalc->getCmsLog();
        $dfData = $this->dbLogCalc->getDFLog();
        $podData = $this->dbLogCalc->getPodcastLog();
        $hivData = $this->dbLogCalc->getHIVLog();
        //$photoData = $this->dbLogCalc->getPhotoLog();

        $hdAllInfo = $this->objLanguage->languageText('phrase_allloggedinformation');
        $hdPage = $this->objLanguage->languageText('word_page');
        $hdHit = $this->objLanguage->languageText('word_hit');
        $hdDate = $this->objLanguage->languageText('phrase_dateofvisit');
        $hdTime = $this->objLanguage->languageText('phrase_timeofvisit');
        $hdReg = $this->objLanguage->languageText('phrase_registereduser');
        $hdUsername = $this->objLanguage->languageText('word_username');
        $hdGender = $this->objLanguage->languageText('word_gender');
        $hdLang = $this->objLanguage->languageText('phrase_homelanguage');
        $hdCountry = $this->objLanguage->languageText('word_country');
        $hdStaff = $this->objLanguage->languageText('mod_hivaids_stafforstudent', 'hivaids');
        $hdFaculty = $this->objLanguage->languageText('word_faculty');
        $hdYear = $this->objLanguage->languageText('phrase_yearofstudy');
        $hdPosts = $this->objLanguage->languageText('word_posts');
        $hdIp = $this->objLanguage->languageText('phrase_ipaddress');
        $lnExport = $this->objLanguage->languageText('phrase_exportascsv');
        $lbExcel = $this->objLanguage->languageText('mod_hivaids_importexcel', 'hivaids');
        $lbHome = $this->objLanguage->languageText('word_home');
        $lbDefault = $this->objLanguage->languageText('word_default');
        $lbMale = $this->objLanguage->languageText('word_male');
        $lbFemale = $this->objLanguage->languageText('word_female');
        $lbLists = $this->objLanguage->languageText('phrase_topiclist');
        $byuser = $this->objLanguage->languageText('phrase_mypodcasts');
        $playpodcast = $this->objLanguage->languageText('phrase_playpodcast');
        $addpodcast = $this->objLanguage->languageText('phrase_addpodcast');
        $home = $this->objLanguage->languageText('phrase_podcasthome');
        $lbOther = $this->objLanguage->languageText('word_other');

        $viewlinks = $this->objLanguage->languageText('phrase_linkspage');
        $playyourmoves = $this->objLanguage->languageText('phrase_playyourmovesgame');
        $showregister = $this->objLanguage->languageText('phrase_newregistration');
        $videolist = $this->objLanguage->languageText('phrase_videolist');

        $objHead = new htmlHeading();
        $objHead->str = $hdAllInfo;
        $objHead->type = 1;
        $str = $objHead->show();

        $objLink = new link($this->uri(array('action' => 'exportlog', 'mode' => 'export')));
        $objLink->link = $lnExport;
        $str .= $objLink->show().' ('.$lbExcel.')';

        $objTable = new htmlTable();
        $objTable->cellpadding = '5';
        $objTable->cellspacing = '2';

        $hdArr = array();
        $hdArr[] = $hdPage;
        $hdArr[] = $hdHit;
        $hdArr[] = $hdDate;
        $hdArr[] = $hdTime;
        $hdArr[] = $hdIp;
        $hdArr[] = $hdReg;
        $hdArr[] = $hdUsername;
        $hdArr[] = $hdGender;
        $hdArr[] = $hdLang;
        $hdArr[] = $hdCountry;
        $hdArr[] = $hdStaff;
        $hdArr[] = $hdFaculty;
        $hdArr[] = $hdYear;
        $hdArr[] = $hdPosts;

        $objTable->addHeader($hdArr, 'heading');

        /* ** CMS Log Data ** */
        $class = 'even';
        if(!empty($cmsData)){
            foreach($cmsData as $sectionid => $arrPages){
                // Get section name
                $section = ($sectionid == 'home') ? $lbHome : $objCMSSections->getMenuText($sectionid);
                foreach ($arrPages as $pageid => $arrHits){
                    // Get page name
                    if($pageid == 'home') {
                        $page = $lbDefault;
                    }else{
                        $arrContent = $objCMSContent->getContentPage($pageid);
                        $page = $arrContent['title'];
                    }
                    $class = ($class == 'even') ? 'odd' : 'even';
                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : '<b>'.$section.': '.$page.'</b>';
                        $row[] = '<b>'.$hitNum.'</b>';
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }

                        $objTable->addRow($row, $class);
                    }
                }
            }
        }

        /* ** Discussion forum log ** */
        if(!empty($dfData)){
            foreach ($dfData as $catId => $arrTopics){
                // Get category title
                $category = $objDbForum->getCatTitle($catId);

                foreach ($arrTopics as $topicid => $arrHits){
                    // Get topic name
                    if($topicid == 'home') {
                        $topic = $lbDefault.' ('.$lbLists.')';
                    }else{
                        $topic = $objDbForum->getTopicTitle($topicid, $catId);
                    }

                    $class = ($class == 'even') ? 'odd' : 'even';
                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : '<b>'.$category.': '.$topic.'</b>';
                        $row[] = '<b>'.$hitNum.'</b>';
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = (isset($item['posts'])) ? $item['posts'] : '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }

                        $objTable->addRow($row, $class);
                    }
                }
            }
        }


        /* ** Podcast log ** */
        if(!empty($podData)){
            foreach ($podData as $action => $arrActions){
                // Get category title
                $view = $$action;

                foreach ($arrActions as $podId => $arrHits){
                    // Get topic name
                    if($podId == 'none') {
                        $podcast = '';
                    }else{
                        // Get podcast title
                        $arrPod = $objDbPodcast->getPodcast($podId);
                        $podcast = (isset($arrPod['title'])) ? ': '.$arrPod['title'] : '';
                    }

                    $class = ($class == 'even') ? 'odd' : 'even';
                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : '<b>'.$view.$podcast.'</b>';
                        $row[] = '<b>'.$hitNum.'</b>';
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }

                        $objTable->addRow($row, $class);
                    }
                }
            }
        }

        /* ** Hivaids log ** */
        if(!empty($hivData)){
            foreach ($hivData as $action => $arrActions){
                // Get category title
                $view = $$action;

                $class = ($class == 'even') ? 'odd' : 'even';
                foreach ($arrActions as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : '<b>'.$view.'</b>';
                        $row[] = '<b>'.$hitNum.'</b>';
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }

                        $objTable->addRow($row, $class);
                }
            }
        }

        $str .= $objTable->show();

        return $str;
    }

    /**
     * Export all logged info to CSV format
     *
     * @access private
     */
    private function exportAllInfo()
    {
        // Get objects
        $objCMSSections = $this->getObject('dbsections', 'cmsadmin');
        $objCMSContent = $this->getObject('dbcontent', 'cmsadmin');
        $objDbForum = $this->getObject('dbhivforum', 'hivaidsforum');
        $objDbPodcast = $this->getObject('dbpodcast', 'podcast');

        // Get data
        $cmsData = $this->dbLogCalc->getCmsLog();
        $dfData = $this->dbLogCalc->getDFLog();
        $podData = $this->dbLogCalc->getPodcastLog();
        $hivData = $this->dbLogCalc->getHIVLog();

        $lbHome = $this->objLanguage->languageText('word_home');
        $lbDefault = $this->objLanguage->languageText('word_default');
        $lbMale = $this->objLanguage->languageText('word_male');
        $lbFemale = $this->objLanguage->languageText('word_female');
        $lbLists = $this->objLanguage->languageText('phrase_topiclist');
        $byuser = $this->objLanguage->languageText('phrase_mypodcasts');
        $playpodcast = $this->objLanguage->languageText('phrase_playpodcast');
        $addpodcast = $this->objLanguage->languageText('phrase_addpodcast');
        $home = $this->objLanguage->languageText('phrase_podcasthome');
        $viewlinks = $this->objLanguage->languageText('phrase_linkspage');
        $playyourmoves = $this->objLanguage->languageText('phrase_playyourmovesgame');
        $showregister = $this->objLanguage->languageText('phrase_newregistration');
        $videolist = $this->objLanguage->languageText('phrase_videolist');
        $lbOther = $this->objLanguage->languageText('word_other');

        $hdArr = array();
        $hdArr[] = $this->objLanguage->languageText('word_page');
        $hdArr[] = $this->objLanguage->languageText('word_hit');
        $hdArr[] = $this->objLanguage->languageText('phrase_dateofvisit');
        $hdArr[] = $this->objLanguage->languageText('phrase_timeofvisit');
        $hdArr[] = $this->objLanguage->languageText('phrase_ipaddress');
        $hdArr[] = $this->objLanguage->languageText('phrase_registereduser');
        $hdArr[] = $this->objLanguage->languageText('word_username');
        $hdArr[] = $this->objLanguage->languageText('word_gender');
        $hdArr[] = $this->objLanguage->languageText('phrase_homelanguage');
        $hdArr[] = $this->objLanguage->languageText('word_country');
        $hdArr[] = $this->objLanguage->languageText('mod_hivaids_stafforstudent', 'hivaids');
        $hdArr[] = $this->objLanguage->languageText('word_faculty');
        $hdArr[] = $this->objLanguage->languageText('phrase_yearofstudy');
        $hdArr[] = $this->objLanguage->languageText('word_posts');

        // Create csv headings
        $csv = '"'.implode('","', $hdArr).'"';
        $csv .= "\n";

        /* ** CMS Log Data ** */
        if(!empty($cmsData)){
            foreach($cmsData as $sectionid => $arrPages){
                // Get section name
                $section = ($sectionid == 'home') ? $lbHome : $objCMSSections->getMenuText($sectionid);
                foreach ($arrPages as $pageid => $arrHits){
                    // Get page name
                    if($pageid == 'home') {
                        $page = $lbDefault;
                    }else{
                        $arrContent = $objCMSContent->getContentPage($pageid);
                        $page = $arrContent['title'];
                    }
                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : $section.': '.$page;
                        $row[] = $hitNum;
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }
                        $csv .= '"'.implode('","', $row).'"';
                        $csv .= "\n";
                    }
                }
            }
        }

        /* ** Discussion forum log ** */
        if(!empty($dfData)){
            foreach ($dfData as $catId => $arrTopics){
                // Get category title
                $category = $objDbForum->getCatTitle($catId);

                foreach ($arrTopics as $topicid => $arrHits){
                    // Get topic name
                    if($topicid == 'home') {
                        $topic = $lbDefault.' ('.$lbLists.')';
                    }else{
                        $topic = $objDbForum->getTopicTitle($topicid, $catId);
                    }

                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : $category.': '.$topic;
                        $row[] = $hitNum;
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = (isset($item['posts'])) ? $item['posts'] : '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }

                        $csv .= '"'.implode('","', $row).'"';
                        $csv .= "\n";
                    }
                }
            }
        }

        /* ** Podcast log ** */
        if(!empty($podData)){
            foreach ($podData as $action => $arrActions){
                // Get category title
                $view = $$action;

                foreach ($arrActions as $podId => $arrHits){
                    // Get topic name
                    if($podId == 'none') {
                        $podcast = '';
                    }else{
                        // Get podcast title
                        $arrPod = $objDbPodcast->getPodcast($podId);
                        $podcast = (isset($arrPod['title'])) ? ': '.$arrPod['title'] : '';
                    }

                    foreach ($arrHits as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : $view.$podcast;
                        $row[] = $hitNum;
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }
                        $csv .= '"'.implode('","', $row).'"';
                        $csv .= "\n";
                    }
                }
            }
        }

        /* ** Hivaids log ** */
        if(!empty($hivData)){
            foreach ($hivData as $action => $arrActions){
                // Get category title
                $view = $$action;

                foreach ($arrActions as $i => $item){
                        $dateArr = explode(' ', $item['log_date']);
                        $hitNum = $i+1;


                        $row = array();
                        $row[] = ($i > 0) ? '' : $view;
                        $row[] = $hitNum;
                        $row[] = $dateArr[0];
                        $row[] = $dateArr[1];
                        $row[] = $item['ipaddress'];

                        // registered user information
                        if(!empty($item['user_id'])){
                            $row[] = 'Y';
                            $row[] = $item['username'];
                            $row[] = ($item['sex'] == 'F') ? $lbFemale : $lbMale;
                            $row[] = $item['language'];
                            $row[] = $this->objCountries->getName($item['country']);
                            $row[] = $item['staff_student'];
                            $row[] = $item['course'];
                            $row[] = ($item['study_year'] == 'NULL') ? $lbOther : $item['study_year'];
                            $row[] = '';
                        }else{
                            $row[] = 'N';
                            for($x = 0; $x < 8; $x++){
                                $row[] = '';
                            }
                        }
                        $csv .= '"'.implode('","', $row).'"';
                        $csv .= "\n";
                }
            }
        }

        return $csv;
    }

    /**
    * Method to create a left menu for the tracking
    *
    * @access public
    * @return string html
    */
    public function showLeftMenu()
    {
        $hdTrackBy = $this->objLanguage->languageText('phrase_trackby');
        $lbPerson = $this->objLanguage->languageText('word_person');
        $lbGroup = $this->objLanguage->languageText('word_group');
        $lbEveryone = $this->objLanguage->languageText('word_everyone');
        $hdMode = $this->objLanguage->languageText('word_mode');
        $lbStudents = $this->objLanguage->languageText('phrase_trackstudents');
        $lbAll = $this->objLanguage->languageText('phrase_trackall');
        $hdView = $this->objLanguage->languageText('phrase_viewstatistics');
        $lbUser = $this->objLanguage->languageText('word_user');
        $lbGeneral = $this->objLanguage->languageText('word_general');
        $lbForum = $this->objLanguage->languageText('phrase_discussionforum');
        $lbLogger = $this->objLanguage->languageText('word_logger');
        $lbClear = $this->objLanguage->languageText('word_clear');
        $hdClear = $this->objLanguage->languageText('mod_hivaids_clearallloggedinfo', 'hivaids');
        $lbClearInfo = $this->objLanguage->languageText('mod_hivaids_explainclearall', 'hivaids');
        $lbConfirm = $this->objLanguage->languageText('mod_hivaids_confirmclearall', 'hivaids');
        $lbRegNum = $this->objLanguage->languageText('mod_hivaids_requirestudentnumber', 'hivaids');
        $lbOff = $this->objLanguage->languageText('phrase_switchoff');
        $lbOn = $this->objLanguage->languageText('phrase_switchon');
        $hdConfig = $this->objLanguage->languageText('word_configure');
        $lbParticipation = $this->objLanguage->languageText('phrase_studentparticipation');
        $lbPrint = $this->objLanguage->languageText('phrase_printfriendly');
        $hdPrint = $this->objLanguage->languageText('word_print');
        $printInfo = $this->objLanguage->languageText('mod_hivaids_printinfo', 'hivaids');
        $lbAllInfo = $this->objLanguage->languageText('phrase_allloggedinfo');

        $view_mode = $this->getSession('view_mode', 'all');
        $track_mode = $this->getSession('track_mode', '');
        $nextMode = $this->getParam('mode');

        // View monitoring by
        $person = $group = $everyone = '';
        $$track_mode = '<b>*</b> ';
        $objLink = new link($this->uri(array('action' => 'settracking', 'mode' => 'track_person', 'nextmode' => '')));
        $objLink->link = $lbPerson;
        $str = '<ul><li>'.$person.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'settracking', 'mode' => 'track_group', 'nextmode' => '')));
        $objLink->link = $lbGroup;
        $str .= '<li>'.$group.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'settracking', 'mode' => 'track_everyone', 'nextmode' => '')));
        $objLink->link = $lbEveryone;
        $str .= '<li>'.$everyone.$objLink->show().'</li></ul>';

        $menuStr = $this->objFeatureBox->show($hdTrackBy, $str);

        // Change the mode of view
        $all = $student = '';
        $$view_mode = '<b>*</b> ';
        $objLink = new link($this->uri(array('action' => 'settracking', 'mode' => 'mode_all', 'nextmode' => $nextMode)));
        $objLink->link = $lbAll;
        $modeStr = '<ul><li>'.$all.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'settracking', 'mode' => 'mode_students', 'nextmode' => $nextMode)));
        $objLink->link = $lbStudents;
        $modeStr .= '<li>'.$student.$objLink->show().'</li></ul>';

        $menuStr .= $this->objFeatureBox->show($hdMode, $modeStr);

        // General links
        $objLink = new link($this->uri(array('action' => 'userstats')));
        $objLink->link = $lbUser;
        $genStr = '<ul><li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'tracking', 'mode' => 'general')));
        $objLink->link = $lbGeneral;
        $genStr .= '<li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'tracking', 'mode' => 'forum')));
        $objLink->link = $lbForum;
        $genStr .= '<li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'tracking', 'mode' => 'participation')));
        $objLink->link = $lbParticipation;
        $genStr .= '<li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri('', 'logger'));
        $objLink->link = $lbLogger;
        $genStr .= '<li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action' => 'tracking', 'mode' => 'allinfo')));
        $objLink->link = $lbAllInfo;
        $genStr .= '<li>'.$objLink->show().'</li></ul>';

        $menuStr .= $this->objFeatureBox->show($hdView, $genStr);

        // print friendly button
        $objButton = new button('print', $lbPrint);
        $objButton->setToSubmit();

        $link = $this->uri(array('action' => 'printtracking', 'printmode' => $nextMode));
        $objForm = new form('print', $link);
        $objForm->addToForm($objButton->show());

        $printStr = '<p class="dim">'.$printInfo.'</p>';
        $printStr .= '<p>'.$objForm->show().'</p>';
        $menuStr .= $this->objFeatureBox->show($hdPrint, $printStr);

        // Remove the student number from the registration
        $regStr = '<p class="dim">'.$lbRegNum.'</p>';
        $lbSet = $this->objSysConfig->getValue('USE_STUDENT_NUMBER', 'hivaids') ? $lbOff : $lbOn;
        $objButton = new button('config', $lbSet);
        $objButton->setToSubmit();

        $link = $this->uri(array('action' => 'settracking', 'mode' => 'setconfig', 'nextmode' => $nextMode));
        $objForm = new form('setconfig', $link);
        $objForm->addToForm($objButton->show());

        $regStr .= '<p>'.$objForm->show().'</p>';

        $menuStr .= $this->objFeatureBox->show($hdConfig, $regStr);

        // Only display when action = manageclear
        $action = $this->getParam('action');
        if($action == 'manageclear'){
            // Clear the logger
            $clearStr = '<p class="dim">'.$lbClearInfo.'</p>';
            $objButton = new button('clear', $lbClear);
            $link = $this->uri(array('action' => 'settracking', 'mode' => 'clearall', 'nextmode' => $nextMode));
            $this->objConfirm->setConfirm($objButton->show(), $link, $lbConfirm);

            $clearStr .= '<p>'.$this->objConfirm->show().'</p>';

            $menuStr .= $this->objFeatureBox->show($hdClear, $clearStr);
        }

        return $menuStr;
    }

    /**
    * Method to display the requested set of tracking calculations
    *
    * @access public
    * @param string $mode
    * @return string html
    */
    public function show($mode)
    {
        switch($mode){
            case 'setconfig':
                $toggle = $this->objSysConfig->getValue('USE_STUDENT_NUMBER', 'hivaids') ? false : true;
                $this->objSysConfig->changeParam('USE_STUDENT_NUMBER', 'hivaids', $toggle);
                break;

            case 'clearall':
                $this->dbLogCalc->clearLogger();
                break;

            case 'participation':
                return $this->showParticipation();
                break;

            case 'general':
                $this->unsetSession('track_mode');
                return $this->showGeneral();
                break;

            case 'forum':
                $this->unsetSession('track_mode');
                return $this->showForum();

            case 'allinfo':
                return $this->showAllInfo();

            case 'export':
                return $this->exportAllInfo();

            case 'forum_topics':
                $this->unsetSession('track_mode');
                $category = $this->getParam('category');
                return $this->showForumTopics($category);

            case 'mode_students':
                $this->setSession('view_mode', 'student');
                break;

            case 'mode_all':
                $this->setSession('view_mode', 'all');
                break;

            case 'track_everyone':
                $this->setSession('track_mode', 'everyone');
                break;

            case 'track_group':
                $this->setSession('track_mode', 'group');
                break;

            case 'track_person':
                $this->setSession('track_mode', 'person');
                break;

            case 'changeuser':
                $user = $this->getParam('username');
                $this->setSession('user', $user);
                return $this->showMonitoring();

            case 'changegroup':
                $group = $this->getParam('group');
                $subgroup = $this->getParam('subgroup');
                $this->setSession('group', $group);
                $this->setSession('subgroup', $subgroup);
                return $this->showMonitoring();

            default:
                return $this->showMonitoring();
        }
    }
}
?>