//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This class models all the content and functionality for the
 * for WYSIWYG form element.
 * \note This js file is used inside the old WYSIWYG form eidtor and is not
 * being used any more. The code in this javascript file is dead.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
function insertTextInputParameters(formElementLabel)
{
    jQuery("#addParametersForTextInput").show();
    jQuery("#addParametersForTextInput").children("#StylingForTextInput").show("slow");
    jQuery("#addParametersForTextInput").children("#insertTextInputName").hide("slow");
    jQuery("#addParametersForDatePicker").children("#insertDatePickerName").hide("slow");
    jQuery("#addParametersForTextInput").children("#endOfInsertionTextInput").show("slow");
    jQuery("#addParametersForTextInput").children("span").show("slow");

    jQuery('.textInputSizeMenu').bind('keypress keydown keyup',function() {
        var test = jQuery('.textInputSizeMenu').val();
        var numeric1= "0123456789";
        var test1 = test.charAt(test.length-1);
        var bool = numeric1.search(test1);
        if (bool == -1)
        {
            test= test.substr(0,test.length-1);
        }
        jQuery('.textInputSizeMenu').val(test);
    });

    jQuery("input:radio[name=textorPasswordRadio]").change(function(){

        if ( jQuery('input:radio[name=textorPasswordRadio]:checked').val() == "text")
        {
            jQuery("#addParametersForTextInput").children("#StylingForTextInput").children('#setTextMenu').show('slow');
            jQuery("#addParametersForTextInput").children("#StylingForTextInput").children("#setMaskedInput").show("slow");
            jQuery("input:radio[name=maskedInputChoice]").removeAttr("disabled");
            jQuery("input:radio[name=maskedInputChoice]:eq(0)").attr('checked', "checked");
            jQuery("textarea[name=setDefaulttext]").removeAttr("disabled");
            jQuery("textarea[name=setDefaulttext]").val("Insert Default text to be displayed inside text input field.");
        }
        else
        {
            jQuery("#addParametersForTextInput").children("#StylingForTextInput").children('#setTextMenu').hide('slow');
            jQuery("#addParametersForTextInput").children("#StylingForTextInput").children("#setMaskedInput").hide("slow");
            jQuery("input:radio[name=maskedInputChoice]").attr("disabled","disabled");
            jQuery("textarea[name=setDefaulttext]").attr("disabled","disabled");
            jQuery("input:radio[name=maskedInputChoice]:eq(0)").attr('checked', "checked");
            jQuery("textarea[name=setDefaulttext]").val("");
        }
    });


    jQuery("#submitTextFieldParameters").unbind('click').bind('click',function () {
        jQuery('.textInputSizeMenu').unbind('keypress keydown keyup');
        var textInputSize = jQuery('.textInputSizeMenu').val();
        if (textInputSize <= 1 || textInputSize >= 150)
        {
            jQuery("#addParametersForTextInput").children("span").html("\
<br>ERROR!! The text input size has to between 1 and 150");
            jQuery("#addParametersForTextInput").children("span").show("slow");
        }

        else if ( jQuery('input:radio[name=textorPasswordRadio]:checked').val() == "text")
        {
            var defaultText = jQuery("textarea[name=setDefaulttext]").val();
            var maskedInputChoice = jQuery("input:radio[name=maskedInputChoice]:checked").val();

            jQuery("#addParametersForTextInput").children("span").html("\
<br>The text field input size is set to \""+textInputSize+"\" characters.<br> \n\
The default text is set to \""+defaultText+"\" to be displayed inside the text field.<br> \n\
The input mask is set to \""+maskedInputChoice+"\" to mask the text input.");
            jQuery("#addParametersForTextInput").children("#StylingForTextInput").hide("slow");
            jQuery("#addParametersForTextInput").children("#endOfInsertionTextInput").hide("slow");
            jQuery("#addParametersForTextInput").children("#insertTextInputName").show("slow");
            jQuery("#addParametersForTextInput").children("span").show("slow");
        }
        else
        {

            jQuery("#addParametersForTextInput").children("span").html("\
<br>The text field input size is set to \""+textInputSize+"\" characters.<br> \n\
The text input is set to a password field.");

            jQuery("#addParametersForTextInput").children("#StylingForTextInput").hide("slow");
            jQuery("#addParametersForTextInput").children("#endOfInsertionTextInput").hide("slow");
            jQuery("#addParametersForTextInput").children("#insertTextInputName").show("slow");
            jQuery("#addParametersForTextInput").children("span").show("slow");
        }
    });

    jQuery("#endOfInsertionTextInput").unbind("click").bind('click',function () {
        jQuery("#addParametersForTextInput").children("span").html("<br>A Text Input Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addParametersForTextInput").children("span").show("slow").unbind();
        jQuery("#addParametersForTextInput").children("#StylingForTextInput").hide("slow").unbind();
        jQuery("#addParametersForTextInput").children("#endOfInsertionTextInput").hide("slow").unbind();
        jQuery("#addParametersForTextInput").children("#insertTextInputName").hide("slow").unbind();
           jQuery("#endOfInsertionTextInput").unbind();
               jQuery("#submitTextFieldParameters").unbind();
                   jQuery("input:radio[name=textorPasswordRadio]").unbind();
                       jQuery("#submitTextforTextInput").unbind();
                           jQuery("#endOfInsertionTextInput").unbind();
       insertNewFormElement();

    });

    jQuery("#submitTextforTextInput").click(function () {

        var formElementLabel = jQuery('.formElementLabel').val();
        var textInputType =  jQuery('input:radio[name=textorPasswordRadio]:checked').val();
        var textInputSize = jQuery('.textInputSizeMenu').val();
        var defaultText = jQuery("textarea[name=setDefaulttext]").val();
        var maskedInputChoice = jQuery("input:radio[name=maskedInputChoice]:checked").val();
        var textInputName = jQuery('.textInputText').val();
        if (textInputName == "")
        {
            jQuery("#addParametersForTextInput").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersForTextInput").children("span").fadeIn(1500);
            jQuery("#addParametersForTextInput").children("span").fadeOut(1500);
        }
        else
        {
            var textInputDataToPost = {
                "formElementName": formElementLabel,
                "textInputName": textInputName,
                "textInputValue" : defaultText,
                "textInputType": textInputType,
                "textInputSize": textInputSize,
                "maskedInputChoice" : maskedInputChoice
            };
            var myurlToProduceTextInput = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceTextInput]").val();
            // var myurlToProduceTextInput ="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditTextInput";
            //jQuery('#tempdivcontainer').show();
            jQuery("#submitTextforTextInput").unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceTextInput, textInputDataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);
                var textInput = jQuery('#tempdivcontainer #WYSIWYGTextInput').html();
                if (textInput == 0)
                {
                    jQuery("#addParametersForTextInput").children("span").html(
                        "<br> ERROR!! A new text input Object \""+textInputName+"\" has NOT been made. <br>\n\
It already exists in the database. Please choose a unique label.");
                    insertPropertiesForFormElement("text_input", formElementLabel);
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementTextInput"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).html(textInput);
                        jQuery("#addParametersForTextInput").children("span").html(" A new text input Object \""+textInputName+"\" has been configured and stored.");
                    }

                    else
                    {
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(textInput);
                        jQuery("#addParametersForTextInput").children("span").html(" A new text input Object \""+textInputName+"\" has been configured and stored.");

                    }

                    insertPropertiesForFormElement("text_input", formElementLabel);
                }
            });
        }
    });
}