//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the button parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new button identifier
 *  or a new button or both. If you are creating a button with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 *  \param addAnotherOption A boolean to determine whether or not to add another
 *  button with the same form element indentifier.
 */
function insertButton(formElementType,formName,formNumber,addAnotherOption)
{
    jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-formElements" ).bind( "dialogclose", function(event, ui) {
        insertFormElement();
    });
    var myurlToCreateButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToInsertFormElement]").val();
    var dataToPost ={
        "formName":formName,
        "formNumber":formNumber,
        "formElementType":formElementType
    };
    jQuery('#tempdivcontainer').load(myurlToCreateButton , dataToPost ,function postSuccessFunction(html) {
        var insertButtonFormContent =     jQuery('#tempdivcontainer').html();
        jQuery('#tempdivcontainer').empty();
        if (insertButtonFormContent == 0)
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
                title: 'Insert Button'
            });
            jQuery("#dialog-box-formElements").children("#content").html(insertButtonFormContent);

            var formElementID = jQuery(':input[name=uniqueFormElementID]');
            var formElementName = jQuery(':input[name=uniqueFormElementName]');
            var resetOrSubmitButton = jQuery(':input[name=resetOrSubmitButton]');
            var buttonLabel = jQuery(':input[name=buttonLabel]');

            resetOrSubmitButton.button();
            jQuery("#buttonPropertiesContainer").children().children('.ui-button-text').css('width','250px');
            var allFields = jQuery([]).add(formElementID).add(formElementName).add(buttonLabel);
            filterInput(formElementID);
            filterInput(formElementName);
            if (addAnotherOption != null)
            {
                formElementID.val(addAnotherOption);
                formElementID.attr('disabled', true);
                formElementID.addClass('ui-state-disabled');
            }
            //if (defaultAlreadySelected == 'on')
            //{
            //     defaultOption.button({ disabled: true });
            //jQuery("label[for=defaultOptionButton]").val("A radio option has already been selected as default");
            //defaultOption.after("A radio option has already been selected as default.");
            //}
            jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                                "Help": function() {
setUpFormElementModalHelp('submitbuttons');
                },
                "Cancel": function() {
                    insertFormElement();

                    jQuery("#dialog-box-formElements").dialog("close");
                       
                },
                "Insert Button": function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                    bValid = bValid && checkRegexp(formElementID,/^([0-9a-zA-Z])+$/,"The button ID field only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(formElementID,'button unique ID',5,150);
                    bValid = bValid && checkRegexp(formElementName,/^([0-9a-zA-Z])+$/,"The button name only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(formElementName,'button name',1,150);
                    //  bValid = bValid && checkRegexp(optionLabel,/^([0-9a-zA-Z])+$/,"The radio label only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(buttonLabel,'button label',1,150);
                    if (bValid) {
                        var formElementIDs= formElementID.val();
                        var formElementNames=  formElementName.val();
                        var buttonLabels= buttonLabel.val();
                        var sumbitOrResetOption= jQuery('input:radio[name=resetOrSubmitButton]:checked').val();
                        if (addAnotherOption != null)
                        {
                            produceButton(formElementIDs,formElementNames,buttonLabels,sumbitOrResetOption);
                        }
                        else
                        {
                            produceButtonIdentifier(formElementIDs,formElementNames,buttonLabels,sumbitOrResetOption);
                        }
                    }
                }
            });


        }


       jQuery("#dialog-box-formElements").dialog("open");
       jQuery(".ui-dialog-buttonset").css('width','920px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');
       //jQuery(".ui-dialog-buttonpane").find("button:first").css('float', 'left');
//        jQuery('.ui-dialog-buttonpane').find('button').css('float', 'left');
//        var btnDelete = jQuery('.ui-dialog-buttonpane').find('button:contains("Insert Button")');
//        btnDelete.css('float', 'left');
//btnDelete.prepend('<span style="float:left; margin-top: 5px;" class="ui-icon ui-icon-trash"></span>');
//btnDelete.width(btnDelete.width() + 25);
    });
}

