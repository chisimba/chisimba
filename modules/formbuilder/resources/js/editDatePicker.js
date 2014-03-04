function LoadEditDatePickerForm(formNumber,formElementName,formElementType){
    var myurlToEditDatePicker = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditDatePicker , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpEditDatePickerForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}

function setUpEditDatePickerForm(formNumber,formElementName){
  
    var mydefaultDate = jQuery("#dialog-box-editFormElements").children("#content").children("#dpPropertiesContainer").children("#selectDefaultDate").children("#datepicker").val();

    
    
    var datePickerValue = jQuery(':input[name=uniqueFormElementName]');
    var dateFormatDropDown = jQuery(':input[name=dateFormat]');
    var defaultDateChoice = jQuery(':input[name=defaultDateChoice]');
    var datePickerDefaultDate= jQuery("#datepicker");

    defaultDateChoice.button();

    jQuery("#dpPropertiesContainer").children().children('.ui-button-text').css('width','250px');
    var allFields = jQuery([]).add(datePickerDefaultDate);
            
    if (mydefaultDate == "Real Time Date"){
  
        jQuery("#dialog-box-editFormElements").children("#content").children("#dpPropertiesContainer").children("#selectDefaultDate").children("#datepicker").datepicker({
            
               
            changeMonth: true,
            changeYear: true,
            yearRange: '2000:2020',
            dateFormat : 'yy-mm-dd'
        });
        jQuery("#dialog-box-editFormElements").children("#content").children("#dpPropertiesContainer").children("#selectDefaultDate").children("#datepicker").val("Real Time Date");  
        jQuery("#selectDefaultDate").hide();    
    } else {
        jQuery("#dialog-box-editFormElements").children("#content").children("#dpPropertiesContainer").children("#selectDefaultDate").children("#datepicker").datepicker({
               
            changeMonth: true,
            changeYear: true,
            yearRange: '2000:2020',
            dateFormat : 'yy-mm-dd',
            defaultDate: mydefaultDate
        });
    }
    defaultDateChoice.change(function(){
        if ( jQuery('input:radio[name=defaultDateChoice]:checked').val() == "Custom Date")
        {
            jQuery("#selectDefaultDate").show("slow");
            jQuery("#datepicker").val("");
        }
        else
        {
            jQuery("#selectDefaultDate").hide("slow");
            jQuery("#datepicker").val("Real Time Date");
        }
    });
    
    
    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('datepicker');
        },
        "Cancel": function() {
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
        },
        "Update Date Picker Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');

            if ( jQuery('input:radio[name=defaultDateChoice]:checked').val() == "Custom Date")
            {
                bValid = bValid && checkRegexp(datePickerDefaultDate,/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/,"Please Select a Default Select Date.");
            }
                    
            if (bValid) {
                        
                var formElementValues= datePickerValue.val();
                var dateFormats=  dateFormatDropDown.val();
                var defaultDates = datePickerDefaultDate.val();

                updateDatePicker(formNumber,formElementName,formElementValues,dateFormats,defaultDates);

            }
        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
    btnHelp.css('float', 'left');    

}

function updateDatePicker(formNumber,formElementName,formElementValue,dateFormat,defaultDate){

    var datePickerDataToPost = {
        "update":1,
        "formNumber":formNumber,
        "datePickerName": formElementName,
        "datePickerValue": formElementValue,
        "dateFormat": dateFormat,
        "defaultCustomDate":defaultDate
    };
    var myurlToUpdateDatePicker = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceDatePicker]").val();

    jQuery('#tempdivcontainer').load(myurlToUpdateDatePicker, datePickerDataToPost ,function postSuccessFunction(html) {
        var datePicker = jQuery('#tempdivcontainer #WYSIWYGDatepicker').html();
        jQuery('#tempdivcontainer').empty();
        if (datePicker == 0){
            updateErrorMessage("Could not update the text input parameters in the database. Please contact software administrator."); 
        } else {
            var datePickerErrorMessage = "<br>[JavaScript Conflict: Date Picker Object can not be displayed.\n\
It \"will\" be displayed in the built form.]<br>";
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementDatePicker">'+datePickerErrorMessage+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}

