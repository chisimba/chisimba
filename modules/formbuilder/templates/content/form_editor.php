<?php

/*! \file form_editor.php
 * \brief The template file is called by the action designWYSIWYGForm in the controller.php.
 * \todo Comment this file for doxygen. This is a very big file to comment with a lot
 * of intricate logic. The developer Salman Noor will comment it soon.
 */
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());
$editFormElementManager = '<script language="JavaScript" src="' . $this->getResourceUri('js/editFormElementManager.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementLabel = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertLabel.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementHTMLHeading = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertHTMLHeading.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementTextInput = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertTextInput.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementTextArea = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertTextArea.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementDatePicker = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertDatePicker.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementCheckBox = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertCheckBox.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementDropDown = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertDropDown.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementMSDropDown = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertMSDropDown.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementButton = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertButton.js', 'formbuilder') . '" type="text/javascript"></script>';
$formElementRadio = '<script language="JavaScript" src="' . $this->getResourceUri('js/insertRadio.js', 'formbuilder') . '" type="text/javascript"></script>';


$editFormElementOptionManager = '<script language="JavaScript" src="' . $this->getResourceUri('js/editFormElementOptionManager.js', 'formbuilder') . '" type="text/javascript"></script>';

$editFormElementLabel = '<script language="JavaScript" src="' . $this->getResourceUri('js/editLabel.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementTextInput = '<script language="JavaScript" src="' . $this->getResourceUri('js/editTextInput.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementTextArea = '<script language="JavaScript" src="' . $this->getResourceUri('js/editTextArea.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementHTMLHeading = '<script language="JavaScript" src="' . $this->getResourceUri('js/editHTMLHeading.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementDatePicker = '<script language="JavaScript" src="' . $this->getResourceUri('js/editDatePicker.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementRadio = '<script language="JavaScript" src="' . $this->getResourceUri('js/editRadio.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementDropDown = '<script language="JavaScript" src="' . $this->getResourceUri('js/editDropDown.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementCheckBox = '<script language="JavaScript" src="' . $this->getResourceUri('js/editCheckBox.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementMSDropDown = '<script language="JavaScript" src="' . $this->getResourceUri('js/editMSDropDown.js', 'formbuilder') . '" type="text/javascript"></script>';
$editFormElementButton = '<script language="JavaScript" src="' . $this->getResourceUri('js/editButton.js', 'formbuilder') . '" type="text/javascript"></script>';

//$jqplotLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jquery.jqplot.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotCSS = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('js/jqplot/jquery.jqplot.css', 'formbuilder').'"';
//$jqplotBarGraphLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.barRenderer.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotAxisLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.categoryAxisRenderer.min.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotPieGraphLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.pieRenderer.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotPntLabelsLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.pointLabels.js', 'formbuilder').'" type="text/javascript"></script>';
//[If browser is IE]; this library needs to be included to jqplot to work

$this->appendArrayVar('headerParams', $editFormElementLabel);
$this->appendArrayVar('headerParams', $editFormElementTextInput);
$this->appendArrayVar('headerParams', $editFormElementTextArea);
$this->appendArrayVar('headerParams', $editFormElementHTMLHeading);
$this->appendArrayVar('headerParams', $editFormElementDatePicker);
$this->appendArrayVar('headerParams', $editFormElementRadio);
$this->appendArrayVar('headerParams', $editFormElementDropDown);
$this->appendArrayVar('headerParams', $editFormElementCheckBox);
$this->appendArrayVar('headerParams', $editFormElementMSDropDown);
$this->appendArrayVar('headerParams', $editFormElementButton);


$this->appendArrayVar('headerParams', $editFormElementOptionManager);
$this->appendArrayVar('headerParams', $editFormElementManager);
$this->appendArrayVar('headerParams', $formElementLabel);
$this->appendArrayVar('headerParams', $formElementHTMLHeading);
$this->appendArrayVar('headerParams', $formElementTextArea);
$this->appendArrayVar('headerParams', $formElementTextInput);
$this->appendArrayVar('headerParams', $formElementDatePicker);
$this->appendArrayVar('headerParams', $formElementCheckBox);
$this->appendArrayVar('headerParams', $formElementDropDown);
$this->appendArrayVar('headerParams', $formElementMSDropDown);
$this->appendArrayVar('headerParams', $formElementButton);
$this->appendArrayVar('headerParams', $formElementRadio);
///[End IF]
//$this->appendArrayVar('headerParams', $jqplotLibrary);
//$this->appendArrayVar('headerParams', $jqplotCSS);
//$this->appendArrayVar('headerParams', $jqplotBarGraphLibrary);
//$this->appendArrayVar('headerParams', $jqplotAxisLibrary);
//$this->appendArrayVar('headerParams', $jqplotPieGraphLibrary);
//$this->appendArrayVar('headerParams', $jqplotPntLabelsLibrary);
?>
<span id="getFormName">
<?php
///The form title parameter will be used in the jQuery member function
///insertNewFormElement(). This span will allow this parameter to be
///passed into the jQuery code.
echo $formTitle = $this->getParam('formTitle', NULL);
?>
</span>

<span id="getFormNumber">
<?php
///The form number parameter will be used in the jQuery member function
///insertNewFormElement(). This span will allow this parameter to be
///passed into the jQuery code.
echo $formNumber = $this->getParam('formNumber', NULL);
?>
</span>
    <?php
    $objSideMenu = $this->getObject('side_menu_handler', 'formbuilder');

    $formEditor = $this->getObject('form_element_editor', 'formbuilder');
    echo $formEditor->showWYSIWYGEditorHeader($formNumber);
    echo "<div>" . $objSideMenu->showSlideMenu() . "<br><br></div>";
    echo $formEditor->showWYSIWYGToolBar();
    ?>
    <div id="WYSIWYGForm" class='ui-widget-content ui-corner-all'style='border:2px solid #CCCCCC;padding:10px 25px 15px 25px;margin: 15px 15px 15px 15px;'>
<?php
    $action = $this->getParam("action", NULL);
    $objFormConstructor = $this->getObject('form_entity_handler', 'formbuilder');
    $objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options', 'formbuilder');

    if ($action == "editWYSIWYGForm") {
//  $objFormConstructor = $this->getObject('form_entity_handler', 'formbuilder');
//  $objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options', 'formbuilder');
        $objDBFormPublishingOptions->unpublishForm($formNumber, NULL);
        echo ($objFormConstructor->buildWYSIWYGForm($formNumber));
    } else {
        echo ($objFormConstructor->buildWYSIWYGForm($formNumber));
    }
?>
</div>

<div id="formElementOrder"></div>



<div id="tempdivcontainer"></div>

<div id="ajaxCallUrlsHiddenInputs">
<?php
//            $ajaxUrlToProduceButton = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditButton";
//
//            $ajaxUrlToProduceCheckbox = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditCheckboxEntity";
//            $ajaxUrlToProduceDatePicker = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditDatePickerEntity";
//            $ajaxUrlToProduceDropdown = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditDropdownEntity";
//            $ajaxUrlToProduceHTMLHeading = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditHTMLHeadingEntity";
//            $ajaxUrlToProduceLabel = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditLabelEntity";
//            $ajaxUrlToProduceRadio = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditRadioEntity";
//            $ajaxUrlToProduceTextArea = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditTextArea";
//            $ajaxUrlToProduceTextInput = $_SERVER[PHP_SELF] . "?module=formbuilder&action=addEditTextInput";
    $this->loadClass('hiddeninput', 'htmlelements');
    $ajaxUrlToEditFormElementOption = html_entity_decode($this->uri(array('action' => 'updateFormElementOption'), 'formbuilder'));
    $ajaxUrlToDeleteFormElementOption = html_entity_decode($this->uri(array('action' => 'deleteFormElementOption'), 'formbuilder'));
        
    $ajaxUrlToProduceButton = html_entity_decode($this->uri(array('action' => 'addEditButton'), 'formbuilder'));
    $ajaxUrlToProduceCheckbox = html_entity_decode($this->uri(array('action' => 'addEditCheckboxEntity'), 'formbuilder'));
    $ajaxUrlToProduceDatePicker = html_entity_decode($this->uri(array('action' => 'addEditDatePickerEntity'), 'formbuilder'));
    $ajaxUrlToProduceDropdown = html_entity_decode($this->uri(array('action' => 'addEditDropdownEntity'), 'formbuilder'));
    $ajaxUrlToProduceHTMLHeading = html_entity_decode($this->uri(array('action' => 'addEditHTMLHeadingEntity'), 'formbuilder'));
    $ajaxUrlToProduceLabel = html_entity_decode($this->uri(array('action' => 'addEditLabelEntity'), 'formbuilder'));
    $ajaxUrlToProduceRadio = html_entity_decode($this->uri(array('action' => 'addEditRadioEntity'), 'formbuilder'));
    $ajaxUrlToProduceTextArea = html_entity_decode($this->uri(array('action' => 'addEditTextArea'), 'formbuilder'));
    $ajaxUrlToProduceTextInput = html_entity_decode($this->uri(array('action' => 'addEditTextInput'), 'formbuilder'));
    $ajaxUrlToInsertFormElement = html_entity_decode($this->uri(array('action' => 'insertFormElement'), 'formbuilder'));
    $ajaxUrlToEditFormElement = html_entity_decode($this->uri(array('action' => 'editFormElement'), 'formbuilder'));
    $ajaxUrlToCreateANewFormElement = html_entity_decode($this->uri(array('action' => 'createNewFormElement'), 'formbuilder'));
    $ajaxUrlToCreateMSDropdown = html_entity_decode($this->uri(array('action' => 'addEditMultiSelectableDropdownEntity'), 'formbuilder'));
    $ajaxUrlToCreateHelpContent = html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder'));

    $hiddenInputToEditFormElementOption = new hiddeninput("urlToEditFormElementOption", $ajaxUrlToEditFormElementOption);
    echo $hiddenInputToEditFormElementOption->show();
    
    $hiddenInputToDeleteFormElementOption = new hiddeninput("urlToDeleteFormElementOption", $ajaxUrlToDeleteFormElementOption);
    echo $hiddenInputToDeleteFormElementOption->show();
    
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

    $hiddenInputToInsertFormElement = new hiddeninput("urlToInsertFormElement", $ajaxUrlToInsertFormElement);
    echo $hiddenInputToInsertFormElement->show();
    
    $hiddenInputToEditFormElement = new hiddeninput("urlToEditFormElement", $ajaxUrlToEditFormElement);
    echo $hiddenInputToEditFormElement->show();
    
    $hiddenInputToCreateANewFormElement = new hiddeninput("urlToCreateANewFormElement", $ajaxUrlToCreateANewFormElement);
    echo $hiddenInputToCreateANewFormElement->show();
    $hiddenInputToProduceMSDropDown = new hiddeninput("urlToProduceMSDropDown", $ajaxUrlToCreateMSDropdown);
    echo $hiddenInputToProduceMSDropDown->show();
    $hiddenInputToProduceHelpContent = new hiddeninput("urlToProduceHelpContent", $ajaxUrlToCreateHelpContent);
    echo $hiddenInputToProduceHelpContent->show();
?>
</div>


<div id="dialog-box" title="Dialogue Box Title">
    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>
    <div id="content"></div>
</div>

<div id="dialog-box-formElements" title="Dialogue Box Title">
    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>
    <div id="content"></div>
</div>

<div id="dialog-box-editFormElements" title="Dialogue Box Title">
    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>
    <div id="content"></div>
</div>

<div id="dialog-box-formElementsAdvanced" title="Dialogue Box With Tabs">

    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>

    <div id="FormElementInserterTabs">
        <ul>
            <li><a href="#simpleInsertForm">Simple (Without Tool Bars)</a></li>
            <li><a href="#advancedInsertForm">Advanced (With Tool Bars)</a></li>

        </ul>
        <div id="simpleInsertForm">
        </div>
        <div id="advancedInsertForm">
        </div>
    </div>
</div>


<div id="dialog-editPublishingParameters" title="Form Publishing Parameters">

    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>


    <div id="publishingFormOption">
<?php
// echo $objFormList->showFormPublishingIndicator();
?>
    </div>
    <div id="editPublishingFormTabs">
        <ul>
            <li><a href="#simpleEditPublishingForm">Simple</a></li>
            <li><a href="#advancedEditPublishingForm">Advanced</a></li>

        </ul>
        <div id="simpleEditPublishingForm">
<?php
//    echo $objFormList->showSimplePublishingForm();
?>
        </div>
        <div id="advancedEditPublishingForm">
            <?php
//   echo $objFormList->showAdvancedPublishingForm();
            ?>
        </div>

    </div>
</div>
<div id="dialog-box-formElementsHelp" title="Form Element Explaination">

    <div id="FormElementInserterTabs">
        <ul>
            <li><a href="#formElementExplaination">Form Element Explication</a></li>
            <li><a href="#formElementInserter">Form Element Inserter Modal Window</a></li>

        </ul>
        <div id="formElementExplaination">

        </div>
        <div id="formElementInserter">

        </div>
    </div>
</div>


<div id="dialog-box-generalHelp" title="WYSIWYG Form Editor Help">
    <div id="helpContent">

        <ul>
            <li><a href="#formEditor">Form Editor</a></li>
            <li><a href="#formPublisher">Form Publisher</a></li>

        </ul>
        <div id="formEditor">
<?php
            $content = $this->getObject('help_page_handler', 'formbuilder');
            echo $pageContent = $content->showContent('formeditor', 1);
?>
        </div>
        <div id="formPublisher">
            <?php
            echo $pageContent = $content->showContent('formpublisher', 1);
            ?>
        </div>

    </div>
</div>

<script type="text/javascript">
    function setUpDialogueBox()
    {
        jQuery("#dialog-box-formElementsHelp").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            zIndex: 3999,
            width:1000,
            resizable: true,
            modal: true,
            closeOnEscape: true,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                    jQuery('html, body').animate({scrollTop:0}, 'slow');
                }
            }
        });
        jQuery("#dialog-box-generalHelp").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            zIndex: 3900,
            width:1050,
            resizable: true,
            modal: true,
            closeOnEscape: true,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                    jQuery('html, body').animate({scrollTop:0}, 'slow');
                }
            }
        });

        jQuery("#dialog-box").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            width:750,
            resizable: true,
            modal: true,
            closeOnEscape: false,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                },
                Cancel: function() {
                    jQuery(this).dialog('close');
                }
            }
        });

        jQuery("#dialog-box-editFormElements").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            width:950,
            resizable: true,
            modal: true,
            closeOnEscape: false,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                },
                Cancel: function() {
                    jQuery(this).dialog('close');
                }
            },