/*!
 *  \brief This javascript function produces the actual button form element and returns
 *  another member function that asks the designer whether or not to add another
 *  button. It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier.
 *  \param formElementName A string to store the actual html name of the form element.
 *  \param buttonLabel A string to store the actual label for the button.
 *  \param submitOrResetOption A string to set whether is button is a 'reset' or
 *  a 'submit'button'.
 */
function produceButton(formElementID,formElementName,buttonLabel,sumbitOrResetOption)
{
    var formnumber = jQuery("#getFormNumber").html();
    formnumber = jQuery.trim(formnumber);
    var buttonDataToPost = {
        "formNumber":formnumber,
        "formElementName": formElementID,
        "buttonName": formElementName,
        "buttonLabel" : buttonLabel,
        "submitOrResetButtonChoice": sumbitOrResetOption
    };
    var myurlToProduceButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceButton]").val();

    jQuery('#tempdivcontainer').load(myurlToProduceButton, buttonDataToPost ,function postSuccessFunction(html) {
        jQuery('#tempdivcontainer').html(html);

        var button = jQuery('#tempdivcontainer #WYSIWYGButton').html();
        if (button == 0)
        {
            updateErrorMessage("A button with the ID \""+formElementID+"\" and name \""+formElementName+"\" already exists in the database.\n\
Please choose a unique button ID and name combination. Insert a unique button name.");
            jQuery(':input[name=uniqueFormElementName]').addClass('ui-state-error');
        }
        else
        {
            if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
            {
                jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementButton"></div>');
                jQuery("#WYSIWYGForm").children("#"+formElementID).html(button);
                var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
                highlightNewConstructedFormElement(elementToHighlight);
            }

            else
            {

                jQuery("#WYSIWYGForm").children("#"+formElementID).empty();
                   jQuery("#WYSIWYGForm").children("#"+formElementID).html(button);
                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
                highlightNewConstructedFormElement(elementToHighlights);

            }
            insertFormElement();
            jQuery( "#dialog-box-formElements").dialog('close');
            addAnotherButtonOption(formElementID);
        }

    });
}

/*!
 *  \brief This javascript function produces the form element identifier for the
 *  new button element and returns a function that actual creates the button.
 *  \param formElementID A string that stores the form element identifier.
 *  \param formElementName A string to store the actual html name of the form element.
 *  \param buttonLabel A string to store the actual label for the button.
 *  \param submitOrResetOption A string to set whether is button is a 'reset' or
 *  a 'submit'button'.
 */
function produceButtonIdentifier(formElementID,formElementName,buttonLabel,sumbitOrResetOption)
{
    var myurlToButtonIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'button',
        "formElementName":formElementID
    };

    jQuery('#tempdivcontainer').load(myurlToButtonIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
            produceButton(formElementID,formElementName,buttonLabel,sumbitOrResetOption);

        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The button ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique button ID.");
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

/*!
 *  \brief After a button has been successfully inserted, this javascript
 *  function produces a dialog box to ask the designer to add another button.
 *  \param formElementID A string that stores the form element identifier.
 */
function addAnotherButtonOption(formElementID)
{
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    jQuery( "#dialog-box").dialog({
        title: 'Add Another Button?'
    });
    jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>A button with an id \"'+formElementID+'\" has\n\
been inserted successfully.</p><p><span class="ui-icon ui-icon-arrowrefresh-1-w" style="float:left; margin:0 7px 20px 0;"></span>Do you want to add another button next\n\
to your newly created one?</p>');
    jQuery("#dialog-box").dialog("open");
    jQuery( "#dialog-box" ).dialog( "option", "buttons", {
        "Add Another Button": function() {
            jQuery(this).dialog("close");

            insertButton('button',formname,formnumber,formElementID);
        },
        "Save": function() {
            jQuery(this).dialog("close");
        }
    });
}