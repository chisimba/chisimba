function hideFormElementOptionSuperContainer(){
    jQuery(".formElementOptionsFormSuperContainer").hide();
    jQuery(".formElementOptionsFormSuperContainer").children().hide(); 
    jQuery(".formElementOptionUpdateHeading").hide();
}


function setUpDeleteLink(formElementType){
    jQuery(".deleteOptionLink").click(function(){
        var parent = jQuery(this).parent();
        var optionID = jQuery(this).parent().attr("optionid");
     
     
        var myurlToDeleteFormElementOption = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToDeleteFormElementOption]").val();
        var deleteDataToPost = {
            "formElementType":formElementType,
            "optionID":optionID
        };
       
        jQuery.ajax({
         
            type: 'post',
            url: myurlToDeleteFormElementOption,
            data: deleteDataToPost,
            beforeSend: function() {
                parent.animate({
                    'backgroundColor':'#fb6c6c'
                },300);
            },
            success: function() {
                parent.slideUp(300,function() {
                    parent.remove();
                });
            }
        });
        
    });
}

function setUpEditLinkForOptions(formElementType,formElementFormLabel,btnUpdateDom,onlySingleDefaultOption){
    jQuery(".editOptionLink").click(function(){
        var editOptionLink = jQuery(this);
        btnUpdateDom.button("disable");
        var labelOrientation = (jQuery(this).parent().attr("labelorientation"));
        var formElementLabel = jQuery(this).parent().attr("formelementlabel");
        var breakSpace = jQuery(this).parent().attr("breakspace");
        var defaultSelected = jQuery(this).parent().attr("defaultvalue");
        var optionValue = jQuery(this).parent().attr("optionvalue");
        var optionLabel = jQuery(this).parent().attr("optionlabel");
        var formElementName = jQuery(this).parent().attr("formelementname");
        var formElementSize = jQuery(this).parent().attr("formelementsize");
        var formNumber = jQuery(this).parent().attr("formnumber");
        var optionID = jQuery(this).parent().attr("optionid");
        

        jQuery('input:radio[name=formElementLayout][value="'+breakSpace+'"]').click();
        
        jQuery(":input[name=optionValue]").val(optionValue);
        jQuery(":input[name=optionLabel]").val(optionLabel);
        var defaultOptionButton = jQuery("#defaultOptionButton");

        if (defaultSelected == "1"){
            if (!defaultOptionButton.is(":checked")){
                defaultOptionButton.click();
            }
        } else {
            if (defaultOptionButton.is(":checked")){
                defaultOptionButton.click();  
            }
        }
        if (onlySingleDefaultOption){
            if(defaultOptionSet() && !defaultOptionButton.is(":checked")){
                addDisabledMessage(defaultOptionButton);
            }  
        }
       
        
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
        var optionValue = jQuery(':input[name=optionValue]');
        var optionLabel = jQuery(':input[name=optionLabel]');
        var layoutOption = jQuery(':input[name=formElementLayout]');
        var defaultOption = jQuery('#defaultOptionButton');
        var allFields = jQuery([]).add(optionValue).add(optionLabel);
            
        allFields.removeClass('ui-state-error');
        jQuery("#formElementOptionActionButton").click(function(){
            allFields.removeClass('ui-state-error');
            var bValid = true;
            bValid = bValid && checkLength(optionValue,formElementFormLabel+' value',1,150);
            bValid = bValid && checkLength(optionLabel,formElementFormLabel+' label',1,150);
            if (bValid){
                var optionValues=  optionValue.val();
                var optionLabels= optionLabel.val();
                var formElementLayouts= jQuery('input:radio[name=formElementLayout]:checked').val();
                var defaultOptions = jQuery("#defaultOptionButton:checked").val();
                        
                var FormElementOptionDataToPost = {
                    "optionID":optionID,
                    "formElementType":formElementType,
                    "formNumber":formNumber,
                    "optionValue":optionValues,
                    "optionLabel":optionLabels,
                    "formElementName":formElementName,
                    "defaultSelected":defaultOptions,
                    "layoutOption":formElementLayouts,
                    "formElementSize":formElementSize,
                    "formElementLabel": formElementLabel,
                    "formElementLabelLayout":labelOrientation
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
                        //var labelOrientation = (jQuery(this).parent().attr("labelorientation"));
                        //var formElementLabel = jQuery(this).parent().attr("formelementlabel");
                        editOptionLink.parent().attr("breakspace",formElementLayouts);
                        if (defaultOptions == "on"){
                            editOptionLink.parent().attr("defaultvalue","1");
                        }else{
                            editOptionLink.parent().attr("defaultvalue","0");
                        }
        
                        editOptionLink.parent().attr("optionvalue",optionValues);
                        editOptionLink.parent().attr("optionlabel", optionLabels);
                        editOptionLink.siblings(".optionValueContainer").html(optionValues);
                        editOptionLink.siblings(".optionLabelContainer").html(optionLabels);
                        editOptionLink.siblings(".optionBreakSpaceContainer").html(formElementLayouts);
                        if (defaultOptions == "on"){
                            editOptionLink.siblings(".defaultOptionContainer").html("yes");
                        }else{
                            editOptionLink.siblings(".defaultOptionContainer").html("no");  
                        }
                        $(".errorMessageDiv").empty().hide();
                        removeDisabledMessage(defaultOptionButton); 
                        closeFormOptionForm();
                        btnUpdateDom.button("enable");

                    }
                });
            }
            
        });
        
    });
}

