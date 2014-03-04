<?php
/*! \file list_current_form_publishing_data.php
 * \brief The template file is called by the action listCurrentFormPublishingData in the controller.php
 * file called through by AJAX functions within the list_all_forms.php or the form_editor.php template files. This template file
 * gets html content for adding and editing publishing parameters for a specific form with a
 * form number.
 * \section sec Template Code Explanation
 * - Request the form number store it into
 * a temporary variable.
 * - Construct the publishing indicator and the simple and advanced publishing forms.
 * - Spit them out.
 */
$formNumber = $this->getParam("formNumber", NULL);
if (isset($formNumber)) {


    $objPublishingMenuConstructor = $this->getObject('view_form_list', 'formbuilder');

    $objPublishingMenuConstructor->getPublishingFormParameters($formNumber);
    echo "form number is" . $formNumber;
?>
    <div id="publishingFormOption">
<?php
    echo $objPublishingMenuConstructor->showFormPublishingIndicator();
?>
</div>


<div id="simple">
<?php
    echo $objPublishingMenuConstructor->showSimplePublishingForm();
?>
</div>
<div id="advanced">
<?php
    echo $objPublishingMenuConstructor->showAdvancedPublishingForm();
}
?>
</div>