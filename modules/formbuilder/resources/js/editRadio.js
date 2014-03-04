function LoadEditRadioForm(formNumber,formElementName,formElementType){
    var myurlToEditRadio = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditRadio , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            
            setUpEditRadioForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}


function setUpEditRadioForm(formNumber,formElementName){

    var layoutOption = jQuery(':input[name=formElementLayout]');
    var defaultOption = jQuery('#defaultOptionButton');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
    defaultOption.button();
    layoutOption.button();
    formElementLabelLayout.button();
    var allFields = jQuery([]).add(formElementLabel);

    jQuery("#editFormElementTabs").children().children('.ui-button-text').css('width','150px');
        

    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('radio');
        },
        "Cancel": function() {
             
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            updateRadio(2,formNumber,formElementName);
        },
        "Update Radio Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');

            if (bValid) {

                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();
                var formElementLabels = formElementLabel.val();

                updateRadio(1,formNumber,formElementName,formElementLabels,formElementLabelLayouts);
                     
            }

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          
                
    btnHelp.css('float', 'left');
    var formElementType = "radio";
    var formElementLargeName = "Radio Button";
    var updateRadioParametersButtonLabel = "Update Radio Parameters";
    var  btnUpdate = jQuery('.ui-dialog-buttonpane').find('button:contains("'+updateRadioParametersButtonLabel+'")');
    setUpEditLinkForOptions(formElementType,formElementLargeName,btnUpdate,true);
    setUpDeleteLink(formElementType);
    hideFormElementOptionSuperContainer();
    $("#editFormElementTabs").tabs({ 
        fx: {
            opacity: 'toggle'
        } 
    });
            

}

function updateRadio(realUpdate,formNumber,formElementName,formElementLabel,formElementLabelLayout){
    var RadioDataToPost = {
        "update":realUpdate,
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementLabel": formElementLabel,
        "formElementLabelLayout":formElementLabelLayout
    };

    var myurlToUpdateRadio = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceRadio]").val();
    
    jQuery('#tempdivcontainer').load(myurlToUpdateRadio, RadioDataToPost ,function postSuccessFunction(html) {  
        var radio = jQuery('#tempdivcontainer #WYSIWYGRadio').html();
        jQuery('#tempdivcontainer').empty();
        if (radio == 0){
            updateErrorMessage("Could not update the radio parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementRadio">'+radio+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}