<?php
$formElementType =  $this->getParam("formElementType");
$optionID = $this->getParam("optionID");


switch ($formElementType) {

    case 'radio':
        $objRadioEntity = $this->getObject('dbformbuilder_radio_entity', 'formbuilder');
       $objRadioEntity->deleteSingle($optionID);
        break;

    case 'checkbox':
       $objCheckBoxEntity = $this->getObject('dbformbuilder_checkbox_entity', 'formbuilder');
       $objCheckBoxEntity->deleteSingle($optionID);
        break;

    case 'dropdown':
       $objDropdownEntity = $this->getObject('dbformbuilder_dropdown_entity', 'formbuilder');
       $objDropdownEntity->deleteSingle($optionID);
        break;
        case 'button':
       $objButtonEntity = $this->getObject('dbformbuilder_button_entity', 'formbuilder');
       $objButtonEntity->deleteSingle($optionID);
        break;

    case 'multiselectable_dropdown':
       $objMSDropdownEntity = $this->getObject('dbformbuilder_multiselect_dropdown_entity', 'formbuilder');
       $objMSDropdownEntity->deleteSingle($optionID);
        break;
    default:
        $postSuccess = 2;
        break;
}
?>
