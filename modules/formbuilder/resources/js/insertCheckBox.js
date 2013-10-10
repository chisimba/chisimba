//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the checkbox parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new checkbox identifier
 *  or a new checkbox or both. If you are creating a check box with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 *  \param checkboxLabel The actual label text for the form element.
 *  \param labelLayout A string position of the label with respect to the form
 *  element.
 *  \param addAnotherOption A boolean to determine whether or not to add another
 *  checkbox with the same form element indentifier.
 */
function insertCheckBox(formElementType,formName,formNumber,checkboxLabel,labelLayout,addAnotherOption)
{
jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-formElements" ).bind( "dialogclose", function(event, ui) {
        insertFormElement();
    });
    var myurlToCreateCheckBox = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToInsertFormElement]").val();
    var formnumber = jQuery("#getFormNumber").html();
    formnumber = jQuery.trim(formnumber);
    var dataToPost ={
        "formNumber":formnumber,
        "formName":formName,
        "formNumber":formNumber,
        "formElementType":formElementType
    };
    jQuery('#tempdivcontainer').load(myurlToCreateCheckBox , dataToPost ,function postSuccessFunction(html) {
        var insertCheckBoxFormContent =     jQuery('#tempdivcontainer').html();
        jQuery('#tempdivcontainer').empty();
        if (insertCheckBoxFormContent == 0)
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
                title: 'Insert Check Box'
            });
             jQuery("#dialog-box-formElements").children("#content").html(insertCheckBoxFormContent);

       var formElementID = jQuery(':input[name=uniqueFormElementID]');
       var optionValue = jQuery(':input[name=optionValue]');
              var optionLabel = jQuery(':input[name=optionLabel]');
              var layoutOption = jQuery(':input[name=formElementLayout]');
           var defaultOption = jQuery('#defaultOptionButton');
            var formElementLabel = jQuery(':input[name=formElementLabel]');
            var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
            defaultOption.button();
layoutOption.button();
//            jQuery('.ui-button-text').css('width','250px');
            jQuery("#checkboxLabelContainer").children().children('.ui-button-text').css('width','250px');
            jQuery("#checkboxLayoutContainer").children().children('.ui-button-text').css('width','250px');
            jQuery("#checkboxOptionAndValueContainer").children().children('.ui-button-text').css('width','250px');
            var allFields = jQuery([]).add(formElementID).add(optionValue).add(optionLabel);
            filterInput(formElementID);
           // filterInput(optionValue);
if (addAnotherOption != null)
{
                         formElementID.val(addAnotherOption);
                formElementID.attr('disabled', true);
                formElementID.addClass('ui-state-disabled');
                jQuery('input[name=labelOrientation]:checked').removeAttr('checked');
                  jQuery('input:radio[name="labelOrientation"]').filter('[value='+labelLayout+']').attr('checked', true);
            formElementLabelLayout.attr('disabled', true);
                  formElementLabelLayout.addClass('ui-state-disabled');

            formElementLabel.val(checkboxLabel);
                        formElementLabel.attr('disabled', true);
               formElementLabel.addClass('ui-state-disabled');
}
            formElementLabelLayout.button();
            jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                  "Help": function() {
                  setUpFormElementModalHelp('checkbox');
              },
                "Cancel": function() {
                    insertFormElement();
                    jQuery("#dialog-box-formElements").dialog("close");
                },
                "Insert Check Box Option": function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                    bValid = bValid && checkRegexp(formElementID,/^([0-9a-zA-Z])+$/,"The check box ID field only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(formElementID,'check box unique ID',5,150);
                  //  bValid = bValid && checkRegexp(optionValue,/^([0-9a-zA-Z])+$/,"The check box value only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(optionValue,'check box name',1,150);
                   // bValid = bValid && checkRegexp(optionLabel,/^([0-9a-zA-Z])+$/,"The check box label only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(optionLabel,'check box label',1,150);
                    if (bValid) {
                        var formElementIDs= formElementID.val();
                      var optionValues=  optionValue.val();
                     var optionLabels= optionLabel.val();

                         var formElementLayouts= jQuery('input:radio[name=formElementLayout]:checked').val();
                         var defaultOptions = jQuery("#defaultOptionButton:checked").val();
                                                 var formElementLabels=formElementLabel.val();
   var formElementLabelLayouts = jQuery('input:radio[name=labelOrientation]:checked').val();
                         if (addAnotherOption != null)
                             {
                                          produceCheckbox(formElementIDs,optionValues,optionLabels,formElementLabels,formElementLabelLayouts,formElementLayouts,defaultOptions);
                             }
                             else
                                 {
produceCheckBoxIdentifier(formElementIDs,optionValues,optionLabels,formElementLabels,formElementLabelLayouts,formElementLayouts,defaultOptions);
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
    });
}

