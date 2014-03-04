function LoadEditMSDropDownForm(formNumber,formElementName,formElementType){
    var myurlToEditMSDropDown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    }; 
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditMSDropDown , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            
            setUpMSDropDownEditForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}




function setUpMSDropDownEditForm(formNumber,formElementName){
    
    var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
    var defaultOption = jQuery('#defaultOptionButton');
    var formElementLabel = jQuery(':input[name=formElementLabel]');
    var menuSizeRadio = jQuery(':input[name=menuSize]');
    var menuSize = jQuery(':input[name=menuSizeParameter]');
    defaultOption.button();
    formElementLabelLayout.button();
    menuSizeRadio.button();
    filterNumberInput(menuSize);

    if ( jQuery('input:radio[name=menuSize]:checked').val() != "custom")
    {
        jQuery("#setCustomMenuSize").hide();

    }

    menuSizeRadio.change(function(){

        if ( jQuery('input:radio[name=menuSize]:checked').val() == "custom")
        {
            jQuery("#setCustomMenuSize").show("slow");
            menuSize.val("2");
        }
        else
        {
            jQuery("#setCustomMenuSize").hide("slow");
            menuSize.val("1");
        }

    });
            

    var allFields = jQuery([]).add(formElementLabel);
    jQuery("#msdropdownLabelContainer").children().children('.ui-button-text').css('width','170px');
    jQuery("#msdropdownSizeContainer").children().children('.ui-button-text').css('width','270px');
  
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('msdropdown');
        },
        "Cancel": function() {
             
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            updateMSDropdown(2,formNumber,formElementName);
        },
        "Update Multi-Select Drop Down Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');
            if ( jQuery('input:radio[name=menuSize]:checked').val() == "custom")
            {
                bValid = bValid && checkValue(menuSize,'menu size drop down size',2,150);
                bValid = bValid && checkRegexp(menuSize,/^\d+$/,"The drop down menu size only allows digits (0-9).");
            }
                    
            if (bValid) {
                var menuSizes= menuSize.val();
                var formElementLabels=formElementLabel.val();
                var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();

                    
                    
                updateMSDropdown(1,formNumber,formElementName,menuSizes,formElementLabels,formElementLabelLayouts);   
            }

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          
                
    btnHelp.css('float', 'left');
    var formElementType = "multiselectable_dropdown";
    var formElementLargeName = "Multi-Selectable Drop Down List";
    var updateDropdownParametersButtonLabel = "Update Multi-Select Drop Down Parameters";
    var  btnUpdate = jQuery('.ui-dialog-buttonpane').find('button:contains("'+updateDropdownParametersButtonLabel+'")');
    setUpEditLinkForOptions(formElementType,formElementLargeName,btnUpdate,false);
    setUpDeleteLink(formElementType);
    hideFormElementOptionSuperContainer();
    $("#editFormElementTabs").tabs({ 
        fx: {
            opacity: 'toggle'
        } 
    });
}

function updateMSDropdown(realUpdate,formNumber,formElementName,menuSize,formElementLabel,formElementLabelLayout){
    var MSDropdowndataToPost = {
        "update":realUpdate,
        "formNumber":formNumber,
        "formElementName":formElementName,
        "menuSize":menuSize,
        "formElementLabel":formElementLabel,
        "formElementLabelLayout":formElementLabelLayout
    };
    
    
    var myurlToUpdateMSDropdown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceMSDropDown]").val();
        
    jQuery('#tempdivcontainer').load(myurlToUpdateMSDropdown, MSDropdowndataToPost ,function postSuccessFunction(html) {  
        var MSdropdown = jQuery('#tempdivcontainer #WYSIWYGMSDropdown').html();
        jQuery('#tempdivcontainer').empty();
        if (MSdropdown == 0){
            updateErrorMessage("Could not update the multi-selectable drop down parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementMultiSelectDropDown">'+MSdropdown+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });   
}