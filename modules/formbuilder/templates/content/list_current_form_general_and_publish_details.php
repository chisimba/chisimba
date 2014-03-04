<?php
/*! \file list_current_form_general_and_publish_details.php
 * \brief The template file is called by the action listCurrentFormGeneralandPublishingDetails in the controller.php
 * file called through by AJAX functions within the list_all_forms.php template file. This template file
 * gets html content for showing the publishing and general parameters for a specific form with a
 * form number.
 * \section sec Template Code Explanation
 * - Request the form number store it into
 * a temporary variable.
 * - Check whether a form number is published through the publishing indicator. If it is
 * published then spit out the general, simple and advanced publishing details in
 * their respective divs.
 * Otherwise spit out a message saying that the form is not published.
 */

$formNumber = $this->getParam("formNumber", NULL);
if (isset($formNumber)) {


    $objPublishingMenuConstructor = $this->getObject('view_form_list', 'formbuilder');

    $objPublishingMenuConstructor->getPublishingFormParameters($formNumber);
    echo "form nuimber is" . $formNumber;
?>
    <div id="publishingFormIndicator">
<?php
    $test = $objPublishingMenuConstructor->showViewPublishingIndicator($formNumber);
?>

    </div>
    <?php
    if ($test == 1) {
    ?>

    <div id="general">
<?php
        echo $objPublishingMenuConstructor->showViewPublishingGeneralDetails($formNumber);
?>
        </div>

        <div id="simple">
    <?php
        echo $objPublishingMenuConstructor->showViewPublishingSimpleDetails($formNumber);
    ?>
    </div>
    <div id="advanced">
<?php
        echo $objPublishingMenuConstructor->showViewPublishingAdvancedDetails($formNumber);
?>
    </div>
<?php
    } else {
?>
    <div id="general">
    <?php
        echo "<p><span class='ui-icon ui-icon-locked' style='float:left; margin:0 7px 20px 0;'></span>Publishing Parameters
cannot be viewed.</p><p><span class='ui-icon ui-icon-info' style='float:left; margin:0 4px 20px 0;'></span>Publish this form to
view publishing instructions and data.</p>";
    ?>
        </div>
<?php
    }
}
?>
