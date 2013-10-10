<?php
/**
 * Class to handle context wizard elements.
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    0.001
 * @package    contextwizard
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle contextwizard elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @version    0.001
 * @package    contextwizard
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
class contextwizardops extends object
{
    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            // Load core system objects.
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->PKId();
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objSvars = $this->getObject('serializevars', 'utilities');
//            $this->objGroups = $this->getObject('gamodel', 'groupadmin');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            //$this->objConfirm = $this->newObject('confirm', 'utilities');
            //$this->objConfig = $this->getObject('altconfig', 'config');
            
            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objFieldset = $this->loadClass('fieldset', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objLayer = $this->loadClass('layer', 'htmlelements');
            //$this->objRadio = $this->loadClass('radio', 'htmlelements');
            
            $this->objDBgrades = $this->getObject('dbgrades', 'grades');
            $this->objDBbridging = $this->getObject('dbbridging', 'grades');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $error = $this->objLanguage->languageText('word_error', 'system', 'WORD: word_error, not found');
        
        $this->objIcon->title = $error;
        $this->objIcon->alt = $error;
        $this->objIcon->setIcon('exclamation', 'png');
        $errorIcon = $this->objIcon->show();
        
        $string = '<span style="color: red">' . $errorIcon . '&nbsp;<b>' . $errorText . '</b></span>';
        return $string;
    }
    
    /**
     *
     * Method to generate the html for the user display template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showMain()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('contextwizard.js', 'contextwizard'));

        $myProfileLabel = $this->objLanguage->languageText('phrase_myprofile', 'system', 'ERROR: phrase_myprofile');
        $nextLabel = $this->objLanguage->languageText('word_next', 'system', 'ERROR: word_next');
        $backLabel = ucfirst(strtolower($this->objLanguage->languageText('word_back', 'system', 'ERROR: word_back')));
        $enterLabel = $this->objLanguage->languageText('word_enter', 'system', 'ERROR: word_enter');
        $splashLabel = $this->objLanguage->languageText('mod_contextwizard_welcome', 'contextwizard', 'ERROR: mod_contextwizard_welcome');
        $firstLabel = $this->objLanguage->code2Txt('mod_contextwizard_first', 'contextwizard', NULL, 'ERROR: mod_contextwizard_first');
        $selectLevelLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectlevel', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectlevel');
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectsubject', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectsubject');
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectstrand', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectstrand');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectcontext', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectcontext');
        $levelLabel = $this->objLanguage->code2Txt('mod_contextwizard_level', 'contextwizard', NULL, 'ERROR: mod_contextwizard_level');
        $subjectLabel = $this->objLanguage->code2Txt('mod_contextwizard_subject', 'contextwizard', NULL, 'ERROR: mod_contextwizard_subject');
        $strandLabel = $this->objLanguage->code2Txt('mod_contextwizard_strand', 'contextwizard', NULL, 'ERROR: mod_contextwizard_strand');
        $contextLabel = $this->objLanguage->code2Txt('mod_contextwizard_context', 'contextwizard', NULL, 'ERROR: mod_contextwizard_context');
        $subjectTitleLabel = $this->objLanguage->code2Txt('mod_contextwizard_titlesubject', 'contextwizard', NULL, 'ERROR: mod_contextwizard_titlesubject');
        $strandTitleLabel = $this->objLanguage->code2Txt('mod_contextwizard_titlestrand', 'contextwizard', NULL, 'ERROR: mod_contextwizard_titlestrand');
        $contextTitleLabel = $this->objLanguage->code2Txt('mod_contextwizard_titlecontext', 'contextwizard', NULL, 'ERROR: mod_contextwizard_titlecontext');
        $subjectErrorLabel = $this->objLanguage->code2Txt('mod_contextwizard_subejcterror', 'contextwizard', NULL, 'ERROR: mod_contextwizard_subejcterror');
        $strandErrorLabel = $this->objLanguage->code2Txt('mod_contextwizard_stranderror', 'contextwizard', NULL, 'ERROR: mod_contextwizard_stranderror');
        $levelErrorLabel = $this->objLanguage->code2Txt('mod_contextwizard_levelerror', 'contextwizard', NULL, 'ERROR: mod_contextwizard_levelerror');
        $contextErrorLabel = $this->objLanguage->code2Txt('mod_contextwizard_contexterror', 'contextwizard', NULL, 'ERROR: mod_contextwizard_contexterror');
        
        $arrayVars = array();
        $arrayVars['no_level'] = $levelErrorLabel;
        $this->objSvars->varsToJs($arrayVars);
       
        // pass error to javascript.
        $arrayVars = array();
        $arrayVars['no_subject'] = $subjectErrorLabel;
        $this->objSvars->varsToJs($arrayVars);

        // pass error to javascript.
        $arrayVars = array();
        $arrayVars['no_strand'] = $strandErrorLabel;
        $this->objSvars->varsToJs($arrayVars);

        // pass error to javascript.
        $arrayVars = array();
        $arrayVars['no_context'] = $contextErrorLabel;
        $this->objSvars->varsToJs($arrayVars);

        // grade dialog
        $array = array('step' => $levelLabel);
        $levelDialogLabel = $this->objLanguage->code2Txt('mod_contextwizard_title', 'contextwizard', $array, 'ERROR: mod_contextwizard_title');

        $userGroups = array();
        $userGroups = $this->objGroups->getUserGroups($this->objUser->userId());
        if (!empty($userGroups))
        {
            foreach ($userGroups as $group)
            $userGroupArray[$group['group_id']] = $group['group_define_name'];
        }

        $gradeGroups = array();
        $gradeString = $this->objDBgrades->getGrades();
        if ($gradeString)
        {
            $gradeGroups = $this->objGroups->getGroups("WHERE group_define_name IN ($gradeString)");
        }
        
        $gradeGroupArray = array();
        if (!empty($gradeGroups))
        {
            foreach ($gradeGroups as $group)
            {
                $gradeGroupArray[$group['group_id']] = $group['group_define_name'];
            }
        }        
        $grade = array_intersect($gradeGroupArray, $userGroupArray);

        if (!empty($grade))
        {
            $objInput = new textinput('group_id', key($grade), 'hidden', '50');
            $idInput = $objInput->show();        
        }
        else
        {
            $objDrop = new dropdown('group_id');
            $objDrop->addOption('', $selectLevelLabel . '...');
            if (!empty($gradeGroups))
            {
                $objDrop->addFromDB($gradeGroups, 'group_define_name', 'group_id');
            }
            $idInput = $objDrop->show();
        }    
        
        $objButton = new button('next', $nextLabel);
        $objButton->setId('level_next');
        $nextButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';

        $objTable->startRow();
        $objTable->addCell($splashLabel, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        if (!empty($grade))
        {
            $array = array('level_name' => current($grade));
            $regularLabel = $this->objLanguage->code2Txt('mod_contextwizard_regular', 'contextwizard', $array, 'ERROR: mod_contextwizard_regular');

            $objLink = new link($this->uri(array(), 'userdetails'));
            $objLink->link = $myProfileLabel;
            $changeLink = $objLink->show();
            $array = array('here' => $changeLink);
            $changeLabel = $this->objLanguage->code2Txt('mod_contextwizard_change', 'contextwizard', $array, 'ERROR: mod_contextwizard_change');
           
            $objTable->startRow();
            $objTable->addCell($regularLabel, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($idInput . $changeLabel, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
        }
        else
        {
            $objTable->startRow();
            $objTable->addCell($firstLabel, '', '', '', '', 'colspan="2"', '');
            $objTable->endRow();
            $objTable->startRow();
            $objTable->addCell($selectLevelLabel, '', '', '', '', '', '');
            $objTable->addCell($idInput, '', '', '', '', '', '');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell($nextButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $levelTable = $objTable->show();
        
        $objForm = new form('wizard_grade', $this->uri(array(
            'action' => 'next'
        ), 'contextwizard'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($levelTable);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->id = 'form_layer';
        $objLayer->str =  $form;
        $formLayer = $objLayer->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_wizard_level');
        $this->objDialog->setTitle(ucwords($levelDialogLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent($formLayer);
        $this->objDialog->setHeight(250);
        $this->objDialog->setWidth(650);
        $this->objDialog->setResizable(FALSE);
        $this->objDialog->setAutoOpen(TRUE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $this->objDialog->unsetButtons();
        $dialog = $this->objDialog->show();
        
        // subject dialog
        $array = array('step' => $subjectLabel);
        $subjectDialogLabel = $this->objLanguage->code2Txt('mod_contextwizard_title', 'contextwizard', $array, 'ERROR: mod_contextwizard_title');

        $objLayer = new layer();
        $objLayer->id = 'subject_layer';
        $subjectLayer = $objLayer->show();

        $objButton = new button('next', $nextLabel);
        $objButton->setId('subject_next');
        $nextButton = $objButton->show();
        
        $objButton = new button('back', $backLabel);
        $objButton->setId('subject_back');
        $backButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($subjectTitleLabel, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($selectSubjectLabel, '', '', '', '', '', '');
        $objTable->addCell($subjectLayer, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($backButton . '&nbsp;' . $nextButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $subjectTable = $objTable->show();
        
        $objForm = new form('wizard_subject', $this->uri(array(
            'action' => 'next'
        ), 'contextwizard'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($subjectTable);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->id = 'form_layer';
        $objLayer->str =  $form;
        $formLayer = $objLayer->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_wizard_subject');
        $this->objDialog->setTitle(ucwords($subjectDialogLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent($formLayer);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $this->objDialog->setHeight(250);
        $this->objDialog->setWidth(650);
        $this->objDialog->setResizable(FALSE);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->unsetButtons();
        $dialog .= $this->objDialog->show();
        
        // strand dialog
        $array = array('step' => $strandLabel);
        $strandDialogLabel = $this->objLanguage->code2Txt('mod_contextwizard_title', 'contextwizard', $array, 'ERROR: mod_contextwizard_title');

        $objLayer = new layer();
        $objLayer->id = 'strand_layer';
        $strandLayer = $objLayer->show();

        $objButton = new button('next', $nextLabel);
        $objButton->setId('strand_next');
        $nextButton = $objButton->show();
        
        $objButton = new button('back', $backLabel);
        $objButton->setId('strand_back');
        $backButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($strandTitleLabel, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($selectStrandLabel, '', '', '', '', '', '');
        $objTable->addCell($strandLayer, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($backButton . '&nbsp;' . $nextButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $strandTable = $objTable->show();
        
        $objForm = new form('wizard_strand', $this->uri(array(
            'action' => 'next'
        ), 'contextwizard'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($strandTable);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->id = 'form_layer';
        $objLayer->str =  $form;
        $formLayer = $objLayer->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_wizard_strand');
        $this->objDialog->setTitle(ucwords($strandDialogLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent($formLayer);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $this->objDialog->setHeight(250);
        $this->objDialog->setWidth(650);
        $this->objDialog->setResizable(FALSE);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->unsetButtons();
        $dialog .= $this->objDialog->show();

        // course dialog
        $array = array('step' => $contextLabel);
        $contextDialogLabel = $this->objLanguage->code2Txt('mod_contextwizard_title', 'contextwizard', $array, 'ERROR: mod_contextwizard_title');

        $objLayer = new layer();
        $objLayer->id = 'context_layer';
        $contextLayer = $objLayer->show();

        $objButton = new button('enter', $enterLabel);
        $objButton->setId('context_enter');
        $nextButton = $objButton->show();
        
        $objButton = new button('back', $backLabel);
        $objButton->setId('context_back');
        $backButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($contextTitleLabel, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($selectContextLabel, '', '', '', '', '', '');
        $objTable->addCell($contextLayer, '', '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($backButton . '&nbsp;' . $nextButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $contextTable = $objTable->show();
        
        $objForm = new form('wizard_context', $this->uri(array(
            'action' => 'joincontext'
        ), 'context'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($contextTable);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->id = 'form_layer';
        $objLayer->str =  $form;
        $formLayer = $objLayer->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_wizard_context');
        $this->objDialog->setTitle(ucwords($contextDialogLabel));
        $this->objDialog->setCloseOnEscape(FALSE);
        $this->objDialog->setContent($formLayer);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->setOpen("jQuery('.ui-dialog-titlebar-close').hide();");
        $this->objDialog->setHeight(250);
        $this->objDialog->setWidth(650);
        $this->objDialog->setResizable(FALSE);
        $this->objDialog->setAutoOpen(FALSE);
        $this->objDialog->unsetButtons();
        $dialog .= $this->objDialog->show();

        return $splashLabel . $dialog;
        
    }
    
    /**
     *
     * Method to process an ajax request to add the user to a group
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxAddToGroup()
    {
        $groupId =$this->getParam('group_id'); 
        $user = $this->objUserAdmin->getUserDetails($this->userId);
        $this->objGroups->addGroupUser($groupId, $user['puid']);
        die();
    }

    /**
     *
     * Method to process an ajax request to get the subjects for a grade
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxGetSubjects()
    {
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectsubject', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectsubject');

        $groupId = $this->getParam('group_id');
        $groupName = $this->objGroups->getName($groupId);
        $grade = $this->objDBgrades->getGradeByName($groupName);
        $subjects = $this->objDBbridging->getLinkedItems('grade_id', 'subject_id', $grade['id']);
        
        $objDrop = new dropdown('subject_id');
        $objDrop->addOption('', $selectSubjectLabel . '...');
        $objDrop->addFromDB($subjects, 'name', 'subject_id');
        $subjectDrop = $objDrop->show();
                       
        echo $subjectDrop;
        die();
    }
    
    /**
     *
     * Method to process an ajax request to get the strands for a grade
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxGetStrands()
    {
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectstrand', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectstrand');

        $subjectId =$this->getParam('subject_id');
        $strands = $this->objDBbridging->getLinkedItems('subject_id', 'strand_id', $subjectId);
        
        $objDrop = new dropdown('strand_id');
        $objDrop->addOption('', $selectStrandLabel . '...');
        $objDrop->addFromDB($strands, 'name', 'strand_id');
        $strandDrop = $objDrop->show();
                       
        echo $strandDrop;
        die();
    }
    
    /**
     *
     * Method to process an ajax request to get the contexts for a strand
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxGetContexts()
    {
        $selectContextLabel = $this->objLanguage->code2Txt('mod_contextwizard_selectcontext', 'contextwizard', NULL, 'ERROR: mod_contextwizard_selectcontext');

        $strandId =$this->getParam('strand_id');
        $contexts = $this->objDBbridging->getLinkedItems('strand_id', 'context_id', $strandId);

        $objDrop = new dropdown('contextcode');
        $objDrop->addOption('', $selectContextLabel . '...');
        $objDrop->addFromDB($contexts, 'title', 'contextcode');
        $contextDrop = $objDrop->show();
                       
        echo $contextDrop;
        die();
    }    
}
?>