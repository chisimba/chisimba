<?php
/* -------------------- survey extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package survey
*/

/**
* Default template for the survey manager
* Author Kevin Cyster
* */

    $this->setLayoutTemplate('layout_tpl.php');

// set up html elements
    $objHeader=&$this->loadClass('htmlheading','htmlelements');
    $objTable=&$this->loadClass('htmltable','htmlelements');
    $objIcon=&$this->newObject('geticon','htmlelements');
    $objPopup=&$this->loadClass('windowpop','htmlelements');
    $objLink=&$this->loadClass('link','htmlelements');

// set up language items
    $heading=$this->objLanguage->languageText('mod_survey_name', 'survey');
    $exitLabel=$this->objLanguage->languageText('mod_survey_exit', 'survey');
    $nameLabel=$this->objLanguage->languageText('mod_survey_wordname', 'survey');
    $ownerLabel=$this->objLanguage->languageText('mod_survey_owner', 'survey');
    $startLabel=$this->objLanguage->languageText('mod_survey_startdate', 'survey');
    $endLabel=$this->objLanguage->languageText('mod_survey_enddate', 'survey');
    $creationLabel=$this->objLanguage->languageText('mod_survey_creationdate', 'survey');
    $statusLabel=$this->objLanguage->languageText('mod_survey_status', 'survey');
    $emptyLabel=$this->objLanguage->languageText('mod_survey_empty', 'survey');
    $surveyLabel=$this->objLanguage->languageText('mod_survey_survey', 'survey');
    $questionLabel=$this->objLanguage->languageText('mod_survey_question', 'survey');
    $resultsLabel=$this->objLanguage->languageText('mod_survey_results', 'survey');
    $notifyLabel=$this->objLanguage->languageText('mod_survey_notify', 'survey');
    $activeLabel=$this->objLanguage->languageText('mod_survey_active', 'survey');
    $inactiveLabel=$this->objLanguage->languageText('mod_survey_inactive', 'survey');
    $activateLabel=$this->objLanguage->languageText('mod_survey_activate', 'survey');
    $deactivateLabel=$this->objLanguage->languageText('mod_survey_deactivate', 'survey');
    $noActivateLabel=$this->objLanguage->languageText('mod_survey_inactivequestion', 'survey');
    $noDeactivateLabel=$this->objLanguage->languageText('mod_survey_activeanswer', 'survey');
    $takeLabel=$this->objLanguage->languageText('mod_survey_take', 'survey');
    $unavailableLabel=$this->objLanguage->languageText('mod_survey_unavailable', 'survey');
    $resultsLabel=$this->objLanguage->languageText('mod_survey_results', 'survey');
    $responsesLabel=$this->objLanguage->languageText('mod_survey_responses', 'survey');
    $blankpagesLabel=$this->objLanguage->languageText('mod_survey_blankpages', 'survey');
    $unassignedLabel=$this->objLanguage->languageText('mod_survey_unassigned', 'survey');
    $groupsLabel=$this->objLanguage->languageText('mod_survey_groupheader', 'survey');
    $commentsLabel=$this->objLanguage->languageText('mod_survey_comments', 'survey');
    $observerLabel=$this->objLanguage->languageText('mod_survey_observer', 'survey');

// set up code to text elements
    $array=array('item'=>strtolower($surveyLabel));
    $addSurveyLabel=$this->objLanguage->code2Txt('mod_survey_add', 'survey', $array);
    $editSurveyLabel=$this->objLanguage->code2Txt('mod_survey_edit', 'survey', $array);
    $deleteSurveyLabel=$this->objLanguage->code2Txt('mod_survey_deleteconfirm', 'survey', $array);
    $copyLabel=$this->objLanguage->code2Txt('mod_survey_copy', 'survey', $array);
    $viewSurveyLabel=$this->objLanguage->code2Txt('mod_survey_view', 'survey', $array);
    $array=array('item'=>strtolower($resultsLabel));
    $viewResultsLabel=$this->objLanguage->code2Txt('mod_survey_view', 'survey', $array);
    $array=array('item'=>strtolower($responsesLabel));
    $viewResponsesLabel=$this->objLanguage->code2Txt('mod_survey_view', 'survey', $array);
    $array=array('item'=>strtolower($groupsLabel));
    $editGroupsLabel=$this->objLanguage->code2Txt('mod_survey_edit', 'survey', $array);
    $array=array('item'=>$observerLabel.' '.$commentsLabel);
    $viewCommentsLabel=$this->objLanguage->code2Txt('mod_survey_view', 'survey', $array);

