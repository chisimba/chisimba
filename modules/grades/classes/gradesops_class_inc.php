<?php
/**
 *
 * The grades operations class
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
 * @package    grades
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
 * 
 * The grades operations class.
 *
 *
 * @category  Chisimba
 * @author    Kevin Cyster kcyster@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class gradesops extends object
{
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        try {
            // Load core system objects.
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objSvars = $this->getObject('serializevars', 'utilities');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objConfirm = $this->newObject('confirm', 'utilities');

            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objFieldset = $this->loadClass('fieldset', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objLayer = $this->loadClass('layer', 'htmlelements');
            $this->objRadio = $this->loadClass('radio', 'htmlelements');
            $this->objText = $this->loadClass('textarea', 'htmlelements');
            $this->objTab = $this->newObject('tabber', 'htmlelements');

            $this->objDBgrades = $this->getObject('dbgrades', 'grades');
            $this->objDBsubjects = $this->getObject('dbsubjects', 'grades');
            $this->objDBstrands = $this->getObject('dbstrands', 'grades');
            $this->objDBclasses = $this->getObject('dbclasses', 'grades');
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
     * Method to show the content on the default main block.
     *
     * @return string $string The display string
     */
    public function showMain() 
    {
        $descriptionLabel = $this->objLanguage->code2Txt('mod_grades_description', 'grades', NULL, 'ERROR: mod_grades_description');

        $objFieldset = new fieldset();
        $objFieldset->contents = $descriptionLabel;
        $mainFieldset = $objFieldset->show();
        
        $string = $mainFieldset;
        
        return $string;
    }

    /**
     *
     * Method to show the content of the left manage block
     * 
     * @return string $string The display string
     */
    public function showManage()
    {
        $gradesLabel = $this->objLanguage->code2Txt('mod_grades_managegrades', 'grades', NULL, 'ERROR: mod_grades_managegrades');
        $subjectsLabel = $this->objLanguage->code2Txt('mod_grades_managesubjects', 'grades', NULL, 'ERROR: mod_grades_managesubjects');
        $strandsLabel = $this->objLanguage->code2Txt('mod_grades_managestrands', 'grades', NULL, 'ERROR: mod_grades_managestrands');
        $classesLabel = $this->objLanguage->code2Txt('mod_grades_manageclasses', 'grades', NULL, 'ERROR: mod_grades_manageclasses');

        $this->objIcon->title = $gradesLabel;
        $this->objIcon->alt = $gradesLabel;
        $this->objIcon->setIcon('brick', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'list', 'type' => 'grade')));
        $objLink->link = $manageIcon . '&nbsp' . $gradesLabel;
        $gradesLink = $objLink->show();

        $this->objIcon->title = $subjectsLabel;
        $this->objIcon->alt = $subjectsLabel;
        $this->objIcon->setIcon('book', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'list', 'type' => 'subject')));
        $objLink->link = $manageIcon . '&nbsp' . $subjectsLabel;
        $subjectsLink = $objLink->show();
    
        $this->objIcon->title = $strandsLabel;
        $this->objIcon->alt = $strandsLabel;
        $this->objIcon->setIcon('page', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'list', 'type' => 'strand')));
        $objLink->link = $manageIcon . '&nbsp' . $strandsLabel;
        $strandsLink = $objLink->show();
    
        $this->objIcon->title = $classesLabel;
        $this->objIcon->alt = $classesLabel;
        $this->objIcon->setIcon('group', 'png');
        $manageIcon = $this->objIcon->show();
        
        $objLink = new link($this->uri(array('action' => 'list', 'type' => 'class')));
        $objLink->link = $manageIcon . '&nbsp' . $classesLabel;
        $classesLink = $objLink->show();
    
        $string = $gradesLink . '<br />' . $subjectsLink . '<br />' . $strandsLink . '<br />' . $classesLink;
                
        return $string;
    }
    
    /**
     *
     * Method to show the list of grades
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showList()
    {
        $type = $this->getParam('type');
        
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'ERROR: word_edit');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $linkLabel = $this->objLanguage->languageText('word_link', 'system', 'ERROR: word_link');
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');

        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $gradesLabel = $this->objLanguage->code2Txt('mod_grades_grades', 'grades', NULL, 'ERROR: mod_grades_grades');
        $addGradeLabel = $this->objLanguage->code2Txt('mod_grades_addgrade', 'grades', NULL, 'ERROR: mod_grades_addgrade');
        $editGradeLabel = $this->objLanguage->code2Txt('mod_grades_editgrade', 'grades', NULL, 'ERROR: mod_grades_editgrade');
        $deleteGradeLabel = $this->objLanguage->code2Txt('mod_grades_deletegrade', 'grades', NULL, 'ERROR: mod_grades_deletegrade');
        $noGradesLabel = $this->objLanguage->code2Txt('mod_grades_nogrades', 'grades', NULL, 'ERROR: mod_grades_nogrades');
        $linkGradeLabel = $this->objLanguage->code2Txt('mod_grades_linkgrade', 'grades', NULL, 'ERROR: mod_grades_linkgrade');
        
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        $subjectsLabel = $this->objLanguage->code2Txt('mod_grades_subjects', 'grades', NULL, 'ERROR: mod_grades_subjects');
        $addSubjectLabel = $this->objLanguage->code2Txt('mod_grades_addsubject', 'grades', NULL, 'ERROR: mod_grades_addsubject');
        $editSubjectLabel = $this->objLanguage->code2Txt('mod_grades_editsubject', 'grades', NULL, 'ERROR: mod_grades_editsubject');
        $deleteSubjectLabel = $this->objLanguage->code2Txt('mod_grades_deletesubject', 'grades', NULL, 'ERROR: mod_grades_deletesubject');
        $noSubjectsLabel = $this->objLanguage->code2Txt('mod_grades_nosubjects', 'grades', NULL, 'ERROR: mod_grades_nosubjects');
        $linkSubjectLabel = $this->objLanguage->code2Txt('mod_grades_linksubject', 'grades', NULL, 'ERROR: mod_grades_linksubject');
        
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $strandsLabel = $this->objLanguage->code2Txt('mod_grades_strands', 'grades', NULL, 'ERROR: mod_grades_strands');
        $addstrandLabel = $this->objLanguage->code2Txt('mod_grades_addstrand', 'grades', NULL, 'ERROR: mod_grades_addstrand');
        $editstrandLabel = $this->objLanguage->code2Txt('mod_grades_editstrand', 'grades', NULL, 'ERROR: mod_grades_editstrand');
        $deletestrandLabel = $this->objLanguage->code2Txt('mod_grades_deletestrand', 'grades', NULL, 'ERROR: mod_grades_deletestrand');
        $nostrandsLabel = $this->objLanguage->code2Txt('mod_grades_nostrands', 'grades', NULL, 'ERROR: mod_grades_nostrands');
        $linkstrandLabel = $this->objLanguage->code2Txt('mod_grades_linkstrand', 'grades', NULL, 'ERROR: mod_grades_linkstrand');

        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        $classesLabel = $this->objLanguage->code2Txt('mod_grades_classes', 'grades', NULL, 'ERROR: mod_grades_classes');
        $addClassLabel = $this->objLanguage->code2Txt('mod_grades_addclass', 'grades', NULL, 'ERROR: mod_grades_addclass');
        $editClassLabel = $this->objLanguage->code2Txt('mod_grades_editclass', 'grades', NULL, 'ERROR: mod_grades_editclass');
        $deleteClassLabel = $this->objLanguage->code2Txt('mod_grades_deleteclass', 'grades', NULL, 'ERROR: mod_grades_deleteclass');
        $noClassesLabel = $this->objLanguage->code2Txt('mod_grades_noclasses', 'grades', NULL, 'ERROR: mod_grades_noclasses');
        $linkClassLabel = $this->objLanguage->code2Txt('mod_grades_linkclass', 'grades', NULL, 'ERROR: mod_grades_linkclass');

        switch ($type)
        {
            case 'grade':
                $addImage = 'brick_add';
                $editImage = 'brick_edit';
                $deleteImage = 'brick_delete';
                $linkImage = 'brick_link';
                $componentLabel = ucfirst(strtolower($gradeLabel));
                $componentsLabel = ucfirst(strtolower($gradesLabel));
                $addComponentLabel = $addGradeLabel;
                $editComponentLabel = $editGradeLabel;
                $deleteComponentLabel = $deleteGradeLabel;
                $noComponentsLabel = $noGradesLabel;
                $linkComponentLabel = $linkGradeLabel;
                $dataArray = $this->objDBgrades->getAll();
                break;
            case 'subject';
                $addImage = 'book_add';
                $editImage = 'book_edit';
                $deleteImage = 'book_delete';
                $linkImage = 'book_link';
                $componentLabel = ucfirst(strtolower($subjectLabel));
                $componentsLabel = ucfirst(strtolower($subjectsLabel));
                $addComponentLabel = $addSubjectLabel;
                $editComponentLabel = $editSubjectLabel;
                $deleteComponentLabel = $deleteSubjectLabel;
                $noComponentsLabel = $noSubjectsLabel;
                $linkComponentLabel = $linkSubjectLabel;
                $dataArray = $this->objDBsubjects->getAll();
                break;
            case 'strand';
                $addImage = 'page_add';
                $editImage = 'page_edit';
                $deleteImage = 'page_delete';
                $linkImage = 'page_link';
                $componentLabel = ucfirst(strtolower($strandLabel));
                $componentsLabel = ucfirst(strtolower($strandsLabel));
                $addComponentLabel = $addstrandLabel;
                $editComponentLabel = $editstrandLabel;
                $deleteComponentLabel = $deletestrandLabel;
                $noComponentsLabel = $nostrandsLabel;
                $linkComponentLabel = $linkstrandLabel;
                $dataArray = $this->objDBstrands->getAll();
                break;
            case 'class';
                $addImage = 'group_add';
                $editImage = 'group_edit';
                $deleteImage = 'group_delete';
                $linkImage = 'group_link';
                $componentLabel = ucfirst(strtolower($classLabel));
                $componentsLabel = ucfirst(strtolower($classesLabel));
                $addComponentLabel = $addClassLabel;
                $editComponentLabel = $editClassLabel;
                $deleteComponentLabel = $deleteClassLabel;
                $noComponentsLabel = $noClassesLabel;
                $linkComponentLabel = $linkClassLabel;
                $dataArray = $this->objDBclasses->getAll();
                break;
        }
        $array = array('item' => $componentLabel);
        $deleteConfirmLabel = ucfirst(strtolower($this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm'))) . '?';

        $this->objIcon->title = $addLabel;
        $this->objIcon->alt = $addLabel;
        $this->objIcon->setIcon($addImage, 'png');
        $addIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'form', 'type' => $type)));
        $objLink->link = $addIcon . '&nbsp;' . $addComponentLabel;
        $addLink = $objLink->show();
            
        if (empty($dataArray))
        {
            $str = $this->error($noComponentsLabel);
            $str .= '<br />' . $addLink . '<br />';
        }
        else
        {
            $str = $addLink . '<br />';
            
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . $componentLabel . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $editLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $linkLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($dataArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                $this->objIcon->setIcon($deleteImage, 'png');
                $this->objIcon->title = $deleteComponentLabel;
                $this->objIcon->alt = $deleteComponentLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'delete', 'type' => $type . '_id', 'id' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteConfirmLabel);
                $deleteIcon = $this->objConfirm->show();

                $this->objIcon->title = $editComponentLabel;
                $this->objIcon->alt = $editComponentLabel;
                $this->objIcon->setIcon($editImage, 'png');
                $editIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => $type, 'id' => $value['id'])));
                $objLink->link = $editIcon;
                $editLink = $objLink->show();

                $this->objIcon->title = $linkComponentLabel;
                $this->objIcon->alt = $linkComponentLabel;
                $this->objIcon->setIcon($linkImage, 'png');
                $linkIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'link', 'type' => $type, 'id' => $value['id'])));
                $objLink->link = $linkIcon;
                $linkLink = $objLink->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($editLink, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->addCell($linkLink, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $gradeTable = $objTable->show();   
            $str .= $gradeTable;
        }
                
        $objLayer = new layer();
        $objLayer->id = 'gradediv';
        $objLayer->str = $str;
        $gradeLayer = $objLayer->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($componentsLabel)) . '</b>';
        $objFieldset->contents = $gradeLayer;
        $gradeFieldset = $objFieldset->show();
        
        return $gradeFieldset;
    }
    
    /**
     *
     * Method to show the add or edit template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showForm()
    {
        $type = $this->getParam('type');
        $id = $this->getParam('id');
        $errorArray = $this->getSession('errors');
        
        if (empty($id))
        {
            $nameValue = NULL;
            $descriptionValue = NULL;
        }
        else
        {
            switch ($type)
            {
                case 'grade':
                    $dataArray = $this->objDBgrades->getGrade($id);
                    break;
                case 'subject':
                    $dataArray = $this->objDBsubjects->getSubject($id);
                    break;
                case 'strand':
                    $dataArray = $this->objDBstrands->getStrand($id);
                    break;
                case 'class':
                    $dataArray = $this->objDBclasses->getClass($id);
                    break;
            }
            $nameValue = $dataArray['name'];
            $descriptionValue = $dataArray['description'];
        }
        $nameValue = (empty($errorArray)) ? $nameValue : $errorArray['data']['name'];
        $descriptionValue = (empty($errorArray)) ? $descriptionValue : $errorArray['data']['description'];
        
        $nameError = (!empty($errorArray) && array_key_exists('name', $errorArray['errors'])) ? $errorArray['errors']['name'] : NULL;
        $descriptionError = (!empty($errorArray) && array_key_exists('description', $errorArray['errors'])) ? $errorArray['errors']['description'] : NULL;
        
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        
        switch ($type)
        {
            case 'grade':
                $nameLabel = ucfirst(strtolower($gradeLabel));
                break;
            case 'subject':
                $nameLabel = ucfirst(strtolower($subjectLabel));
                break;
            case 'strand':
                $nameLabel = ucfirst(strtolower($strandLabel));
                break;
            case 'class':
                $nameLabel = ucfirst(strtolower($classLabel));
                break;
        }
        
        $objInput = new textinput('name', $nameValue, '', '50');
        $nameInput = $objInput->show();
  
        $objText = new textarea('description', $descriptionValue);
        $descriptionText = $objText->show();

        $objInput = new textinput('id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel');
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($nameError . $nameInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($descriptionLabel, '200px', 'top', '', 'even', '', '');
        $objTable->addCell($descriptionError . $descriptionText, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'odd', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('component', $this->uri(array(
            'action' => 'validate', 'type' => $type,
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $form = $objForm->show();
        
        $string = $form;
        
        return $string;        
    }

    /**
     *
     * Method to validate the component data
     * 
     * @access public
     * @param array $data The data to validate
     * @return boolean TRUE on errors | FALSE if no errors
     */
    public function validate($data)
    {
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'id')
            {
                if ($value == NULL)
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_schools_error_1', 'schools', $array);
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
        }
        $errorArray = array();
        $errorArray['data'] = $data;
        $errorArray['errors'] = $errors;

        $this->setSession('errors', $errorArray);
        if (empty($errors))
        {
            return FALSE;
        }
        return TRUE;
    }        
    
    /**
     *
     * Method to return the subject links template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showSubjectLink()
    {
        $nameLabel = $this->objLanguage->languageText('word_name', 'system', 'ERROR: word_name');
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $noLinkLabel = $this->objLanguage->languageText('mod_grades_nolinks', 'grades', 'ERROR: mod_grades_nolinks');
        $deleteLinkLabel = $this->objLanguage->languageText('mod_grades_deletelink', 'grades', 'ERROR: mod_grades_deletelink');
        $addGradeLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktograde', 'grades', NULL, 'ERROR: mod_grades_linktograde');
        $addClassLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoclass', 'grades', NULL, 'ERROR: mod_grades_linktoclass');
        $addContextLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktocontext', 'grades', NULL, 'ERROR: mod_grades_linktocontext');
        $addStrandLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktostrand', 'grades', NULL, 'ERROR: mod_grades_linktostrand');
        $linkedGradeLabel = $this->objLanguage->code2Txt('mod_grades_linkedgrades', 'grades', NULL, 'ERROR: mod_grades_linkedgrades');
        $linkedClassLabel = $this->objLanguage->code2Txt('mod_grades_linkedclasses', 'grades', NULL, 'ERROR: mod_grades_linkedclasses');
        $linkedContextLabel = $this->objLanguage->code2Txt('mod_grades_linkedcontexts', 'grades', NULL, 'ERROR: mod_grades_linkedcontexts');
        $linkedStrandLabel = $this->objLanguage->code2Txt('mod_grades_linkedstrands', 'grades', NULL, 'ERROR: mod_grades_linkedstrands');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $selectGradeLabel = $this->objLanguage->code2Txt('mod_grades_selectgrade', 'grades', NULL, 'ERROR: mod_grades_selectgrade');
        $selectClassLabel = $this->objLanguage->code2Txt('mod_grades_selectclass', 'grades', NULL, 'ERROR: mod_grades_selectclass');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_grades_selectcontext', 'grades', NULL, 'ERROR: mod_grades_selectcontext');
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_grades_selectstrand', 'grades', NULL, 'ERROR: mod_grades_selectstrand');
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        $contextLabel = $this->objLanguage->code2Txt('word_context', 'system', NULL, 'ERROR: word_context');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $noGradesLabel =$this->objLanguage->code2Txt('mod_grades_nogrades', 'grades', NULL, 'ERROR: mod_grades_nogrades');
        $noClassesLabel =$this->objLanguage->code2Txt('mod_grades_noclasses', 'grades', NULL, 'ERROR: mod_grades_noclasses');
        $noContextsLabel =$this->objLanguage->code2Txt('mod_grades_nocontexts', 'grades', NULL, 'ERROR: mod_grades_nocontexts');
        $noStrandsLabel =$this->objLanguage->code2Txt('mod_grades_nostrands', 'grades', NULL, 'ERROR: mod_grades_nostrands');
        $errorGradeLabel = $this->objLanguage->code2Txt('mod_grades_errorgrade', 'grades', NULL, 'ERROR: mod_grades_errorgrade');
        $errorClassLabel = $this->objLanguage->code2Txt('mod_grades_errorclass', 'grades', NULL, 'ERROR: mod_grades_errorclass');
        $errorContextLabel = $this->objLanguage->code2Txt('mod_grades_errorcontext', 'grades', NULL, 'ERROR: mod_grades_errorcontext');
        $errorStrandLabel = $this->objLanguage->code2Txt('mod_grades_errorstrand', 'grades', NULL, 'ERROR: mod_grades_errorstrand');
        $addGradeLabel = $this->objLanguage->code2Txt('mod_grades_addgrade', 'grades', NULL, 'ERROR: mod_grades_addgrade');
        $addClassLabel = $this->objLanguage->code2Txt('mod_grades_addclass', 'grades', NULL, 'ERROR: mod_grades_addclass');
        $addContextLabel = $this->objLanguage->code2Txt('mod_grades_addcontext', 'grades', NULL, 'ERROR: mod_grades_addcontext');
        $addStrandLabel = $this->objLanguage->code2Txt('mod_grades_addstrand', 'grades', NULL, 'ERROR: mod_grades_addstrand');
        
        $addSchoolLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoschool', 'grades', NULL, 'ERROR: mod_grades_linktoschool');
        $linkedSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_linkedschools', 'grades', NULL, 'ERROR: mod_grades_linkedschools');
        $selectSchoolLabel = $this->objLanguage->code2Txt('mod_grades_selectschool', 'grades', NULL, 'ERROR: mod_grades_selectschool');
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $errorSchoolLabel = $this->objLanguage->code2Txt('mod_grades_errorschool', 'grades', NULL, 'ERROR: mod_grades_errorschool');
        $addSchoolLabel = $this->objLanguage->code2Txt('mod_grades_addschool', 'grades', NULL, 'ERROR: mod_grades_addschool');
        $noSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_noschools', 'grades', NULL, 'ERROR: mod_grades_noschools');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');

        $array = array('item' => $gradeLabel);
        $deleteGradeLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $classLabel);
        $deleteClassLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $contextLabel);
        $deleteContextLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $schoolLabel);
        $deleteSchoolLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $strandLabel);
        $deleteStrandLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        
        $arrayVars = array();
        $arrayVars['no_grade'] = $errorGradeLabel;
        $arrayVars['no_class'] = $errorClassLabel;
        $arrayVars['no_strand'] = $errorStrandLabel;
        $arrayVars['no_context'] = $errorContextLabel;
        $arrayVars['no_school'] = $errorSchoolLabel;
        
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $id = $this->getParam('id');
        $type = $this->getParam('type');
        $tab = $this->getParam('tab', 0);
        $subjectArray = $this->objDBsubjects->getSubject($id);
        $linkedGradesArray = $this->objDBbridging->getLinkedItems('subject_id', 'grade_id', $id);
        $unlinkedGradesArray = $this->objDBbridging->getUnlinkedItems('subject_id', 'grade_id', $id);
        $linkedClassesArray = $this->objDBbridging->getLinkedItems('subject_id', 'class_id', $id);
        $unlinkedClassesArray = $this->objDBbridging->getUnlinkedItems('subject_id', 'class_id', $id);
        $linkedContextsArray = $this->objDBbridging->getLinkedItems('subject_id', 'context_id', $id);
        $unlinkedContextsArray = $this->objDBbridging->getUnlinkedItems('subject_id', 'context_id', $id);
        $linkedSchoolsArray = $this->objDBbridging->getLinkedItems('subject_id', 'school_id', $id);
        $unlinkedSchoolsArray = $this->objDBbridging->getUnlinkedItems('subject_id', 'school_id', $id);
        $linkedStrandsArray = $this->objDBbridging->getLinkedItems('subject_id', 'strand_id', $id);
        $unlinkedStrandsArray = $this->objDBbridging->getUnlinkedItems('subject_id', 'strand_id', $id);
        
        //-- subject fieldset --//        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($subjectArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($descriptionLabel, '200px', 'top', '', 'even', '', '');
        $objTable->addCell($subjectArray['description'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $subjectTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($subjectLabel)) . '</b>';
        $objFieldset->contents = $subjectTable;
        $subjectFieldset = $objFieldset->show();

        $string = $subjectFieldset;
        
        //-- grade tab --//
        $objDrop = new dropdown('grade_id');
        $objDrop->addOption('', $selectGradeLabel);
        $objDrop->addFromDB($unlinkedGradesArray, 'name', 'id');
        $objDrop->setSelected('');
        $gradesDrop = $objDrop->show();
  
        $objInput = new textinput('subject_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_grade');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_grade');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($gradeLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($gradesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('grade', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'grade_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $gradeForm = $objForm->show();

        if (!empty($linkedGradesArray))
        {
            if (!empty($unlinkedGradesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($gradeLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedGradesArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'grade', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteGradeLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'gradestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'gradesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $gradesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedGradesArray))
            {
                $noGrades = $this->error($noGradesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('brick_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'grade_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addGradeLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $noLinks . '<br />' . $noGrades . '<br />' . $addLink;
                $gradesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $gradesLayer = $objLayer->show();
            }
        }

        $gradeTabArray = array(
            'name' => ucfirst(strtolower($linkedGradeLabel)),
            'content' => $gradesLayer,
        );

        //-- strand tab --//
        $objDrop = new dropdown('strand_id');
        $objDrop->addOption('', $selectStrandLabel);
        $objDrop->addFromDB($unlinkedStrandsArray, 'name', 'id');
        $objDrop->setSelected('');
        $strandsDrop = $objDrop->show();
  
        $objInput = new textinput('subject_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_strand');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_strand');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($strandLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($strandsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('strand', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'strand_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $strandForm = $objForm->show();

        if (!empty($linkedStrandsArray))
        {
            if (!empty($unlinkedStrandsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $strandForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addstrandsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($strandLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedStrandsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'strand', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteStrandLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'strandstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'strandsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $strandsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedStrandsArray))
            {
                $noStrands = $this->error($noStrandsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('brick_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'strand_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addStrandLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $noLinks . '<br />' . $noStrands . '<br />' . $addLink;
                $strandsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $strandForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addstrandsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $strandsLayer = $objLayer->show();
            }
        }

        $strandTabArray = array(
            'name' => ucfirst(strtolower($linkedStrandLabel)),
            'content' => $strandsLayer,
        );

        //-- class tab --//
        $objDrop = new dropdown('class_id');
        $objDrop->addOption('', $selectClassLabel);
        $objDrop->addFromDB($unlinkedClassesArray, 'name', 'id');
        $objDrop->setSelected('');
        $classesDrop = $objDrop->show();
  
        $objInput = new textinput('subject_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_class');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_class');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($classLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($classesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('class', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'class_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $classForm = $objForm->show();

        if (!empty($linkedClassesArray))
        {
            if (!empty($unlinkedClassesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($classLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedClassesArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'class', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteClassLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'classestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'classesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $classesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedClassesArray))
            {
                $noClasses = $this->error($noClassesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('group_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'class_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addClassLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $noLinks . '<br />' . $noClasses . '<br />' . $addLink;
                $classesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addClassLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $classesLayer = $objLayer->show();
            }
        }
        
        $classTabArray = array(
            'name' => ucfirst(strtolower($linkedClassLabel)),
            'content' => $classesLayer,
        );

        //-- context tab --//
        $objDrop = new dropdown('context_id');
        $objDrop->addOption('', $selectContextLabel);
        $objDrop->addFromDB($unlinkedContextsArray, 'title', 'id');
        $objDrop->setSelected('');
        $contextsDrop = $objDrop->show();
  
        $objInput = new textinput('subject_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_context');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_context');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($contextLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($contextsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('context', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'context_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $contextForm = $objForm->show();

        if (!empty($linkedContextsArray))
        {
            if (!empty($unlinkedContextsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($contextLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedContextsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'context', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteContextLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['title'], '', '', '', $class, '', '');
                $objTable->addCell($value['about'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'contextstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'contextsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $contextsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedContextsArray))
            {
                $noContexts = $this->error($noContextsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'add'), 'contextadmin'));
                $objLink->link = $addIcon . '&nbsp;' . $addContextLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $noLinks . '<br />' . $noContexts . '<br />' . $addLink;
                $contextsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $contextsLayer = $objLayer->show();
            }
        }
        
        $contextTabArray = array(
            'name' => ucfirst(strtolower($linkedContextLabel)),
            'content' => $contextsLayer,
        );

        //-- school tab --//
        $objDrop = new dropdown('school_id');
        $objDrop->addOption('', $selectSchoolLabel);
        $objDrop->addFromDB($unlinkedSchoolsArray, 'name', 'id');
        $objDrop->setSelected('');
        $schoolsDrop = $objDrop->show();
  
        $objInput = new textinput('subject_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_school');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_school');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($schoolLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($schoolsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('school', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'school_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $schoolForm = $objForm->show();

        if (!empty($linkedSchoolsArray))
        {
            if (!empty($unlinkedSchoolsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($schoolLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $addressLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSchoolsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'school', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSchoolLabel);
                $deleteIcon = $this->objConfirm->show();

                $temp = explode('|', $value['address']);
                $addressArray = array();
                foreach($temp as $line)
                {
                    if (!empty($line))
                    {
                        $addressArray[] = $line;
                    }
                }
                $addressString = implode(',<br />', $addressArray);
                
                $objTable->startRow();
                $objTable->addCell($value['name'], '', 'top', '', $class, '', '');
                $objTable->addCell($addressString, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'schoolstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'schoolsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $schoolsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSchoolsArray))
            {
                $noSchools = $this->error($noSchoolsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add'), 'schools'));
                $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSchools . '<br />' . $addLink;
                $schoolsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addSchoolLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $schoolsLayer = $objLayer->show();
            }
        }
        
        $schoolTabArray = array(
            'name' => ucfirst(strtolower($linkedSchoolsLabel)),
            'content' => $schoolsLayer,
        );

        $this->objTab->init();
        $this->objTab->tabId = 'links_tab';
        switch ($tab)
        {
            case 'school':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSchoolsLabel));
                break;
            case 'grade':
                $this->objTab->setSelected = ucfirst(strtolower($linkedGradeLabel));
                break;
            case 'context':
                $this->objTab->setSelected = ucfirst(strtolower($linkedContextLabel));
                break;
            case 'strand':
                $this->objTab->setSelected = ucfirst(strtolower($linkedStrandLabel));
                break;
            case 'class':
                $this->objTab->setSelected = ucfirst(strtolower($linkedClassLabel));
                break;
            default:
                $this->objTab->setSelected = 0;
        }
           
        $this->objTab->addTab($schoolTabArray);
        $this->objTab->addTab($gradeTabArray);
        $this->objTab->addTab($strandTabArray);
        $this->objTab->addTab($contextTabArray);
        $this->objTab->addTab($classTabArray);
        $linkTab = $this->objTab->show();

        $string .= $linkTab;        

        return $string;
    }

    /**
     *
     * Method to return the strand links template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showStrandLink()
    {
        $nameLabel = $this->objLanguage->languageText('word_name', 'system', 'ERROR: word_name');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $noLinkLabel = $this->objLanguage->languageText('mod_grades_nolinks', 'grades', 'ERROR: mod_grades_nolinks');
        $deleteLinkLabel = $this->objLanguage->languageText('mod_grades_deletelink', 'grades', 'ERROR: mod_grades_deletelink');
        $addGradeLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktograde', 'grades', NULL, 'ERROR: mod_grades_linktograde');
        $addClassLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoclass', 'grades', NULL, 'ERROR: mod_grades_linktoclass');
        $addContextLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktocontext', 'grades', NULL, 'ERROR: mod_grades_linktocontext');
        $addSubjectLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktosubject', 'grades', NULL, 'ERROR: mod_grades_linktosubject');
        $linkedGradeLabel = $this->objLanguage->code2Txt('mod_grades_linkedgrades', 'grades', NULL, 'ERROR: mod_grades_linkedgrades');
        $linkedClassLabel = $this->objLanguage->code2Txt('mod_grades_linkedclasses', 'grades', NULL, 'ERROR: mod_grades_linkedclasses');
        $linkedContextLabel = $this->objLanguage->code2Txt('mod_grades_linkedcontexts', 'grades', NULL, 'ERROR: mod_grades_linkedcontexts');
        $linkedSubjectLabel = $this->objLanguage->code2Txt('mod_grades_linkedsubjects', 'grades', NULL, 'ERROR: mod_grades_linkedsubjects');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $selectGradeLabel = $this->objLanguage->code2Txt('mod_grades_selectgrade', 'grades', NULL, 'ERROR: mod_grades_selectgrade');
        $selectClassLabel = $this->objLanguage->code2Txt('mod_grades_selectclass', 'grades', NULL, 'ERROR: mod_grades_selectclass');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_grades_selectcontext', 'grades', NULL, 'ERROR: mod_grades_selectcontext');
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_grades_selectsubject', 'grades', NULL, 'ERROR: mod_grades_selectsubject');
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        $contextLabel = $this->objLanguage->code2Txt('word_context', 'system', NULL, 'ERROR: word_context');
        $subjectLabel = $this->objLanguage->code2Txt('word_subject', 'system', NULL, 'ERROR: word_subject');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $noGradesLabel =$this->objLanguage->code2Txt('mod_grades_nogrades', 'grades', NULL, 'ERROR: mod_grades_nogrades');
        $noClassesLabel =$this->objLanguage->code2Txt('mod_grades_noclasses', 'grades', NULL, 'ERROR: mod_grades_noclasses');
        $noContextsLabel =$this->objLanguage->code2Txt('mod_grades_nocontexts', 'grades', NULL, 'ERROR: mod_grades_nocontexts');
        $noSubjectsLabel =$this->objLanguage->code2Txt('mod_grades_nosubjects', 'grades', NULL, 'ERROR: mod_grades_nosubjects');
        $errorGradeLabel = $this->objLanguage->code2Txt('mod_grades_errorgrade', 'grades', NULL, 'ERROR: mod_grades_errorgrade');
        $errorClassLabel = $this->objLanguage->code2Txt('mod_grades_errorclass', 'grades', NULL, 'ERROR: mod_grades_errorclass');
        $errorContextLabel = $this->objLanguage->code2Txt('mod_grades_errorcontext', 'grades', NULL, 'ERROR: mod_grades_errorcontext');
        $errorSubjectLabel = $this->objLanguage->code2Txt('mod_grades_errorsubject', 'grades', NULL, 'ERROR: mod_grades_errorsubject');
        $addGradeLabel = $this->objLanguage->code2Txt('mod_grades_addgrade', 'grades', NULL, 'ERROR: mod_grades_addgrade');
        $addClassLabel = $this->objLanguage->code2Txt('mod_grades_addclass', 'grades', NULL, 'ERROR: mod_grades_addclass');
        $addContextLabel = $this->objLanguage->code2Txt('mod_grades_addcontext', 'grades', NULL, 'ERROR: mod_grades_addcontext');
        $addSubjectLabel = $this->objLanguage->code2Txt('mod_grades_addsubject', 'grades', NULL, 'ERROR: mod_grades_addsubject');
        
        $addSchoolLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoschool', 'grades', NULL, 'ERROR: mod_grades_linktoschool');
        $linkedSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_linkedschools', 'grades', NULL, 'ERROR: mod_grades_linkedschools');
        $selectSchoolLabel = $this->objLanguage->code2Txt('mod_grades_selectschool', 'grades', NULL, 'ERROR: mod_grades_selectschool');
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $errorSchoolLabel = $this->objLanguage->code2Txt('mod_grades_errorschool', 'grades', NULL, 'ERROR: mod_grades_errorschool');
        $addSchoolLabel = $this->objLanguage->code2Txt('mod_grades_addschool', 'grades', NULL, 'ERROR: mod_grades_addschool');
        $noSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_noschools', 'grades', NULL, 'ERROR: mod_grades_noschools');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');

        $array = array('item' => $gradeLabel);
        $deleteGradeLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $classLabel);
        $deleteClassLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $contextLabel);
        $deleteContextLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $schoolLabel);
        $deleteSchoolLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $strandLabel);
        $deleteSubjectLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        
        $arrayVars = array();
        $arrayVars['no_grade'] = $errorGradeLabel;
        $arrayVars['no_class'] = $errorClassLabel;
        $arrayVars['no_subject'] = $errorSubjectLabel;
        $arrayVars['no_context'] = $errorContextLabel;
        $arrayVars['no_school'] = $errorSchoolLabel;
        
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $id = $this->getParam('id');
        $type = $this->getParam('type');
        $tab = $this->getParam('tab', 0);
        $strandArray = $this->objDBstrands->getStrand($id);
        $linkedGradesArray = $this->objDBbridging->getLinkedItems('strand_id', 'grade_id', $id);
        $unlinkedGradesArray = $this->objDBbridging->getUnlinkedItems('strand_id', 'grade_id', $id);
        $linkedClassesArray = $this->objDBbridging->getLinkedItems('strand_id', 'class_id', $id);
        $unlinkedClassesArray = $this->objDBbridging->getUnlinkedItems('strand_id', 'class_id', $id);
        $linkedContextsArray = $this->objDBbridging->getLinkedItems('strand_id', 'context_id', $id);
        $unlinkedContextsArray = $this->objDBbridging->getUnlinkedItems('strand_id', 'context_id', $id);
        $linkedSchoolsArray = $this->objDBbridging->getLinkedItems('strand_id', 'school_id', $id);
        $unlinkedSchoolsArray = $this->objDBbridging->getUnlinkedItems('strand_id', 'school_id', $id);
        $linkedSubjectsArray = $this->objDBbridging->getLinkedItems('strand_id', 'subject_id', $id);
        $unlinkedSubjectsArray = $this->objDBbridging->getUnlinkedItems('strand_id', 'subject_id', $id);
        
        //-- strand fieldset --//        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($strandArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($descriptionLabel, '200px', 'top', '', 'even', '', '');
        $objTable->addCell($strandArray['description'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $strandTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($strandLabel)) . '</b>';
        $objFieldset->contents = $strandTable;
        $strandFieldset = $objFieldset->show();

        $string = $strandFieldset;
        
        //-- grade tab --//
        $objDrop = new dropdown('grade_id');
        $objDrop->addOption('', $selectGradeLabel);
        $objDrop->addFromDB($unlinkedGradesArray, 'name', 'id');
        $objDrop->setSelected('');
        $gradesDrop = $objDrop->show();
  
        $objInput = new textinput('strand_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_grade');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_grade');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($gradeLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($gradesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('grade', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'grade_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $gradeForm = $objForm->show();

        if (!empty($linkedGradesArray))
        {
            if (!empty($unlinkedGradesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($gradeLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedGradesArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'grade', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteGradeLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'gradestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'gradesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $gradesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedGradesArray))
            {
                $noGrades = $this->error($noGradesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('brick_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'grade_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addGradeLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $noLinks . '<br />' . $noGrades . '<br />' . $addLink;
                $gradesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $gradesLayer = $objLayer->show();
            }
        }

        $gradeTabArray = array(
            'name' => ucfirst(strtolower($linkedGradeLabel)),
            'content' => $gradesLayer,
        );

        //-- subject tab --//
        $objDrop = new dropdown('subject_id');
        $objDrop->addOption('', $selectSubjectLabel);
        $objDrop->addFromDB($unlinkedSubjectsArray, 'name', 'id');
        $objDrop->setSelected('');
        $subjectsDrop = $objDrop->show();
  
        $objInput = new textinput('strand_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_subject');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_subject');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($subjectLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($subjectsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('subject', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'subject_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $subjectForm = $objForm->show();

        if (!empty($linkedSubjectsArray))
        {
            if (!empty($unlinkedSubjectsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjectsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($subjectLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSubjectsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'subject', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSubjectLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'subjectstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'subjectsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $subjectsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSubjectsArray))
            {
                $noSubjects = $this->error($noSubjectsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('page_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'strand_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addSubjectLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSubjects . '<br />' . $addLink;
                $subjectsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $subjectsLayer = $objLayer->show();
            }
        }

        $subjectTabArray = array(
            'name' => ucfirst(strtolower($linkedSubjectLabel)),
            'content' => $subjectsLayer,
        );

        //-- class tab --//
        $objDrop = new dropdown('class_id');
        $objDrop->addOption('', $selectClassLabel);
        $objDrop->addFromDB($unlinkedClassesArray, 'name', 'id');
        $objDrop->setSelected('');
        $classesDrop = $objDrop->show();
  
        $objInput = new textinput('strand_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_class');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_class');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($classLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($classesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('class', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'class_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $classForm = $objForm->show();

        if (!empty($linkedClassesArray))
        {
            if (!empty($unlinkedClassesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($classLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedClassesArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'class', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteClassLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'classestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'classesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $classesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedClassesArray))
            {
                $noClasses = $this->error($noClassesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('group_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'class_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addClassLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $noLinks . '<br />' . $noClasses . '<br />' . $addLink;
                $classesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addClassLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $classesLayer = $objLayer->show();
            }
        }
        
        $classTabArray = array(
            'name' => ucfirst(strtolower($linkedClassLabel)),
            'content' => $classesLayer,
        );

        //-- context tab --//
        $objDrop = new dropdown('context_id');
        $objDrop->addOption('', $selectContextLabel);
        $objDrop->addFromDB($unlinkedContextsArray, 'title', 'id');
        $objDrop->setSelected('');
        $contextsDrop = $objDrop->show();
  
        $objInput = new textinput('strand_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_context');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_context');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($contextLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($contextsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('context', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'context_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $contextForm = $objForm->show();

        if (!empty($linkedContextsArray))
        {
            if (!empty($unlinkedContextsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($contextLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedContextsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'context', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteContextLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['title'], '', '', '', $class, '', '');
                $objTable->addCell($value['about'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'contextstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'contextsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $contextsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedContextsArray))
            {
                $noContexts = $this->error($noContextsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'add'), 'contextadmin'));
                $objLink->link = $addIcon . '&nbsp;' . $addContextLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $noLinks . '<br />' . $noContexts . '<br />' . $addLink;
                $contextsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $contextsLayer = $objLayer->show();
            }
        }
        
        $contextTabArray = array(
            'name' => ucfirst(strtolower($linkedContextLabel)),
            'content' => $contextsLayer,
        );

        //-- school tab --//
        $objDrop = new dropdown('school_id');
        $objDrop->addOption('', $selectSchoolLabel);
        $objDrop->addFromDB($unlinkedSchoolsArray, 'name', 'id');
        $objDrop->setSelected('');
        $schoolsDrop = $objDrop->show();
  
        $objInput = new textinput('strand_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_school');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_school');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($schoolLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($schoolsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('school', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'school_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $schoolForm = $objForm->show();

        if (!empty($linkedSchoolsArray))
        {
            if (!empty($unlinkedSchoolsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($schoolLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $addressLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSchoolsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'school', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSchoolLabel);
                $deleteIcon = $this->objConfirm->show();

                $temp = explode('|', $value['address']);
                $addressArray = array();
                foreach($temp as $line)
                {
                    if (!empty($line))
                    {
                        $addressArray[] = $line;
                    }
                }
                $addressString = implode(',<br />', $addressArray);
                
                $objTable->startRow();
                $objTable->addCell($value['name'], '', 'top', '', $class, '', '');
                $objTable->addCell($addressString, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'schoolstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'schoolsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $schoolsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSchoolsArray))
            {
                $noSchools = $this->error($noSchoolsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add'), 'schools'));
                $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSchools . '<br />' . $addLink;
                $schoolsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addSchoolLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $schoolsLayer = $objLayer->show();
            }
        }
        
        $schoolTabArray = array(
            'name' => ucfirst(strtolower($linkedSchoolsLabel)),
            'content' => $schoolsLayer,
        );

        $this->objTab->init();
        $this->objTab->tabId = 'links_tab';
        switch ($tab)
        {
            case 'school':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSchoolsLabel));
                break;
            case 'grade':
                $this->objTab->setSelected = ucfirst(strtolower($linkedGradeLabel));
                break;
            case 'subject':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSubjectLabel));
                break;
            case 'context':
                $this->objTab->setSelected = ucfirst(strtolower($linkedContextLabel));
                break;
            case 'class':
                $this->objTab->setSelected = ucfirst(strtolower($linkedClassLabel));
                break;
            default:
                $this->objTab->setSelected = 0;
        }
        $this->objTab->addTab($schoolTabArray);
        $this->objTab->addTab($gradeTabArray);
        $this->objTab->addTab($subjectTabArray);
        $this->objTab->addTab($contextTabArray);
        $this->objTab->addTab($classTabArray);
        $linkTab = $this->objTab->show();

        $string .= $linkTab;        

        return $string;
    }

    /**
     * Method to return the grades links template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showGradeLink()
    {
        $nameLabel = $this->objLanguage->languageText('word_name', 'system', 'ERROR: word_name');
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $noLinkLabel = $this->objLanguage->languageText('mod_grades_nolinks', 'grades', 'ERROR: mod_grades_nolinks');
        $deleteLinkLabel = $this->objLanguage->languageText('mod_grades_deletelink', 'grades', 'ERROR: mod_grades_deletelink');
        $addSubjectLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktosubject', 'grades', NULL, 'ERROR: mod_grades_linktosubject');
        $addStrandLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktostrand', 'grades', NULL, 'ERROR: mod_grades_linktostrand');
        $addContextLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktocontext', 'grades', NULL, 'ERROR: mod_grades_linktocontext');
        $addClassLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoclass', 'grades', NULL, 'ERROR: mod_grades_linktoclass');
        $linkedSubjectLabel = $this->objLanguage->code2Txt('mod_grades_linkedsubjects', 'grades', NULL, 'ERROR: mod_grades_linkedsubjects');
        $linkedStrandLabel = $this->objLanguage->code2Txt('mod_grades_linkedstrands', 'grades', NULL, 'ERROR: mod_grades_linkedstrands');
        $linkedContextLabel = $this->objLanguage->code2Txt('mod_grades_linkedcontexts', 'grades', NULL, 'ERROR: mod_grades_linkedcontexts');
        $linkedClassLabel = $this->objLanguage->code2Txt('mod_grades_linkedclasses', 'grades', NULL, 'ERROR: mod_grades_linkedclasses');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_grades_selectsubject', 'grades', NULL, 'ERROR: mod_grades_selectsubject');
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_grades_selectstrand', 'grades', NULL, 'ERROR: mod_grades_selectstrand');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_grades_selectcontext', 'grades', NULL, 'ERROR: mod_grades_selectcontext');
        $selectClassLabel = $this->objLanguage->code2Txt('mod_grades_selectclass', 'grades', NULL, 'ERROR: mod_grades_selectclass');
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $contextLabel = $this->objLanguage->code2Txt('word_context', 'system', NULL, 'ERROR: word_context');
        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $noSubjectsLabel =$this->objLanguage->code2Txt('mod_grades_nosubjects', 'grades', NULL, 'ERROR: mod_grades_nosubjects');
        $noStrandsLabel =$this->objLanguage->code2Txt('mod_grades_nostrands', 'grades', NULL, 'ERROR: mod_grades_nostrands');
        $noContextsLabel =$this->objLanguage->code2Txt('mod_grades_nocontexts', 'grades', NULL, 'ERROR: mod_grades_nocontexts');
        $noClassesLabel =$this->objLanguage->code2Txt('mod_grades_noclasses', 'grades', NULL, 'ERROR: mod_grades_noclasses');
        $errorSubjectLabel = $this->objLanguage->code2Txt('mod_grades_errorsubject', 'grades', NULL, 'ERROR: mod_grades_errorsubject');
        $errorStrandLabel = $this->objLanguage->code2Txt('mod_grades_errorstrand', 'grades', NULL, 'ERROR: mod_grades_errorstrand');
        $errorContextLabel = $this->objLanguage->code2Txt('mod_grades_errorcontext', 'grades', NULL, 'ERROR: mod_grades_errorcontext');
        $errorClassLabel = $this->objLanguage->code2Txt('mod_grades_errorclass', 'grades', NULL, 'ERROR: mod_grades_errorclass');
        $addSubjectLabel = $this->objLanguage->code2Txt('mod_grades_addsubject', 'grades', NULL, 'ERROR: mod_grades_addsubject');
        $addStrandLabel = $this->objLanguage->code2Txt('mod_grades_addstrand', 'grades', NULL, 'ERROR: mod_grades_addstrand');
        $addContextLabel = $this->objLanguage->code2Txt('mod_grades_addcontext', 'grades', NULL, 'ERROR: mod_grades_addcontext');
        $addClassLabel = $this->objLanguage->code2Txt('mod_grades_addclass', 'grades', NULL, 'ERROR: mod_grades_addclass');
        
        $addSchoolLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoschool', 'grades', NULL, 'ERROR: mod_grades_linktoschool');
        $linkedSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_linkedschools', 'grades', NULL, 'ERROR: mod_grades_linkedschools');
        $selectSchoolLabel = $this->objLanguage->code2Txt('mod_grades_selectschool', 'grades', NULL, 'ERROR: mod_grades_selectschool');
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $errorSchoolLabel = $this->objLanguage->code2Txt('mod_grades_errorschool', 'grades', NULL, 'ERROR: mod_grades_errorschool');
        $addSchoolLabel = $this->objLanguage->code2Txt('mod_grades_addschool', 'grades', NULL, 'ERROR: mod_grades_addschool');
        $addressLabel = $this->objLanguage->languageTExt('word_address', 'system', 'ERROR: word_address');
        $noSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_noschools', 'grades', NULL, 'ERROR: mod_grades_noschools');

        $array = array('item' => $subjectLabel);
        $deleteSubjectLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $strandLabel);
        $deleteStrandLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $contextLabel);
        $deleteContextLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $classLabel);
        $deleteClassLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $schoolLabel);
        $deleteSchoolLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');        
        
        $arrayVars = array();
        $arrayVars['no_subject'] = $errorSubjectLabel;
        $arrayVars['no_strand'] = $errorStrandLabel;
        $arrayVars['no_class'] = $errorClassLabel;
        $arrayVars['no_school'] = $errorSchoolLabel;
        $arrayVars['no_context'] = $errorContextLabel;
                
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $id = $this->getParam('id');
        $type = $this->getParam('type');
        $tab = $this->getParam('tab', 0);
        $gradeArray = $this->objDBgrades->getGrade($id);
        $linkedSubjectsArray = $this->objDBbridging->getLinkedItems('grade_id', 'subject_id', $id);
        $unlinkedSubjectsArray = $this->objDBbridging->getUnlinkedItems('grade_id', 'subject_id', $id);
        $linkedStrandsArray = $this->objDBbridging->getLinkedItems('grade_id', 'strand_id', $id);
        $unlinkedStrandsArray = $this->objDBbridging->getUnlinkedItems('grade_id', 'strand_id', $id);
        $linkedContextsArray = $this->objDBbridging->getLinkedItems('grade_id', 'context_id', $id);
        $unlinkedContextsArray = $this->objDBbridging->getUnlinkedItems('grade_id', 'context_id', $id);
        $linkedClassesArray = $this->objDBbridging->getLinkedItems('grade_id', 'class_id', $id);
        $unlinkedClassesArray = $this->objDBbridging->getUnlinkedItems('grade_id', 'class_id', $id);
        $linkedSchoolsArray = $this->objDBbridging->getLinkedItems('grade_id', 'school_id', $id);
        $unlinkedSchoolsArray = $this->objDBbridging->getUnlinkedItems('grade_id', 'school_id', $id);
        
        //-- grade fieldset --//        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($gradeArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($descriptionLabel, '200px', 'top', '', 'even', '', '');
        $objTable->addCell($gradeArray['description'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $gradeTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($gradeLabel)) . '</b>';
        $objFieldset->contents = $gradeTable;
        $gradeFieldset = $objFieldset->show();

        $string = $gradeFieldset;
        
        //-- subject tab --//
        $objDrop = new dropdown('subject_id');
        $objDrop->addOption('', $selectSubjectLabel);
        $objDrop->addFromDB($unlinkedSubjectsArray, 'name', 'id');
        $objDrop->setSelected('');
        $subjectsDrop = $objDrop->show();
  
        $objInput = new textinput('grade_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_subject');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_subject');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($subjectLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($subjectsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('subject', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'subject_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $subjectForm = $objForm->show();

        if (!empty($linkedSubjectsArray))
        {
            if (!empty($unlinkedSubjectsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjetcsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($subjectLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSubjectsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'subject', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSubjectLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'subjectstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'subjectsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $subjectsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSubjectsArray))
            {
                $noSubjects = $this->error($noSubjectsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('book_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'subject_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addSubjectLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSubjects . '<br />' . $addLink;
                $subjectsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $subjectsLayer = $objLayer->show();
            }
        }

        $subjectTabArray = array(
            'name' => ucfirst(strtolower($linkedSubjectLabel)),
            'content' => $subjectsLayer,
        );

        //-- strand tab --//
        $objDrop = new dropdown('strand_id');
        $objDrop->addOption('', $selectStrandLabel);
        $objDrop->addFromDB($unlinkedStrandsArray, 'name', 'id');
        $objDrop->setSelected('');
        $strandsDrop = $objDrop->show();
  
        $objInput = new textinput('grade_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_strand');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_strand');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($strandLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($strandsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('strand', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'strand_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $strandForm = $objForm->show();

        if (!empty($linkedStrandsArray))
        {
            if (!empty($unlinkedStrandsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addstrandsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($strandLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedStrandsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'strand', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteStrandLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'strandstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'strandsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $strandsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedStrandsArray))
            {
                $noStrands = $this->error($noStrandsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('page_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'strand_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addStrandLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $noLinks . '<br />' . $noStrands . '<br />' . $addLink;
                $strandsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $strandForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addstrandsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $strandsLayer = $objLayer->show();
            }
        }

        $strandTabArray = array(
            'name' => ucfirst(strtolower($linkedStrandLabel)),
            'content' => $strandsLayer,
        );

        //-- context tab --//
        $objDrop = new dropdown('context_id');
        $objDrop->addOption('', $selectContextLabel);
        $objDrop->addFromDB($unlinkedContextsArray, 'title', 'id');
        $objDrop->setSelected('');
        $contextsDrop = $objDrop->show();
  
        $objInput = new textinput('grade_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_context');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_context');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($contextLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($contextsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('context', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'context_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $contextForm = $objForm->show();

        if (!empty($linkedContextsArray))
        {
            if (!empty($unlinkedContextsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($contextLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedContextsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'context', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteContextLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['title'], '', '', '', $class, '', '');
                $objTable->addCell($value['about'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'contextstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'contextsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $contextsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedContextsArray))
            {
                $noSubjects = $this->error($noContextsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('page_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'context_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addContextLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $noLinks . '<br />' . $noContexts . '<br />' . $addLink;
                $contextsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $contextsLayer = $objLayer->show();
            }
        }

        $contextTabArray = array(
            'name' => ucfirst(strtolower($linkedContextLabel)),
            'content' => $contextsLayer,
        );

        //-- class tab --//
        $objDrop = new dropdown('class_id');
        $objDrop->addOption('', $selectClassLabel);
        $objDrop->addFromDB($unlinkedClassesArray, 'name', 'id');
        $objDrop->setSelected('');
        $classesDrop = $objDrop->show();
  
        $objInput = new textinput('grade_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_class');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_class');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($classLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($classesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('class', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'class_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $classForm = $objForm->show();

        if (!empty($linkedClassesArray))
        {
            if (!empty($unlinkedClassesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($classLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedClassesArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'class', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteClassLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'classestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'classesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $classesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedClassesArray))
            {
                $noClasses = $this->error($noClassesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('group_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'class_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addClassLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $noLinks . '<br />' . $noClasses . '<br />' . $addLink;
                $classesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'classesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $classForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addclasslink">' . $linkIcon . '&nbsp;' . $addClassLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addclassesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'classesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $classesLayer = $objLayer->show();
            }
        }
        
        $classTabArray = array(
            'name' => ucfirst(strtolower($linkedClassLabel)),
            'content' => $classesLayer,
        );

        //-- school tab --//
        $objDrop = new dropdown('school_id');
        $objDrop->addOption('', $selectSchoolLabel);
        $objDrop->addFromDB($unlinkedSchoolsArray, 'name', 'id');
        $objDrop->setSelected('');
        $schoolsDrop = $objDrop->show();
  
        $objInput = new textinput('grade_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_school');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_school');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($schoolLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($schoolsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('school', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'school_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $schoolForm = $objForm->show();

        if (!empty($linkedSchoolsArray))
        {
            if (!empty($unlinkedSchoolsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($schoolLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $addressLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSchoolsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'school', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSchoolLabel);
                $deleteIcon = $this->objConfirm->show();

                $temp = explode('|', $value['address']);
                $addressArray = array();
                foreach($temp as $line)
                {
                    if (!empty($line))
                    {
                        $addressArray[] = $line;
                    }
                }
                $addressString = implode(',<br />', $addressArray);
                
                $objTable->startRow();
                $objTable->addCell($value['name'], '', 'top', '', $class, '', '');
                $objTable->addCell($addressString, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'schoolstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'schoolsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $schoolsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSchoolsArray))
            {
                $noSchools = $this->error($noSchoolsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add'), 'schools'));
                $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSchools . '<br />' . $addLink;
                $schoolsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addSchoolLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $schoolsLayer = $objLayer->show();
            }
        }
        
        $schoolTabArray = array(
            'name' => ucfirst(strtolower($linkedSchoolsLabel)),
            'content' => $schoolsLayer,
        );

        $this->objTab->init();
        $this->objTab->tabId = 'links_tab';
        switch ($tab)
        {
            case 'school':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSchoolsLabel));
                break;
            case 'subject':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSubjectLabel));
                break;
            case 'strand':
                $this->objTab->setSelected = ucfirst(strtolower($linkedStrandLabel));
                break;
            case 'context':
                $this->objTab->setSelected = ucfirst(strtolower($linkedContextLabel));
                break;
            case 'class':
                $this->objTab->setSelected = ucfirst(strtolower($linkedClassLabel));
                break;
            default:
                $this->objTab->setSelected = 0;
        }
        $this->objTab->addTab($schoolTabArray);
        $this->objTab->addTab($subjectTabArray);
        $this->objTab->addTab($strandTabArray);
        $this->objTab->addTab($contextTabArray);
        $this->objTab->addTab($classTabArray);
        $linkTab = $this->objTab->show();

        $string .= $linkTab;        

        return $string;
    }    

    /**
     * Method to return the grades links template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showClassLink()
    {
        $nameLabel = $this->objLanguage->languageText('word_name', 'system', 'ERROR: word_name');
        $classLabel = $this->objLanguage->code2Txt('mod_grades_class', 'grades', NULL, 'ERROR: mod_grades_class');
        $descriptionLabel = $this->objLanguage->languageText('word_description', 'system', 'ERROR: word_description');
        $noLinkLabel = $this->objLanguage->languageText('mod_grades_nolinks', 'grades', 'ERROR: mod_grades_nolinks');
        $deleteLinkLabel = $this->objLanguage->languageText('mod_grades_deletelink', 'grades', 'ERROR: mod_grades_deletelink');
        $addSubjectLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktosubject', 'grades', NULL, 'ERROR: mod_grades_linktosubject');
        $addStrandLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktostrand', 'grades', NULL, 'ERROR: mod_grades_linktostrand');
        $addContextLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktocontext', 'grades', NULL, 'ERROR: mod_grades_linktocontext');
        $addGradeLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktograde', 'grades', NULL, 'ERROR: mod_grades_linktograde');
        $linkedSubjectLabel = $this->objLanguage->code2Txt('mod_grades_linkedsubjects', 'grades', NULL, 'ERROR: mod_grades_linkedsubjects');
        $linkedStrandLabel = $this->objLanguage->code2Txt('mod_grades_linkedstrands', 'grades', NULL, 'ERROR: mod_grades_linkedstrands');
        $linkedContextLabel = $this->objLanguage->code2Txt('mod_grades_linkedcontexts', 'grades', NULL, 'ERROR: mod_grades_linkedcontexts');
        $linkedGradeLabel = $this->objLanguage->code2Txt('mod_grades_linkedgrades', 'grades', NULL, 'ERROR: mod_grades_linkedgrades');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_grades_selectsubject', 'grades', NULL, 'ERROR: mod_grades_selectsubject');
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_grades_selectstrand', 'grades', NULL, 'ERROR: mod_grades_selectstrand');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_grades_selectcontext', 'grades', NULL, 'ERROR: mod_grades_selectcontext');
        $selectGradeLabel = $this->objLanguage->code2Txt('mod_grades_selectgrade', 'grades', NULL, 'ERROR: mod_grades_selectgrade');
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        $contextLabel = $this->objLanguage->code2Txt('word_context', 'system', NULL, 'ERROR: word_context');
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $noSubjectsLabel =$this->objLanguage->code2Txt('mod_grades_nosubjects', 'grades', NULL, 'ERROR: mod_grades_nosubjects');
        $noStrandsLabel =$this->objLanguage->code2Txt('mod_grades_nostrands', 'grades', NULL, 'ERROR: mod_grades_nostrands');
        $noContextsLabel =$this->objLanguage->code2Txt('mod_grades_nocontexts', 'grades', NULL, 'ERROR: mod_grades_nocontexts');
        $noGradesLabel =$this->objLanguage->code2Txt('mod_grades_nogrades', 'grades', NULL, 'ERROR: mod_grades_nogrades');
        $errorSubjectLabel = $this->objLanguage->code2Txt('mod_grades_errorsubject', 'grades', NULL, 'ERROR: mod_grades_errorsubject');
        $errorStrandLabel = $this->objLanguage->code2Txt('mod_grades_errorstrand', 'grades', NULL, 'ERROR: mod_grades_errorstrand');
        $errorContextLabel = $this->objLanguage->code2Txt('mod_grades_errorcontext', 'grades', NULL, 'ERROR: mod_grades_errorcontext');
        $errorGradeLabel = $this->objLanguage->code2Txt('mod_grades_errorgrade', 'grades', NULL, 'ERROR: mod_grades_errorgrade');
        $addSubjectLabel = $this->objLanguage->code2Txt('mod_grades_addsubject', 'grades', NULL, 'ERROR: mod_grades_addsubject');
        $addStrandLabel = $this->objLanguage->code2Txt('mod_grades_addstrand', 'grades', NULL, 'ERROR: mod_grades_addstrand');
        $addContextLabel = $this->objLanguage->code2Txt('mod_grades_addcontext', 'grades', NULL, 'ERROR: mod_grades_addcontext');
        $addGradeLabel = $this->objLanguage->code2Txt('mod_grades_addgrade', 'grades', NULL, 'ERROR: mod_grades_addgrade');
        
        $addSchoolLinkLabel = $this->objLanguage->code2Txt('mod_grades_linktoschool', 'grades', NULL, 'ERROR: mod_grades_linktoschool');
        $linkedSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_linkedschools', 'grades', NULL, 'ERROR: mod_grades_linkedschools');
        $selectSchoolLabel = $this->objLanguage->code2Txt('mod_grades_selectschool', 'grades', NULL, 'ERROR: mod_grades_selectschool');
        $schoolLabel = $this->objLanguage->code2Txt('mod_schools_school', 'schools', NULL, 'ERROR: mod_schools_school');
        $errorSchoolLabel = $this->objLanguage->code2Txt('mod_grades_errorschool', 'grades', NULL, 'ERROR: mod_grades_errorschool');
        $addSchoolLabel = $this->objLanguage->code2Txt('mod_grades_addschool', 'grades', NULL, 'ERROR: mod_grades_addschool');
        $addressLabel = $this->objLanguage->languageText('word_address', 'system', 'ERROR: word_address');
        $noSchoolsLabel = $this->objLanguage->code2Txt('mod_grades_noschools', 'grades', NULL, 'ERROR: mod_grades_noschools');

        $array = array('item' => $subjectLabel);
        $deleteSubjectLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $strandLabel);
        $deleteStrandLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $contextLabel);
        $deleteContextLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $gradeLabel);
        $deleteGradeLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        $array = array('item' => $schoolLabel);
        $deleteSchoolLabel = $this->objLanguage->code2Txt('mod_grades_deleteconfirm', 'grades', $array, 'ERROR: mod_grades_deleteconfirm');
        
        $arrayVars = array();
        $arrayVars['no_subject'] = $errorSubjectLabel;
        $arrayVars['no_strand'] = $errorStrandLabel;
        $arrayVars['no_context'] = $errorContextLabel;
        $arrayVars['no_grade'] = $errorGradeLabel;
        $arrayVars['no_school'] = $errorSchoolLabel;
        
        // pass error to javascript.
        $this->objSvars->varsToJs($arrayVars);

        $id = $this->getParam('id');
        $type = $this->getParam('type');
        $tab = $this->getParam('tab', 0);
        $classArray = $this->objDBclasses->getClass($id);
        $linkedSubjectsArray = $this->objDBbridging->getLinkedItems('class_id', 'subject_id', $id);
        $unlinkedSubjectsArray = $this->objDBbridging->getUnlinkedItems('class_id', 'subject_id', $id);
        $linkedStrandsArray = $this->objDBbridging->getLinkedItems('class_id', 'strand_id', $id);
        $unlinkedStrandsArray = $this->objDBbridging->getUnlinkedItems('class_id', 'strand_id', $id);
        $linkedContextsArray = $this->objDBbridging->getLinkedItems('class_id', 'context_id', $id);
        $unlinkedContextsArray = $this->objDBbridging->getUnlinkedItems('class_id', 'context_id', $id);
        $linkedGradesArray = $this->objDBbridging->getLinkedItems('class_id', 'grade_id', $id);
        $unlinkedGradesArray = $this->objDBbridging->getUnlinkedItems('class_id', 'grade_id', $id);
        $linkedSchoolsArray = $this->objDBbridging->getLinkedItems('class_id', 'school_id', $id);
        $unlinkedSchoolsArray = $this->objDBbridging->getUnlinkedItems('class_id', 'school_id', $id);
        
        //-- class fieldset --//        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($nameLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($classArray['name'], '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($descriptionLabel, '200px', 'top', '', 'even', '', '');
        $objTable->addCell($classArray['description'], '', '', '', 'even', '', '');
        $objTable->endRow();
        $classTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($classLabel)) . '</b>';
        $objFieldset->contents = $classTable;
        $classFieldset = $objFieldset->show();

        $string = $classFieldset;
        
        //-- subject tab --//
        $objDrop = new dropdown('subject_id');
        $objDrop->addOption('', $selectSubjectLabel);
        $objDrop->addFromDB($unlinkedSubjectsArray, 'name', 'id');
        $objDrop->setSelected('');
        $subjectsDrop = $objDrop->show();
  
        $objInput = new textinput('class_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_subject');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_subject');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($subjectLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($subjectsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('subject', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'subject_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $subjectForm = $objForm->show();

        if (!empty($linkedSubjectsArray))
        {
            if (!empty($unlinkedSubjectsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjetcsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($subjectLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSubjectsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'subject', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSubjectLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'subjectstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'subjectsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $subjectsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSubjectsArray))
            {
                $noSubjects = $this->error($noSubjectsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('book_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'subject_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addSubjectLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSubjects . '<br />' . $addLink;
                $subjectsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'subjectsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $subjectForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addsubjectlink">' . $linkIcon . '&nbsp;' . $addSubjectLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjectsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'subjectsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $subjectsLayer = $objLayer->show();
            }
        }

        $subjectTabArray = array(
            'name' => ucfirst(strtolower($linkedSubjectLabel)),
            'content' => $subjectsLayer,
        );

        //-- strand tab --//
        $objDrop = new dropdown('strand_id');
        $objDrop->addOption('', $selectStrandLabel);
        $objDrop->addFromDB($unlinkedStrandsArray, 'name', 'id');
        $objDrop->setSelected('');
        $strandsDrop = $objDrop->show();
  
        $objInput = new textinput('class_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_strand');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_strand');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($strandLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($strandsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('strand', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'strand_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $strandForm = $objForm->show();

        if (!empty($linkedStrandsArray))
        {
            if (!empty($unlinkedStrandsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $strandForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjetcsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($strandLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedStrandsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'strand', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteStrandLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $class, '', '');
                $objTable->addCell($value['description'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'strandstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'strandsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $strandsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedStrandsArray))
            {
                $noStrands = $this->error($noStrandsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('book_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'strand_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addStrandLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $noLinks . '<br />' . $noStrands . '<br />' . $addLink;
                $strandsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'strandsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $strandForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addstrandlink">' . $linkIcon . '&nbsp;' . $addStrandLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addstrandsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'strandsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $strandsLayer = $objLayer->show();
            }
        }

        $strandTabArray = array(
            'name' => ucfirst(strtolower($linkedStrandLabel)),
            'content' => $strandsLayer,
        );

        //-- context tab --//
        $objDrop = new dropdown('context_id');
        $objDrop->addOption('', $selectContextLabel);
        $objDrop->addFromDB($unlinkedContextsArray, 'title', 'id');
        $objDrop->setSelected('');
        $contextsDrop = $objDrop->show();
  
        $objInput = new textinput('class_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_context');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_context');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($contextLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($contextsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('context', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'context_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $contextForm = $objForm->show();

        if (!empty($linkedContextsArray))
        {
            if (!empty($unlinkedContextsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addsubjetcsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($contextLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedContextsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'context', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteContextLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['title'], '', '', '', $class, '', '');
                $objTable->addCell($value['about'], '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'contextstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'contextsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $contextsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedContextsArray))
            {
                $noContexts = $this->error($noContextsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('book_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'context_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addContextLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $noLinks . '<br />' . $noContexts . '<br />' . $addLink;
                $contextsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'contextsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $contextForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addcontextlink">' . $linkIcon . '&nbsp;' . $addContextLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addcontextsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'contextsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $contextsLayer = $objLayer->show();
            }
        }

        $contextTabArray = array(
            'name' => ucfirst(strtolower($linkedContextLabel)),
            'content' => $contextsLayer,
        );

        //-- grade tab --//
        $objDrop = new dropdown('grade_id');
        $objDrop->addOption('', $selectGradeLabel);
        $objDrop->addFromDB($unlinkedGradesArray, 'name', 'id');
        $objDrop->setSelected('');
        $gradesDrop = $objDrop->show();
  
        $objInput = new textinput('class_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_grade');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_grade');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($gradeLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($gradesDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('grade', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'grade_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $gradeForm = $objForm->show();

        if (!empty($linkedGradesArray))
        {
            if (!empty($unlinkedGradesArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($gradeLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $descriptionLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedGradesArray as $value)
            {
                $grade = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'grade', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteGradeLabel);
                $deleteIcon = $this->objConfirm->show();

                $objTable->startRow();
                $objTable->addCell($value['name'], '', '', '', $grade, '', '');
                $objTable->addCell($value['description'], '', '', '', $grade, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $grade, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'gradestablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'gradesdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $gradesLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedGradesArray))
            {
                $noGrades = $this->error($noGradesLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('group_add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'type' => 'grade_id')));
                $objLink->link = $addIcon . '&nbsp;' . $addGradeLabel;
                $addLink = $objLink->show();
            
                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $noLinks . '<br />' . $noGrades . '<br />' . $addLink;
                $gradesLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'gradesformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $gradeForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addgradelink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addgradesdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'gradesdiv';
                $objLayer->str = $formLayer . $addLayer;
                $gradesLayer = $objLayer->show();
            }
        }
        
        $gradeTabArray = array(
            'name' => ucfirst(strtolower($linkedGradeLabel)),
            'content' => $gradesLayer,
        );

          //-- school tab --//
        $objDrop = new dropdown('school_id');
        $objDrop->addOption('', $selectSchoolLabel);
        $objDrop->addFromDB($unlinkedSchoolsArray, 'name', 'id');
        $objDrop->setSelected('');
        $schoolsDrop = $objDrop->show();
  
        $objInput = new textinput('class_id', $id, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save_school');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel_school');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($schoolLabel)), '200px', '', '', 'odd', '', '');
        $objTable->addCell($schoolsDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('school', $this->uri(array(
            'action' => 'savelink', 'from' => $type . '_id', 'to' => 'school_id',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $schoolForm = $objForm->show();

        if (!empty($linkedSchoolsArray))
        {
            if (!empty($unlinkedSchoolsArray))
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();
                
                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addGradeLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $addLink;
                $addLayer = $objLayer->show();
            }
            else
            {
                $formLayer = NULL;
                $addLayer = NULL;
            }

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startHeaderRow();
            $objTable->addHeaderCell('<b>' . ucfirst(strtolower($schoolLabel)) . '</b>', '15%', '', 'left', 'heading', '');
            $objTable->addHeaderCell('<b>' . $addressLabel . '</b>', '55%', '', 'left', 'heading', '');            
            $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
            $objTable->endHeaderRow();
            
            $i = 0;
            foreach ($linkedSchoolsArray as $value)
            {
                $class = (($i++ % 2) == 0) ? 'even' : 'odd';
                
                $this->objIcon->setIcon('link_delete', 'png');
                $this->objIcon->title = $deleteLinkLabel;
                $this->objIcon->alt = $deleteLinkLabel;
                $icon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'deletelink', 'type' => $type, 'link' => 'school', 'id' => $id, 'del' => $value['id']));

                $this->objConfirm->setConfirm($icon, $location, $deleteSchoolLabel);
                $deleteIcon = $this->objConfirm->show();

                $temp = explode('|', $value['address']);
                $addressArray = array();
                foreach($temp as $line)
                {
                    if (!empty($line))
                    {
                        $addressArray[] = $line;
                    }
                }
                $addressString = implode(',<br />', $addressArray);
                
                $objTable->startRow();
                $objTable->addCell($value['name'], '', 'top', '', $class, '', '');
                $objTable->addCell($addressString, '', '', '', $class, '', '');
                $objTable->addCell($deleteIcon, '', '', '', $class, '', '');
                $objTable->endRow();
            }
            $linkTable = $objTable->show();
            
            $objLayer = new layer();
            $objLayer->id = 'schoolstablediv';
            $objLayer->str = $linkTable;
            $tableLayer = $objLayer->show();            

            $objLayer = new layer();
            $objLayer->id = 'schoolsdiv';
            $objLayer->str = $formLayer . $addLayer . $tableLayer;
            $schoolsLayer = $objLayer->show();            
        }
        else
        {   
            $noLinks = $this->error($noLinkLabel);
            
            if (empty($unlinkedSchoolsArray))
            {
                $noSchools = $this->error($noSchoolsLabel);

                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('add', 'png');
                $addIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'addoredit', 'mode' => 'add'), 'schools'));
                $objLink->link = $addIcon . '&nbsp;' . $addSchoolLabel;
                $addLink = $objLink->show();
                
                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $noSchools . '<br />' . $addLink;
                $schoolsLayer = $objLayer->show();
            }
            else
            {
                $objLayer = new layer();
                $objLayer->id = 'schoolsformdiv';
                $objLayer->display = 'none';
                $objLayer->str = $schoolForm;
                $formLayer = $objLayer->show();
                
                $this->objIcon->title = $addLabel;
                $this->objIcon->alt = $addLabel;
                $this->objIcon->setIcon('link_add', 'png');
                $linkIcon = $this->objIcon->show();

                $addLink = '<a href="#" id="addschoollink">' . $linkIcon . '&nbsp;' . $addSchoolLinkLabel . '</a>';

                $objLayer = new layer();
                $objLayer->id = 'addschoolsdiv';
                $objLayer->str = $noLinks . '<br />' . $addLink;
                $addLayer = $objLayer->show();

                $objLayer = new layer();
                $objLayer->id = 'schoolsdiv';
                $objLayer->str = $formLayer . $addLayer;
                $schoolsLayer = $objLayer->show();
            }
        }
        
        $schoolTabArray = array(
            'name' => ucfirst(strtolower($linkedSchoolsLabel)),
            'content' => $schoolsLayer,
        );

        $this->objTab->init();
        $this->objTab->tabId = 'links_tab';
        switch ($tab)
        {
            case 'school':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSchoolsLabel));
                break;
            case 'grade':
                $this->objTab->setSelected = ucfirst(strtolower($linkedGradeLabel));
                break;
            case 'subject':
                $this->objTab->setSelected = ucfirst(strtolower($linkedSubjectLabel));
                break;
            case 'strand':
                $this->objTab->setSelected = ucfirst(strtolower($linkedStrandLabel));
                break;
            case 'context':
                $this->objTab->setSelected = ucfirst(strtolower($linkedContextLabel));
                break;
            default:
                $this->objTab->setSelected = 0;
        }
       $this->objTab->addTab($schoolTabArray);
        $this->objTab->addTab($gradeTabArray);
        $this->objTab->addTab($subjectTabArray);
        $this->objTab->addTab($strandTabArray);
        $this->objTab->addTab($contextTabArray);
        $linkTab = $this->objTab->show();

        $string .= $linkTab;        

        return $string;
    }        

    /**
     *
     * Method to return the enter context template
     * 
     * @access public
     * @return string $string The display string 
     */
    public function showEnter()
    {
        $this->appendArrayVar('headerParams',
            $this->getJavaScriptFile('grade_block.js',
            'grades'));

        $selectGradeLabel = $this->objLanguage->code2Txt('mod_grades_selectgrade', 'grades', NULL, 'ERROR: mod_grades_selectgrade');
        $gradeLabel = $this->objLanguage->code2Txt('mod_grades_grade', 'grades', NULL, 'ERROR: mod_grades_grade');
        $enterContextLabel = $this->objLanguage->code2Txt('mod_context_entercourse', 'context', NULL, 'ERROR: mod_context_entercourse');
      
        $gradeArray = $this->objDBgrades->getAll();

        $objDrop = new dropdown('grade_id');
        $objDrop->addOption('', $selectGradeLabel);
        $objDrop->addFromDB($gradeArray, 'name', 'id');
        $objDrop->setSelected('');
        $gradeDrop = $objDrop->show();
  
        $objButton = new button('enter', $enterContextLabel);
        $objButton->setId('enter');
        $objButton->setToSubmit();
        $enterButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($gradeDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $gradeTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($gradeLabel)) . '</b>';
        $objFieldset->contents = $gradeTable;
        $gradeFieldset = $objFieldset->show();
        
        $objLayer = new layer();
        $objLayer->id = 'gradediv';
        $objLayer->str = $gradeFieldset;
        $gradeLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'subjectdiv';
        $subjectLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'contextdiv';
        $contextLayer = $objLayer->show();

        $objLayer = new layer();
        $objLayer->id = 'buttondiv';
        $objLayer->display = 'none';
        $objLayer->str = $enterButton;
        $buttonLayer = $objLayer->show();

        $objForm = new form('enter', $this->uri(array(
            'action' => 'joincontext',
        ), 'context'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($contextLayer);
        $objForm->addToForm($buttonLayer);
        $enterForm = $objForm->show();

        return $gradeLayer . $subjectLayer . $enterForm;
    }

    /**
     *
     * Method to return the subject dropdown via ajax
     * 
     * @access public
     * @return string $string The display string 
     */
    public function ajaxShowSubject()
    {
        $selectSubjectLabel = $this->objLanguage->code2Txt('mod_grades_selectsubject', 'grades', NULL, 'ERROR: mod_grades_selectsubject');
        $subjectLabel = $this->objLanguage->code2Txt('mod_grades_subject', 'grades', NULL, 'ERROR: mod_grades_subject');
        
        $gradeId = $this->getParam('grade_id');
        $subjectArray = $this->objDBbridging->getLinkedItems('grade_id', 'subject_id', $gradeId);

        $objDrop = new dropdown('subject_id');
        $objDrop->addOption('', $selectSubjectLabel);
        $objDrop->addFromDB($subjectArray, 'name', 'subject_id');
        $objDrop->setSelected('');
        $subjectDrop = $objDrop->show();
  
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($subjectDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $subjectTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($subjectLabel)) . '</b>';
        $objFieldset->contents = $subjectTable;
        $subjectFieldset = $objFieldset->show();
        
        echo $subjectFieldset;
        die();
    }
    
    /**
     *
     * Method to return the strand dropdown via ajax
     * 
     * @access public
     * @return string $string The display string 
     */
    public function ajaxShowStrand()
    {
        $selectStrandLabel = $this->objLanguage->code2Txt('mod_grades_selectstrand', 'grades', NULL, 'ERROR: mod_grades_selectstrand');
        $strandLabel = $this->objLanguage->code2Txt('mod_grades_strand', 'grades', NULL, 'ERROR: mod_grades_strand');
        
        $subjectId = $this->getParam('subject_id');
        $strandsArray = $this->objDBbridging->getLinkedItems('subject_id', 'strand_id', $subjectId);

        $objDrop = new dropdown('strand_id');
        $objDrop->addOption('', $selectStrandLabel);
        $objDrop->addFromDB($strandsArray, 'name', 'strand_id');
        $objDrop->setSelected('');
        $strandDrop = $objDrop->show();
  
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($strandDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $strandTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($strandLabel)) . '</b>';
        $objFieldset->contents = $strandTable;
        $strandFieldset = $objFieldset->show();
        
        echo $strandFieldset;
        die();
    }
    
    /**
     *
     * Method to return the context dropdown via ajax
     * 
     * @access public
     * @return string $string The display string 
     */
    public function ajaxShowContext()
    {
        $selectContextLabel = $this->objLanguage->code2Txt('mod_grades_selectcontext', 'grades', NULL, 'ERROR: mod_grades_selectcontext');
        $contextLabel = $this->objLanguage->code2Txt('word_context', 'system', NULL, 'ERROR: word_context');
        
        $strandId = $this->getParam('strand_id');
        $contextArray = $this->objDBbridging->getLinkedItems('strand_id', 'context_id', $strandId);

        $objDrop = new dropdown('contextcode');
        $objDrop->addOption('', $selectContextLabel);
        $objDrop->addFromDB($contextArray, 'title', 'contextcode');
        $objDrop->setSelected('');
        $contextDrop = $objDrop->show();
  
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($contextDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $contextTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . ucfirst(strtolower($contextLabel)) . '</b>';
        $objFieldset->contents = $contextTable;
        $contextFieldset = $objFieldset->show();
        
        echo $contextFieldset;
        die();
    }
}
?>