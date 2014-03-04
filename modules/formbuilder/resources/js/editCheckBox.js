function LoadEditCheckBoxForm(formNumber,formElementName,formElementType){
    var myurlToEditCheckBox = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };  
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditCheckBox , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            
            setUpEditCheckBoxForm(formNumber,formElementName);
            
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}


function setUpEditCheckBoxForm(formNumber,formElementName){
    
    var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
    var defaultOption = jQuery('#defaultOptionButton');
    var layoutOption = jQuery(':input[name=formElementLayout]');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    defaultOption.button();
    formElementLabelLayout.button();
    layoutOption.button();
    var allFields = jQuery([]).add(formElementLabel);

    jQuery("#editFormElementTabs").children().children('.ui-button-text').css('width','150px');
        

    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('checkbox');
        },
        "Cancel": function() {
             
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            updateCheckBox(2,formNumber,formElementName);
        },
        "Update Check Box Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');

            if (bValid) {
                var formElementLabels=formElementLabel.val();
                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();
                updateCheckBox(1,formNumber,formElementName,formElementLabels,formElementLabelLayouts);
            }

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          
                
    btnHelp.css('float', 'left');
    var formElementType = "checkbox";
    var formElementLargeName = "Check Box";
    var updateCheckBoxParametersButtonLabel = "Update Check Box Parameters";
    var  btnUpdate = jQuery('.ui-dialog-buttonpane').find('button:contains("'+updateCheckBoxParametersButtonLabel+'")');
    setUpEditLinkForOptions(formElementType,formElementLargeName,btnUpdate,false);
    setUpDeleteLink(formElementType);
    hideFormElementOptionSuperContainer();
    $("#editFormElementTabs").tabs({ 
        fx: {
            opacity: 'toggle'
        } 
    });
}



function updateCheckBox(realUpdate,formNumber,formElementName,formElementLabel,formElementLabelLayout){
    var CheckBoxDataToPost = {
        "update":realUpdate,
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementLabel": formElementLabel,
        "formElementLabelLayout":formElementLabelLayout
    };
    
    var myurlToUpdateCheckbox = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceCheckbox]").val();
    
        
    jQuery('#tempdivcontainer').load(myurlToUpdateCheckbox, CheckBoxDataToPost ,function postSuccessFunction(html) {  
        var checkbox = jQuery('#tempdivcontainer #WYSIWYGCheckbox').html();
        jQuery('#tempdivcontainer').empty();
        if (checkbox == 0){
            updateErrorMessage("Could not update the check box parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementCheckBox">'+checkbox+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}




