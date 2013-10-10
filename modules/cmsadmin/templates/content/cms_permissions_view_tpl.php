<?php
/**
* Template for viewing section permission details in cmsadmin
*
* @author Warren Windvogel, Charl Mert
* @package cmsadmin
*/

if (!isset($layoutData)) {
	$layoutData = array();
	$layoutData['name'] = '';
}

if (!isset($tblDetails)) {
	$tblDetails = '';
}

    $parentId = $this->getParam('parent');


    //Set layout template
    $this->setLayoutTemplate('cms_layout_tpl.php');

    //Load the link class
    $this->loadClass('link', 'htmlelements');

    //echo "<h1>Permissions Manager</h1>";

    //Create htmlheading for page title
    $objH = $this->newObject('htmlheading', 'htmlelements');
    $objH->type = '2';
    //Create instance of geticon object
    $objIcon = & $this->newObject('geticon', 'htmlelements');
    //Setup Header Navigation objects

    $objLayer =$this->newObject('layer','htmlelements');
    $headIcon = $this->newObject('geticon', 'htmlelements');
    $headIcon->setIcon('permissions_small','png', 'icons/cms');
    $this->loadClass('htmltable', 'htmlelements');

    //Get blocks icon
    $objIcon->setIcon('modules/blocks');
    //$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin');
    $objIcon->title = "Permissions Manager";
    $blockIcon = $objIcon->show();

    //Check if blocks module is registered
    $this->objModule = &$this->newObject('modules', 'modulecatalogue');
    $isRegistered = $this->objModule->checkIfRegistered('blocks');

    //Get section data

    if (isset($section)) {
        $sectionId = $section['id'];
        $title = html_entity_decode($section['title']);
        $menuText = $section['menutext'];
        $layout = $section['layout'];
        $published = $section['published'];
        $description = $section['description'];
    } else {
        $sectionId = '';
        $title = '';
        $menuText = '';
        $layout = '';
        $published = '';
        $description = '';
    }

    //Get layout icon
/*
$layoutData = $this->_objLayouts->getLayout($layout);
$imageName = $layoutData['imagename'];
$imgPath = $this->getResourceUri($imageName,'cmsadmin');
$img = "<img src=\"{$imgPath}\" alt=\"'$imageName'\"/>";
*/

    $other = '<b>'.$this->objLanguage->languageText('mod_cmsadmin_treemenuname', 'cmsadmin').':'.'</b>'.'&nbsp;'.$menuText.'<br/>';

    $other .= '<b>'.$this->objLanguage->languageText('mod_cmsadmin_visibleontreemenu', 'cmsadmin').':'.'</b>&nbsp;';

    if ($this->_objUtils->sectionIsVisibleOnMenu($sectionId)) {
        $other .= $this->objLanguage->languageText('mod_cmsadmin_sectionwillbevisible', 'cmsadmin');
    } else {
        $other .= $this->objLanguage->languageText('mod_cmsadmin_sectionwillnotbevisible', 'cmsadmin');
    }

    $other .= '<br/>';

    $other .= '<br/>'.'&nbsp;'.'<br/>';

    $other .= '<b>'.$this->objLanguage->languageText('mod_cmsadmin_pagesorderedby', 'cmsadmin').':'.'</b>&nbsp;'.$this->_objSections->getPageOrderType($section['ordertype']);


    //Create table contain layout, visible, etc details