// set up data
    $currentDate=$this->formatDate(date('Y-m-d'));

// set up add icon
    $objIcon->title=$addSurveyLabel;
    $addIcon=$objIcon->getAddIcon($this->uri(array('action'=>'addsurvey')));

// set up heading
    $objHeader = new htmlheading();
    $objHeader->str=$heading.' '.$addIcon;
    $objHeader->type=1;
    echo $objHeader->show().'<hr />';

// set up table
    $objTable = new htmltable();
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startHeaderRow();
    $objTable->addHeaderCell($nameLabel,'','','center','heading','rowspan="2"');
    $objTable->addHeaderCell($ownerLabel,'','','center','heading','');
    $objTable->addHeaderCell($startLabel,'','','center','heading','rowspan="2"');
    $objTable->addHeaderCell($endLabel,'','','center','heading','rowspan="2"');
    $objTable->addHeaderCell($statusLabel,'','','center','heading','rowspan="2"');
    $objTable->addHeaderCell('','','','center','heading','rowspan="2"');
    $objTable->endHeaderRow();

    // show closed surveys only to group members
    if(!empty($arrSurveyList)){
        foreach($arrSurveyList as $key=>$survey){
            $surveyId=$survey['id'];
            $userGroup=$this->groups->getUserGroup($this->userId,$surveyId);
            $groupId=$this->objGroupAdmin->getLeafId(array($surveyId,'Respondents'));
            $arrRespondentList=$this->objGroupAdmin->getGroupUsers($groupId,array('userId'));
            if(!empty($arrRespondentList)){
                if($userGroup=='None' && !$this->isAdmin){
                    unset($arrSurveyList[$key]);
                }
            }
        }
    }

    if(empty($arrSurveyList)){
        // set up no records message
        $objTable->startRow();
        $objTable->addCell($emptyLabel,'','','','noRecordsMessage','colspan="6"');
        $objTable->endRow();
    }else{
        // set up list of surveys
        $i=0;
        foreach($arrSurveyList as $survey){
            $class=(($i++%2)==0)?'odd':'even';
            $icons='';

            $surveyId=$survey['id'];
            $surveyName=$survey['survey_name'];
            $startDate=$this->formatDate($survey['start_date']);
            $endDate=$this->formatDate($survey['end_date']);
            $surveyActive=$survey['survey_active'];
            $groupEmailSent=$survey['email_sent'];
            $responseCounter=$survey['response_counter'];
            $responseMaximum=$survey['max_responses'];
            $singleResponses=$survey['single_responses'];
            $creatorId=$survey['creator_id'];
            $dateCreated=$this->formatDate($survey['date_created']);
            $owner=$this->objUser->fullName($creatorId);
            $commentCount=$survey['commentcount'];

            $groupId=$this->objGroupAdmin->getLeafId(array($surveyId,'Respondents'));
            $arrRespondentList=$this->objGroupAdmin->getGroupUsers($groupId,array('userId'));
            $userGroup=$this->groups->getUserGroup($this->userId,$surveyId);
            $canViewResults=$this->canViewResults($surveyId);

            // set up name link
            $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);

            if($userGroup=='None' || $userGroup=='Respondents'){
                $nameLink=$surveyName;
            }else{
                if(($userGroup=='Creator' || $userGroup=='Collaborators') && $surveyActive!=1){
                    if(!empty($arrQuestionList)){
                        $objLink=new link($this->uri(array('action'=>'listquestions','survey_id'=>$surveyId),'survey'));
                        $objLink->link=$surveyName;
                        $nameLink=$objLink->show();
                    }else{
                        $objLink=new link($this->uri(array('action'=>'addquestion','survey_id'=>$surveyId),'survey'));
                        $objLink->link=$surveyName;
                        $nameLink=$objLink->show();
                    }
                }else{
                    if(!empty($arrQuestionList)){
                        $objLink=new link($this->uri(array('action'=>'previewsurvey','survey_id'=>$surveyId,'method'=>'back'),'survey'));
                        $objLink->link=$surveyName;
                        $nameLink=$objLink->show();
                    }else{
                        $nameLink=$surveyName;
                    }
                }
            }

            // set up add comment icon
            if($userGroup=='Observers'){
                $this->objComment->set('tableName', 'tbl_survey');
                $this->objComment->set('moduleCode', 'survey');
                $this->objComment->set('sourceId', $surveyId);
                $commentIcon=$this->objComment->addCommentLink();
                $nameLink.='&nbsp;'.$commentIcon;

                // set up view comments icon
                if($commentCount>0){
                    $viewLink=$this->uri(array('action'=>'viewobservercomments','survey_id'=>$surveyId),'survey');
                    $viewCommentsIcon=$this->objComment->addViewLink($viewLink,'');
                    $nameLink.='&nbsp;'.$viewCommentsIcon;
                }
            }

            // set up view comments icon
            if(($userGroup=='Creator' || $userGroup=='Collaborators') && $commentCount>0){
                $viewLink=$this->uri(array('action'=>'viewobservercomments','survey_id'=>$surveyId),'survey');
                $viewCommentsIcon=$this->objComment->addViewLink($viewLink,'');
                $nameLink.='&nbsp;'.$viewCommentsIcon;
            }

            // set up status icon
            $questionCount=count($arrQuestionList);
            $arrPageList=$this->dbPages->listPages($surveyId);
            $arrAllPageQuestionList=$this->dbPageQuestions->listSurveyPages($surveyId);
            if($surveyActive!='1'){
                if($userGroup!='Creator'){
                    $objIcon->title=$inactiveLabel;
                    $objIcon->setIcon('surveyinactive');
                    $statusIcon=$objIcon->show();
                }else{
                    if(empty($questionCount)){
                        $objIcon->title=$noActivateLabel;
                        $objIcon->setIcon('surveyinactive');
                        $statusIcon=$objIcon->show();
                    }elseif(!empty($arrPageList)){
                        $blankPages=FALSE;
                        foreach($arrPageList as $page){
                            $pageId=$page['id'];
                            $arrPageQuestionList=$this->dbPageQuestions->listRows($pageId);
                            if(empty($arrPageQuestionList)){
                                $blankPages=TRUE;
                            }
                        }
                        $unassignedQuestions=FALSE;
                        if(count($arrQuestionList)!=count($arrAllPageQuestionList)){
                            $unassignedQuestions=TRUE;
                        }
                        if($blankPages){
                            $objIcon->title=$blankpagesLabel;
                            $objIcon->setIcon('surveyinactive');
                            $statusIcon=$objIcon->show();
                        }elseif($unassignedQuestions){
                            $objIcon->title=$unassignedLabel;
                            $objIcon->setIcon('surveyinactive');
                            $statusIcon=$objIcon->show();
                        }else{
                            $objIcon->title=$activateLabel;
                            $statusIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'changestatus','survey_id'=>$surveyId,'status'=>'activate')),'surveyinactive');
                        }
                    }else{
                        $objIcon->title=$activateLabel;
                        $statusIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'changestatus','survey_id'=>$surveyId,'status'=>'activate')),'surveyinactive');
                    }
                }
            }else{
                if($userGroup!='Creator' && $this->objUser->isAdmin()){
                    $objIcon->title=$activeLabel;
                    $statusIcon=$objIcon->setIcon('surveyactive');
                    $statusIcon=$objIcon->show();
                }else{
                    //Giving the user a message indicating that the survey is active and been responded to.
					if($responseCounter>='1'){
                        $objIcon->title=$noDeactivateLabel;
                        //$objIcon->setIcon('surveyactive');
                        //$statusIcon=$objIcon->show();
                        $statusIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'changestatus','survey_id'=>$surveyId,'status'=>'deactivate')),'surveyactive');
                    }else{
                        $objIcon->title=$deactivateLabel;
                        $statusIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'changestatus','survey_id'=>$surveyId,'status'=>'deactivate')),'surveyactive');
                    }
                }
            }

            // set up email icon
            if($userGroup=='Creator' && $surveyActive=='1' && $groupEmailSent!='1' && !empty($arrRespondentList)){
                $objIcon->title=$notifyLabel;
                $objIcon->setIcon('notes');
                $emailIcon=$objIcon->show();

                $objPopup = new windowpop();
                $objPopup->set('location',$this->uri(array('action'=>'mailpopup','survey_id'=>$surveyId,'mode'=>'Respondents')));
                $objPopup->set('linktext',$emailIcon);
                $objPopup->set('width','600');
                $objPopup->set('height','350');
                $objPopup->set('left','200');
                $objPopup->set('top','200');
                $objPopup->putJs(); // you only need to do this once per page
                $emailPopup=$objPopup->show();
                $icons.=$emailPopup;
            }

            // set up groups icon
            if($userGroup=='Creator' && $surveyActive!='1' || $this->objUser->isAdmin()){
                $objIcon->title=$editGroupsLabel;
                $groupsIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'surveygroups','survey_id'=>$surveyId)),'groups');
                $icons.=$groupsIcon;
            }

            // set up edit icon
            if($userGroup=='Creator' && $surveyActive!='1' || $this->objUser->isAdmin()){
                $objIcon->title=$editSurveyLabel;
                $editIcon=$objIcon->getEditIcon($this->uri(array('action'=>'editsurvey','survey_id'=>$surveyId)));
                $icons.='&nbsp;'.$editIcon;
            }

            // checks to see if the user has responded
            $arrResponseList=$this->dbResponse->listResponses($surveyId);
            $responded=FALSE;
            if($singleResponses=='1'){
                if(!empty($arrResponseList)){
                    foreach($arrResponseList as $response){
                        if($response['userId']==$this->userId){
                            $responded=TRUE;
                            break;
                        }
                    }
                }
            }

            // set up take survey link
            if($surveyActive=='1' && strtotime($startDate)<=strtotime($currentDate) && strtotime($endDate)>=strtotime($currentDate) && $responseCounter<$responseMaximum && $responded==FALSE){
                //if((!empty($arrRespondentList) && $userGroup=='Respondents') || (empty($arrRespondentList) && $userGroup=='None')){
                    $objLink=new    link($this->uri(array('action'=>'takesurvey','survey_id'=>$surveyId)));
                    $objLink->link=$takeLabel;
                    $takeSurvey=$objLink->show();
               // }else{
               //     $takeSurvey='';
               // }
            }else{
                if((!empty($arrRespondentList) && $userGroup=='Respondents') || (empty($arrRespondentList) && $userGroup=='None')){
                    $takeSurvey=$unavailableLabel;
                }else{
                    $takeSurvey='';
                }
            }
            $icons.=$takeSurvey;

            // checks to see if the user can see the results
            if($responseCounter>0 && ($userGroup!='None' && $userGroup!='Respondents')){
                // set up view results icon
                $objIcon->title=$viewResultsLabel;
                $resultsIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresults','survey_id'=>$surveyId)),'viewresults');
                $icons.='&nbsp;'.$resultsIcon;
            }

            if($responseCounter>0 && ($userGroup!='None' && $userGroup!='Respondents')){
                // set up view respondents icon
                $objIcon->title=$viewResponsesLabel;
                $responsesIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewresponses','survey_id'=>$surveyId)),'viewresponses');
                $icons.='&nbsp;'.$responsesIcon;
            }

            // set up view survey link (Observers and Collaborators and Admin)
            if($userGroup=='Observers' || $userGroup=='Collaborators'){
                $objIcon->title=$viewSurveyLabel;
                $surveyIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'viewsurvey','survey_id'=>$surveyId)),'visible');
                $icons.='&nbsp;'.$surveyIcon;
            }

            // set up copy icon
            if($userGroup=='Creator' && $surveyActive=='1'){
                $objIcon->title=$copyLabel;
                $copyIcon=$objIcon->getLinkedIcon($this->uri(array('action'=>'copysurvey','survey_id'=>$surveyId)),'copysurvey');
                $icons.='&nbsp;'.$copyIcon;
            }

            // set up delete icon
            if($userGroup=='Creator' || $this->isAdmin){
                $deleteArray=array('action'=>'deletesurvey','survey_id'=>$surveyId);
                $deleteIcon=$objIcon->getDeleteIconWithConfirm('', $deleteArray,'survey',$deleteSurveyLabel);
                $icons.='&nbsp;'.$deleteIcon;
            }
            
            // export the survey as csv
            if($userGroup == 'Creator' || $this->isAdmin){
                $objLinkEx= new link($this->uri(array('action'=>'exportcsv', 'survey_id'=>$surveyId)));
                $exportLabel = $this->objLanguage->languageText('mod_survey_exportlabel', 'survey');
                $objLinkEx->link = $exportLabel;
                $exportSurvey = $objLinkEx->show();
                $icons.='&nbsp;'.$exportSurvey;
            }
            
            $objTable->startRow();
            $objTable->addCell($nameLink,'','','',$class,'rowspan="2"');
            $objTable->addCell($owner,'','','center',$class,'');
            $objTable->addCell($startDate,'','','center',$class,'rowspan="2"');
            $objTable->addCell($endDate,'','','center',$class,'rowspan="2"');
            $objTable->addCell($statusIcon,'','','center',$class,'rowspan="2"');
            $objTable->addCell('<nobr>'.$icons.'</nobr>','','','',$class,'rowspan="2"');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($dateCreated,'','','center',$class,'');
            $objTable->endRow();
        }
    }
    echo $objTable->show();

// set up exit link
    $objLink=new link($this->uri(array(),'_default'));
    $objLink->link=$exitLabel;
    echo '<hr /><br />'.$objLink->show();
?>
