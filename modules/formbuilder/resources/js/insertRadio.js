//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This javascript function first gets the radio button parameters insert form
 *  and displays it with all the javascript validiation.
 *  \brief Once the designer hits submit, it either creates a new radio button identifier
 *  or a new radio button or both. If you are creating a radio button with the same form element
 *  identifier, the latter will happen.
 *  \param formElementType A string that stores the form element type
 *  \param formName A string that stores the form name metadata.
 *  \param formNumber An integer.
 *  \param radioLabel The actual label text for the form element.
 *  \param labelLayout A string position of the label with respect to the form
 *  element.
 *  \param addAnotherOption A boolean to determine whether or not to add another
 *  checkbox with the same form element indentifier.
 *  \param defaultAlreadySelected A boolean. True if a previous radio button
 *  option has been selected as default.
 */
function insertRadio(formElementType,formName,formNumber,addAnotherOption,radioLabel,labelLayout,defaultAlreadySelected)
{
    jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-formElements" ).bind( "dialogclose", function(event, ui) {
        insertFormElement();
    });
    var myurlToCreateRadio = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToInsertFormElement]").val();
    var dataToPost ={
        "formName":formName,
        "formNumber":formNumber,
        "formElementType":formElementType
    };
    jQuery('#tempdivcontainer').load(myurlToCreateRadio , dataToPost ,function postSuccessFunction(html) {
        var insertRadioFormContent =     jQuery('#tempdivcontainer').html();
        jQuery('#tempdivcontainer').empty();
        if (insertRadioFormContent == 0)
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
                title: 'Insert Radio'
            });
            jQuery("#dialog-box-formElements").children("#content").html(insertRadioFormContent);

            var formElementID = jQuery(':input[name=uniqueFormElementID]');
            var optionValue = jQuery(':input[name=optionValue]');
            var optionLabel = jQuery(':input[name=optionLabel]');
            var layoutOption = jQuery(':input[name=formElementLayout]');
            var defaultOption = jQuery('#defaultOptionButton');
            var formElementLabel = jQuery(':input[name=formElementLabel]');
            var formElementLabelLayout = jQuery(':input[name=labelOrientation]');
            defaultOption.button();
            layoutOption.button();
            //jQuery('.ui-button-text').css('width','250px');
            var allFields = jQuery([]).add(formElementID).add(optionValue).add(optionLabel);
            filterInput(formElementID);
            //filterInput(optionValue);
            if (addAnotherOption != null)
            {
                formElementID.val(addAnotherOption);
                formElementID.attr('disabled', true);
                formElementID.addClass('ui-state-disabled');
                jQuery('input[name=labelOrientation]:checked').removeAttr('checked');
                  jQuery('input:radio[name="labelOrientation"]').filter('[value='+labelLayout+']').attr('checked', true);
            formElementLabelLayout.attr('disabled', true);
                  formElementLabelLayout.addClass('ui-state-disabled');

            formElementLabel.val(radioLabel);
                        formElementLabel.attr('disabled', true);
               formElementLabel.addClass('ui-state-disabled');
            }
            formElementLabelLayout.button();
            if (defaultAlreadySelected == 'on')
            {
                defaultOption.button({
                    disabled: true
                });
                jQuery("label[for=defaultOptionButton]").val("A radio option has already been selected as default");
                defaultOption.after("A radio option has already been selected as default.");
            }
            jQuery( "#dialog-box-formElements" ).dialog( "option", "buttons", {
                        "Help": function() {
                  setUpFormElementModalHelp('radio');
              },
                "Cancel": function() {
                    insertFormElement();
                    jQuery("#dialog-box-formElements").dialog("close");
                },
               "Insert Radio Option": function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                    bValid = bValid && checkRegexp(formElementID,/^([0-9a-zA-Z])+$/,"The radio ID field only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(formElementID,'radio unique ID',5,150);
                   // bValid = bValid && checkRegexp(optionValue,/^([0-9a-zA-Z])+$/,"The radio value only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(optionValue,'radio value',1,150);
                    //  bValid = bValid && checkRegexp(optionLabel,/^([0-9a-zA-Z])+$/,"The radio label only allows alphanumeric characters (a-z 0-9).");
                    bValid = bValid && checkLength(optionLabel,'radio label',1,150);
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
                            produceRadio(formElementIDs,optionValues,optionLabels,formElementLabels,formElementLabelLayouts,formElementLayouts,defaultOptions,defaultAlreadySelected);
                        }
                        else
                        {
                            produceRadioIdentifier(formElementIDs,optionValues,optionLabels,formElementLabels,formElementLabelLayouts,formElementLayouts,defaultOptions);
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
 *  \brief This javascript function produces the actual radio form element and returns
 *  another member function that asks the designer whether or not to add another
 *  radio button option. It also highlights the new created form element.
 *  \param formElementID A string that stores the form element identifier and the html name of
 *  the form element.
 *  \param optionValue A string to store the value for the form element option.
 *  \param optionLabel A string to store the actual label for the form element option.
 *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
 *  \param defaultOption A string to determine whether or not the form element option is
 *  selected by default.
 *  \param defaultAlreadySelected A string to determine whether or not an option before
 *  this option has been previous been selected as default.
 */
function   produceRadio(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption,defaultAlreadySelected)
{
      var formnumber = jQuery("#getFormNumber").html();
    formnumber = jQuery.trim(formnumber);
    
    var RadioDataToPost = {
        "formNumber":formnumber,
        "optionValue":optionValue,
        "optionLabel":optionLabel,
        "formElementName":formElementID,
        "defaultSelected":defaultOption,
        "layoutOption":formElementLayout,
       "formElementLabel": formElementLabel,
       "formElementLabelLayout":formElementLabelLayout
    };

    var myurlToCreateRadio = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceRadio]").val();

    jQuery('#tempdivcontainer').load(myurlToCreateRadio, RadioDataToPost ,function postSuccessFunction(html) {
        jQuery('#tempdivcontainer').html(html);
        var radio = jQuery('#tempdivcontainer #WYSIWYGRadio').html();
        if (radio == 0)
        {
            updateErrorMessage("A radio with the ID \""+formElementID+"\" and value \""+optionLabel+"\" already exists in the database.\n\
Please choose a unique radio ID and value combination. Insert a unique radio value.");
            jQuery(':input[name=optionValue]').addClass('ui-state-error');
        }
        else
        {

//                         jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementDropDown"></div>');
//                jQuery("#WYSIWYGForm").children("#"+formElementID).append('<div id =input_'+formElementID+'></div>');
//                jQuery("#WYSIWYGForm").children("#"+formElementID).children("#input_"+formElementID).replaceWith(dropdown);
//                var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
//                highlightNewConstructedFormElement(elementToHighlight);

         if (jQuery("#WYSIWYGForm").children("#"+formElementID).length <= 0)
            {
                jQuery("#WYSIWYGForm").append('<div id ='+formElementID+' class="witsCCMSFormElementRadio"></div>');
                 jQuery("#WYSIWYGForm").children("#"+formElementID).append('<div id ='+formElementID+'></div>');
//                jQuery("#WYSIWYGForm").children("#"+formElementID).children("#input_"+formElementID).html(radio);
                             jQuery("#WYSIWYGForm").children("#"+formElementID).children("#"+formElementID).replaceWith(radio);
             var elementToHighlight = jQuery("#WYSIWYGForm").children("#"+formElementID);
                highlightNewConstructedFormElement(elementToHighlight);

            }
            else
            {



                 jQuery("#WYSIWYGForm").children("#"+formElementID).children("#"+formElementID).replaceWith(radio);
                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
                highlightNewConstructedFormElement(elementToHighlights);

//              jQuery("#WYSIWYGForm").children("#"+formElementID).append(radio);
//                var elementToHighlights = jQuery("#WYSIWYGForm").children("#"+formElementID);
//                highlightNewConstructedFormElement(elementToHighlights);

            }
            insertFormElement();
            jQuery( "#dialog-box-formElements").dialog('close');
            addAnotherRadioOption(formElementID,defaultOption,formElementLabel,formElementLabelLayout,defaultAlreadySelected);

        }

    });
}

/*!
 *  \brief This javascript function produces the form element identifier for the
 *  new radio element and returns a function that actual creates the radio.
 *  \note This function actually inserts the metadata for the form element which includes
 *  in which form it belongs to and in which order it is put.
 *  \param formElementID A string that stores the form element identifier and the html name of
 *  the form element.
 *  \param optionValue A string to store the value for the form element option.
 *  \param optionLabel A string to store the actual label for the form element option.
 *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
 *  \param defaultOption A string to determine whether or not the form element option is
 *  selected by default.
 */
function produceRadioIdentifier(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption)
{
    var myurlToRadioIndentifier = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToCreateANewFormElement]").val();
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    var dataToPost = {
        "formNumber":formnumber,
        "formName":formname,
        "formElementType":'radio',
        "formElementName":formElementID
    };

    jQuery('#tempdivcontainer').load(myurlToRadioIndentifier, dataToPost ,function postSuccessFunction(html) {

        var postSuccessBoolean =jQuery('#tempdivcontainer #postSuccess').html();
        jQuery('#tempdivcontainer').empty();
        if (postSuccessBoolean == 1)
        {
            produceRadio(formElementID,optionValue,optionLabel,formElementLabel,formElementLabelLayout,formElementLayout,defaultOption);

        }
        else if (postSuccessBoolean == 0)
        {
            updateErrorMessage("The radio ID \""+formElementID+"\" already exists in the database.\n\
Please choose a unique radio ID.");
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
 *  \brief After a radio element has been successfully inserted, this javascript
 *  function produces a dialog box to ask the designer to add another a radio button option.
 *  \param formElementID A string that stores the form element identifier.
  *  \param formElementLabel A string to store the actual label text for the actual
 *  entire form element.
 *  \param formElementLabelLayout A string to store the relative postion of the
 *  label to the form element.
  *  \param defaultAlreadySelected A string to determine whether or not an option before
 *  this option has been previous been selected as default.
 */
function addAnotherRadioOption(formElementID,defaultOption,formElementLabel,formElementLabelLayout,defaultAlreadySelected)
{
    var formname = jQuery("#getFormName").html();
    var formnumber = jQuery("#getFormNumber").html();
    formname = jQuery.trim(formname);
    formnumber = jQuery.trim(formnumber);
    jQuery( "#dialog-box").dialog({
        title: 'Add Another Radio Option?'
    });
    jQuery("#dialog-box").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>A radio option with an id \"'+formElementID+'\" has\n\
been inserted successfully.</p><p><span class="ui-icon ui-icon-arrowrefresh-1-w" style="float:left; margin:0 7px 20px 0;"></span>Do you want to add another radio option with this same\n\
id?</p>');
    jQuery("#dialog-box").dialog("open");
    jQuery( "#dialog-box" ).dialog( "option", "buttons", {
        "Add Another Radio Option": function() {
            jQuery(this).dialog("close");
            if (defaultOption == 'on' ||defaultAlreadySelected == 'on')
            {
                insertRadio('radio',formname,formnumber,formElementID,formElementLabel,formElementLabelLayout,'on');
            }
            else
            {
                insertRadio('radio',formname,formnumber,formElementID,formElementLabel,formElementLabelLayout,defaultOption);
            }

        },
        "Save": function() {
            jQuery(this).dialog("close");
        }
    });
}