close: function(event, ui) {
}
        });
        
        jQuery("#dialog-box-formElements").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            width:950,
            resizable: true,
            modal: true,
            closeOnEscape: false,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                },
                Cancel: function() {
                    jQuery(this).dialog('close');
                }
            },
close: function(event, ui) {
 jQuery("#sortFormElementsButton .ui-button").css('width','50px');
 jQuery("#deleteFormElementsButton .ui-button").css('width','50px');
 jQuery('#sortFormElementsButton .ui-button-text').css('width','50px');
}
        });

        jQuery("#dialog-box-formElementsAdvanced").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            width:1050,
            resizable: true,
            modal: true,
            closeOnEscape: false,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                },
                Cancel: function() {
                    jQuery(this).dialog('close');
                }
            }
        });
        jQuery("#dialog-editPublishingParameters").dialog({
            resizable: true,
            width:740,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Close': function() {
                    jQuery(this).dialog('close');
                }
            }
        });
    }




    function setUpFormElementModalHelp(formElementType)
    {
        var dataToPost = {"contentType":formElementType};
        var myurlToCreateButtonHelp = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceHelpContent]").val();
        jQuery('#tempdivcontainer').empty();
        jQuery('#tempdivcontainer').load(myurlToCreateButtonHelp , dataToPost ,function postSuccessFunction(html) {
            jQuery('#tempdivcontainer').hide();

            var firstTabContent = jQuery('#tempdivcontainer').children('#firstTab').html();
            var secondTabContent = jQuery('#tempdivcontainer').children('#secondTab').html();
            jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').html(firstTabContent);
            jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementInserter').html(secondTabContent);
            jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').tabs();
            jQuery('#dialog-box-formElementsHelp').dialog('open');
            var  btnOK = jQuery('.ui-dialog-buttonpane').find('button:contains("OK")');
            btnOK.css('float', 'right');
                        jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').children('#dpContainer').children("#datepicker").addClass("myDatePicker");
//             $("#ui-datepicker-div").css("z-index", "9999"); 
            jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').children('#dpContainer').children("#datepicker").datepicker({
               dateFormat: 'yy-mm-dd'
//               showOn: 'button',
//                buttonImage: 'packages/formbuilder/resources/images/userManual/calendar.gif',
//                buttonImageOnly: true
            });
