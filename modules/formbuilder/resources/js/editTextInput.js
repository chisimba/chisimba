
function LoadEditTextInputForm(formNumber,formElementName,formElementType){
    var myurlToEditTextInput = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditTextInput , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpEditTextInputForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}


function setUpEditTextInputForm(formNumber,formElementName){
    var formElementSize = jQuery(':input[name=textInputLength]');
    var maskedInputChoice = jQuery(':input[name=maskedInputChoice]');
    var textOrPassword = jQuery(':input[name=textOrPasswordRadio]');
    var formElementText = jQuery(':input[name=formElementDesiredText]');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
            
    maskedInputChoice.button();
    textOrPassword.button();
    formElementLabelLayout.button();
            
    jQuery("#textInputLabelContainer").children().children('.ui-button-text').css('width','250px');
    jQuery("#textInputPropertiesContainer").children().children('.ui-button-text').css('width','250px');
    jQuery("#textInputPropertiesContainer").children("#additionalTextProperties").children().children('.ui-button-text').css('width','250px');
    var allFields = jQuery([]).add(formElementText).add(formElementSize);
    filterNumberInput(formElementSize);
    
    if (jQuery('input:radio[name=textOrPasswordRadio]:checked').val() != "text"){
        jQuery("#additionalTextProperties").hide();
    }
    
    if  ( jQuery('input:radio[name=maskedInputChoice]:checked').val() != "default"){
        jQuery("#defaultTextInput").hide();
    }
    textOrPassword.change(function(){
        if ( jQuery('input:radio[name=textOrPasswordRadio]:checked').val() == "text")
        {
            jQuery("#additionalTextProperties").show("slow");
            jQuery("input:radio[name=maskedInputChoice]:eq(0)").attr('checked', "checked");
        }
        else
        {
            jQuery("#additionalTextProperties").hide("slow");
            jQuery("input:radio[name=maskedInputChoice]:eq(0)").attr('checked', "checked");
            formElementText.val("");
        }

    });
    maskedInputChoice.change(function(){
        if ( jQuery('input:radio[name=maskedInputChoice]:checked').val() == "default")
        {
            jQuery("#defaultTextInput").show("slow");
            formElementText.val('');
        }
        else
        {
            jQuery("#defaultTextInput").hide("slow");
            formElementText.val('');
        }
    });
    
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('textinput');
        },
        "Cancel": function() {
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
        },
        "Update Text Input Properties": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');
            bValid = bValid && checkRegexp(formElementSize,/^\d+$/,"The text input character length only allows digits (0-9).");
            bValid = bValid && checkValue(formElementSize,'text input character length',1,150);

            if (bValid) {
      
                var textOrPasswords= jQuery('input:radio[name=textOrPasswordRadio]:checked').val();
                var maskedInputChoices=jQuery('input:radio[name=maskedInputChoice]:checked').val();
                var formElementTexts= formElementText.val();
                var formElementSizes= formElementSize.val();
                var formElementLabels= formElementLabel.val();
                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();
                updateTextInput(formNumber,formElementName,formElementSizes,textOrPasswords,maskedInputChoices,formElementTexts,formElementLabels,formElementLabelLayouts);
            }
        }             
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
    btnHelp.css('float', 'left');
}

function updateTextInput(formNumber,formElementName,formElementSize,textOrPasswordOpt,maskedInputChoice,formElementText,formElementLabel,formElementLabelLayout){
    var textInputDataToPost = {
        "update":1,
        'formNumber':formNumber,
        "formElementName": formElementName,
        "textInputName": formElementName,
        "textInputValue" : formElementText,
        "textInputType": textOrPasswordOpt,
        "textInputSize": formElementSize,
        "maskedInputChoice" : maskedInputChoice,
        "formElementLabel" :formElementLabel,
        "formElementLabelLayout":formElementLabelLayout
    };
    var myurlToUpdateTextInput = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceTextInput]").val();
    jQuery('#tempdivcontainer').load(myurlToUpdateTextInput, textInputDataToPost ,function postSuccessFunction(html) {  
        var textinput = jQuery('#tempdivcontainer #WYSIWYGTextInput').html();
        jQuery('#tempdivcontainer').empty();
        if (textinput == 0){
            updateErrorMessage("Could not update the text input parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementTextInput">'+textinput+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}