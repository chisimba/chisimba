<script type="text/javascript" src="core_modules/htmlelements/resources/datepicker/calendarDateInput.js"></script>
<!--<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_button_entity.js"></script>-->
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_button_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_checkbox_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_datepicker_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_dropdown_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_htmlheading_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_label_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_radio_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_textarea_entity.js"></script>
<script type="text/javascript" src="packages/formbuilder/resources/js/add_edit_textinput_entity.js"></script>

<?php
/*! \file form_element_editor.php
 * \brief The template file is called by an action designWYSIWYGEditor.
 * \note This template file modelled the old WYSIWYG form editor and is
 * not being used. If you want to revert to the old editor then return this
 * template file inside the action designWYSIWYGEditor. 
*/
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());
///Include the jQuery Library

//$this->appendArrayVar('headerParams', $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery'));
//$jQueryUI = '<link type="text/css" href="'.$this->getResourceUri('jquery/jquery-ui-personalized-1.6rc6/theme/ui.all.css', 'jquery').'" rel="Stylesheet" />';
//$jQueryUI = '<script language="JavaScript" src="' . $this->getResourceUri('jquery/jquery-ui-personalized-1.6rc6/jquery-ui-personalized-1.6rc6.min.js', 'jquery'). '" type="text/javascript"></script>';
//$jQueryUICSS = '<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>';
//$jQueryUICSS = '<link href=' . $this->getResourceUri('js/jquery-ui-1.8.4/themes/base/jquery.ui.all.css', 'formbuilder') . '"rel="stylesheet" type="text/css"';
//$jQueryLib = '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
//$jQueryUI = '<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>';
//$jQueryLibUI = '<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>';
//$jQueryLibdialog = '<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/ui/jquery.ui.dialog.js"></script>';
//$this->appendArrayVar('headerParams', $jQueryUICSS);
//$this->appendArrayVar('headerParams', $jQueryLib);
//$this->appendArrayVar('headerParams', $jQueryUI);
//$this->appendArrayVar('headerParams', $jQueryLibUI);
//$this->appendArrayVar('headerParams', $jQueryLibdialog);

///Include Javascript files from the resources/js folder that consist of functionalilty to add and edit form elements
//$addEditRadioEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_radio_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditCheckboxEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_checkbox_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditDropdownEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_checkbox_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditLabelEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_label_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditHTMLHeadingEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_htmlheading_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditDatePickerEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_datepicker_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditTextInputEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_textinput_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditTextAreaEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_textarea_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//$addEditButtonEntity = '<script language="JavaScript" src="' . $this->getResourceUri('js/add_edit_button_entity.js', 'formbuilder') . '" type="text/javascript"></script>';
//
//$this->appendArrayVar('headerParams', $addEditRadioEntity);
//$this->appendArrayVar('headerParams', $addEditCheckboxEntity);
//$this->appendArrayVar('headerParams', $addEditDropdownEntity);
//$this->appendArrayVar('headerParams', $addEditLabelEntity);
//$this->appendArrayVar('headerParams', $addEditHTMLHeadingEntity);
//$this->appendArrayVar('headerParams', $addEditDatePickerEntity);
//$this->appendArrayVar('headerParams', $addEditTextInputEntity);
//$this->appendArrayVar('headerParams', $addEditTextAreaEntity);
//$this->appendArrayVar('headerParams', $addEditButtonEntity);

//$this->appendArrayVar('headerParams', '<script type="text/javascript">jQuery.noConflict();</script>');
//$this->appendArrayVar('headerParams', $this->getJavascriptFile('1.2.2/jquery.maskedinput-1.2.2.js', 'jquery'));
///Instatiate an object of the class form_element_inserter
$formElementsAdderMenu = $this->getObject('form_element_inserter', 'formbuilder');

