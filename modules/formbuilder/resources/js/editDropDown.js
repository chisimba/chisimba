
function LoadEditDropDownForm(formNumber,formElementName,formElementType){
    var myurlToEditDropDown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditDropDown , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpDropDownForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}

function setUpDropDownForm(formNumber,formElementName){
    
    var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
    var defaultOption = jQuery('#defaultOptionButton');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    defaultOption.button();
    formElementLabelLayout.button();
    var allFields = jQuery([]).add(formElementLabel);
            
    jQuery("#editFormElementTabs").children().children('.ui-button-text').css('width','150px');
        

    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('dropdown');
        },
        "Cancel": function() {
             
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            updateDropdown(2,formNumber,formElementName);
        },
        "Update Drop Down Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');

            if (bValid) {
                var formElementLabels=formElementLabel.val();
                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();

                updateDropdown(1,formNumber,formElementName,formElementLabels,formElementLabelLayouts);   
            }

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          
                
    btnHelp.css('float', 'left');
    var formElementType = "dropdown";
    var formElementLargeName = "Drop Down List";
    var updateDropdownParametersButtonLabel = "Update Drop Down Parameters";
    var  btnUpdate = jQuery('.ui-dialog-buttonpane').find('button:contains("'+updateDropdownParametersButtonLabel+'")');
    setUpEditLinkForOptions(formElementType,formElementLargeName,btnUpdate,true);
    setUpDeleteLink(formElementType);
    hideFormElementOptionSuperContainer();
    $("#editFormElementTabs").tabs({ 
        fx: {
            opacity: 'toggle'
        } 
    });
}


function updateDropdown(realUpdate,formNumber,formElementName,formElementLabel,formElementLabelLayout){
    var dropDownDataToPost = {
        "update":realUpdate,
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementLabel": formElementLabel,
        "formElementLabelLayout":formElementLabelLayout
    };
    
    var myurlToUpdateDropdown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceDropdown]").val();
        
    jQuery('#tempdivcontainer').load(myurlToUpdateDropdown, dropDownDataToPost ,function postSuccessFunction(html) {  
        var dropdown = jQuery('#tempdivcontainer #WYSIWYGDropdown').html();
        jQuery('#tempdivcontainer').empty();
        if (dropdown == 0){
            updateErrorMessage("Could not update the drop down parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementDropDown">'+dropdown+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}