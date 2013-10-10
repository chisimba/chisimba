//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the label parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new label identifier
 *  or a new label or both. If you are creating a label with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 */
function insertLabel(formElementType,formName,formNumber)
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
            jQuery("#dialog-box-formElements").dialog({
                title: 'Form Element Does Not Exist'
            });
            jQuery("#dialog-box-formElements").children("#content").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>Internal Error. Form Element\n\
does not exist. Please contact your software administrator.</p>');
            jQuery( "#dialog-box-formElements").dialog( "option", "buttons", {
                "OK": function() {
             insertFormElement();
                    jQuery(this).dialog("close");
                }
            });
    }
    else
    {
        jQuery( "#dialog-box-formElements").dialog({
            title: 'Insert Label'
        });
        jQuery("#dialog-box-formElements").children("#content").html(insertLabelFormContent);
        var formElementID = jQuery(':input[name=uniqueFormElementID]');
        var formElementLayout = jQuery(':input[name=formElementLayout]');
        var formElementText = jQuery(':input[name=formElementDesiredText]');
  formElementLayout.button();
  //formElementLayout.css('width','200px');
//  jQuery('.ui-button-text').css('width','150px');
  jQuery("#labelSizeContainer").children().children('.ui-button-text').css('width','250px');
        var allFields = jQuery([]).add(formElementID).add(formElementText);
        filterInput(formElementID);
        jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                                                      "Help": function() {
                  setUpFormElementModalHelp('label');
              },
            "Cancel": function() {
             insertFormElement();
             jQuery("#dialog-box-formElements").dialog("close");
                          insertFormElement();
            },
           "Insert Label": function() {
                var bValid = true;
                allFields.removeClass('ui-state-error');
                bValid = bValid && checkRegexp(formElementID,/^([0-9a-zA-Z])+$/,"The insert ID field only allow alphanumeric characters (a-z 0-9).");
                bValid = bValid && checkLength(formElementID,'unique ID',5,100);
                bValid = bValid && checkLength(formElementText," desired text for label",1,550);
                if (bValid) {
                    var formElementIDs= formElementID.val();

                    var formElementLayouts= jQuery('input:radio[name=formElementLayout]:checked').val();
                    var formElementTexts= formElementText.val();
                    produceLabel(formElementIDs,formElementLayouts,formElementTexts);
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
 *  \brief This javascript function produces the actual label form element and the form
 *  element identifier for label.
 *   It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier.
  *  \param formElementLayout A string to store the break space before the form element option.
 *  \param formElementText A string to store the actual label text.
 */
function produceLabel(formElementID,formElementLayout,formElementText)
{
    var myurlToCreateLabelIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'label',
        "formElementName":formElementID
    };

    jQuery('#tempdivcontainer').load(myurlToCreateLabelIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
            var formnumber = jQuery("#getFormNumber").html();
            formnumber = jQuery.trim(formnumber);
            
            var labelDataToPost = {
                "formNumber":formnumber,
                "labelValue":formElementText,
                "formElementName":formElementID,
                "layoutOption":formElementLayout
            };
            var myurlToCreateLabel = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceLabel]").val();
            jQuery('#tempdivcontainer').load(myurlToCreateLabel, labelDataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);

                var label = jQuery('#tempdivcontainer #WYSIWYGLabel').html();
                if (label == 0)
                {
                    updateErrorMessage("A label with the ID \""+formElementID+"\" and text \""+formElementText+"\" already exists in the database.\n\
Please choose a unique label ID and text combination.");
                    jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
                    jQuery(':input[name=formElementDesiredText]').addClass('ui-state-error');
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementLabel"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementID).html(label);
          var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
  highlightNewConstructedFormElement(elementToHighlight);
                    }

                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementID).append(label);
          var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
  highlightNewConstructedFormElement(elementToHighlights);
                    }
  jQuery("#dialog-box-formElements").children("#content").empty();
                    jQuery( "#dialog-box-formElements").dialog('close');
                }
            });
        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The label ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique label ID.");
            jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
        }
        else
        {

            jQuery( "#dialog-box-formElements").dialog('close');
            produceInsertErrorMessage();
        }
    });


}