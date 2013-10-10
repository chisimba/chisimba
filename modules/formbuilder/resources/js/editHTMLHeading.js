function LoadEditHTMLHeadingForm(formNumber,formElementName,formElementType){
    var myurlToEditHTMLHeading = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToEditFormElement]").val();
    var dataToPost ={
        "formNumber":formNumber,
        "formElementName":formElementName,
        "formElementType":formElementType
    };
    jQuery( "#dialog-box-editFormElements").children("#content").load(myurlToEditHTMLHeading , dataToPost ,function postSuccessFunction(html) {
        if (jQuery( "#dialog-box-editFormElements").children("#content").html() == 0){
            jQuery("#dialog-box-editFormElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist or is empty. Please contact your software administrator.</p>');   
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");     
        }else{
            setUpEditHTMLHeadingForm(formNumber,formElementName);
            jQuery( "#dialog-box-editFormElements").children("#content").fadeIn("slow");   
       
        }
    });
}

function setUpEditHTMLHeadingForm(formNumber,formElementName){
    var fontSize = jQuery(':input[name=fontSize]');
    var textAlignment = jQuery(':input[name=textAlignment]');
    var formElementText = jQuery(':input[name=formElementDesiredText]');
    fontSize.button();
    textAlignment.button();

    jQuery("#HeadingPropertiesContainer").children().children('.ui-button-text').css('width','150px');
    var allFields = jQuery([]).add(formElementText);

    jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
        "Help": function() {
            setUpFormElementModalHelp('htmlheading');
        },
        "Cancel": function() {
            insertFormElement();
            jQuery("#dialog-box-editFormElements").dialog("close");
            jQuery("#dialog-box-editFormElements").children("#content").empty();
        },
        "Update HTML Heading Parameters": function() {
            var bValid = true;
            allFields.removeClass('ui-state-error');

            bValid = bValid && checkLength(formElementText," desired text for HTML Heading",1,550);
            if (bValid) {


                var fontSizes= jQuery('input:radio[name=fontSize]:checked').val();
                var textAlignments=jQuery('input:radio[name=textAlignment]:checked').val();
                var formElementTexts= formElementText.val();
                updateHTMLHeading(formNumber,formElementName,fontSizes,textAlignments,formElementTexts);
            }

        }
    });
    jQuery(".ui-dialog-buttonset").css('width','920px');
    jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
    var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
    btnHelp.css('float', 'left');
}

function updateHTMLHeading(formNumber,formElementName,fontSize,textAlignment,formElementText){
    var HTMLHeadingdataToPost = {
        "update":1,
        "formNumber":formNumber,
        "formElementName": formElementName,
        "HTMLHeadingValue": formElementText,
        "fontSize": fontSize,
        "textAlignment": textAlignment
    };

    var myurlToUpdateHTMLHeading = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceHTMLHeading]").val();

    jQuery('#tempdivcontainer').load(myurlToUpdateHTMLHeading, HTMLHeadingdataToPost ,function postSuccessFunction(html) {
                
        var HTMLHeading = jQuery('#tempdivcontainer #WYSIWYGHTMLHeading').html();
        jQuery('#tempdivcontainer').empty();
        if (HTMLHeading == 0){
            updateErrorMessage("Could not update the HTML Heading parameters in the database. Please contact software administrator."); 
        } else {
            jQuery("#WYSIWYGForm").children("#"+formElementName).replaceWith('<div id ='+formElementName+' class="witsCCMSFormElementHTMLHeading">'+HTMLHeading+'</div>');
            var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementName);
            jQuery( "#dialog-box-editFormElements").dialog('close');
            jQuery("#dialog-box-editFormElements").children("#content").empty();
            highlightNewConstructedFormElement(elementToHighlight);        
        }  
    });
}