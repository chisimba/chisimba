//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the text area parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new text area identifier
 *  or a new text area or both. If you are creating a text area with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 */
function insertTextArea(formElementType,formName,formNumber)
{
    jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-formElementsAdvanced" ).bind( "dialogclose", function(event, ui) {
        insertFormElement();
    });
    var myurlToCreateTextArea = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToInsertFormElement]").val();
    var dataToPost ={
        "formName":formName,
        "formNumber":formNumber,
        "formElementType":formElementType
    };
    jQuery('#tempdivcontainer').load(myurlToCreateTextArea, dataToPost ,function postSuccessFunction(html) {
        var insertSimpleTextAreaFormContent =     jQuery('#tempdivcontainer').children("#ALL").children("#simpleTAForm").html();
        var insertAdvancedTextAreaFormContent =     jQuery('#tempdivcontainer').children("#ALL").children("#advancedTAForm").html();
        jQuery('#tempdivcontainer').empty();
        if (jQuery("tempdivcontainer").html() == 0)
        {
            jQuery( "#dialog-box-formElementsAdvanced").dialog({
                title: 'Form Element Does Not Exist'
            });
            jQuery("#dialog-box-formElementsAdvanced").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist. Please contact your software administrator.</p>');
            jQuery( "#dialog-box-formElementsAdvanced" ).dialog( "option", "buttons", {
                "OK": function() {

                    jQuery(this).dialog("close");
                }
            });
        }
        else
        {
            jQuery(  "#dialog-box-formElementsAdvanced").dialog({
                title: 'Insert Text Area'
            });
            jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").html(insertSimpleTextAreaFormContent);
            jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").html(insertAdvancedTextAreaFormContent);
            jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").tabs();

            var selected =   jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").tabs( "option", "selected" );
            var formElementIDSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaNameAndIDContainer").children(':input[name=uniqueFormElementID]');
            var formElementIDAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaNameAndIDContainer").children(':input[name=uniqueFormElementID]');
            var formElementNameSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaNameAndIDContainer").children(':input[name=uniqueFormElementName]');
            var formElementNameAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaNameAndIDContainer").children(':input[name=uniqueFormElementName]');

            var formElementLengthAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaSizeMenuContainer").children(':input[name=textAreaLength]');
            var formElementLengthSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaSizeMenuContainer").children(':input[name=textAreaLength]');
            var formElementHeightSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaSizeMenuContainer").children(':input[name=textAreaHeight]');
            
            var formElementHeightAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaSizeMenuContainer").children(':input[name=textAreaHeight]');
            //var formElementTextSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaPropertiesContainer").children(':input[name=formElementDesiredText]');
            var formElementTextSimple = jQuery("textarea#input_formElementDesiredText");
            var formElementTextAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaPropertiesContainer").children(':input[name=formElementDesiredText]');

//            var formElementTextSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaPropertiesContainer").children('#input_formElementDesiredText');
           var formElementLabelSimple =jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaLabelContainer").children(':input[name=formElementLabel]');
               var formElementLabelAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaLabelContainer").children(':input[name=formElementLabel]');
          var formElementLabelLayoutSimple = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").children("#textAreaLabelContainer").children(':input[name=labelOrientationSimple]');
           var formElementLabelLayoutAdvanced = jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").children("#textAreaLabelContainer").children(':input[name=labelOrientationAdvanced]');

          var allFields = jQuery([]).add(formElementIDSimple).add(formElementIDAdvanced).add(formElementNameSimple)
            .add(formElementNameAdvanced).add(formElementLengthAdvanced).add(formElementLengthSimple).add(formElementHeightSimple)
            .add(formElementHeightAdvanced);
            filterInput(formElementIDSimple);
            filterInput(formElementIDAdvanced);
            filterInput(formElementNameSimple);
            filterInput(formElementNameAdvanced);
            filterNumberInput(formElementLengthAdvanced);
            filterNumberInput(formElementLengthSimple);
            filterNumberInput(formElementHeightSimple);
            filterNumberInput(formElementHeightAdvanced);
formElementLabelLayoutSimple.button();
//formElementLabelLayoutAdvanced.button();
            jQuery( "#dialog-box-formElementsAdvanced" ).dialog( "option", "buttons", {
                                    "Help": function() {
                  setUpFormElementModalHelp('textarea');
              },
                "Cancel": function() {
                    insertFormElement();
                    jQuery("#dialog-box-formElementsAdvanced").dialog("close");
                },
            "Insert Text Area": function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                    var selected =   jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").tabs( "option", "selected" );
                    if (selected == 0)
                    {
                        var bValid = true;
                        bValid = bValid && checkRegexp(formElementIDSimple,/^([0-9a-zA-Z])+$/,"The simple text area ID field only allows alphanumeric characters (a-z 0-9).");
                        bValid = bValid && checkLength(formElementIDSimple,'unique simple text area ID',5,150);
                        bValid = bValid && checkRegexp(formElementNameSimple,/^([0-9a-zA-Z])+$/,"The simple text area name only allows alphanumeric characters (a-z 0-9).")
                        bValid = bValid && checkLength(formElementNameSimple,'simple text area name',5,150);
                        bValid = bValid && checkValue(formElementLengthSimple,'simple text area column size',1,140);
                        bValid = bValid && checkRegexp(formElementLengthSimple,/^\d+$/,"The simple text area column size only allows digits (0-9).");
                        bValid = bValid && checkValue(formElementHeightSimple,'simple text area row size',1,140);
                        bValid = bValid && checkRegexp(formElementHeightSimple,/^\d+$/,"The simple text area row size only allows digits (0-9).");
                        if (bValid) {
                            var formElementIDSimples = formElementIDSimple.val();
                            var formElementNameSimples = formElementNameSimple.val();
                            var formElementValueSimples = formElementTextSimple.val();
                            var rowSizeSimple = formElementHeightSimple.val();
                            var columnSizeSimple = formElementLengthSimple.val();
                            var simpleOrAdvancedChoiceSimple = "textarea";
                            var toolbarChoiceSimple = jQuery('input:radio[name=toolBarChoice]:checked').val();
                            var formElementLabelSimples= formElementLabelSimple.val();
                            var formElementLabelLayouts = jQuery('input:radio[name=labelOrientationSimple]:checked').val();
                            produceTextArea(formElementIDSimples,formElementNameSimples,formElementValueSimples,rowSizeSimple,columnSizeSimple,simpleOrAdvancedChoiceSimple,toolbarChoiceSimple,formElementLabelSimples,formElementLabelLayouts);
                        }
                    }
                    else
                    {
                        var bValid = true;
                        bValid = bValid && checkRegexp(formElementIDSimple,/^([0-9a-zA-Z])+$/,"The simple text area ID field only allows alphanumeric characters (a-z 0-9).");
                        bValid = bValid && checkLength(formElementIDSimple,'unique simple text area ID',5,150);
                        bValid = bValid && checkRegexp(formElementNameSimple,/^([0-9a-zA-Z])+$/,"The simple text area name only allows alphanumeric characters (a-z 0-9).")
                        bValid = bValid && checkLength(formElementNameSimple,'simple text area name',5,150);
                        bValid = bValid && checkValue(formElementLengthSimple,'simple text area column size',1,140);
                        bValid = bValid && checkRegexp(formElementLengthSimple,/^\d+$/,"The simple text area column size only allows digits (0-9).");
                        bValid = bValid && checkValue(formElementHeightSimple,'simple text area row size',1,140);
                        bValid = bValid && checkRegexp(formElementHeightSimple,/^\d+$/,"The simple text area row size only allows digits (0-9).");
                        if (bValid) {
                            var formElementIDSimples = formElementIDSimple.val();
                            var formElementNameSimples = formElementNameSimple.val();
                            var formElementValueSimples = formElementTextSimple.val();
                            var rowSizeSimple = formElementHeightSimple.val();
                            var columnSizeSimple = formElementLengthSimple.val();
                            var simpleOrAdvancedChoiceSimple = "textarea";
                            var toolbarChoiceSimple = jQuery('input:radio[name=toolBarChoice]:checked').val();
                            var formElementLabelSimples= formElementLabelSimple.val();
                            var formElementLabelLayouts = jQuery('input:radio[name=labelOrientationSimple]:checked').val();
                            produceTextArea(formElementIDSimples,formElementNameSimples,formElementValueSimples,rowSizeSimple,columnSizeSimple,simpleOrAdvancedChoiceSimple,toolbarChoiceSimple,formElementLabelSimples,formElementLabelLayouts);
                        }                        
//                        
//                        
//                        
//                        var bValid = true;
//                        bValid = bValid && checkRegexp(formElementIDAdvanced,/^([0-9a-zA-Z])+$/,"The advanced text area ID field only allows alphanumeric characters (a-z 0-9).");
//                        bValid = bValid && checkLength(formElementIDAdvanced,'unique advanced text area ID',5,150);
//                        bValid = bValid && checkRegexp(formElementNameAdvanced,/^([0-9a-zA-Z])+$/,"The advanced text area name only allows alphanumeric characters (a-z 0-9).");
//                        bValid = bValid && checkLength(formElementNameAdvanced,'advanced text area name',5,150);
//                        bValid = bValid && checkValue(formElementLengthAdvanced,'advanced text area column size',1,140);
//                        bValid = bValid && checkRegexp(formElementLengthAdvanced,/^\d+$/,"The advanced text area column size only allows digits (0-9).");
//                        bValid = bValid && checkValue(formElementHeightAdvanced,'advanced text area row size',1,140);
//                        bValid = bValid && checkRegexp(formElementHeightAdvanced,/^\d+$/,"The advanced text area row size only allows digits (0-9).");
//                        if (bValid) {
//                            var formElementIDAdvanceds = formElementIDAdvanced.val();
//                            var formElementNameAdvanceds = formElementNameAdvanced.val();
//                            var formElementValueAdvanced = formElementTextAdvanced.text();
//                            var rowSizeAdvanced = formElementHeightAdvanced.val();
//                            var columnSizeAdvanced = formElementLengthAdvanced.val();
//                            var simpleOrAdvancedChoiceAdvanced = "htmlarea";
//                            var toolbarChoiceAdvanced = jQuery('input:radio[name=toolBarChoice]:checked').val();
//                           var formElementLabelAdvanceds= formElementLabelAdvanced.val();
//                             var formElementLabelLayouts = jQuery('input:radio[name=labelOrientationAdvanced]:checked').val();
//                            produceTextArea(formElementIDAdvanceds,formElementNameAdvanceds,formElementValueAdvanced,rowSizeAdvanced,columnSizeAdvanced,simpleOrAdvancedChoiceAdvanced,toolbarChoiceAdvanced,formElementLabelAdvanceds,formElementLabelLayouts);
//                        }
                    }
                }
            });
        }
        
        
        jQuery("#dialog-box-formElementsAdvanced").dialog("open");
                      jQuery(".ui-dialog-buttonset").css('width','1025px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');
    });
}

