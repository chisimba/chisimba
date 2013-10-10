
<?php
/*! \file create_new_form_element.php
 * \brief The template file is called by an action createNewFormElement which is
 * called by my AJAX functions that create form element identifiers for their
 * respective form elements.
 * \brief This template file inserts the metadata for each form elment. The metadata includes
 * a unique form element ID or identifier, which form it belongs to, what type of
 * form element it is and in which order in the form it belongs to.
 * \section sec Template Code Explanation
 * - Request all the metadata for the form element from the post from the
  Ajax function and store them into temporary variables.
 * - Depending on the form element type insert the form element metadata into the
 * database.
 * - If there was a successful insertion into the database then spit out a boolean
 * 1. The integer 2 is for if the form element type does not exist. The integer 0 is
 * for when there already exists a form element identifier for that form inside the
 * database ie there is duplicate entry.
 */

//$this->appendArrayVar('headerParams', $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery'));
$formNumber = $this->getParam('formNumber');
$formName = $this->getParam('formName');
$formElementType = $this->getParam('formElementType');
$formElementName = $this->getParam('formElementName');


$objFormEntityHandler = $this->getObject('form_entity_handler', 'formbuilder');

switch ($formElementType) {

    case 'radio':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;

    case 'checkbox':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;

    case 'dropdown':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
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
    case 'button':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    case 'multiselectable_dropdown':
        $postSuccess = $objFormEntityHandler->insertNewFormElement($formNumber, $formName, $formElementType, $formElementName);
        break;
    default:
        $postSuccess = 2;
        break;
}
?>

<html>
    <div id="postSuccess">
<?php
echo $postSuccess;
?>
    </div>

</html>