function addDisabledMessage(theDefaultSelectionButton){
    theDefaultSelectionButton.button( "option", "disabled", true );
    theDefaultSelectionButton.after("<div class='disabledDefaultSelectionMessage'>Another option is selected to be a default. To set this option be the selected by default, unselect the default selected button for the option that is default selected.</div>");
    
}

function removeDisabledMessage(theDefaultSelectionButton){
    var defaultOptionButton;
    if (theDefaultSelectionButton == null){
        defaultOptionButton = jQuery("#defaultOptionButton");  
    }else {
        defaultOptionButton = theDefaultSelectionButton;
    }
    
    defaultOptionButton.button( "option", "disabled", false );
    jQuery(".disabledDefaultSelectionMessage").remove();
}
function defaultOptionSet(){
   
    var defaultOptionSet = false;
    $('.singleOptionContainer').each(function() {
   
        var defaultValue = $(this).attr('defaultvalue');

        if (defaultValue == 1){

            defaultOptionSet = true;
        }
    });
    return defaultOptionSet;

}

function setDefaultOption(){
    $('#example a').each(function(idx, formElementOption) {  
        hrefs += item.href + '\n' ;  
    });
    jQuery("").get()
}

function setUpCancelButton(btnUpdateDom){
    jQuery("#formElementOptionCancelButton").click(function(){
        $(".errorMessageDiv").empty().hide();
        removeDisabledMessage(null); 
        closeFormOptionForm();
        btnUpdateDom.button("enable");
    });
}

function closeFormOptionForm(){
     
    jQuery(".formElementOptionUpdateHeading").fadeOut(); 
    jQuery(".editFormElementFormContainer").fadeOut();
    jQuery(".editFormElementOptionsSideSeperator").toggle( "blind", {}, 1000 );
    jQuery(".editFormElementOptionsHeadingSpacer").toggle("slide",{},1000,function(){
            
            
        clearFormElementOptionFormHeading();
        clearButtonsForFormElementOptionsForm();
        jQuery(".formElementOptionsFormSuperContainer").hide();
        jQuery("#editFormElementTabs").fadeIn();
    });
}


function createFormElementOptionFormHeading(edit,formElementLabel){
    if (edit == true){
        jQuery(".formElementOptionUpdateHeading").html("Edit "+formElementLabel+" Option");  
    } else {
        jQuery(".formElementOptionUpdateHeading").html("Add New "+formElementLabel+" Option");   
    }
}

function clearFormElementOptionFormHeading(){
    jQuery(".formElementOptionUpdateHeading").html("");     
}

function createButtonsForFormElementOptionsForm(actionButtonLabel){
    jQuery(".formElementOptionsFormButtonsContainer").empty();
   
    jQuery(".formElementOptionsFormButtonsContainer").append("<button id='formElementOptionCancelButton' style='float:right; margin-left:15px;margin-right:15px;'>Cancel</button>");
    jQuery(".formElementOptionsFormButtonsContainer").append("<button id='formElementOptionActionButton' style='float:right; margin-left:15px;margin-right:15px;'>"+actionButtonLabel+"</button>");
    jQuery("#formElementOptionActionButton").button();
    jQuery("#formElementOptionCancelButton").button();
}

function clearButtonsForFormElementOptionsForm(){
    jQuery(".formElementOptionsFormButtonsContainer").empty(); 
}


