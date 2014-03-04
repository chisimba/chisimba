<?php

$formNumber = $this->getParam('formNumber');
$formElementName = $this->getParam('formElementName');
$formElementType = $this->getParam('formElementType');
$objInsertElementFormEntity = $this->getObject('form_element_editor', 'formbuilder');
if (isset($formElementType)) {
    switch ($formElementType) {

        case 'witsCCMSFormElementRadio':
            echo $objInsertElementFormEntity->showEditRadioForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementCheckBox':
            echo $objInsertElementFormEntity->showEditCheckBoxForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementDropDown':
            echo $objInsertElementFormEntity->showEditDropDownForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementMultiSelectDropDown':
            echo $objInsertElementFormEntity->showEditMSDropDownForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementButton':
            echo $objInsertElementFormEntity->showEditButtonForm($formNumber, $formElementName);
            break;
        
        
        
        
        //form elements without options
        case 'witsCCMSFormElementLabel':
            echo $objInsertElementFormEntity->showEditLabelForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementHTMLHeading':
            echo $objInsertElementFormEntity->showEditHTMLHeadingForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementDatePicker':
            echo $objInsertElementFormEntity->showEditDatePickerForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementTextInput':
            echo $objInsertElementFormEntity->showEditTextInputForm($formNumber, $formElementName);
            break;
        case 'witsCCMSFormElementTextArea':
            echo $objInsertElementFormEntity->showEditTextAreaForm($formNumber, $formElementName);
            break;
        default:
            echo $postSuccess = 0;
            break;
    }
}
?>
