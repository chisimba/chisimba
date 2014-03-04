function LoadEditLabelForm(formNumber,formElementName,formElementType){
    var myurlToEditLabel = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditLabel , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpEditLabelForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}

function setUpEditLabelForm(formNumber,formElementName){
    var formElementLayout = jQuery(':input[name=formElementLayout]');
    var formElementText = jQuery(':input[name=formElementDesiredText]');
    formElementLayout.button();

    jQuery("#labelSizeContainer").children().children('.ui-button-text').css('width','250px');
    var allFields = jQuery([]).add(formElementText);
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('label');
        },
        "Cancel": function() {
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            
            insertFormElement();
        },
        "Update Label Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');
            bValid = bValid && checkLength(formElementText," desired text for label",1,550);
            if (bValid) {

                var formElementLayouts= jQuery('input:radio[name=formElementLayout]:checked').val();
                var formElementTexts= formElementText.val();
                updateLabel(formNumber,formElementName,formElementLayouts,formElementTexts);
            }
        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
    btnHelp.css('float', 'left');   
}

function updateLabel(formNumber,formElementName,formElementLayout,formElementText){
    var myurlToUpdateLabel = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceLabel]").val();

    var labelDataToPost = {
        "update":1,
        "formNumber":formNumber,
        "labelValue":formElementText,
        "formElementName":formElementName,
        "layoutOption":formElementLayout
    };
           
    jQuery('#tempdivcontainer').load(myurlToUpdateLabel, labelDataToPost ,function postSuccessFunction(html) {                      
        var label = jQuery('#tempdivcontainer #WYSIWYGLabel').html();
        jQuery('#tempdivcontainer').empty();
        if (label == 0){
            updateErrorMessage("Could not update the label parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementLabel">'+label+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }
    });
}