///Load classes from the htmlelements core module of chisimba
///before you use them.
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('htmlarea', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

///Get form parameters from the form parsed in add_edit_form_parameters.php tpl file
$formLabel = $_REQUEST['formLabel'];
$formCaption = $_REQUEST['formCaption'];
?>
<!--    <body style="font-size:82.5%;">
<STYLE type="" >
    div#formElementsAdderMenu {border: dashed;border-color: coral; border-width: 1px;   text-align: center}
</STYLE>-->
<span id="getFormName">
<?php
///The form title parameter will be used in the jQuery member function
///insertNewFormElement(). This span will allow this parameter to be
///passed into the jQuery code.
echo $formTitle = $_REQUEST['formTitle'];
?>
</span>

<span id="getFormNumber">
<?php
///The form number parameter will be used in the jQuery member function
///insertNewFormElement(). This span will allow this parameter to be
///passed into the jQuery code.
echo $formNumber = $_REQUEST['formNumber'];
///This variable is used in the link with the action
///"buildCurrentForm". The variable needs to be
///trimmed to remove any white space before or after the variable.
$trimmedformNumber = trim("$formNumber");
?>
</span>

<!--<input type="button" value="Rearrange Form Elements" id="Sort"/>
<input type="button" value="Stop Rearranging Form Elements" id="Stop"/>-->
<input type="checkbox" id="sortFormElementsButton" /><label for="sortFormElementsButton">Rearrange Form Elements</label>
<input type="checkbox" id="deleteFormElementsButton" /><label for="deleteFormElementsButton">Delete Form Elements</label>
<div id="formElementsAdderMenu">
<?php
///This div contains an object from the class 'form_element_inserter'
///that models a dropdown which allows the insertion of various form elements
$formElementsAdderMenu = $this->getObject('form_element_inserter', 'formbuilder');
echo $formElementsAdderMenu->showFormElementInserterDropDown() . "<br>";
?>
    <div id="finishAddingFormElementsMenu">

    <?php
///Get an icon object from the class 'geticon' belonging to the htmlelements core 
///chisimba module.
    $iconSelect = $this->getObject('geticon', 'htmlelements');
///Set the icon picture to the ok icon.
    $iconSelect->setIcon('ok');
///Set the alternative text of the icon.
    $iconSelect->alt = "Finish Adding Form Elements";

///Set the text for a link.
    $linkText = "Finish Adding Form Elements and Finalise Form";

///Create a new link to build the current form that accepts a parameter
///from the "getFormNumber" span.
    $mnglink = new link($this->uri(array(
                        'module' => 'formbuilder',
                        'action' => 'buildCurrentForm',
                        "formNumber" => $trimmedformNumber
                    )));

///Set the link text and image.
    $mnglink->link = $iconSelect->show() . " " . $linkText . " " . $iconSelect->show();
///Build the link and show it.
    echo $linkManage = $mnglink->show();
    ?>
    </div>
</div>

<?php
///IMPORTANT NOTE!
///Some Form Elements are built solely using html. PHP and the classes from
/// chisimba core htmlelements module were not used since they lacked some
/// of the simplest HTML attributes that are essential.
///eg. The MAXLENGTH attribute in the textinput and textarea classes are not
///available.
?>

    <div id="labelInputForFormElements">

        <label class="labelForFormTextInput">Enter the a Unique Name for Your Form Element: </label>
        <br> <input type="text" name="formLabel" value="Enter Form Label" maxlength="65" size="50" class="formElementLabel"/>
        <input type="submit" value="Create Form Element" id="submitFormElementName"/>
        <span></span>
    </div>

    <div id="tempdivcontainer"></div>


    <div id="addParametersForButton">
        <div id="stylingForButton">
            <label>Button Parameters Menu</label><br>
            <div id="resetOrSubmitChoiceMenu">
                <br><label for="resetOrSubmitButtonRadio">Select Button Type: </label><br>
                <input type="radio"  name="resetOrSubmitButtonRadio" value="submit" checked>Submit Button (Submit Form)<br>
                <input type="radio"  name="resetOrSubmitButtonRadio" value="reset">Reset Button (Reset All Fields in Form)<br>
            </div>
            <div id="insertLabelForButton">
                <br><label>Insert Label for Button: </label>
                <input type="text" name="buttonLabel" maxlength="100" size="100" class="buttonLabel"/><br>
            </div>

            <input type="submit" value="Set Button Parameters" id="submitButtonParameters"/>
        </div>
        <div id="endOfInsertionButton">
            <br><br><input type="submit" value="Finish Inserting Button Objects" id="submitEndOfInsertionButton"/>
        </div>
        <div id="insertButtonName">
            <label>Insert Name for Button Object: </label>
            <input type="text" name="buttonName" maxlength="100" size="100" class="buttonName"/><br>
            <input type="submit" value="Insert Button Object" id="submitNameforButton"/>
        </div>
        <span></span>
    </div>

    <div id="addParametersforTextArea">
        <div id="stylingForTextArea">
            <label>Text Area Parameters Menu</label><br>
            <div id="setTextAreaSize">
                <label for="textAreaColumnSizeParameter">Set horizontal or column size for text area (between 1-140):</label>
                <input type="text" name="textAreaColumnSizeParameter" value="60" maxlength="3" size="3" class="textAreaColumnSizeMenu"/>
                <br><label for="textAreaRowSizeParameter">Set vertical or row size for text area (between 1-240):</label>
                <input type="text" name="textAreaRowSizeParameter" value="10" maxlength="3" size="3" class="textAreaRowSizeMenu"/>
            </div>
            <div id="setTextAreaText">
                <br><label>Set default text for text area: </label><br>
<?php
//   $ha = $this->newObject('htmlarea','htmlelements');
//   $ha->setName("textAreaText");
//$ha->width ="80%";
//$ha->height ="200px";
//$ha->toolbarSet='DefaultWithoutSave';
//echo $ha->show();
?>
            <textarea name="setDefaulttextArea" rows="8" cols="60">

            </textarea>
        </div>
        <div id="simpleOrAdvancedTextAreaMenu">
            <br><label for="simpleOrAdvancedTextAreaRadio">Select Text Area Type: </label><br>
            <input type="radio"  name="simpleOrAdvancedTextAreaRadio" value="textarea" checked>Simple Text Area (Without Tool Bars)<br>
            <input type="radio"  name="simpleOrAdvancedTextAreaRadio" value="htmlarea">Advanced Text Area (With Tool Bars)<br>
        </div>

        <div id="toolbarChoiceMenu">
            <br><label for="toolbarChoiceRadio">Select Tool Bar for Text Area: </label><br>
            <p> <input type="radio"  name="toolbarChoiceRadio" value="simple" checked>Basic Tool Bar<br>
                <img src="packages/formbuilder/resources/images/simpletoolbar.png" alt="Basic Tool Bar"><br></p>
            <p> <input type="radio"  name="toolbarChoiceRadio" value="DefaultWithoutSave">Default Tool Bar Without Save<br>
                <img src="packages/formbuilder/resources/images/defaultwithoutsavetoolbar.png" alt="Default Without Save Tool Bar"><br></p>
            <p> <input type="radio"  name="toolbarChoiceRadio" value="advanced">Advanced Tool Bar<br>
                <img src="packages/formbuilder/resources/images/advancedtoolbar.png" alt="Advanced Tool Bar"><br></p>
            <p> <input type="radio"  name="toolbarChoiceRadio" value="cms">Content Management System (CMS) Tool Bar<br>
                <img src="packages/formbuilder/resources/images/cmstoolbar.png" alt="CMS Tool Bar"><br></p>
            <p> <input type="radio"  name="toolbarChoiceRadio" value="forms">Forms Tool Bar<br>
                <img src="packages/formbuilder/resources/images/formstoolbar.png" alt="Forms Tool Bar"><br></p>
        </div>
        <input type="submit" value="Set Text Area Parameters" id="submitTextAreaParameters"/>
    </div>
    <div id="endOfInsertionTextArea">
        <br><br><input type="submit" value="Finish Inserting Text Area Objects" id="submitEndOfInsertionTextArea"/>
    </div>

    <div id="insertTextAreaName">
        <label>Insert Name for Text Area Object: </label>
        <input type="text" name="textAreaName" maxlength="100" size="100" class="textAreaName"/><br>
        <input type="submit" value="Insert Text Area Object" id="submitTextforTextArea"/>
    </div>
    <span></span>
</div>


<div id="addParametersForTextInput">
    <div id="StylingForTextInput">
        <label>Text Field Parameters Menu</label>
        <div id="setTextInputSize">
            <label for="textInputSizeParameter">Set character or field size for text input (between 1-150):</label>
            <input type="text" name="textInputSizeParameter" value="20" maxlength="3" size="3" class="textInputSizeMenu"/>

        </div>

        <br><div id="textorPasswordMenu">
            <label for="textorPasswordRadio">Select Text Input Field Type: </label><br>
            <input type="radio"  name="textorPasswordRadio" value="text" checked>Insert Text<br>
            <input type="radio"  name="textorPasswordRadio" value="password">Insert Password<br>
        </div>

        <div id="setTextMenu">
            <br><label for="setDefaultText">Set default text for text input: </label><br>

            <textarea name="setDefaulttext" rows="8" cols="60">
Insert default text to be displayed inside text input field.
            </textarea>
        </div>

        <div id="setMaskedInput">
            <br><label for="textInputSizeParameter">Select and Set Input Mask:</label><br>
            <input type="radio"  name="maskedInputChoice" value="default" checked>No Masked Input<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_number">Number<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_date_us">Date (US Format) : day/month/year<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_date_iso">Date (ISO Foramt) : year-month-day<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_time">Time : hour:minute<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_phone">Phone Number : (000)000-0000<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_ssn">Social Security Number : 000-00-0000<br>
            <input type="radio"  name="maskedInputChoice" value="text input_mask mask_visa">Visa Number : 0000-0000-0000-0000<br>
        </div><br>
        <input type="submit" value="Set Text Field Parameters" id="submitTextFieldParameters"/>
    </div>

    <div id="endOfInsertionTextInput">
        <br><br><input type="submit" value="Finish Inserting Text Input Objects" id="submitEndOfInsertionTextInput"/>
    </div>

    <div id="insertTextInputName">
        <label>Insert Name for Text Input Object: </label>
        <input type="text" name="textInputLabel" maxlength="100" size="100" class="textInputText"/><br>
        <input type="submit" value="Insert Text Input Object" id="submitTextforTextInput"/>
    </div>
    <span></span>
</div>


<div id="addParametersForDatePicker">
    <div id="datePickerParametersContainer">
        <div id="dateFormat">
<?php
    $possibleDateFomats = array(
        'YYYYMMDD', 'YYYY-MM-DD', 'YYYY-DD-MM',
        'YYYY/MM/DD', 'YYYY/DD/MM', 'YYYY-DD-MON',
        'YYYY-MON-DD', 'MM-DD-YYYY', 'MM/DD/YYYY',
        'MON-DD-YYYY');
    $labelDateFormat = new label("Please Select Date Format:");
    $defaultDateFormatRadio = new dropdown("Date_Format");
    foreach ($possibleDateFomats as $thisDateFormat) {
        $defaultDateFormatRadio->addOption($thisDateFormat, $thisDateFormat);
    }
    $defaultDateFormatRadio->setSelected('YYYY-MM-DD');
    echo $labelDateFormat->show() . "<br>";
    echo $defaultDateFormatRadio->show() . "<br><br>";
?>
        </div>
        <div id="defaultDateChoiceForm">
            <?php
            $labelDateChoice = new label("Please Select Choice of a Default Set Date:");
            echo $labelDateChoice->show() . "<br>";
            $defaultDateRadio = new radio("Default Date Choice");
            $defaultDateRadio->addOption("Real Date", "Set the default selected date to real time");
            $defaultDateRadio->addOption("Custom Date", "Customize the default selected date");
            $defaultDateRadio->setBreakSpace("<br>");
            $defaultDateRadio->setSelected('Real Date');
            echo $defaultDateRadio->show();
            ?>
        </div>

        <div id="selectDefaultDate">
<?php
            $labelforDefaultDatePicker = new label("Select a Default Date:", NULL);
            $defaultDatePicker = $this->newObject('datepicker', 'htmlelements');
            $test = 'YYYY-MON-DD';
            $defaultDatePicker->name = 'defaultDateSelection';
            $defaultDatePicker->setDateFormat($test);
            echo $labelforDefaultDatePicker->show();
            echo $defaultDatePicker->show();
?>
        </div><br>
        <input type="submit"  value="Apply Parameters to Date Picker" id="submitDatePickerParameters"/><br>

        <div id="endOfInsertionDatePicker">
            <br><br><input type="submit" value="Finish Inserting Date Picker Object" id="submitEndOfInsertionDatePicker"/>
        </div>
    </div>
    <div id="insertDatePickerName">
        <label class="datePickerLabel">Insert Name or Value for Date Picker Object: </label>
        <input type="text" name="datePickerLabel" maxlength="100" size="100" class="datePickerText"/><br>
        <input type="submit" value="Insert Date Picker Object" id="submitTextforDatePicker"/>

    </div>
    <span></span>
</div>

<div id="addParametersForTextElements">
    <div id="selectLayoutMenu">
        <div id="LayoutMenu">
            <label for="submitFormElementLayout">Select Layout of Text: </label><br>
            <input type="radio"  name="textLayout" value="tab">Insert a tab after the text.<br>
            <input type="radio"  name="textLayout" value="new line">Insert a new line after the text.<br>
            <input type="radio"  name="textLayout" value="normal" checked> Use normal layout.<br>
            <br>
        </div>

        <div id="SizeMenu">
            <label for="submitFormElementLayout">Select Font Size: </label><br>

            <input type="radio"  name="formEntitySize" value="1"><font size="+2"><b>Size 1</b></font> <br>
            <input type="radio"  name="formEntitySize" value="2"><font size="4"><b>Size 2</b></font> <br>
            <input type="radio"  name="formEntitySize" value="3" checked><font size="3"><b>Size 3</b></font> <br>
            <input type="radio"  name="formEntitySize" value="4"><font size="2"><b>Size 4</b></font> <br>
            <input type="radio"  name="formEntitySize" value="5"><font size="1"><b>Size 5 (Normal Text)</b></font> <br>
            <input type="radio"  name="formEntitySize" value="6"><font size="-2"><SUB>Size 6</SUB></font> <br>
            <br>
        </div>

        <div id="AlignMenu">
            <label for="submitFormElementLayout">Select Alignment Type: </label><br>

            <input type="radio"  name="formEntityAlign" value="left" checked><b> Left Align</b><br>
            <input type="radio"  name="formEntityAlign" value="center"><b>Center Align</b><br>
            <input type="radio"  name="formEntityAlign" value="right"><b>Right Align</b><br>

        </div>

        <input type="submit"  value="Apply Layout and Insert Text" id="submitTextLayout"/><br>

        <div id="endOfInsertionText">
            <br><br><input type="submit" value="Finish Inserting Text" id="submitEndOfInsertionText"/>
        </div>
    </div>
    <div id="insertTextMenu">
        <label class="TextName">Insert Name For Text Object: </label>
        <input type="text" name="formEntityText" maxlength="100" size="100" class="formEntityText"/><br>
        <input type="submit" value="Insert Text Object" id="submitTextforFormElement"/>

    </div>


    <span></span>

</div>




<div id="addOptionnValueForFormElements">
    <div id="selectLayoutMenu">
        <label for="submitFormElementLayout">Select Layout of Form Entity: </label><br>
        <div id="LayoutMenu">

            <input type="radio"  name="formEntityLayout" value="tab">Insert a tab space before option.<br>
            <input type="radio"  name="formEntityLayout" value="new line"> Place option in a new line.<br>
            <input type="radio"  name="formEntityLayout" value="normal"checked> Use normal layout.<br>

        </div>
        <div id="setMenuSize">
            <br><label for="menuSizeRadio">Multi-Selectable Drop Down Menu Size :</label><br>
            <input type="radio"  name="menuSizeRadio" value="autofit" >Set menu size to auto-fit all menu values<br>
            <input type="radio"  name="menuSizeRadio" value="custom" checked>Specify Custom Size<br>


        </div>
        <div id="setCustomMenuSize">
            <label for="menuSizeParameter">Set Custom Menu Size (Choose any number greater than 2):</label>
            <input type="text" name="menuSizeParameter" value="2" maxlength="3" size="3" class="menuSize"/>
        </div>

        <input type="submit"  value="Apply Layout and Insert Option" id="submitFormElementLayout"/><br>

        <div id="endOfInsertion">
            <br><br><input type="submit" value="Finish Inserting Options" id="submitEndOfInsetion"/>
        </div>
    </div>
    <div id="insertOptionMenu">
        <label class="formEntityValueOption">Enter a Value for your form element option: </label>
        <input type="text" name="formEntityValue" value="Value" maxlength="35" size="50" class="formEntityValue"/><br>
        <label class="formEntityLabelOption">Enter a Label for your form element option: </label>
        <input type="text" name="formEntityLabel" value="Label" maxlength="70" size="50" class="formEntityLabel"/><br>
        <INPUT class="formEntityDefaultOption"TYPE=CHECKBOX NAME="Default Value">
        <label class="formEntityDefaultOptionLabel">Set this option selected as default. </label><br>

        <input type="submit" value="Insert Option" id="submitOptionforFormElement"/>

    </div>

    <span></span>
</div>
<div id="ajaxCallUrlsHiddenInputs">
<?php
            $ajaxUrlToProduceButton = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditButton";
            $ajaxUrlToProduceCheckbox = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditCheckboxEntity";
            $ajaxUrlToProduceDatePicker = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditDatePickerEntity";
            $ajaxUrlToProduceDropdown = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditDropdownEntity";
            $ajaxUrlToProduceHTMLHeading = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditHTMLHeadingEntity";
            $ajaxUrlToProduceLabel = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditLabelEntity";
            $ajaxUrlToProduceRadio = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditRadioEntity";
            $ajaxUrlToProduceTextArea = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditTextArea";
            $ajaxUrlToProduceTextInput = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditTextInput";

            $hiddenInputToProduceButton = new hiddeninput("urlToProduceButton", $ajaxUrlToProduceButton);
            echo $hiddenInputToProduceButton->show();

            $hiddenInputToProduceCheckbox = new hiddeninput("urlToProduceCheckbox", $ajaxUrlToProduceCheckbox);
            echo $hiddenInputToProduceCheckbox->show();

            $hiddenInputToProduceDatePicker = new hiddeninput("urlToProduceDatePicker", $ajaxUrlToProduceDatePicker);
            echo $hiddenInputToProduceDatePicker->show();

            $hiddenInputToProduceDropdown = new hiddeninput("urlToProduceDropdown", $ajaxUrlToProduceDropdown);
            echo $hiddenInputToProduceDropdown->show();

            $hiddenInputToProduceHTMLHeading = new hiddeninput("urlToProduceHTMLHeading", $ajaxUrlToProduceHTMLHeading);
            echo $hiddenInputToProduceHTMLHeading->show();

            $hiddenInputToProduceLabel = new hiddeninput("urlToProduceLabel", $ajaxUrlToProduceLabel);
            echo $hiddenInputToProduceLabel->show();

            $hiddenInputToProduceRadio = new hiddeninput("urlToProduceRadio", $ajaxUrlToProduceRadio);
            echo $hiddenInputToProduceRadio->show();

            $hiddenInputToProduceTextArea = new hiddeninput("urlToProduceTextArea", $ajaxUrlToProduceTextArea);
            echo $hiddenInputToProduceTextArea->show();

            $hiddenInputToProduceTextInput = new hiddeninput("urlToProduceTextInput", $ajaxUrlToProduceTextInput);
            echo $hiddenInputToProduceTextInput->show();
?>
        </div>
        <p style="border-bottom: 2px dotted #000000; width: 1000px;"></p>
        <style>
/*            div.form1{ float:left;margin: 5px;margin-right: 30px;border: 3px coral solid;}
            div.form2{float:left;clear:both; margin: 5px; border: 3px coral solid;}
            div.form3{float:left; clear:right; margin: 5px; border: 3px coral solid;}*/
        </style>
        <div id="WYSIWYGForm">

            <?php
      $action = $this->getParam("action", NULL);
   if ($action == "editWYSIWYGForm") {
$objFormConstructor = $this->getObject('form_entity_handler','formbuilder');
 $objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options','formbuilder');
 $objDBFormPublishingOptions->unpublishForm($formNumber,NULL);
echo ($objFormConstructor->buildWYSIWYGForm($formNumber));
   }
    ?>
        </div>
        <div id="formElementOrder">

        </div>
<div id="dialog-box" title="Dialogue Box Title">
    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>
    <div id="content"></div>
</div>
    </body>
        <script type="text/javascript">


            jQuery(document).ready(function() {

		jQuery("#dialog-box").dialog({
			autoOpen: false,
                        show: 'clip',
                        hide: 'clip',
                        width:550,
                        resizable: true,
			modal: true,
			buttons: {
				'OK': function() {
					jQuery(this).dialog('close');
				},
				Cancel: function() {
					jQuery(this).dialog('close');
				}
			}
		});

             jQuery("#tempdivcontainer").show();
                jQuery("#addParametersForButton").hide();
                jQuery("#addParametersForTextInput").hide();
                jQuery("#addParametersForDatePicker").hide();
                jQuery("#addParametersForTextElements").hide();
                jQuery("#labelInputForFormElements").hide();
                jQuery("#addOptionnValueForFormElements").hide();
                jQuery("#addParametersforTextArea").hide();
                jQuery("#tempdivcontainer").hide();
                jQuery("#formElementsAdderMenu").show();
                jQuery("#formElementsAdderMenu").children("#finishAddingFormElementsMenu").hide();
jQuery("#sortFormElementsButton").button();
jQuery("#deleteFormElementsButton").button();
                insertNewFormElement();
            }

        );
            function sortFormElements(){

                jQuery("#WYSIWYGForm").children().css("border", "1px dashed red");
                 jQuery("#WYSIWYGForm").children().prepend('<span id="sortSpan" ><span style="clear:left; float:right;" class="ui-icon ui-icon-arrowthick-2-n-s"></span><br></span>');
jQuery("#WYSIWYGForm").children().hover(
 function(){ jQuery("#WYSIWYGForm").children().children('#sortSpan').children().css('background-color','green') },
 function(){ jQuery("#WYSIWYGForm").children().children('#sortSpan').children().css('background-color','white') }
);
                jQuery("#WYSIWYGForm").sortable('enable');
                jQuery("#WYSIWYGForm").sortable({
                    placeholder: 'ui-state-highlight',
                    update: function(event, ui) {
                        var formElementOrder = jQuery("#WYSIWYGForm").sortable('toArray').toString();
                        jQuery("#formElementOrder").empty();
                    jQuery("#formElementOrder").append("Order of div IDs of form Elements:<BR>");
                       jQuery("#formElementOrder").append(formElementOrder);
                    }
                });
            }

            function resetSort(){

                 if (jQuery("#deleteFormElementsButton:checked").val() != "on" )
                     {
           jQuery("#WYSIWYGForm").children().css("border", "0");
                     }
     jQuery("#WYSIWYGForm").children().children().remove('#sortSpan');
                jQuery("#WYSIWYGForm").sortable('disable');
            }

            function updateFormOrder(formNumber)
            {
                 var currentformElementOrder = jQuery("#formElementOrder").html();
                    var newformElementOrder = jQuery("#WYSIWYGForm").sortable('toArray').toString();
                        jQuery("#formElementOrder").empty();
                       jQuery("#formElementOrder").append(newformElementOrder);
                       if (currentformElementOrder != newformElementOrder)
                           {


                              jQuery('#tempdivcontainer').append("<BR>Not Equal<Br>");
  //jQuery('#tempdivcontainer').show();
  var FormElementOrderToPost = {"formElementOrderString": newformElementOrder, "formNumber":formNumber};
      var myurlToUpdateFormElementOrder = "<?php echo html_entity_decode($this->uri(array('action'=>'updateWYSIWYGFormElementOrder'),'formbuilder'));?>";
         //  //var myurlToProduceTextInput = "<?php echo $_SERVER[PHP_SELF]; ?>?module=jqueryajaxproblem&action=produceTextInput";

           jQuery('#tempdivcontainer').load(myurlToUpdateFormElementOrder, FormElementOrderToPost ,function postSuccessFunction(html) {
           //jQuery('#tempdivcontainer').show();
        });
                           }
                           else
                               {
                              jQuery('#tempdivcontainer').append("<BR>Is Equal<Br>");
 // jQuery('#tempdivcontainer').show();
                               }
            }

function deleteFormElements(formNumber)
{

  jQuery("#WYSIWYGForm").children().css("border", "1px dashed red");
    jQuery("#WYSIWYGForm").children().each(function (index, domEle) {
        // domEle == this
        var domFormElementIDs = jQuery(domEle).attr("id");
 jQuery(domEle).prepend('<span id="'+domFormElementIDs+'" class="deleteSpan"><span style="clear:both; float:right;" class="ui-icon ui-icon-scissors"></span><br></span>');
        //jQuery("#tempdivcontainer").append(domIDs+"<br>");
              // jQuery("#tempdivcontainer").show();
//        $(domEle).css("backgroundColor", "yellow");
//        if ($(this).is("#stop")) {
//          $("span").text("Stopped at div index #" + index);
//          return false;
   //     }
      });

jQuery("#WYSIWYGForm").children().hover(
 function(){ jQuery("#WYSIWYGForm").children().children('.deleteSpan').children().css('background-color','red') },
 function(){ jQuery("#WYSIWYGForm").children().children('.deleteSpan').children().css('background-color','white') }
);

 jQuery("#WYSIWYGForm").children().children('.deleteSpan').unbind("click").bind('click',function () {
              var idOfElementToBeDeleted= jQuery(this).parent().attr('id');
    jQuery( "#dialog-box").dialog({ title: 'Confirm Form Element Delete' });
     jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This form element will be permanently deleted and cannot be recovered. Are you sure?</p>');
                   jQuery("#dialog-box").dialog("open");
    jQuery( "#dialog-box" ).dialog( "option", "buttons", {
         "Delete Form Element": function() {
removeSpan(idOfElementToBeDeleted,formNumber);
},  "Cancel": function() {
             jQuery(this).dialog("close");
         }
             //jQuery(this).dialog("close");
         });

    
 });
}

function removeSpan(idOfElementToBeDeleted,formNumber)
{
   jQuery(this).parent().remove();
    //    jQuery('#tempdivcontainer').append("paretn id  :"+idOfElementToBeDeleted);
  //jQuery('#tempdivcontainer').show();
  FormElementIDToPost = {formElementName : idOfElementToBeDeleted,formNumber:formNumber};
      var myurlToDeleteFormElement = "<?php echo html_entity_decode($this->uri(array('action'=>'deleteWYSIWYGFormElement'),'formbuilder'));?>";
         //  //var myurlToProduceTextInput = "<?php echo $_SERVER[PHP_SELF]; ?>?module=jqueryajaxproblem&action=produceTextInput";

           jQuery('#tempdivcontainer').load(myurlToDeleteFormElement, FormElementIDToPost ,function postSuccessFunction(html) {
           //jQuery('#tempdivcontainer').html(html);
           //jQuery('#tempdivcontainer').show();

          var deleteSuccess = jQuery('#tempdivcontainer').html();
          if (deleteSuccess == 1)
              {
                   jQuery("#dialog-box").dialog("close");
      jQuery( "#dialog-box").dialog({ title: 'Delete Successful' });
      jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>Form Element Deleted Succesfully.</p>');
                         jQuery("#dialog-box").dialog("open");
      jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {
              jQuery("#"+idOfElementToBeDeleted).remove();
              jQuery("#dialog-box").dialog("close"); } } );
}
if (deleteSuccess == 0)
    {
                        jQuery("#dialog-box").dialog("close");
      jQuery( "#dialog-box").dialog({ title: 'Delete Successful' });
      jQuery("#dialog-box").html("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 20px 0;'></span>Avoid Inserting Empty\n\
Form Elements.<BR><span class='ui-icon ui-icon-check' style='float:left; margin:0 7px 20px 0;'></span>Empty Form Element Deleted Succesfully.</p>");
                         jQuery("#dialog-box").dialog("open");
      jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {
              jQuery("#"+idOfElementToBeDeleted).remove();
              jQuery("#dialog-box").dialog("close"); } } );
    }
    if (deleteSuccess == 3)
    {
                        jQuery("#dialog-box").dialog("close");
      jQuery( "#dialog-box").dialog({ title: 'Delete Unsuccessful' });
      jQuery("#dialog-box").html("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 20px 0;'></span>This form element does not exist in the database. It may have been created \n\
outside the WYSIWYG interface.<BR><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Form Element Could Not Be Deleted.</p>");
                         jQuery("#dialog-box").dialog("open");
      jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {
             // jQuery("#"+idOfElementToBeDeleted).remove();
              jQuery("#dialog-box").dialog("close"); } } );
    }
    else
        {
//         jQuery("#dialog-box").dialog("close");
//      jQuery( "#dialog-box").dialog({ title: 'Delete Unsuccessful' });
//      jQuery("#dialog-box").html("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 20px 0;'></span>Internal Error. \n\
//Please contact software administrator.<BR><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Form Element Could Not Be Deleted.</p>//");
//                         jQuery("#dialog-box").dialog("open");
//      jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {
//             // jQuery("#"+idOfElementToBeDeleted).remove();
//              jQuery(this).dialog("close"); } } );
        }
});
}

