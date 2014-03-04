function LoadEditTextAreaForm(formNumber,formElementName,formElementType){
    var myurlToEditTextArea = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditTextArea , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpEditTextAreaForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}

function setUpEditTextAreaForm(formNumber,formElementName){
    var textAreaLength = jQuery(':input[name=textAreaLength]');
    var textAreaHeight = jQuery(':input[name=textAreaHeight]');
    var textAreaName = jQuery(":input[name=textAreaName]");
    var formElementText = jQuery(':input[name=formElementDesiredText]');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    var formElementLabelLayout = jQuery(':input[name=labelOrientationSimple]');
    
    formElementLabelLayout.button();
    jQuery("#textAreaLabelContainer").children().children('.ui-button-text').css('width','250px');
    
    filterNumberInput(textAreaLength);
    filterNumberInput(textAreaHeight);
    var allFields = jQuery([]).add(formElementText).add(textAreaLength).add(textAreaHeight);
    
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('textarea');
        },
        "Cancel": function() {
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
        },
        "Update Text Area Properties": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');
            bValid = bValid && checkRegexp(textAreaLength,/^\d+$/,"The simple text area column size only allows digits (0-9).");
            bValid = bValid && checkValue(textAreaLength,'simple text area column size',1,140);
            bValid = bValid && checkRegexp(textAreaHeight,/^\d+$/,"The simple text area row size only allows digits (0-9).");
            bValid = bValid && checkValue(textAreaHeight,'simple text area row size',1,140);

            if (bValid) {
      

                var formElementTexts= formElementText.val();
                var formElementWidths= textAreaLength.val();
                var formElementHeights= textAreaHeight.val();
                var formElementLabels= formElementLabel.val();
                var textAreaNames = textAreaName.val();
                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientationSimple]:checked').val();
                updateTextArea(formNumber,formElementName,textAreaNames,formElementWidths,formElementHeights,formElementTexts,formElementLabels,formElementLabelLayouts);
            }
        }             
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
    btnHelp.css('float', 'left');
}

function updateTextArea(formNumber,formElementName,textAreaName,formElementWidth,formElementHeight,formElementText,formElementLabel,formElementLabelLayout){
    var textAreaDataToPost = {
        "update":1,
        "formNumber":formNumber,
        "formElementName": formElementName,
        "textAreaName": textAreaName,
        "textAreaValue" : formElementText,
        "ColumnSize": formElementWidth,
        "RowSize": formElementHeight,
        "simpleOrAdvancedHAChoice" : "textarea",
        "toolbarChoice" : null,
        "formElementLabel": formElementLabel,
        "labelLayout":formElementLabelLayout
    };
            
    var myurlToUpdateTextArea = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceTextArea]").val();

    jQuery('#tempdivcontainer').load(myurlToUpdateTextArea, textAreaDataToPost ,function postSuccessFunction(html) {
        var textarea = jQuery('#tempdivcontainer #WYSIWYGTextArea').html();
        jQuery('#tempdivcontainer').empty();
        if (textarea == 0){
            updateErrorMessage("Could not update the text area parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementTextArea">'+textarea+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }     
    });
}