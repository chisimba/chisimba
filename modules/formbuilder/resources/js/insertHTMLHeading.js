//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the html heading parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new drop down list identifier
 *  or a new html heading or both. If you are creating a html heading with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 */
function insertHTMLHeading(formElementType,formName,formNumber)
{
  jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-formElements" ).bind( "dialogclose", function(event, ui) {
           insertFormElement();
});
  var myurlToCreateLabel = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToInsertFormElement]").val();
    var dataToPost ={
        "formName":formName,
        "formNumber":formNumber,
        "formElementType":formElementType
    };
    jQuery('#tempdivcontainer').load(myurlToCreateLabel, dataToPost ,function postSuccessFunction(html) {
        var insertLabelFormContent =     jQuery('#tempdivcontainer').html();
        jQuery('#tempdivcontainer').empty();
        if (insertLabelFormContent == 0)
        {
            jQuery( "#dialog-box-formElements").dialog({
                title: 'Form Element Does Not Exist'
            });
            jQuery("#dialog-box-formElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist. Please contact your software administrator.</p>');
            jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                "OK": function() {

                    jQuery(this).dialog("close");
                }
            });
    }
    else
    {
        jQuery( "#dialog-box-formElements").dialog({
            title: 'Insert HTML Heading'
        });
        jQuery("#dialog-box-formElements").children("#content").html(insertLabelFormContent);
        var formElementID = jQuery(':input[name=uniqueFormElementID]');
        var fontSize = jQuery(':input[name=fontSize]');
                var textAlignment = jQuery(':input[name=textAlignment]');
        var formElementText = jQuery(':input[name=formElementDesiredText]');
        fontSize.button();
        textAlignment.button();
//  jQuery('.ui-button-text').css('width','100px');
  jQuery("#HeadingPropertiesContainer").children().children('.ui-button-text').css('width','150px');
        var allFields = jQuery([]).add(formElementID).add(formElementText);
        filterInput(formElementID);
        jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                                           "Help": function() {
                  setUpFormElementModalHelp('htmlheading');
              },
            "Cancel": function() {
             insertFormElement();
             jQuery("#dialog-box-formElements").dialog("close");
            },
         "Insert HTML Heading": function() {
                var bValid = true;
                allFields.removeClass('ui-state-error');
                bValid = bValid && checkRegexp(formElementID,/^([0-9a-zA-Z])+$/,"The HTML Heading ID field only allows alphanumeric characters (a-z 0-9).");
                bValid = bValid && checkLength(formElementID,'unique ID',5,100);
                bValid = bValid && checkLength(formElementText," desired text for HTML Heading",1,550);
                if (bValid) {
                    var formElementIDs= formElementID.val();
                               jQuery(".ui-dialog-buttonset").css('width','870px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');
                    var fontSizes= jQuery('input:radio[name=fontSize]:checked').val();
                    var textAlignments=jQuery('input:radio[name=textAlignment]:checked').val();
                    var formElementTexts= formElementText.val();
                    produceHTMLHeading(formElementIDs,fontSizes,textAlignments,formElementTexts);
                }

            }
        });


    }

    jQuery("#dialog-box-formElements").dialog("open");
                                   jQuery(".ui-dialog-buttonset").css('width','920px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');
    });
}

/*!
 *  \brief This javascript function produces the actual html heading form element and the form
 *  element identifier for html heading.
 *   It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier and the html name of
 *  the form element.
 *  \param fontSize An integer to store the font size of the form element.
 *  \param textAlignment A string to set the text alignment of the form element.
 *  \param formElementText A string to store the actual html heading text.
 */
function produceHTMLHeading(formElementID,fontSize,textAlignment,formElementText)
{
    var myurlToCreateLabelIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'HTML_heading',
        "formElementName":formElementID
    };

    jQuery('#tempdivcontainer').load(myurlToCreateLabelIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
            var formnumber = jQuery("#getFormNumber").html();
            formnumber = jQuery.trim(formnumber);
    
            var HTMLHeadingdataToPost = {
                "formNumber":formnumber,
                "formElementName": formElementID,
                "HTMLHeadingValue": formElementText,
                "fontSize": fontSize,
                "textAlignment": textAlignment
            };

            var myurlToCreateHTMLHeading = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceHTMLHeading]").val();

            jQuery('#tempdivcontainer').load(myurlToCreateHTMLHeading, HTMLHeadingdataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);

                var HTMLheading = jQuery('#tempdivcontainer #WYSIWYGHTMLHeading').html();
                if (HTMLheading == 0)
                {
                   updateErrorMessage("A HTML Heading with the ID \""+formElementID+"\" and text \""+formElementText+"\" already exists in the database.\n\
Please choose a unique HTML Heading ID and text combination.");
                    jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
                    jQuery(':input[name=formElementDesiredText]').addClass('ui-state-error');
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementHTMLHeading"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementID).html(HTMLheading);
                       var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
  highlightNewConstructedFormElement(elementToHighlight);
                    }

                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementID).append(HTMLheading);
                                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
  highlightNewConstructedFormElement(elementToHighlights);
                    }
insertFormElement();
                    jQuery( "#dialog-box-formElements").dialog('close');
                }
            });
        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The HTML Heading ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique HTML Heading ID.");
            jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
        }
        else
        {
               insertFormElement();
            jQuery( "#dialog-box-formElements").dialog('close');
            produceInsertErrorMessage();
        }
    });
}