function resetDelete()
{
                    if (jQuery("#sortFormElementsButton:checked").val() != "on" )
                     {
 jQuery("#WYSIWYGForm").children().css("border", "0");
                     }
     jQuery("#WYSIWYGForm").children().children().remove('.deleteSpan');
      jQuery("#WYSIWYGForm").children().children('.deleteSpan').unbind("click");
}

            function insertNewFormElement()
            {
                                var formName = jQuery("#getFormName").html();
                var formNumber = jQuery("#getFormNumber").html();
                formName = jQuery.trim(formName);
                formNumber = jQuery.trim(formNumber);
               // formNumber = "1";
              //  formName="formanme";

         jQuery( "#deleteFormElementsButton" ).button({ disabled: false });
         jQuery( "#deleteFormElementsButton" ).attr('checked',false);
                          jQuery( "#deleteFormElementsButton").button( "refresh" );
jQuery("#deleteFormElementsButton").change(function() {
      if (jQuery("#deleteFormElementsButton:checked").val() == "on" )
          {
              deleteFormElements(formNumber);
          }
          else
              {
                  resetDelete();
              }
});

       jQuery( "#sortFormElementsButton" ).button({ disabled: false });
         jQuery( "#sortFormElementsButton" ).attr('checked',false);
                 jQuery( "#sortFormElementsButton").button( "refresh" );
                 jQuery("#WYSIWYGForm").sortable();
                 jQuery("#WYSIWYGForm").sortable("disable");
jQuery("#sortFormElementsButton").change(function() {
    if (jQuery("#sortFormElementsButton:checked").val() == "on" )
        {
sortFormElements();
        }
        else
            {
             resetSort();
                updateFormOrder(formNumber);
            }
 //var t=jQuery("#sortFormElementsButton").val();
 //jQuery('#tempdivcontainer').append(t);
  //jQuery('#tempdivcontainer').show();
});
                //jQuery('#tempdivcontainer').empty();

                fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Insert Menu","#formElementsAdderMenu");
                if (jQuery("#WYSIWYGForm").children(".witsCCMSFormElementButton").children(":input[type=submit]").length > 0 )
                {
                    jQuery("#formElementsAdderMenu").show();
                    jQuery("#formElementsAdderMenu").children("#finishAddingFormElementsMenu").show("slow");
                }


                jQuery('#input_add_form_elements_drop_down').change(function() {
                    //jQuery("#WYSIWYGForm").sortable( "disable" );
                    if (jQuery('#input_add_form_elements_drop_down').val() == "form_heading")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("HTML_heading",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "label")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("label",formName,formNumber);

                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "radio")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("radio",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "check_box")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("checkbox",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "drop_down")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("dropdown",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "date_picker")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("datepicker",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "text_input")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("text_input",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "text_area")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("text_area",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "button")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("button",formName,formNumber);
                    }
                    if (jQuery('#input_add_form_elements_drop_down').val() == "multiselect_drop_down")
                    {
                        fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Remove Menu");
                        getInputFromFormElementLabel("multiselectable_dropdown",formName,formNumber);
                    }
                });
            }

            