/*
$objDetailsTable = new htmltable();
$objDetailsTable->cellspacing = '2';
$objDetailsTable->cellpadding = '2';
$objDetailsTable->startRow();
//$objDetailsTable->addCell($img, '39%', 'top', 'center', '');
//$objDetailsTable->addCell($other, '60%', 'top', 'left', '');
$objDetailsTable->endRow();

$tblDetails = $objDetailsTable->show();
*/

    //Create table for subsections
    $objSubSecTable =  new htmltable();
    $objSubSecTable->cellpadding = '2';
    $objSubSecTable->cellspacing = '2';
    $objSubSecTable->width = '99%';

    //Create table header row
    $objSubSecTable->startHeaderRow();
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_menuname', 'cmsadmin'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_nameofsection', 'cmsadmin'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('word_pages'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_owner', 'cmsadmin'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_user', 'cmsadmin'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_group', 'cmsadmin'));
    $objSubSecTable->addHeaderCell($this->objLanguage->languageText('word_options'));
    $objSubSecTable->endHeaderRow();

    $toggle = 1;

    if (isset($subSections)) {
        $i = 0;
        foreach($subSections as $subsec) {
            //Set odd even row count variable

            $class = (($i++ % 2) == 0) ? 'odd' : 'even';
            //Get sub sec data
            $subSecId = $subsec['id'];
            $subSecTitle = $subsec['title'];
            $subSecMenuText = $subsec['menutext'];
            $subSecPublished = $subsec['published'];
            $subSecLayout = $this->_objLayouts->getLayout($subsec['layout']);
            $subSecLayoutName = $subSecLayout['name'];

            //publish, visible
            if($subsec['public_access']){
                $url = $this->uri(array('action' => 'setpublicaccess', 'id' => $subsec['id'], 'mode' => 'unpublic', 'subview' => '1', 'parent' => $sectionId));
                $icon = $this->_objUtils->getPublicAccessIcon(TRUE);
            }else{
                $url = $this->uri(array('action' => 'setpublicaccess', 'id' => $subsec['id'], 'mode' => 'public', 'subview' => '1', 'parent' => $sectionId));
                $icon = $this->_objUtils->getPublicAccessIcon(FALSE);
            }
            $objLink = new link($url);
            $objLink->link = $icon;

			//Applying Security
            if ($this->_objSecurity->canUserWriteSection($subSecId)){
            	$publicAccessLink = $objLink->show();
            } else {
            	$publicAccessLink = '';
            }
           
            //Create delete icon
            $delArray = array('action' => 'deletesection', 'confirm' => 'yes', 'id' => $subSecId);
            $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
            $delIcon = $objIcon->getDeleteIconWithConfirm($subSecId, $delArray, 'cmsadmin', $deletephrase);
            //Create edit icon
            //edit icon
            if ($this->_objSecurity->canUserWriteSection($subSecId)){
                $editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addpermissions', 'id' => $subSecId, 'parent' => $sectionId, 'subview' => '1')));
            } else {
                $editIcon = '';
            }

            //Make title link to view section
            $objLink = new link($this->uri(array('action' => 'view_permissions_section', 'id' => $subSecId, 'parent' => $sectionId)));
            $objLink->link = $subSecMenuText;
            $viewSubSecLink = $objLink->show();

                
            //Sections Permissions Data
                
            //print_r($subsec);
                
            $users = $this->_objUtils->getSectionUserNames($subsec['id']);
            $groups = $this->_objUtils->getSectionGroupNames($subsec['id']);		

            //Will iterate and display one user per line with the access type.
            //Updating Checks for the assigned users
            $usersList = $this->_objSecurity->getAssignedSectionUsers($subsec['id']);
            $usersCount = count($usersList);
                        
            //Preparing a list of GROUP_ID's
            $groupsList = $this->_objSecurity->getAssignedSectionGroups($subsec['id']);
            $groupsCount = count($groupsList);
                        
            //Reporting on Users and Access
            //$userReport = '<select style="padding:3px">';
            $userReport = '';
            for ($x = 0; $x < $usersCount; $x++){
                $memberName = $usersList[$x]['username'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];

                if ($memberReadAccess != '1' && $memberWriteAccess != '1'){
                    //$userReport .= "<option> $memberName - (NONE)</option>";
                    $userReport .= "$memberName - (NONE)<br/>";
                } else {
                    //$userReport .= "<option> $memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")</option>";
                    $userReport .= "$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")<br/>";
                }
            }       //End Loop		
            //$userReport .= "</select>";
                        
            //Reporting on Groups and Access
            //$groupReport = '<select style="padding:3px">';
            $groupReport = '';
            for ($x = 0; $x < $groupsCount; $x++){
                $memberName = $groupsList[$x]['name'];
                $memberReadAccess = $groupsList[$x]['read_access'];
                $memberWriteAccess = $groupsList[$x]['write_access'];

                if ($memberReadAccess != '1' && $memberWriteAccess != '1'){
                    //$groupReport .= "<option>$memberName - (NONE)</option>";
                    $groupReport .= "$memberName - (NONE)<br/>";
                } else {
                    //$groupReport .= "<option>$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")</option>";
                    $groupReport .= "$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")<br/>";
                }
            }	//End Loop
            //$groupReport .= '</select>';
            

            //Will iterate and display one group per line with the access type.
                
            //Add sub sec data to table
                /*
        $objSubSecTable->startRow();
        $objSubSecTable->addCell($viewSubSecLink, '', '', '', $class);
        $objSubSecTable->addCell($subSecTitle, '', '', '', $class);
        $objSubSecTable->addCell($this->_objContent->getNumberOfPagesInSection($subSecId), '', '', '', $class);
        $objSubSecTable->addCell($this->_objUser->userName($subsec['userid']), '', '', '', $class);
        $objSubSecTable->addCell($userReport, '', '', '', $class);
        $objSubSecTable->addCell($groupReport, '', '', '', $class);
        $objSubSecTable->addCell('<nobr>'.$editIcon.'</nobr>', '', '', '', $class);
        $objSubSecTable->endRow();
                */
                
            $tableRowSub[0] = $viewSubSecLink;
            $tableRowSub[1] = $subSecTitle;
            $tableRowSub[2] = $this->_objContent->getNumberOfPagesInSection($subSecId);
            $tableRowSub[3] = $this->_objUser->userName($subsec['userid']);
            $tableRowSub[4] = $userReport;
            $tableRowSub[5] = $groupReport;
            $tableRowSub[6] = '<nobr>'.$editIcon.$publicAccessLink.'</nobr>';
                
            $toggle *= -1;
            $oddOrEven = (($toggle > 0) ? 'odd' : 'even');
                
            $objSubSecTable->addRow($tableRowSub, $oddOrEven);
        }
    }
    $tblSubSec = $objSubSecTable->show();

    //Create table for pages
    $objPagesTable = new htmltable();
    $objPagesTable->cellpadding = '2';
    $objPagesTable->cellspacing = '2';
    $objPagesTable->width = '99%';

    //Create table header row
    $objPagesTable->startHeaderRow();
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_pagetitle', 'cmsadmin'));
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_articledate', 'cmsadmin'));
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_owner', 'cmsadmin'));
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_user', 'cmsadmin'));
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('mod_cmsadmin_word_group', 'cmsadmin'));
    $objPagesTable->addHeaderCell($this->objLanguage->languageText('word_options'));
    $objPagesTable->endHeaderRow();

    $toggle = 1;

    if (!empty($pages)) {
        $i = 0;
        foreach($pages as $page) {
            //Set odd even row count variable

            $class = (($i++ % 2) == 0) ? 'odd' : 'even';

            //Get page data
            $pageId = $page['page_id'];
            $ordering = $page['co_order'];
            $pageTitle = html_entity_decode($page['title']);
            $articleDate = $page['modified'];
            $pagePublished = $page['published'];
            $publicAccess = $page['public_access'];
            
            //publish, visible
            if($publicAccess){
                $url = $this->uri(array('action' => 'setpublicaccess', 'cid' => $pageId, 'mode' => 'unpublic', 'subview' => '1', 'parent' => $sectionId));
                $icon = $this->_objUtils->getPublicAccessIcon(TRUE);
            }else{
                $url = $this->uri(array('action' => 'setpublicaccess', 'cid' => $pageId, 'mode' => 'public', 'subview' => '1', 'parent' => $sectionId));
                $icon = $this->_objUtils->getPublicAccessIcon(FALSE);
            }
            $objLink = new link($url);
            $objLink->link = $icon;
            if ($this->_objSecurity->canUserWriteContent($pageId)){
            	$publicAccessLink = $objLink->show();
            } else {
            	$publicAccessLink = '';
            }

            //Create delete icon
            $delArray = array('action' => 'trashcontent', 'confirm' => 'yes', 'id' => $pageId, 'sectionid' => $sectionId);
            $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelpage', 'cmsadmin');
            $delIcon = $objIcon->getDeleteIconWithConfirm($pageId, $delArray, 'cmsadmin', $deletephrase);
            //Create edit icon
            //edit icon
            if ($this->_objSecurity->canUserWriteContent($pageId)){
                $editIcon = $objIcon->getEditIcon($this->uri(array('action' => 'add_content_permissions', 'id' => $pageId, 'parent' => $sectionId)));
            } else {
                $editIcon = '';
            }

            //Make title link to view section
            $objLink = new link($this->uri(array('action' => 'showcontent', 'id' => $pageId, 'fromadmin' => TRUE, 'sectionid' => $sectionId), 'cms'));
            $objLink->link = $pageTitle;
            $viewPageLink = $objLink->show();

            //Icon for toggling front page status
            if(isset($page['front_id']) && !empty($page['front_id'])) {
                $objIcon->setIcon('greentick');
                $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_confirmremovefromfp', 'cmsadmin');
                $url = array('action' => 'changefpstatus', 'id' => $page['front_id'], 'sectionid' => $sectionId, 'mode' => 'remove');
            } else {
                $objIcon->setIcon('redcross');
                $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addpagetofp', 'cmsadmin');
                $url = array('action' => 'changefpstatus', 'id' => $pageId, 'sectionid' => $sectionId, 'mode' => 'add');
            }
            $frontPageLink = new link($this->uri($url, 'cmsadmin'));
            $frontPageLink->link = $objIcon->show();

            // set up link to view contact details in a popup window
            $objBlocksLink = new link('#');
            $objBlocksLink->link = $blockIcon;
            $objBlocksLink->extra = "onclick = \"javascript:window.open('" . $this->uri(array('action' => 'addblock', 'sectionId' => $sectionId, 'pageid' => $pageId, 'blockcat' => 'content')) . "', 'branch', 'width=500, height=350, top=50, left=50, scrollbars')\"";

                
            //Content Permissions Data
                
            //print_r($subsec);
                
            $users = $this->_objUtils->getContentUserNames($pageId);
            $groups = $this->_objUtils->getContentGroupNames($pageId);		
            //Will iterate and display one user per line with the access type.
            //Updating Checks for the assigned users
            $usersList = $this->_objSecurity->getAssignedContentUsers($pageId);
            $usersCount = count($usersList);

            //Preparing a list of GROUP_ID's
            $groupsList = $this->_objSecurity->getAssignedContentGroups($pageId);
            $groupsCount = count($groupsList);
                        
            //Reporting on Users and Access
            //$userReport = '<select style="padding:3px">';
            $userReport = '';
            for ($x = 0; $x < $usersCount; $x++){
                $memberName = $usersList[$x]['username'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];

                if ($memberReadAccess != '1' && $memberWriteAccess != '1'){
                    //$userReport .= "<option> $memberName - (NONE)</option>";
                    $userReport .= "$memberName - (NONE)<br/>";
                } else {
                    //$userReport .= "<option> $memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")</option>";
                    $userReport .= "$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")<br/>";
                }
            }       //End Loop		
            //$userReport .= "</select>";
                        
            //Reporting on Groups and Access
            //$groupReport = '<select style="padding:3px">';
            $groupReport = '';
            for ($x = 0; $x < $groupsCount; $x++){
                $memberName = $groupsList[$x]['name'];
                $memberReadAccess = $groupsList[$x]['read_access'];
                $memberWriteAccess = $groupsList[$x]['write_access'];

                if ($memberReadAccess != '1' && $memberWriteAccess != '1'){
                    //$groupReport .= "<option>$memberName - (NONE)</option>";
                    $groupReport .= "$memberName - (NONE)<br/>";
                } else {
                    //$groupReport .= "<option>$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")</option>";
                    $groupReport .= "$memberName - (".(($memberReadAccess == '1') ? 'R' : '').' '.(($memberWriteAccess == '1') ? 'W' : '').")<br/>";
                }
            }	//End Loop
            //$groupReport .= '</select>';		
                
            //Add sub sec data to table
                
            $tableRow[0] = $pageTitle;
            $tableRow[1] = $articleDate;
            $tableRow[2] = $this->_objUser->userName($page['created_by']);
            $tableRow[3] = $userReport;
            $tableRow[4] = $groupReport;
            $tableRow[5] = '<nobr>'.$editIcon.$publicAccessLink.'</nobr>';
                
            $toggle *= -1;
            $oddOrEven = (($toggle > 0) ? 'odd' : 'even');
                
            $objPagesTable->addRow($tableRow, $oddOrEven);
                

        }
    }
    $tblPages = $objPagesTable->show();

    //Create add sub section icon
    $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addsubsection','cmsadmin');
    //$addSubSecIcon = $objIcon->getLinkedIcon($this->uri(array('action' => 'addsection', 'parentid' => $sectionId)), 'create_folder');
    $addSubSecIcon = '';



    //Create add page icon
    $objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_addpage','cmsadmin');
    //$addPageIcon = $objIcon->getLinkedIcon($this->uri(array('action' => 'addcontent', 'parent' => $sectionId)), 'create_page');
    $addPageIcon = '';

    //Create edit section icon
    $editSectionIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addpermissions', 'id' => $sectionId)));

    //edit icon
    if ($this->_objSecurity->canUserWriteContent($pageId)){
        $editSectionIcon = $objIcon->getEditIcon($this->uri(array('action' => 'addpermissions', 'id' => $sectionId, 'parent' => $sectionId, 'subview' => '1')));
    } else {
        $editSectionIcon = '';
    }


    //Create delete section icon
    $delArray = array('action' => 'deletesection', 'confirm' => 'yes', 'id' => $sectionId);
    $deletephrase = $this->objLanguage->languageText('mod_cmsadmin_confirmdelsection', 'cmsadmin');
    $delIcon = $objIcon->getDeleteIconWithConfirm($sectionId, $delArray, 'cmsadmin', $deletephrase);

    //Create add section link
    $objNewSectionLink = new link($this->uri(array('action' => 'addsection', 'parentid' => $sectionId)));
    $objNewSectionLink->link = $this->objLanguage->languageText('mod_cmsadmin_addnewsection', 'cmsadmin');

    //Create add page link
    $objNewPageLink = new link($this->uri(array('action' => 'addcontent', 'parent' => $sectionId)));
    $objNewPageLink->link = $this->objLanguage->languageText('phrase_addanewpage');

    //Add content to the output layer
    $middleColumnContent = "";
    if($isRegistered){
        if($layoutData['name'] == 'summaries' || $layoutData['name'] == 'list'){
            //Create add block link
            $objAddSectionBlockLink = new link('javascript:void(0)');
            $objAddSectionBlockLink->link = $blockIcon;
            $objAddSectionBlockLink->extra = "onclick = \"javascript:window.open('" . $this->uri(array('action' => 'addblock', 'sectionid' => $sectionId, 'blockcat' => 'section')) . "', 'branch', 'width=500, height=350, top=50, left=50, scrollbars')\"";
            //Set heading
            $objH->str = '<h1>'.$headIcon->show().$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin').'</h1>&nbsp;<h2>'.$this->objLanguage->languageText('word_section').':'.'&nbsp;'.$title.'&nbsp;'.$editSectionIcon.$objAddSectionBlockLink->show().'</h2>';
        } else {
            $objH->str = '<h1>'.$headIcon->show().$this->objLanguage->languageText('mod_cmsadmin_permissionsmanager', 'cmsadmin').'</h1>&nbsp;<h2>'.$this->objLanguage->languageText('word_section').':'.'&nbsp;'.$title.$editSectionIcon.'</h2>';
        }
    } else {
        //Set heading
        $objH->str = $this->objLanguage->languageText('word_section').':'.'&nbsp;'.$title.'&nbsp;'.$editSectionIcon.'&nbsp;'.$delIcon;
    }

    $objLayer->str = $objH->show();
    //$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
    $objLayer->id = 'cms_header_left';
    $header = $objLayer->show();

    $objLayer->str = $topNav;
    //$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
    $objLayer->id = 'cms_header_right';
    $header .= $objLayer->show();

    $objLayer->str = '';
    //$objLayer->border = '; clear:both; margin:0px; padding:0px;';
    $objLayer->id = 'cms_header_clear';
    $objLayer->cssClass = 'clearboth';
    $headShow = $objLayer->show();

    $objLayer->str = '&nbsp;';
    $objLayer->id = 'cmsvspacer';
    $vspacer = $objLayer->show();

    $middleColumnContent .= $header.$headShow.$vspacer;//$tbl->show());

    //Display layout info
    $middleColumnContent .= $tblDetails;

    //Sub sections table
    $objH->str = $this->objLanguage->languageText('mod_cmsadmin_subsections', 'cmsadmin').'&nbsp;'.'('.$this->_objSections->getNumSubSections($sectionId).')'.'&nbsp;'.$addSubSecIcon;
    $middleColumnContent .= '&nbsp;'.'<br/>';
    $middleColumnContent .= $objH->show();
    $middleColumnContent .= $tblSubSec;

/*
if (empty($subSections)) {
    //$middleColumnContent .= '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_cmsadmin_nosubsectionsfound', 'cmsadmin').'</div>';
}
*/

    //Pages table
    $objH->str = $this->objLanguage->languageText('word_pages').'&nbsp;'.'('.$this->_objContent->getNumberOfPagesInSection($sectionId).')'.'&nbsp;'.$addPageIcon;
    $middleColumnContent .= '&nbsp;'.'<br/>';
    $middleColumnContent .= $objH->show();
    $middleColumnContent .= $tblPages;

    if (empty($pages)) {
        $middleColumnContent .= '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_cmsadmin_nopagesfoundinthissection', 'cmsadmin').'</div>';
    }
    $middleColumnContent .= '&nbsp;'.'<br/>';
    //$middleColumnContent .= $objNewSectionLink->show().'&nbsp;'.'/'.'&nbsp;'.$objNewPageLink->show();

    echo $middleColumnContent;

?>
