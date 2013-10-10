function LoadEditButtonForm(formNumber,formElementName,formElementType){
    var myurlToEditButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditButton , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpButtonForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}


function setUpButtonForm(formNumber,formElementName){
    var formElementNameInput = jQuery(':input[name=uniqueFormElementName]');
    var resetOrSubmitButton = jQuery(':input[name=resetOrSubmitButton]');
    var buttonLabel = jQuery(':input[name=buttonLabel]');

    resetOrSubmitButton.button();
    filterInput(formElementNameInput);
     
    jQuery("#editFormElementTabs").children().children('.ui-button-text').css('width','150px');
    jQuery("#buttonPropertiesContainer").children().children('.ui-button-text').css('width','350px');
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('button');
        },
        "Cancel": function() {
             
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            //updateDropdown(2,formNumber,formElementName);
            updateButton(2,formNumber,formElementName);
        },
        "Update Button Parameters": function() {
            updateButton(2,formNumber,formElementName);

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          
                
    btnHelp.css('float', 'left');
    var formElementType = "button";
    var formElementLargeName = "Button";
    var updateDropdownParametersButtonLabel = "Update Button Parameters";
    var  btnUpdate = jQuery('.ui-dialog-buttonpane').find('button:contains("'+updateDropdownParametersButtonLabel+'")');
    setUpEditLinkForButtonOptions(formElementType,formElementLargeName,btnUpdate,true);
    setUpDeleteLink(formElementType);
    hideFormElementOptionSuperContainer();
    $("#editFormElementTabs").tabs({ 
        fx: {
            opacity: 'toggle'
        } 
    });
}

function updateButton(realUpdate,formNumber,formElementName,buttonName,buttonLabel,sumbitOrResetOption){
    var buttonDataToPost = {
        "update":realUpdate,
        "formNumber":formNumber,
        "formElementName":formElementName,
        "buttonName": buttonName,
        "buttonLabel" : buttonLabel,
        "submitOrResetButtonChoice": sumbitOrResetOption
    };
    
    var myurlToUpdateButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceButton]").val();
        
    jQuery('#tempdivcontainer').load(myurlToUpdateButton, buttonDataToPost ,function postSuccessFunction(html) {  
        var button = jQuery('#tempdivcontainer #WYSIWYGButton').html();
        jQuery('#tempdivcontainer').empty();
        if (button == 0){
            updateErrorMessage("Could not update the button parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementButton">'+button+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}




function setUpEditLinkForButtonOptions(formElementType,formElementFormLabel,btnUpdateDom,onlySingleDefaultOption){
    jQuery(".editOptionLink").click(function(){
        var editOptionLink = jQuery(this);
        btnUpdateDom.button("disable");
        
        
        var buttonLabel = (jQuery(this).parent().attr("buttonlabel"));
        var buttonName = (jQuery(this).parent().attr("buttonname"));
        var buttonType = (jQuery(this).parent().attr("buttontype"));
        var optionID = jQuery(this).parent().attr("optionid");
        var formElementName = jQuery(this).parent().attr("formelementname");
        var formNumber = jQuery(this).parent().attr("formnumber");

        jQuery('input:radio[name=resetOrSubmitButton][value="'+buttonType+'"]').click();
        
        jQuery(":input[name=uniqueFormElementName]").val(buttonName);
        jQuery(":input[name=buttonLabel]").val(buttonLabel);
        
        jQuery("#editFormElementTabs").fadeOut("fast");
        jQuery(".formElementOptionsFormSuperContainer").show();
        jQuery(".editFormElementOptionsSideSeperator").toggle( "drop", {}, 1500 );
        jQuery(".editFormElementOptionsHeadingSpacer").toggle("slide",{},1500,function(){
            createFormElementOptionFormHeading(true,formElementFormLabel);
           
            jQuery(".formElementOptionUpdateHeading").fadeIn("slow"); 
            jQuery(".editFormElementFormContainer").fadeIn("slow");
        });
        createButtonsForFormElementOptionsForm("Update "+formElementFormLabel+" Option Parameters");
        setUpCancelButton(btnUpdateDom);
        $(".errorMessageDiv").empty().hide();
        var buttonNameInput = jQuery(':input[name=uniqueFormElementName]');
        var buttonLabelInput = jQuery(':input[name=buttonLabel]');
        var resetOrSubmitInput = jQuery(':input[name=resetOrSubmitButton]');

        var allFields = jQuery([]).add(buttonNameInput).add(buttonLabelInput);
            
        allFields.removeClass('ui-state-error');
        jQuery("#formElementOptionActionButton").click(function(){
            allFields.removeClass('ui-state-error');
            var bValid = true;
            bValid = bValid && checkLength(buttonNameInput,formElementFormLabel+' value',1,150);
            bValid = bValid && checkLength(buttonLabelInput,formElementFormLabel+' label',1,150);
            if (bValid){
                var ButtonNamesS=  buttonNameInput.val();
                var ButtonLabelS= buttonLabelInput.val();
                        
                var isSubmitOrResetS= jQuery('input:radio[name=resetOrSubmitButton]:checked').val();
                        
                var FormElementOptionDataToPost = {
                    "optionID":optionID,
                    "formElementType":formElementType,
                    "formNumber":formNumber,
                    "optionValue":ButtonNamesS,
                    "optionLabel":ButtonLabelS,
                    "formElementName":formElementName,
                    "defaultSelected":isSubmitOrResetS
                };
    
                var myurlToEditFormElementOption = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElementOption]").val();
                jQuery('#tempdivcontainer').load(myurlToEditFormElementOption, FormElementOptionDataToPost ,function postSuccessFunction(html) {
                    jQuery('#tempdivcontainer').html(html);
        
                    var updateResponse = jQuery('#tempdivcontainer').html();
                    if (updateResponse != 1)
                    {
                        updateErrorMessage("The parameters of this form elment option could not be updated. Please contact your software administrator.");
                        jQuery(':input[name=optionValue]').addClass('ui-state-error');
                    }
                    else
                    {

        
                        editOptionLink.parent().attr("buttonname",ButtonNamesS);
                        editOptionLink.parent().attr("buttonlabel", ButtonLabelS);
                        editOptionLink.parent().attr("buttontype", isSubmitOrResetS);        

                        editOptionLink.siblings(".buttonValueContainer").html(ButtonNamesS);
                        editOptionLink.siblings(".buttonLabelContainer").html(ButtonLabelS);
                        editOptionLink.siblings(".buttonTypeContainer").html(isSubmitOrResetS);

                        $(".errorMessageDiv").empty().hide();
                        closeFormOptionForm();
                        btnUpdateDom.button("enable");

                    }
                });
            }
            
        });
        
    });
}

