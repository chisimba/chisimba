<?php

/*! \file insert_form_element.php
 * \brief The template file is called by the action insertFormElement in the controller.php
 * file called through by AJAX within the form_editor.php template file. This template file
 * gets all of the form element inserter form content.
 * \section sec Template Code Explanation
 * - Request the form name and number and the form element type and store them into
 * temporary variables.
 * - Depending on the the form element type will get you the right form element
 * inserter form.
 * -Spit out the form element inserter form content.
 */

$formName = $this->getParam('formName');
$formNumber = $this->getParam('formNumber');
$formElementType = $this->getParam('formElementType');
$objInsertElementFormEntity = $this->getObject('form_element_editor', 'formbuilder');
if (isset($formElementType)) {
    switch ($formElementType) {

        case 'radio':
            echo $objInsertElementFormEntity->showInsertRadioForm($formName);
            break;
        case 'checkbox':
            echo $objInsertElementFormEntity->showInsertCheckboxForm($formName);
            break;
        case 'dropdown':
            echo $objInsertElementFormEntity->showInsertDropDownForm($formName);
            break;
        case 'label':
            echo $objInsertElementFormEntity->showInsertLabelForm($formName);
            break;
        case 'HTML_heading':
            echo $objInsertElementFormEntity->showInsertHTMLHeadingForm($formName);
            break;
        case 'datepicker':
            echo $objInsertElementFormEntity->showInsertDatePickerForm($formName);
            break;
        case 'text_input':
            echo $objInsertElementFormEntity->showInsertTextInputForm($formName);
            break;
        case 'text_area':
            echo $objInsertElementFormEntity->showInsertTextAreaForm($formName);
            break;
        case 'button':
            echo $objInsertElementFormEntity->showInsertButtonForm($formName);
            break;
        case 'multiselectable_dropdown':
            echo $objInsertElementFormEntity->showInsertMSDropDownForm($formName);
            break;
        default:
            echo $postSuccess = 0;
            break;
    }
}
?>