/*!
 *  \brief This javascript function produces the actual checkbox form element and returns
 *  another member function that asks the designer whether or not to add another
 *  checkbox. It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier
 *  \param optionValue A string to store the actual html name of the form element
 *  \param optionLabel A string to store the actual label for the checkbox option.
 *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
 *  \param formElementLayout A string to store the break space before the form element option.
 *  \param defaultOption A string to determine whether or not the form element is
 *  selected by default.
 */
function produceCheckbox(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption)
{
    var formnumber = jQuery("#getFormNumber").html();
    formnumber = jQuery.trim(formnumber);
    
            var checkBoxDataToPost = {
                "formNumber":formnumber,
                "optionValue":optionValue,
                "optionLabel":optionLabel,
                "formElementName":formElementID,
                "defaultSelected":defaultOption,
                "layoutOption":formElementLayout,
               "formElementLabel": formElementLabel,
               "formElementLabelLayout":formElementLabelLayout
            };

            var myurlToProduceCheckbox = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceCheckbox]").val();
            jQuery('#tempdivcontainer').load(myurlToProduceCheckbox, checkBoxDataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);
                var checkbox = jQuery('#tempdivcontainer #WYSIWYGCheckbox').html();
                if (checkbox == 0)
                {
  updateErrorMessage("A check box with the ID \""+formElementID+"\" and value \""+optionLabel+"\" already exists in the database.\n\
Please choose a unique check box ID and value combination. Insert a unique check box value.");
                  //  jQuery(':input[name=uniqueFormElementID]').addClass('ui-state-error');
                    jQuery(':input[name=optionValue]').addClass('ui-state-error');
                }
                else
                {

                   
                   if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementCheckBox"></div>');
                                        jQuery("#WYSIWYGForm").children("#"+formElementID).append('<div id =input_'+formElementID+'></div>');
                jQuery("#WYSIWYGForm").children("#"+formElementID).children("#input_"+formElementID).replaceWith(checkbox);
                var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
                        //jQuery("#WYSIWYGForm").children("#"+formElementID).html(checkbox);
                             //    var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
                        highlightNewConstructedFormElement(elementToHighlight);
                }
                    else
                    {
//                        jQuery("#WYSIWYGForm").children("#"+formElementID).append(checkbox);
//                                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
//                        highlightNewConstructedFormElement(elementToHighlights);

                                        jQuery("#WYSIWYGForm").children("#"+formElementID).children("#"+formElementID).replaceWith(checkbox);
                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
                highlightNewConstructedFormElement(elementToHighlights);
               }
                   insertFormElement();
                    jQuery( "#dialog-box-formElements").dialog('close');
                    addAnotherCheckBoxOption(formElementID,formElementLabel,formElementLabelLayout);
                }
            });
}

/*!
 *  \brief This javascript function produces the form element identifier for the
 *  new checkbox element and returns a function that actual creates the checkbox.
 *  \note This function actually inserts the metadata for the form element which includes
 *  in which form it belongs to and in which order it is put.
 *  \param formElementID A string that stores the form element identifier
 *  \param optionValue A string to store the actual html name of the form element
 *  \param optionLabel A string to store the actual label for the checkbox option.
 *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
 *  \param formElementLayout A string to store the break space before the form element option.
 *  \param defaultOption A string to determine whether or not the form element is
 *  selected by default.
 */
function produceCheckBoxIdentifier(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption)
{
 var myurlToCheckBoxIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'checkbox',
        "formElementName":formElementID
    };

    jQuery('#tempdivcontainer').load(myurlToCheckBoxIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
           produceCheckbox(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption);

        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The check box ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique check box ID.");
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
 *  \brief After a check box has been successfully inserted, this javascript
 *  function produces a dialog box to ask the designer to add another check box.
 *  \param formElementID A string that stores the form element identifier.
  *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
 */
function addAnotherCheckBoxOption(formElementID,formElementLabel,formElementLabelLayout)
{
   var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
 jQuery( "#dialog-box").dialog({ title: 'Add Another Check Box Option?' });
            jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>A check box option with an id \"'+formElementID+'\" has\n\
been inserted successfully.</p><p><span class="ui-icon ui-icon-arrowrefresh-1-w" style="float:left; margin:0 7px 20px 0;"></span>Do you want to add another check box option with this same\n\
id?</p>');
            jQuery("#dialog-box").dialog("open");
            jQuery( "#dialog-box" ).dialog( "option", "buttons", {
                "Add Another Check Box Option": function() {
                          jQuery(this).dialog("close");
insertCheckBox('checkbox',formname,formnumber,formElementLabel,formElementLabelLayout,formElementID);

                },  "Save": function() {
                    jQuery(this).dialog("close");
                }
            });
}