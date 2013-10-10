<?php
$formElementType =  $this->getParam("formElementType");
$optionID = $this->getParam("optionID");
$formNumber = $this->getParam("formNumber");
$optionValue = $this->getParam('optionValue');
$optionLabel = $this->getParam('optionLabel');
$formElementName = $this->getParam('formElementName');

$layoutOption = $this->getParam('layoutOption');
$defaultSelected = $this->getParam('defaultSelected');

$formElementSize = $this->getParam('formElementSize');

$formElementLabelLayout = $this->getParam('formElementLabelLayout');
$formElementLabel = $this->getParam('formElementLabel');

if ($defaultSelected == "on") {
    $defaultSelected = true;
} else {
    $defaultSelected = false;
}

$postSuccess = "0";
switch ($formElementType) {

    case 'radio':
        $objRadioEntity = $this->getObject('form_entity_radio', 'formbuilder');
        $postSuccess = $objRadioEntity->updateOptionandValue($optionID,$formNumber,$formElementName, $optionLabel, $optionValue, $defaultSelected, $layoutOption,$formElementLabel,$formElementLabelLayout);
        break;

    case 'checkbox':
        $objCheckBoxEntity = $this->getObject('form_entity_checkbox', 'formbuilder');
        $postSuccess = $objCheckBoxEntity->updateCheckBoxOption($optionID,$formNumber,$formElementName, $optionLabel, $optionValue, $defaultSelected,$layoutOption, $formElementLabel, $formElementLabelLayout);
        break;

    case 'dropdown':
       $objDropDownEntity = $this->getObject('form_entity_dropdown', 'formbuilder');
       $objDropDownEntity->createFormElement($formNumber,$formElementName);
       $postSuccess = $objDropDownEntity->updateOptionandValue($optionID,$formNumber,$formElementName, $optionLabel, $optionValue, $defaultSelected, $formElementLabel, $formElementLabelLayout);
        break;
    
    case 'button':
       $objButtonEntity = $this->getObject('form_entity_button', 'formbuilder');        
        $postSuccess = $objButtonEntity->updateFormElement($optionID,$formNumber,$formElementName, $optionValue, $optionLabel, $defaultSelected);
        break;
    case 'label':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'HTML_heading':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'datepicker':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'text_input':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'text_area':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'multiselectable_dropdown':
       $objMSDropDownEntity = $this->getObject('form_entity_multiselect_dropdown', 'formbuilder');
       $objMSDropDownEntity->createFormElement($formNumber,$formElementName);
       $postSuccess = $objMSDropDownEntity->updateOptionandValue($optionID,$formNumber,$formElementName, $optionLabel, $optionValue, $defaultSelected,$formElementSize, $formElementLabel, $formElementLabelLayout);
        break;
    default:
        $postSuccess = 2;
        break;
}

echo $postSuccess;

?>