/*!
 *  \brief This javascript function produces the actual text area form element and also creates
 *  a form element identifier. It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier and the html name of
 *  the form element.
 *  \param formElementName A string that stores the actual html name of the form
 *  element.
 *  \param formElementValue A string to store the actual text that will be displayed
 *  inside the form element by default.
 *  \param rowSize An integer to store the height of the form element.
 *  \param columnSize An integer to store the width of the form element.
 *  \param simpleOrAdvancedChoice A string to store whether a 'simple' text area
 *  or an 'advanced' text area with toolbars is set.
 *  \param toolbarChoice If the 'advanced' text area is chosen this string stores
 *  the toolbar type that will be displayed.
 *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param labelLayout A string to store the relative postion of the
 *  label to the form element.
 */
function produceTextArea(formElementID,formElementName,formElementValue,rowSize,columnSize,simpleOrAdvancedChoice,toolbarChoice, formElementLabel,labelLayout)
{
    var myurlToCreateTextAreaIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'text_area',
        "formElementName":formElementID
    };
    jQuery('#tempdivcontainer').load(myurlToCreateTextAreaIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
            
          var formnumber = jQuery("#getFormNumber").html();
            formnumber = jQuery.trim(formnumber);
    
            var textAreaDataToPost = {
                "formNumber":formnumber,
                "formElementName": formElementID,
                "textAreaName": formElementName,
                "textAreaValue" : formElementValue,
                "ColumnSize": columnSize,
                "RowSize": rowSize,
                "simpleOrAdvancedHAChoice" : simpleOrAdvancedChoice,
                "toolbarChoice" : toolbarChoice,
               "formElementLabel": formElementLabel,
                "labelLayout":labelLayout
            };
            var myurlToProduceTextArea = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceTextArea]").val();
            //             jQuery('#SortFormElementsButton').unbind();
            // jQuery('#Stop').unbind();
            // unsetRearrangeAndDeleteElementButtons(formnumber);
            jQuery('#tempdivcontainer').load(myurlToProduceTextArea, textAreaDataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);

                var textArea = jQuery('#tempdivcontainer').children('#WYSIWYGTextArea').html();

                if (textArea == 0)
                {
                    updateErrorMessage("A text area with the ID \""+formElementID+"\" and name \""+formElementName+"\" already exists in the database.\n\
Please choose a unique text area ID and name combination.");
                    jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
                    jQuery(':input[name=uniqueFormElementName]').addClass('ui-state-error');
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementTextArea"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementID).append(textArea);
                        var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
                        highlightNewConstructedFormElement(elementToHighlights);
                    }
                    else
                    {
                        jQuery("#WYSIWYGForm").children("#"+formElementID).append(textArea);
                        var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
                        highlightNewConstructedFormElement(elementToHighlight);
                    }
                    //insertFormElement();
                    jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").empty();
                    jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").empty();
                    jQuery("#dialog-box-formElementsAdvanced").dialog('close');
                }
            });

        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The text area ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique text area ID.");
            jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
        }
        else
        {
            jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#simpleInsertForm").empty();
            jQuery("#dialog-box-formElementsAdvanced").children("#FormElementInserterTabs").children("#advancedInsertForm").empty();
            jQuery( "#dialog-box-formElementsAdvanced").dialog('close');
            produceInsertErrorMessage();
        }
    });

}