//            	jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').children('#dpContainer').children("#datepicker").live('click', function() {
//		jQuery(this).datepicker({showOn:'focus'}).focus();
//	});

        });
    }

    function setUpMainMenuIcons()
    {

        jQuery('.homeButton').button({
            text: true,
            icons: {
                primary: 'ui-icon-home'
            }
        }).next().button({
            icons: {
                primary: 'ui-icon-script'

            },
            text: true
        }).next().button({
            icons: {
                primary: 'ui-icon-document'

            },
            text: true
        }).next().button({
            icons: {
                // primary: 'ui-icon-seek-next',
                primary: 'ui-icon-help'
            },
            text: true
        });
        jQuery(".helpButton").removeAttr('onclick');
        jQuery("#dialog-box-generalHelp").children('#helpContent').tabs();
        jQuery(".helpButton").unbind("click").bind('click',function () {

            jQuery("#dialog-box-generalHelp").dialog('open');
            jQuery(".interactiveFormElementButtons").button();
            jQuery(".interactiveFormElementButtons").unbind('click').bind('click',function () {
                var pageContentType= jQuery(this).attr('name');
                var dataToPost = {"contentType":pageContentType};
                var myurlToGetFormElementContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
                //jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
                jQuery("#tempdivcontainer").empty();
                jQuery("#tempdivcontainer").load(myurlToGetFormElementContent , dataToPost ,function postSuccessFunction(html) {
                    jQuery("#tempdivcontainer").hide();

                    var firstTabContent = jQuery("#tempdivcontainer").children('#firstTab').html();
                    var secondTabContent = jQuery("#tempdivcontainer").children('#secondTab').html();

                    jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').html(firstTabContent);
                    jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementInserter').html(secondTabContent);

                    jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').tabs();
                    jQuery('#dialog-box-formElementsHelp').children('#FormElementInserterTabs').children('#formElementExplaination').children('#dpContainer').children("#datepicker").datepicker({
                        showOn: 'button',
                        buttonImage: 'packages/formbuilder/resources/images/userManual/calendar.gif',
                        buttonImageOnly: true

                    });

                    jQuery('#dialog-box-formElementsHelp').dialog('open');

                });
            });


        });

    }
    
    jQuery(document).ready(function() {

        jQuery("#tempdivcontainer").hide();
        jQuery("#formElementOrder").hide();
        jQuery("#getFormName").hide();
        jQuery("#getFormNumber").hide();
        setUpDialogueBox();
        insertFormElement();
 jQuery("#sortFormElementsButton .ui-button").css('margin','50px');
 jQuery("#deleteFormElementsButton .ui-button").css('margin','50px');
 jQuery("#toolbar").children("#sortFormElementsToggleContainer").children(".ui-button").css('margin-top','15px');
    });



    function setUpEditPublishingParametersMenu()
    {

        //  jQuery(".editPublishingDataButton").unbind('click').bind('click',function () {
        //var formNumber= jQuery(this).attr('name');
        var formNumber = jQuery("#getFormNumber").html();
        formNumber = jQuery.trim(formNumber);
        var dataToPost = {"formNumber":formNumber};
        var myurlToProduceEditPublishingDataMenu ="<?php echo html_entity_decode($this->uri(array('action' => 'listCurrentFormPublishingData'), 'formbuilder')); ?>";
        jQuery("#tempdivcontainer").load(myurlToProduceEditPublishingDataMenu , dataToPost ,function postSuccessFunction(html) {
            jQuery("#tempdivcontainer").hide();
            var publishingIndictor = jQuery("#tempdivcontainer").children("#publishingFormOption").html();
            var simplePublishingForm = jQuery("#tempdivcontainer").children("#simple").html();
            var advancedPublishingForm = jQuery("#tempdivcontainer").children("#advanced").html();
            jQuery("#dialog-editPublishingParameters").children("#publishingFormOption").html(publishingIndictor);
            jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").children("#simpleEditPublishingForm").html(simplePublishingForm);
            jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").children("#advancedEditPublishingForm").html(advancedPublishingForm);
            jQuery("#dialog-editPublishingParameters").children(".errorMessageDiv").html("");
            jQuery("#tempdivcontainer").empty();
            jQuery("#dialog-editPublishingParameters").dialog("open");
            jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs();
            var simpleOrAdvancedTab =jQuery("#dialog-editPublishingParameters").children("#publishingFormOption").children('input:hidden[name=simpleOrAdvancedHiddenInput]').val();
            if (simpleOrAdvancedTab == 'advanced')
            {
                jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs('select', 1);
            }else
            {
                jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs('select', 0);
            }
            var simpleFormUrlChoice =  jQuery('input[name=urlChoice]');
            var advancedNextActionModule = jQuery(':input[name=nextActionModule]');
            var advancedNextAction = jQuery(':input[name=nextAction]');

            var allFields = jQuery([]).add(simpleFormUrlChoice).add(advancedNextActionModule).add(advancedNextAction);

            jQuery(":input:radio[name=publishingRadio]").button();
            jQuery(":input:radio[name=simplePostActionRadio]").button();
            jQuery("input:radio[name=simpleDivertDelayRadio]").button();
            jQuery("input:radio[name=advancedDivertDelayRadio]").button();
            if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
            {
                jQuery("#editPublishingFormTabs").hide("slow");
            }
            else
            {
                jQuery("#editPublishingFormTabs").show("slow");
            }

            jQuery("input:radio[name=publishingRadio]").change(function(){

                if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
                {
                    jQuery("#editPublishingFormTabs").hide("slow");
                }
                else
                {
                    jQuery("#editPublishingFormTabs").show("slow");
                }

            });
            if ( jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
            {
                jQuery("#urlInserter").hide("slow");
                simpleFormUrlChoice.val("")
            }
            else
            {
                jQuery("#urlInserter").show("slow");
            }
            jQuery("input:radio[name=simplePostActionRadio]").change(function(){

                if ( jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
                {
                    jQuery("#urlInserter").hide("slow");
                    simpleFormUrlChoice.val("")
                }
                else
                {
                    jQuery("#urlInserter").show("slow");
                }

            });

            jQuery( "#dialog-editPublishingParameters" ).dialog( "option", "buttons", {
                "Set Publishing Parameters": function() {
                    var selected =   jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs( "option", "selected" );
                    if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
                    {
                        addeditPublishingParameters(formNumber, '','',"","","");
                        jQuery("#dialog-editPublishingParameters" ).dialog('close');
                    }
                    else
                    {
                        if (selected == 0)
                        {
                            var bValid = true;
                            if (jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
                            {
                                var publishingOption = "simple";
                                var urlChoice = "";
                                jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                addeditPublishingParameters(formNumber, publishingOption, urlChoice,"","","",'');
                            }
                            else
                            {
                                allFields.removeClass('ui-state-error');
                                bValid = bValid && checkLength(simpleFormUrlChoice,"the dirverting site url",4,550);
                                bValid = bValid && checkRegexp(simpleFormUrlChoice,/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,"Enter a valid url, eg. http://www.witsccmsformbuilder.ac.za");
                                if (bValid) {
                                    jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                    var publishingOption = "simple";
                                    var urlChoice = simpleFormUrlChoice.val();
                                    var delay =jQuery(':input:radio[name=simpleDivertDelayRadio]:checked').val();
                                    addeditPublishingParameters(formNumber, publishingOption, urlChoice,"","","",delay);

                                }
                            }
                        }
                        else
                        {
                            var bValid = true;
                            allFields.removeClass('ui-state-error');
                            bValid = bValid && checkLength(advancedNextActionModule,"chisimba module",1,550);
                            bValid = bValid && checkLength(advancedNextAction,"next chisimba action",1,550);
                            if (bValid) {
                                jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                var publishingOption = "advanced";
                                if (jQuery('input[name=chisimbaParameters]:checked').val() ==  'on')
                                {
                                    var  chisimbaParameters = "yes";
                                }
                                else
                                {
                                    chisimbaParameters = "no";
                                }

                                var chisimbaModule = advancedNextActionModule.val();
                                var chisimbaNextAction = advancedNextAction.val();
                                var delay =jQuery('input:radio[name=advancedDivertDelayRadio]:checked').val();
                                addeditPublishingParameters(formNumber, publishingOption, '',chisimbaNextAction,chisimbaModule,chisimbaParameters,delay);

                            }
                        }
                    }

                },  "Cancel": function() {
                    jQuery(this).dialog("close");
                }
            });


        });
        //});



    }

    function addeditPublishingParameters(formNumber, publishingOption, urlChoice,chisimbaAction,chisimbaModule,formParameters,divertDelay)
    {

        var dataToPost = {"formNumber":formNumber, "publishingOption":publishingOption, "urlChoice":urlChoice,"chisimbaAction": chisimbaAction,"chisimbaModule":chisimbaModule,"formParameters":formParameters, "divertDelay":divertDelay };
        var myurlToAddEditPublishingData ="<?php echo html_entity_decode($this->uri(array('action' => 'addEditFormPublishingData'), 'formbuilder')); ?>";
        jQuery("#tempdivcontainer").load(myurlToAddEditPublishingData , dataToPost ,function postSuccessFunction(html) {
            jQuery("#tempdivcontainer").hide();
            jQuery("#tempdivcontainer").html(html);

            var myurlToGotToFormListings = "<?php echo html_entity_decode($this->uri(array('action' => 'listAllForms'), 'formbuilder')); ?>";

            window.location.replace(myurlToGotToFormListings);
        });

    }

    function setUpDoneButton()
    {
        jQuery(".finishDesigningForm").button({

            icons: {
                primary: 'ui-icon-check'
            },
            text: true
        });
        jQuery(".finishDesigningForm").unbind("click").bind('click',function () {
            var formNumber = jQuery("#getFormNumber").html();
            formNumber = jQuery.trim(formNumber);
            unsetRearrangeAndDeleteElementButtons(formNumber);
            setUpRearrangeAndDeleteElementButtons(formNumber);
            if (jQuery("#WYSIWYGForm").children(".witsCCMSFormElementButton").children(":input[type=submit]").length > 0 )
            {
                jQuery( "#dialog-box").dialog({ title: 'Finalize Form Details' });
                jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>This form seems to be constructed properly\n\
and is complete.</p><p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 35px 0;"></span>To make your form\n\
ready for submission, please publish it. Additionally,you may want to set what actions to perform when your form is submitted in the publishing options menu.</p>');
                jQuery("#dialog-box").dialog("open");
                jQuery( "#dialog-box" ).dialog( "option", "buttons", {
                    "Publish Form": function() {
                        // var myurlToGotToFormListings = "<?php echo html_entity_decode($this->uri(array('action' => 'listAllForms'), 'formbuilder')); ?>";
                        jQuery(this).dialog("close");
                        setUpEditPublishingParametersMenu();
                        //        window.location.replace(myurlToGotToFormListings);
                    },  "Close": function() {
                        jQuery(this).dialog("close");
                    }
                });

            }
            else
            {
                jQuery( "#dialog-box").dialog({ title: 'Form is Incomplete' });
                jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This form is incomplete as it does not\n\
have a submit button. Are you sure you want to continue with an incomplete form?</p>');
                jQuery("#dialog-box").dialog("open");
                jQuery( "#dialog-box" ).dialog( "option", "buttons", {
                    "Yes": function() {

                        jQuery(this).dialog("close");

                        jQuery( "#dialog-box").dialog({ title: 'Finalize Form Details' });
                        jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This form does not have a\n\
submit button and therefore is incomplete.</p><p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 35px 0;"></span>However, you\n\
can complete this form any time in the future. Search for your form in the form listings and select the option to \n\
edit your form.</p>');
                        jQuery("#dialog-box").dialog("open");
                        jQuery( "#dialog-box" ).dialog( "option", "buttons", {
                            "Publish Form": function() {
                                // var myurlToGotToFormListings = "<?php echo html_entity_decode($this->uri(array('action' => 'listAllForms'), 'formbuilder')); ?>";

                                //window.location.replace(myurlToGotToFormListings);
                                jQuery(this).dialog("close");
                                setUpEditPublishingParametersMenu();
                            },  "Close": function() {
                                jQuery(this).dialog("close");
                            }
                        });
                    },  "No": function() {
                        jQuery(this).dialog("close");
                    }
                });
            }
        });
    }
    function sortFormElements(){

        jQuery("#WYSIWYGForm").children().css("border", "1px dashed red");
        jQuery("#WYSIWYGForm").children().prepend('<span id="sortSpan" ><span style="clear:none; float:right;" class="ui-icon ui-icon-arrowthick-2-n-s"></span><br></span>');
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

        if (jQuery("#deleteFormElementsButton:checked").val() != "on" && jQuery("#editFormElementsButton:checked").val() != "on" )
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
            var FormElementOrderToPost = {"formElementOrderString": newformElementOrder, "formNumber":formNumber};
            var myurlToUpdateFormElementOrder = "<?php echo html_entity_decode($this->uri(array('action' => 'updateWYSIWYGFormElementOrder'), 'formbuilder')); ?>";

            jQuery('#tempdivcontainer').load(myurlToUpdateFormElementOrder, FormElementOrderToPost ,function postSuccessFunction(html) {
            });
        }
        else
        {
            jQuery('#tempdivcontainer').append("<BR>Is Equal<Br>");
        }
    }

    function deleteFormElements(formNumber)
    {

        jQuery("#WYSIWYGForm").children().css("border", "1px dashed red");
        jQuery("#WYSIWYGForm").children().each(function (index, domEle)
        {
            var domFormElementIDs = jQuery(domEle).attr("id");
            jQuery(domEle).prepend('<span id="'+domFormElementIDs+'" class="deleteSpan"><span style="clear:none; float:right;" class="ui-icon ui-icon-scissors"></span><br></span>');
        });

        jQuery("#WYSIWYGForm").children().children('.deleteSpan').hover(
        function()
        {
            jQuery(this).children().css('background-color','red');
        },
        function()
        {
            jQuery("#WYSIWYGForm").children().children().children().css('background-color','white')
        }
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
            });
        });
    }

    function removeSpan(idOfElementToBeDeleted,formNumber)
    {
        jQuery(this).parent().remove();
        FormElementIDToPost = {formElementName : idOfElementToBeDeleted,formNumber:formNumber};
        var myurlToDeleteFormElement = "<?php echo html_entity_decode($this->uri(array('action' => 'deleteWYSIWYGFormElement'), 'formbuilder')); ?>";
        jQuery('#tempdivcontainer').load(myurlToDeleteFormElement, FormElementIDToPost ,function postSuccessFunction(html) {
            var deleteSuccess = jQuery('#tempdivcontainer').html();
            if (deleteSuccess == 1)
            {
                jQuery("#dialog-box").dialog("close");
                jQuery( "#dialog-box").dialog({ title: 'Delete Successful' });
                jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>Form Element Deleted Succesfully.</p>');
                jQuery("#dialog-box").dialog("open");
                jQuery( "#dialog-box" ).bind( "dialogbeforeclose", function(event, ui) {
                    jQuery("#"+idOfElementToBeDeleted).remove();
                });
                jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {
                        //   jQuery("#"+idOfElementToBeDeleted).remove();
                        jQuery("#dialog-box").dialog("close"); } } );
            }
            if (deleteSuccess == 0)
            {
                jQuery("#dialog-box").dialog("close");

                jQuery( "#dialog-box").dialog({ title: 'Delete Successful' });
                jQuery("#dialog-box").html("<p><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 20px 0;'></span>Avoid Inserting Empty\n\
Form Elements.<BR><span class='ui-icon ui-icon-check' style='float:left; margin:0 7px 20px 0;'></span>Empty Form Element Deleted Succesfully.</p>");
                jQuery( "#dialog-box" ).bind( "dialogbeforeclose", function(event, ui) {
                    jQuery("#"+idOfElementToBeDeleted).remove();
                });
                jQuery("#dialog-box").dialog("open");
                jQuery( "#dialog-box" ).dialog( "option", "buttons", { "Ok": function() {

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
                        jQuery("#dialog-box").dialog("close"); } } );
            }
            else
            {

            }
        });
    }

    function resetDelete()
    {
        if (jQuery("#sortFormElementsButton:checked").val() != "on" && jQuery("#editFormElementsButton:checked").val() != "on" )
        {
            jQuery("#WYSIWYGForm").children().css("border", "0");
        }
        jQuery("#WYSIWYGForm").children().children().remove('.deleteSpan');
        jQuery("#WYSIWYGForm").children().children('.deleteSpan').unbind("click");
    }
    
    function setUpRearrangeAndDeleteElementButtons(formNumber)
    {

        jQuery("#sortFormElementsButton").unbind();
        jQuery("#deleteFormElementsButton").unbind();
        jQuery("#sortFormElementsButton").button({icons: {
                primary: "ui-icon-arrowthick-2-n-s"
            }});

        jQuery("#deleteFormElementsButton").button({icons: {
                primary: "ui-icon-scissors"
            }});
        


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
        });

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
    }

    function unsetRearrangeAndDeleteElementButtons(formnumber)
    {
        resetSort();
        resetDelete();
        resetEditFormElements();
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
        resetSort();
        jQuery("#editFormElementsButton").attr('checked', false);
        jQuery( "#editFormElementsButton").button( "refresh" );
        jQuery( "#editFormElementsButton").button({ disabled: true });
        jQuery( "#editFormElementsButton" ).unbind();
        jQuery("#WYSIWYGForm").children().css("border", "0");
    }

    function insertFormElement()
    {
        var formName = jQuery("#getFormName").html();
        var formNumber = jQuery("#getFormNumber").html();
        formName = jQuery.trim(formName);
        formNumber = jQuery.trim(formNumber);
        setUpRearrangeAndDeleteElementButtons(formNumber);
        setUpDoneButton();
        setUpMainMenuIcons();
        setUpEditFormElementButton();
        jQuery('#input_add_form_elements_drop_down').change(function() {

            if (jQuery('#input_add_form_elements_drop_down').val() == "form_heading")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertHTMLHeading("HTML_heading",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "label")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertLabel("label",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "radio")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertRadio("radio",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "check_box")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertCheckBox("checkbox",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "drop_down")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertDropDown("dropdown",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "date_picker")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertDatePicker("datepicker",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "text_input")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertTextInput("text_input",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "text_area")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertTextArea("text_area",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "button")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertButton("button",formName,formNumber);
            }
            if (jQuery('#input_add_form_elements_drop_down').val() == "multiselect_drop_down")
            {
                jQuery('#input_add_form_elements_drop_down').val("default");
                unsetRearrangeAndDeleteElementButtons(formNumber);
                insertMSDropDown("multiselectable_dropdown",formName,formNumber);
            }
        });
    }






    function highlightNewConstructedFormElement(elementToHighlight)
    {
        elementToHighlight.addClass('ui-state-highlight');
        setTimeout(function() {
            elementToHighlight.removeClass('ui-state-highlight');
        }, 2500);
    }
    function filterNumberInput(elementToFilter)
    {
        elementToFilter.bind('keypress keydown keyup',function() {
            var test = elementToFilter.val();
            var numeric1= "0123456789";
            var test1 = test.charAt(test.length-1);
            var bool = numeric1.search(test1);
            if (bool == -1)
            {
                test= test.substr(0,test.length-1);
            }
            elementToFilter.val(test);
        });
    }
    function filterInput(elementToFilter)
    {
        elementToFilter.bind('keypress keydown keyup',function() {
            var test = elementToFilter.val();
            var alphabetic= "qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM_-!@#$%&";
            var symbols = ",./;'[]\\=<>?:\"{}|+)(*";
            var test1 = test.charAt(test.length-1);

            var boolalphebetic = alphabetic.search(test1);
            if (boolalphebetic == -1)
            {
                var test= test.substr(0,test.length-1);
            }
            var boolsymbols = symbols.search(test1);
            if (boolsymbols == 0)
            {
                var test= test.substr(0,test.length-1);
            }
            elementToFilter.val(test);
        });
    }


    function produceInsertErrorMessage()
    {
        jQuery( "#dialog-box").dialog({ title: 'Insert Error' });

        jQuery("#dialog-box").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element could \n\
not be made. Please enter a  Please contact your software administrator.</p>');
        jQuery( "#dialog-box" ).dialog( "option", "buttons", {
            "OK": function() {

                jQuery(this).dialog("close");
            }         });
        jQuery( "#dialog-box").dialog('open');
    }

    function checkValue(o,n,min,max)
    {
        if ( o.val() > max || o.val() < min ) {
            o.addClass('ui-state-error');
            updateErrorMessage("Length of " + n + " must be between "+min+" and "+max+".");
            return false;
        } else {
            return true;
        }
    }
    function checkLength(o,n,min,max) {

        if ( o.val().length > max || o.val().length < min ) {
            o.addClass('ui-state-error');
            updateErrorMessage("Length of " + n + " must be between "+min+" and "+max+".");
            return false;
        } else {
            return true;
        }

    }
    function updateErrorMessage(errorText) {

        jQuery('.errorMessageDiv').css("color","red");
        jQuery('.errorMessageDiv').html("Error. ");

        jQuery('.errorMessageDiv').append(errorText)
        .addClass('ui-state-highlight');
        jQuery('.errorMessageDiv').show('slow');
        setTimeout(function() {
            jQuery('.errorMessageDiv').removeClass('ui-state-highlight', 1500);
        }, 1500);
    }

    function checkRegexp(o,regexp,n) {

        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass('ui-state-error');
            updateErrorMessage(n);
            return false;
        } else {
            return true;
        }

    }

</script>