//            function getInputFromFormElementLabel(formelementtype,formname,formnumber)
//            {
//                jQuery("#labelInputForFormElements").children('#submitFormElementName').hide();
//                      var label = jQuery("#labelInputForFormElements").children(".labelForFormTextInput").html();
//var textInput = jQuery("#labelInputForFormElements").children(":input[name=formLabel]").html;
//var span = jQuery("#labelInputForFormElements").children("span");
//
//jQuery(":input[name=formLabel].formElementLabel").attr("value",formnumber+"_"+formname+"_"+formelementtype+"_");
//jQuery(":input[name=formLabel]").html("Enter a unique name for your "+formelementtype+" element:");
//
//var content =jQuery("#labelInputForFormElements").html();
////var content =  label+textInput + span;
//        	//jQuery("#dialog-box").attr("title","testing");
//                jQuery("#dialog-box" ).dialog( "option", "title", 'Form Element Name Menu' );
//               // jQuery("#labelInputForFormElements").children
//        jQuery("#dialog-box").children("#content").html(content);
//
//jQuery("#dialog-box").dialog('open');
//            }
            
            
            function getInputFromFormElementLabel(formelementtype,formname,formnumber)
            {


             jQuery("#labelInputForFormElements").fadeIn(1000);
                jQuery("#labelInputForFormElements span").show();
                jQuery("#labelInputForFormElements input").show();
                jQuery("#labelInputForFormElements label").show();
                jQuery("#addOptionnValueForFormElements").children("span").hide("slow");
                jQuery("#addParametersForTextElements").children("span").hide('slow');
                jQuery("#addParametersForDatePicker").children("span").hide("slow");
                jQuery("#addParametersForTextInput").children("span").hide("slow");
                jQuery("#addParametersForButton").children("span").hide("slow");
                jQuery(".formElementLabel").attr("value",formnumber+"_"+formname+"_"+formelementtype+"_");
                jQuery(".labelForFormTextInput").html("Enter a unique name for your "+formelementtype+" element:");
                jQuery("#submitFormElementName").attr("value","Create new "+formelementtype+" element");
//                jQuery('#Sort').bind('click',sort);
//                jQuery('#Stop').bind('click',reset);
                jQuery('#submitFormElementName').click(function () {
                    var formElementLabel = jQuery('.formElementLabel').val();

                    if (formElementLabel == "")
                    {
                        jQuery("#labelInputForFormElements span").html("<br>A NULL field is not allowed!<br>Please Enter a Unique Name.");
                        jQuery("#labelInputForFormElements span").fadeIn(1500);
                        jQuery("#labelInputForFormElements span").fadeOut(1500);
                    }
                    else
                    {
                        jQuery('#submitFormElementName').unbind();
                        var dataToPost = {"formNumber":formnumber,"formName":formname, "formElementType":formelementtype, "formElementName":formElementLabel};
                        var myurl="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=createNewFormElement";
                        jQuery.ajax({
                            type:"POST",
                            url:myurl,
                            cache:false,
                            data: dataToPost,
                            async: false,
                            success: function postSuccessFunction(html) {
                                jQuery('#tempdivcontainer').html(html);
                                var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
                                jQuery('#tempdivcontainer').empty();
                                if (postSuccessBoolean == 1)
                                {
                                    jQuery("#labelInputForFormElements span").html("<br>A new "+formelementtype+" element has been made named \""+formElementLabel+"\".");
                                    jQuery("#labelInputForFormElements span").fadeIn(500);
                                    jQuery('#tempdivcontainer').append(1);
                                    jQuery("#labelInputForFormElements input").hide();
                                    jQuery("#labelInputForFormElements label").hide();
                                    jQuery("#labelInputForFormElements").fadeOut(7500);
                                }
                                if (postSuccessBoolean == 0)
                                {
                                    jQuery("#labelInputForFormElements span").html(
                                    "<br> ERROR!! A new "+formelementtype+" element has NOT been made. <br> \""+formElementLabel+"\" already exists in the database. Please enter a unique form name.");
                                    jQuery("#labelInputForFormElements span").fadeIn(3500);
                                    jQuery("#labelInputForFormElements input").hide();
                                    jQuery("#labelInputForFormElements label").hide();
                                    insertNewFormElement();
                                    jQuery('#tempdivcontainer').append(0);
                                }
                                if (postSuccessBoolean == 2)
                                {
                                    jQuery("#labelInputForFormElements span").html(
                                    "<br> ERROR!! A new "+formelementtype+" element has NOT been made <br> The form element tpye \""+formelementtype+"\" has not been configured or does not exist. Please Contact Software Administrator");
                                    jQuery("#labelInputForFormElements span").fadeIn(500);
                                    jQuery('#tempdivcontainer').append(2);
                                    jQuery("#labelInputForFormElements input").hide();
                                    jQuery("#labelInputForFormElements label").hide();
                                    jQuery("#labelInputForFormElements").fadeOut(4500);
                                }
                            },
                            complete: function callBackFunction()
                            {
                                if (jQuery('#tempdivcontainer').html() == 1)
                                {
                                    PostSuccess=1;
                                    // jQuery("*").unbind();
                                    //jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").show();



    if (jQuery("#sortFormElementsButton:checked").val() == "on" )
        {
            resetSort();
            updateFormOrder(formnumber);

        }
            jQuery("#sortFormElementsButton").attr('checked', false);
          jQuery( "#sortFormElementsButton").button( "refresh" );
       jQuery( "#sortFormElementsButton" ).button({ disabled: true });
      jQuery( "#sortFormElementsButton" ).unbind();
                  resetDelete();
                              jQuery("#deleteFormElementsButton").attr('checked', false);
          jQuery( "#deleteFormElementsButton").button( "refresh" );
       jQuery( "#deleteFormElementsButton").button({ disabled: true });
      jQuery( "#deleteFormElementsButton" ).unbind();

                                   insertPropertiesForFormElement(formelementtype, formElementLabel);

                                }
                                if(jQuery('#tempdivcontainer').html() == 0)
                                {

                                }
                                if(jQuery('#tempdivcontainer').html() == 2)
                                {
                                    fadeInOrOutFormElementsEditorMenus("#formElementsAdderMenu","Insert Menu","#formElementsAdderMenu");
                                }
                                return true;
                            }
                        });
                    }
                });
                return true;
            }
            function fadeInOrOutFormElementsEditorMenus(divId,option,divPredecessor)
            {
                if(option =="Remove Menu")
                {
                    jQuery(divId).fadeOut(1000);
                    jQuery('#input_add_form_elements_drop_down').val("default");
                }
                if(option =="Insert Menu")
                {
                    jQuery('#input_add_form_elements_drop_down').val("default");
                    jQuery(divId).clone().insertAfter(divPredecessor).remove();
                    return jQuery(divId).fadeIn(1000);
                }
            }


            function insertPropertiesForFormElement(formelementtype, formElementLabel)
            {
                if (formelementtype == "HTML_heading")
                {


                    insertHTMLHeadingParameters(formElementLabel)
                }

                if (formelementtype == "label")
                {
                    insertLabelParameters(formElementLabel);
                }
                if (formelementtype == "radio")
                {
                    insertRadioEntityParameters(formElementLabel);
                }
                if (formelementtype == "checkbox")
                {
                    insertCheckBoxEntityParameters(formElementLabel);
                }
                if (formelementtype == "dropdown")
                {
                    insertDropdownEntityParameters(formElementLabel);
                }
                if (formelementtype == "datepicker")
                {
                    insertDatePickerParameters(formElementLabel);
                }

                if (formelementtype == "text_input")
                {
                    insertTextInputParameters(formElementLabel);
                }
                if (formelementtype == "text_area")
                {
                    insertTextAreaParameters(formElementLabel);
                }
                if (formelementtype == "button")
                {
                    insertButtonParameters(formElementLabel);
                }
                if (formelementtype == "multiselectable_dropdown")
                {

                    insertMSDropdownParameters(formElementLabel);
                }

            }


            function insertMSDropdownParameters(formElementLabel)
            {
                jQuery("#addOptionnValueForFormElements").show();
                jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").show("slow");
                jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#LayoutMenu").hide();
                jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#submitFormElementLayout").show();
                jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setMenuSize").show();
                jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#endOfInsertion").show();
                jQuery("#addOptionnValueForFormElements #submitFormElementLayout").attr("value","Apply Layout and Insert Option for Multi-Selectable Dropdown Menu");
                jQuery("label[for='submitFormElementLayout']").html("Select Layout for Multi-Selectable Drop Down Entity:");
                jQuery("#addOptionnValueForFormElements").children("span").show("slow");
                jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").hide();
                jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").children("#setCustomMenuSize").hide();

                jQuery(".menuSize").bind('keypress keydown keyup',function() {
                    var test = jQuery(".menuSize").val();
                    var numeric1= "0123456789";
                    var test1 = test.charAt(test.length-1);
                    var bool = numeric1.search(test1);
                    if (bool == -1)
                    {
                        var test= test.substr(0,test.length-1);
                    }
                    jQuery(".menuSize").val(test);
                });

                jQuery("#submitFormElementLayout").unbind('click').bind('click',function () {
                    jQuery(".menuSize").unbind('keypress keydown keyup');
                    var menuSize = jQuery(".menuSize").val();


                    if ( jQuery('input:radio[name=menuSizeRadio]:checked').val() == "custom")
                    {

                        if (menuSize < 2)
                        {
                            jQuery("#addOptionnValueForFormElements").children("span").html("\
        <br>Error. The menu size has to greater than 2 label sizes.");
                            jQuery("#addOptionnValueForFormElements").children("span").show("slow");
                        }

                        jQuery("#addOptionnValueForFormElements").children("span").html("\
        <br>The menu size is set to only display \""+menuSize+"\" labels in the menu.<br> \n\
        If there are more than \""+menuSize+"\" labels, a scroll bar will be added automatically.");
                        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
                        jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").show("slow");
                        jQuery("#addOptionnValueForFormElements").children("span").show("slow");

                    }
                    else
                    {

                        jQuery("#addOptionnValueForFormElements").children("span").html("\
        <br>The menu size is set to grow automatically to accomodate and display all the\n\
        values of the drop down menu. <br>No scroll bar will be added.");
                        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
                        jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").show("slow");
                        jQuery("#addOptionnValueForFormElements").children("span").show("slow");

                    }

                });

                jQuery("#submitEndOfInsetion").unbind('click').bind('click',function () {

                    jQuery("#addOptionnValueForFormElements").children("span").html("<br>A Multi-Selectable Dropdown Enitity has been Inserted and Configured.<br>\n\
        Please choose your next Form element.");
                    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#LayoutMenu").show();

                    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
                    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide("slow");
                    jQuery("#addOptionnValueForFormElements #endOfInsertion").hide("slow");
                    //jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").show();
                    jQuery('input[name=Default Value]').attr('checked', false);
                    //jQuery("*").unbind();
                    insertNewFormElement();


                });

                jQuery("input:radio[name=menuSizeRadio]").change(function(){

                    if ( jQuery('input:radio[name=menuSizeRadio]:checked').val() == "custom")
                    {
                        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setCustomMenuSize").show("slow");

                        jQuery(".menuSize").val("2");
                        jQuery(".menuSize").removeAttr("disabled");
                        jQuery("input:radio[name=maskedInputChoice]:eq(1)").attr('checked', "checked");
                    }
                    else
                    {
                        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setCustomMenuSize").hide("slow");

                        jQuery(".menuSize").attr("disabled","disabled");
                        jQuery(".menuSize").val("1");
                        jQuery("input:radio[name=maskedInputChoice]:eq(0)").attr('checked', "checked");

                    }

                });


                if (jQuery(".formEntityDefaultOption:checked").val() == "on" )
                {
                    jQuery('input[name=Default Value]').attr('checked', false);
                }
                //
                jQuery("#submitOptionforFormElement").unbind('click').bind('click',function () {
                    var optionValue = jQuery('.formEntityValue').val();
                    var optionLabel = jQuery('.formEntityLabel').val();
                    //var layoutOption= jQuery('input:radio[name=formEntityLayout]:checked').val();
                    var defaultSelected = jQuery(".formEntityDefaultOption:checked").val();
                    var formElementLabel = jQuery('.formElementLabel').val();
                    var menuSize = jQuery('.menuSize').val();

                    if (optionValue == "" ||optionLabel == "")
                    {
                        jQuery("#addOptionnValueForFormElements").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
                        jQuery("#addOptionnValueForFormElements").children("span").fadeIn(1500);
                        jQuery("#addOptionnValueForFormElements").children("span").fadeOut(1500);
                    }
                    else
                    {
                        var dataToPost = {"optionValue":optionValue, "optionLabel":optionLabel, "formElementName":formElementLabel, "defaultSelected":defaultSelected, "menuSize":menuSize};
                        var myurlToProduceMSDropdown="<?php echo $_SERVER[PHP_SELF]; ?>?module=formbuilder&action=addEditMultiSelectableDropdownEntity";
                // var myurlToProduceDropdown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceDropdown]").val();
                //jQuery('#tempdivcontainer').show();
                jQuery("#submitOptionforFormElement").unbind();
                jQuery("*").unbind();
                jQuery('#tempdivcontainer').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {

                    jQuery('#tempdivcontainer').html(html);

                    var dropdown = jQuery('#tempdivcontainer #WYSIWYGMSDropdown').html();

                    if (dropdown == 0)
                    {
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                        "<br> Error. A new dropdown option \""+optionValue+"\" has NOT been made. <br> \n\
\""+optionValue+"\" option already exists for \""+formElementLabel+"\" dropdown. Please enter a unique option value.");
                        jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").hide("slow");
                        //  if(  jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").is(":visible") == true )
                        //      {
                        jQuery('input[name=Default Value]').attr('checked', false);
                        jQuery('input[name=Default Value]').unbind();
                        //      }
                        insertPropertiesForFormElement("multi selectable dropdown", formElementLabel);
                    }
                    else
                    {
                        if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                        //if (jQuery("#WYSIWYGForm").children("#input_"+formElementLabel).length <= 0)
                        {
                            jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementMultiSelectDropDown"></div>');

                            //jQuery("#WYSIWYGForm").children("#"+formElementLabel).append('<div id =input_'+formElementLabel+'></div>');
                            //jQuery("#WYSIWYGForm").children("#"+formElementLabel).children("#input_"+formElementLabel).replaceWith(dropdown);

                            jQuery("#WYSIWYGForm").children("#"+formElementLabel).append('<div id =input_'+formElementLabel+'></div>');
                            jQuery("#WYSIWYGForm").children("#"+formElementLabel).children("#input_"+formElementLabel).replaceWith(dropdown);
                            jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.");
                        }

                        //            if ($("#WYSIWYGForm #input_"+formElementLabel).length > 0)
                        else
                        {
                            //  jQuery("*").unbind();
                            jQuery("#WYSIWYGForm").children("#"+formElementLabel).children("#input_"+formElementLabel).replaceWith(dropdown);
                            jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.");

                        }

                        if (jQuery(".formEntityDefaultOption:checked").val() =="on")
                        {
                            jQuery("*").unbind();
                            jQuery(".formEntityDefaultOption").unbind();
                            jQuery('input[name=Default Value]').attr('checked', false);
                            // jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").hide();
                            jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.<br>\n\
<br> The option with value \""+optionValue+"\" has been set selected as default.<br>\n\
More options can be selected as default in a multi-selectable dropdown.");
                            //   insertPropertiesForFormElement("dropdown", formElementLabel);

                        }
                        jQuery("*").unbind();
                        insertPropertiesForFormElement("multiselectable_dropdown", formElementLabel);
                    }

                }

            );

            }
        });

    }
</script>
